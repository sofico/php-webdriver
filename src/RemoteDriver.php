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
class RemoteDriver extends RemoteWebDriver
{

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
            file_exists($config->getCommonReportDir()) ? "" : mkdir($config->getCommonReportDir());
            $this->testReportDir = "{$config->getBaseDir()}/Reports/{$this->timestamp}_{$this->config->getProperty(BasicConfig::TEST_NAME)}";
            mkdir($this->testReportDir);
            $logFile = "{$this->testReportDir}/driver.log";
            fopen($logFile, 'a');
            $this->logger = new Logger('DriverLogger');
            $this->logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));
        }
    }

    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return mixed
     */
    public function findModule(WebDriverBy $by, string $class)
    {
        $params = ['using' => $by->getMechanism(), 'value' => $by->getValue()];
        $raw_element = $this->execute(
            DriverCommand::FIND_ELEMENT,
            $params
        );

        return $this->newModule($raw_element['ELEMENT'], $class);
    }

    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return array
     */
    public function findModules(WebDriverBy $by, string $class)
    {
        $params = ['using' => $by->getMechanism(), 'value' => $by->getValue()];
        $raw_elements = $this->execute(
            DriverCommand::FIND_ELEMENTS,
            $params
        );

        $elements = [];
        foreach ($raw_elements as $raw_element) {
            $elements[] = $this->newModule($raw_element['ELEMENT'], $class);
        }

        return $elements;
    }

    /**
     * @param $id
     * @param $class
     * @return mixed
     */
    protected function newModule($id, $class)
    {
        return new $class($this->getExecuteMethod(), $id);
    }

    /**
     * @param $level
     * @param $message
     */
    public function log($level, $message)
    {
        $context = array(debug_backtrace()[1]['class']);
        $context[] = debug_backtrace()[1]['function'];
        $this->reportingActive ? $this->logger->log($level, $message, $context) : "";
    }

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


}
