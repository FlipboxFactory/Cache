<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/cache/blob/master/LICENSE
 * @link       https://github.com/flipbox/cache
 */

namespace Flipbox\Cache\Exceptions;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
class InvalidDriverException extends \Exception
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Invalid Cache Driver';
    }
}
