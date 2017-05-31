<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/cache/blob/master/LICENSE
 * @link       https://github.com/flipbox/cache
 */

namespace Flipbox\Cache\Middleware;

use Flipbox\Cache\Exceptions\InvalidCachePoolException;
use Flipbox\Http\Stream\Factory as StreamFactory;
use Flipbox\Relay\Middleware\AbstractMiddleware;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Stash\Interfaces\ItemInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
class Cache extends AbstractMiddleware
{

    /**
     * @var CacheItemPoolInterface The connection
     */
    public $pool;

    /**
     * @inheritdoc
     */
    public function init()
    {
        // Parent
        parent::init();

        // Ensure we have a valid pool
        if (!$this->pool instanceof CacheItemPoolInterface) {
            throw new InvalidCachePoolException(
                sprintf(
                    "The class '%s' requires a cache pool that is an instance of '%s'",
                    get_class($this),
                    'Psr\Cache\CacheItemPoolInterface'
                )
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next = null): ResponseInterface
    {
        // Do parent (logging)
        parent::__invoke($request, $response);

        // Create a cache key
        $key = $this->getCacheKey($request);

        /** @var ItemInterface $item */
        $item = $this->pool->getItem($key);

        // If it's cached
        if ($item->isHit()) {
            $this->info(
                "Item found in Cache", [
                'key' => $key,
                'expires' => $item->getExpiration()
            ]);

            // Add response body
            $response = $response->withBody(
                StreamFactory::create($item->get())
            );

            return $response;
        } else {
            $this->info(
                "Item not found in Cache", [
                'key' => $key
            ]);
        }

        // Lock item
        $item->lock();

        /** @var ResponseInterface $response */
        $response = $next($request, $response);

        // Only cache successful responses
        if ($this->isResponseSuccessful($response)) {
            // Set cache contents
            $item->set($response->getBody()->getContents());

            // Save cache item
            $this->pool->save($item);

            $this->info(
                "Save item to Cache", [
                'key' => $key,
                'expires' => $item->getExpiration()
            ]);
        } else {
            $this->info(
                "Did not save to cache because request was unsuccessful.", [
                'key' => $key,
                'statusCode' => $response->getStatusCode()
            ]);
        }

        return $response;
    }

    /**
     * Returns the id used to cache a request.
     *
     * @param RequestInterface $request
     *
     * @return string
     */
    private function getCacheKey(RequestInterface $request): string
    {
        return $request->getMethod() . md5((string)$request->getUri());
    }
}
