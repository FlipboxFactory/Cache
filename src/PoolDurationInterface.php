<?php

/**
 * Pool Duration Interface
 *
 * Set a default duration for all cache items within the pool.
 *
 * @package    Cache
 * @author     Flipbox Factory <hello@flipboxfactory.com>
 * @copyright  2010-2016 Flipbox Digital Limited
 * @license    https://github.com/FlipboxFactory/Cache/blob/master/LICENSE
 * @version    Release: 1.0.0
 * @link       https://github.com/FlipboxFactory/Cache
 * @since      Class available since Release 1.0.0
 */

namespace Flipbox\Cache;

use Stash\Interfaces\PoolInterface;

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
