<?php

namespace Sofico\Webdriver;

use Exception;

/**
 * By default reads values from src/Config/config.ini and src/Config/config.{env}.ini, where {env} is specified in src/Config/config.ini.
 * Minimal required values are: <ul>
 *  <li>env - used for loading proper file with environment specific values</li>
 *  <li>driver_dir - path to drivers</li>
 *  <li>browser_name - now supporting values chrome, firefox, internet explorer</li>
 *  <li>base_url - base url to which pages will append their path</li>
 * </ul>
 * @package Sofico\Webdriver
 */
class BasicConfig
{

    const ENV = 'env';
    const DRIVER_DIR = 'driver_dir';
    const BROWSER_NAME = 'browser_name';
    const HUB_ADDRESS = 'hub_address';
    const REPORT = 'report';
    const TEST_NAME = 'test_name';
    const BASE_URL = 'base_url';

    protected $baseDir;
    protected $configDir;
    protected $commonReportDir;
    protected $basicConfigFile;
    protected $config = [];

    /**
     * BasicConfig constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->baseDir = dirname(dirname(dirname(dirname(__DIR__))));
        $this->configDir = $this->baseDir . "/src/Config";
        $this->commonReportDir = $this->baseDir . "/Reports";
        $this->basicConfigFile = $this->configDir . "/config.ini";
        if (!file_exists($this->basicConfigFile)) {
            throw new Exception($this->basicConfigFile . " not found");
        }
        $this->config['default'] = parse_ini_file($this->basicConfigFile);
        $env = $this->getProperty(self::ENV);
        $this->config['env'] = parse_ini_file("{$this->configDir}/config.$env.ini");

    }

    /**
     * @return string
     */
    public function getDriverDir(): string
    {
        return $this->getProperty(self::DRIVER_DIR);
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->getProperty(self::BASE_URL);
    }

    /**
     * @return string
     */
    public function getBrowserName(): string
    {
        return $this->getProperty(self::BROWSER_NAME);
    }

    /**
     * @return string
     */
    public function getHubAddress(): string
    {
        return $this->getProperty(self::HUB_ADDRESS, 'http://localhost:4444/wd/hub');
    }

    /**
     * @return string
     */
    public function reportingActive(): string
    {
        return $this->getProperty(self::REPORT, true);
    }

    /**
     * @return string
     */
    public function getCommonReportDir(): string
    {
        return $this->commonReportDir;
    }

    /**
     * @return string
     */
    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    /**
     * @return string
     */
    public function getConfigDir(): string
    {
        return $this->configDir;
    }

    /**
     * Later properties in $config override the preceding ones.
     * @param string $propertyName
     * @param null $default
     * @return string
     * @throws Exception
     */
    public function getProperty(string $propertyName, $default = null): string
    {
        $property = null;
        foreach ($this->config as $configItem) {
            $property = array_key_exists($propertyName, $configItem) ? $configItem[$propertyName] : $property;
        }
        if (is_null($property) && !is_null($default)) {
            return $default;
        }
        if (is_null($property) && is_null($default)) {
            throw new Exception($propertyName . " property not found");
        }
        return $property;
    }

    /**
     * @param string $propertyName
     * @param string $propertyValue
     */
    public function addProperty(string $propertyName, string $propertyValue)
    {
        $this->config['code'][$propertyName] = $propertyValue;
    }


}
