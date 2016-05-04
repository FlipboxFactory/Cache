<?php

/**
 * Pool
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

use Stash\Pool as BasePool;

class Pool extends BasePool implements PoolDurationInterface
{

    /**
     * The cache duration
     *
     * @var string|int
     */
    protected $itemDuration;

    /**
     * @inheritdoc
     */
    public function setItemDuration($duration)
    {
        $this->itemDuration = $duration;
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
