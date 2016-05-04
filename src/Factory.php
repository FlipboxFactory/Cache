<?php

/**
 * Cache Factory
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

use Flipbox\Cache\Exceptions\InvalidDriverException;
use Flipbox\Skeleton\Helpers\ArrayHelper;
use Flipbox\Skeleton\Helpers\ObjectHelper;
use Flipbox\Skeleton\Logger\AutoLoggerTrait;
use Psr\Log\LoggerInterface;
use Stash\Driver\BlackHole as DummyDriver;
use Stash\Interfaces\DriverInterface;
use Stash\Interfaces\PoolInterface;

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
     * @var array
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
    public function create($config = [])
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
     * @return mixed
     * @throws InvalidDriverException
     */
    public function autoGetDriver($identifier)
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
     * @return mixed
     * @throws InvalidDriverException
     */
    public function getDriver($identifier)
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
     * @param $drivers
     * @throws InvalidDriverException
     */
    public function registerDrivers($drivers)
    {

        foreach ($drivers as $handle => $driver) {

            $this->registerDriver($handle, $driver);

        }

    }

    /**
     * Get an array of registered cache drivers
     *
     * @return array
     */
    public function getDrivers()
    {
        return $this->_registeredDrivers;
    }

}
