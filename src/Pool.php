<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/cache/blob/master/LICENSE
 * @link       https://github.com/flipbox/cache
 */

namespace Flipbox\Cache;

use Stash\Pool as BasePool;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
class Pool extends BasePool implements PoolDurationInterface
{

    /**
     * The cache duration
     *
     * @var null|int|\DateInterval
     */
    protected $itemDuration;

    /**
     * @inheritdoc
     */
    public function setItemDuration($duration)
    {
        if(!$duration instanceof \DateInterval) {
            $duration = (int) $duration;
        }

        $this->itemDuration = $duration;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getItemDuration()
    {
        return $this->itemDuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        // Get cache item
        $item = parent::getItem($key);

        // Set the expiration
        $item->expiresAfter($this->getItemDuration());

        return $item;
    }
}
