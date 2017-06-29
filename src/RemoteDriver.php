<?php

namespace Sofico\Webdriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use const true;
use function fclose;
use function fopen;
use function fwrite;

/**
 * Extended {@see WebDriver} with custom functions.
 * @package Sofico\Webdriver
 */
class RemoteDriver extends RemoteWebDriver implements Context
{
    use CommonTrait {
        findElement as traitFindElement;
        findElements as traitFindElements;
        findModules as traitFindModules;
        findModule as traitFindModule;
        waitForElement as traitWaitForElement;
        waitForModule as traitWaitForModule;
    }
    use LoggingTrait;


    /* @var Logger */
    protected $logger;
    protected $testReportDir;
    /* @var BasicConfig */
    protected $config;
    /* @var bool */
    protected $reportingActive;

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
        $date = date("Y-m-d_H.i.s.s");
        $projectName = $this->getConfig()->getProjectName();
        $env = $this->config->getEnv();
        $browserName = $this->config->getBrowserName();
        $testName = $this->config->getProperty(BasicConfig::TEST_NAME);

        $this->testReportDir = "{$config->getBaseDir()}/Reports/$date-$projectName-$env-$browserName-$testName";
        mkdir($this->testReportDir, 0777, true);
        $logFile = "{$this->testReportDir}/" . BasicConfig::LOG_FILE_NAME;
        fopen($logFile, 'a');
        return $logFile;
    }

    /**
     * @param $logFile
     */
    private function createLogger($logFile)
    {
        $this->logger = new Logger('DriverLogger');
        $fileHandler = new StreamHandler($logFile, Logger::DEBUG);
        $fileHandler->getFormatter()->ignoreEmptyContextAndExtra(true);
        $fileHandler->getFormatter()->includeStacktraces(true);
        $this->logger->pushHandler($fileHandler);

        $consoleHandler = new StreamHandler('php://stdout', Logger::DEBUG);
        $consoleHandler->getFormatter()->ignoreEmptyContextAndExtra(true);
        $consoleHandler->getFormatter()->includeStacktraces(true);
        $this->logger->pushHandler($consoleHandler);
    }

    /**
     * Use to navigate directly to page and initialize it.
     * @param string $pageClass
     * @return mixed
     */
    public function goToPage(string $pageClass)
    {
        return $this->constructPage($pageClass)->goTo();
    }

    /**
     * Use this to initialize page you got on by click or any other interaction with browser.
     * @param string $pageClass
     * @return mixed
     */
    public function initPage(string $pageClass)
    {
        $page = $this->constructPage($pageClass);
        $page->initializeElements();
        return $page;
    }

    /**
     * @param string $pageClass
     * @param bool $initElements
     * @return mixed
     */
    public function constructPage(string $pageClass)
    {
        return new $pageClass($this, $this->getExecuteMethod());
    }

    /**
     * @param WebDriverBy $by
     * @return RemoteWebElement
     */
    public function findElement(WebDriverBy $by, bool $throwEx = true)
    {
        return $this->traitFindElement($by, $throwEx, false);
    }

    /**
     * @param WebDriverBy $by
     * @param int $timeout in s (default 5)
     * @return mixed
     */
    public function waitForElement(WebDriverBy $by, int $timeout = 5)
    {
        return $this->traitWaitForElement($by, $timeout, false);
    }


    /**
     * @param WebDriverBy $by
     * @return RemoteWebElement[]
     */
    public function findElements(WebDriverBy $by)
    {
        return $this->traitFindElements($by, false);
    }

    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return mixed
     */
    public function findModule(WebDriverBy $by, string $class, bool $throwEx = true)
    {
        return $this->traitFindModule($by, $class, $throwEx, false);
    }

    /**
     * @param WebDriverBy $by
     * @param string $class
     * @param int $timeout
     * @return mixed
     */
    public function waitForModule(WebDriverBy $by, string $class, int $timeout = 5)
    {
        return $this->traitWaitForModule($by, $class, $timeout, false);
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

    public function reportPageSource()
    {
        if ($this->reportingActive) {
            $resource = fopen($this->getTestReportDir() . '/pagesource.html', 'wb');
            fwrite($resource, chr(239) . chr(187) . chr(191) . $this->getPageSource());
            fclose($resource);
        }
    }

    /**
     *
     */
    public function reportResultScreen()
    {
        $this->reportingActive ? $this->takeScreenshot($this->getTestReportDir() . '/' . BasicConfig::SCREEN_FILE_NAME) : "";
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

    /**
     * @return bool
     */
    public function isReportingActive(): bool
    {
        return $this->reportingActive;
    }

}
