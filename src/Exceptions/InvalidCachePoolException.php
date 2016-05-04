<?php

/**
 * Invalid Cache Pool Exception
 *
 * @package    Cache
 * @author     Flipbox Factory <hello@flipboxfactory.com>
 * @copyright  2010-2016 Flipbox Digital Limited
 * @license    https://github.com/FlipboxFactory/Cache/blob/master/LICENSE
 * @version    Release: 1.0.0
 * @link       https://github.com/FlipboxFactory/Cache
 * @since      Class available since Release 1.0.0
 */

namespace Flipbox\Cache\Exceptions;

class InvalidCachePoolException extends \Exception
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Invalid Cache Pool';
    }
}