<?php

namespace Lavary\Menus\Configuration;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration
{
    /**
     * Store parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * The instance of the configuration class
     *
     * @var $this
     */
    protected static $instance;

    /**
     * Process the configuration file into an array
     *
     * @param string $filename
     */
    public function __construct($filename = null)
    {
        $this->parameters = $this->process(
            $this->parse(
                $this->load($filename)
            )
        );
    }

    /**
     * Handle the configuration settings
     *
     * @param array $settings
     *
     * @return array
     */
    protected function process($settings)
    {
        try {
            return (new Processor())->processConfiguration(
                new Definition(),
                $settings
            );
        } catch (InvalidConfigurationException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Load configuration files and parse them
     *
     * @param string $yml
     *
     * @return array
     */
    protected function parse($yml)
    {
        return Yaml::parse($yml);
    }

    /**
     * Locate the right config file and return its name
     *
     * @param string $filename
     *
     * @return string
     */
    protected function load($filename = null)
    {
        foreach ([
            $filename,
            __DIR__ . '/../../../../../menu.yml',
            __DIR__ . '/../../menu.yml',
            __DIR__ . '/../menu.yml'
        
        ] as $file) {
            if (file_exists($file)) {
                return file_get_contents($file);
            }
        }

        return null;
    }

    /**
     * Set a parameter
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return array
     */
    public function set($key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Check if a parameter exist
     *
     * @param  string $key
     *
     * @return boolean
     */
    public function has($key)
    {
        if (! $array) {
            return false;
        }

        if (is_null($key)) {
            return false;
        }

        if (array_key_exists($key, $this->parameters)) {
            return true;
        }

        $array = $this->parameters;
        
        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($key, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Return a parameter based on a key
     *
     * @param  string $key
     *
     * @return string
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }
        
        $array = $this->parameters;

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return null;
            }
        }

        return $array;
    }

    /**
     * Return all the parameters as an array
     *
     * @return array
     */
    public function all()
    {
        return $this->parameters;
    }
}
