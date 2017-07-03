<?php

namespace Sofico\Webdriver;

use Exception;

/**
 * By default reads values from src/Config/config.ini and src/Config/{project_name}/config.{env}.ini, where {env} and {project_name} is specified in src/Config/config.ini.
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

    // External
    const PROJECT_NAME = 'project_name';
    const ENV = 'env';
    const BROWSER_NAME = 'browser_name';
    const DRIVER_DIR = 'driver_dir';
    const HUB_ADDRESS = 'hub_address';
    const STORE_RESULT = 'store_result';
    const REPORT = 'report';
    const REPORT_DIR = 'report_dir';
    const TEST_NAME = 'test_name';
    const DOMAIN = 'domain';
    const WAIT_BEFORE_ELEMENT_INIT = 'wait_before_element_init';

    const ELK_USERNAME = 'elk_username';
    const ELK_PASSWORD = 'elk_password';

    // Internal
    const LOG_FILE_NAME = 'driver.log';
    const SCREEN_FILE_NAME = 'endingScreen.png';

    protected $baseDir;
    protected $configDir;
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
        $this->basicConfigFile = $this->configDir . "/config.ini";
        if (!file_exists($this->basicConfigFile)) {
            throw new Exception($this->basicConfigFile . " not found");
        }
        $this->config = array_fill_keys(array('ini_default', 'ini_env', 'code', 'env'), array());
        $this->config['ini_default'] = parse_ini_file($this->basicConfigFile);
        $this->config['env'] = $_SERVER;
        $envConfigFile = "{$this->configDir}/config.{$this->getEnv()}.ini";
        if (!file_exists($envConfigFile)) {
            throw new Exception("$envConfigFile not found");
        }
        $this->config['ini_env'] = parse_ini_file($envConfigFile);
    }

    /**
     * @return string
     */
    public function getEnv(): string
    {
        return $this->getProperty(self::ENV);
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
    public function getDomain(): string
    {
        return $this->getProperty(self::DOMAIN);
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
    public function getProjectName(): string
    {
        return $this->getProperty(self::PROJECT_NAME);
    }

    /**
     * @return string
     */
    public function getHubAddress(): string
    {
        return $this->getProperty(self::HUB_ADDRESS, 'http://localhost:4444/wd/hub');
    }

    /**
     * @return bool
     */
    public function storeResult(): bool
    {
        return $this->getAsBoolean($this->getProperty(self::STORE_RESULT, true));
    }

    /**
     * @return bool
     */
    public function reportingActive(): bool
    {
        return $this->getAsBoolean($this->getProperty(self::REPORT, true));
    }

    /**
     * @return string
     */
    public function getCommonReportDir(): string
    {
        return $this->getProperty(self::REPORT_DIR, $this->baseDir . "/Reports");
    }

    /**
     * @return int
     */
    public function getWaitBeforeElInit(): int
    {
        return (int)$this->getProperty(self::WAIT_BEFORE_ELEMENT_INIT, 0);
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

    /**
     * @param $value
     * @return bool
     */
    protected function getAsBoolean($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
