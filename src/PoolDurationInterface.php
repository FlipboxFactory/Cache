<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/cache/blob/master/LICENSE
 * @link       https://github.com/flipbox/cache
 */

namespace Flipbox\Cache;

use Stash\Interfaces\PoolInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
interface PoolDurationInterface extends PoolInterface
{

    /**
     * Set the cache duration
     *
     * @param $duration
     */
    public function setItemDuration($duration);

    /**
     * Return the cache duration
     *
     * @return string|int
     */
    public function getItemDuration();

}
