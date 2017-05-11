<?php

namespace Sofico\Webdriver;

use Exception;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Throwable;

/**
 * Extend this to get environment ready to test.
 * @package Sofico\Webdriver
 */
abstract class BasicTest extends TestCase
{
    /** @var RemoteDriver */
    protected $driver;

    protected function setUp()
    {
        $config = new BasicConfig();
        $driverDir = $config->getDriverDir();
        $browserName = $config->getBrowserName();
        $capabilities = null;
        switch ($browserName) {
            case WebDriverBrowserType::FIREFOX:
                putenv("webdriver.gecko.driver=$driverDir/geckodriver");
                $capabilities = DesiredCapabilities::firefox();
                break;
            case WebDriverBrowserType::CHROME:
                putenv("webdriver.chrome.driver=$driverDir/chromedriver");
                $capabilities = DesiredCapabilities::chrome();
                break;
            case WebDriverBrowserType::IE:
                putenv("webdriver.ie.driver=$driverDir/IEDriverServer.exe");
                $capabilities = DesiredCapabilities::internetExplorer();
                break;
            default:
                throw new Exception("Unsupported browser $browserName");
        }

        $hub = $config->getHubAddress();
        $this->driver = RemoteDriver::create($hub, $capabilities);
        $config->addProperty(BasicConfig::TEST_NAME, $this->getName());
        $this->driver->withConfig($config);

    }


    protected function tearDown()
    {
        $this->driver->logResultScreen();
        $this->driver->quit();
    }

    protected function onNotSuccessfulTest(Throwable $e)
    {
        $this->driver->log(LogLevel::ERROR, "{$e->getMessage()} \n{$e->getTraceAsString()}");
    }

}
