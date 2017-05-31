<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/cache/blob/master/LICENSE
 * @link       https://github.com/flipbox/cache
 */

namespace Flipbox\Cache;

use Flipbox\Cache\Exceptions\InvalidDriverException;
use Flipbox\Skeleton\Helpers\ArrayHelper;
use Flipbox\Skeleton\Helpers\ObjectHelper;
use Flipbox\Skeleton\Logger\AutoLoggerTrait;
use Psr\Log\LoggerInterface;
use Stash\Driver\BlackHole as DummyDriver;
use Stash\Interfaces\DriverInterface;
use Stash\Interfaces\PoolInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
class Factory
{

    use AutoLoggerTrait;

    /**
     * This is the default time, in seconds, that objects are cached for.
     *
     * @var int
     */
    public $defaultDuration = 432000;

    /**
     * The default driver
     *
     * @var string
     */
    public $defaultDriver = 'file';

    /**
     * Registered cache drivers
     *
     * @var DriverInterface[]
     */
    protected $_registeredDrivers = [];

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);

        // Manually register the default driver
        $this->_registeredDrivers['dummy'] = new DummyDriver();
    }

    /*******************************************
     * CREATE
     *******************************************/

    /**
     * Create a new cache pool
     *
     * @param array $config
     * @return PoolInterface
     * @throws InvalidDriverException
     */
    public function create($config = []): PoolInterface
    {
        // Array
        $config = ArrayHelper::toArray($config);

        // Get driver from config
        if (!$driverType = ObjectHelper::findClassFromConfig($config)) {

            $driverType = $this->defaultDriver;

            $this->warning(
                "Cache pool configuration did not indicate a driver type...using default",
                [
                    'config' => $config,
                    'default' => $driverType
                ]
            );

        }

        /** @var Pool $pool */
        $pool = new Pool(
            $this->autoGetDriver($driverType)
        );

        // Set logger
        $pool->setLogger($this->getLogger());

        // Set duration
        $pool->setItemDuration(ArrayHelper::getValue(
            $config,
            'duration',
            $this->defaultDuration
        ));

        return $pool;
    }

    /**
     * Get a cache driver.  If the driver is not found, return the default.
     *
     * @param $identifier
     * @return DriverInterface
     * @throws InvalidDriverException
     */
    public function autoGetDriver($identifier): DriverInterface
    {
        if (!$driver = ArrayHelper::getValue($this->_registeredDrivers, $identifier)) {

            $this->critical(
                "Cache driver not available...switching to default",
                [
                    'driver' => $identifier,
                    'default' => $this->defaultDriver,
                    'registered' => array_keys(
                        $this->_registeredDrivers
                    )
                ]
            );

            // Get default driver
            $driver = $this->getDriver($this->defaultDriver);

        }
        return $driver;
    }

    /**
     * Get a cache driver
     *
     * @param $identifier
     * @return DriverInterface
     * @throws InvalidDriverException
     */
    public function getDriver($identifier): DriverInterface
    {
        if (!$driver = ArrayHelper::getValue($this->_registeredDrivers, $identifier)) {

            throw new InvalidDriverException(sprintf(
                "Driver type '%s' is not registered.",
                $identifier
            ));

        }
        return $driver;
    }

    /**
     * Register a cache driver for use
     *
     * @param $identifier
     * @param DriverInterface $driver
     * @throws InvalidDriverException
     */
    public function registerDriver($identifier, DriverInterface $driver)
    {
        // Handle already taken
        if (ArrayHelper::keyExists($identifier, $this->_registeredDrivers)) {

            throw new InvalidDriverException(sprintf(
                "Driver type '%s' is already registered.",
                $identifier
            ));

        }

        $this->info(
            "Registered cache driver",
            [
                'driver' => $identifier,
                'class' => get_class($driver)
            ]
        );

        $this->_registeredDrivers[$identifier] = $driver;
    }

    /**
     * Register an array of drivers
     *
     * @param DriverInterface[] $drivers
     * @throws InvalidDriverException
     */
    public function registerDrivers(array $drivers)
    {
        foreach ($drivers as $handle => $driver) {

            $this->registerDriver($handle, $driver);

        }
    }

    /**
     * Get an array of registered cache drivers
     *
     * @return DriverInterface[]
     */
    public function getDrivers(): array
    {
        return $this->_registeredDrivers;
    }
}
