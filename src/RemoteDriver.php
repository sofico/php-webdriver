<?php

namespace Sofico\Webdriver;

use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\HttpCommandExecutor;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Extended {@see WebDriver} with custom functions.
 * @package Sofico\Webdriver
 */
class RemoteDriver extends RemoteWebDriver implements Context
{
    use FindModuleTrait {
        findModules as traitFindModules;
        findModule as traitFindModule;
    }

    /* @var Logger */
    protected $logger;
    protected $timestamp;
    protected $testReportDir;
    /* @var BasicConfig */
    protected $config;
    /* @var bool */
    protected $reportingActive;

    public function __construct(HttpCommandExecutor $commandExecutor, $sessionId, $capabilities = null)
    {
        $this->timestamp = time();
        parent::__construct($commandExecutor, $sessionId, $capabilities);
    }

    /**
     * @param BasicConfig $config
     */
    public function withConfig(BasicConfig $config)
    {
        $this->config = $config;
        $this->reportingActive = $config->reportingActive();
        if ($this->reportingActive) {
            $logFile = $this->createLogFile($config);
            $this->createLogger($logFile);
        }
    }

    /**
     * @param BasicConfig $config
     * @return string
     */
    private function createLogFile(BasicConfig $config): string
    {
        file_exists($config->getCommonReportDir()) ? "" : mkdir($config->getCommonReportDir());
        $this->testReportDir = "{$config->getBaseDir()}/Reports/{$this->timestamp}_{$this->config->getProperty(BasicConfig::TEST_NAME)}";
        mkdir($this->testReportDir);
        $logFile = "{$this->testReportDir}/driver.log";
        fopen($logFile, 'a');
        return $logFile;
    }

    /**
     * @param $logFile
     */
    private function createLogger($logFile)
    {
        $this->logger = new Logger('DriverLogger');
        $handler = new StreamHandler($logFile, Logger::DEBUG);
        $handler->getFormatter()->ignoreEmptyContextAndExtra(true);
        $handler->getFormatter()->includeStacktraces(true);
        $this->logger->pushHandler($handler);
    }

    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return mixed
     */
    public function findModule(WebDriverBy $by, string $class)
    {
        return $this->traitFindModule($by, $class, false);
    }

    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return array
     */
    public function findModules(WebDriverBy $by, string $class)
    {
        return $this->traitFindModules($by, $class, false);
    }


    /**
     * @param $level
     * @param $message
     */
    public function log($level, $message)
    {
        $context = debug_backtrace()[1]['class'];
        $this->reportingActive ? $this->logger->log($level, "$context: $message") : "";
    }

    /**
     *
     */
    public function logResultScreen()
    {
        $this->reportingActive ? $this->takeScreenshot($this->getTestReportDir() . '/endingScreen.jpg') : "";
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    public function getTestReportDir()
    {
        return $this->testReportDir;
    }

    /**
     * @return BasicConfig
     */
    public function getConfig(): BasicConfig
    {
        return $this->config;
    }


    /**
     * @return RemoteDriver
     */
    public function getWebdriver(): self
    {
        return $this;
    }

    /**
     * @return HttpCommandExecutor|null
     */
    public function getExecutor()
    {
        return $this->executor;
    }

    /**
     * @param string $propertyName
     * @return string
     */
    public function getProperty(string $propertyName): string
    {
        return $this->config->getProperty($propertyName);
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

}
