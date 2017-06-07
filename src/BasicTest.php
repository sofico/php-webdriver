<?php

namespace Sofico\Webdriver;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Exception;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\BaseTestRunner;
use Psr\Log\LogLevel;
use ReflectionMethod;
use Sofico\Webdriver\Storage\ElasticsearchStorageImpl;
use Sofico\Webdriver\Storage\Result;
use Sofico\Webdriver\Storage\ResultStatus;
use Throwable;
use function microtime;

/**
 * Extend this to get environment ready to test.
 * @package Sofico\Webdriver
 */
abstract class BasicTest extends TestCase
{
    /** @var RemoteDriver */
    protected $driver;
    /** @var Result */
    protected $result;

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
        $this->driver->log(LogLevel::INFO, "=== {$this->getName()} starting ===");

        $this->result = new Result();
        $this->result->setBrowser($browserName);
        $this->result->setProjectName($config->getProjectName());
        $this->result->setTestname($this->getName());
        $this->result->setEnvironment($config->getEnv());
        $this->result->setStarted((int)round(microtime(true) * 1000));
        $this->result->setStatus(ResultStatus::SUCCESS);
        $this->result->setLogPath("{$this->driver->getTestReportDir()}/" . BasicConfig::LOG_FILE_NAME);
        $this->result->setScreenPath("{$this->driver->getTestReportDir()}/" . BasicConfig::SCREEN_FILE_NAME);
        $testConfig = $this->getTestConfigFromAnnotation();
        if (!is_null($testConfig)) $this->result->setSeverity($testConfig->getSeverity());
    }

    /**
     * @return TestConfig
     */
    private function getTestConfigFromAnnotation()
    {
        AnnotationRegistry::registerLoader('class_exists');
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $reflMethod = new ReflectionMethod(get_class($this), $this->getName());
        return $reader->getMethodAnnotation($reflMethod, TestConfig::class);
    }

    protected function tearDown()
    {
        $this->driver->logResultScreen();
        $this->driver->quit();
        $this->result->setEnded((int)round(microtime(true) * 1000));
        $this->driver->log(LogLevel::INFO, "=== {$this->getName()} finished ===");
        if ($this->getStatus() !== BaseTestRunner::STATUS_PASSED) {
            $this->result->setStatus(ResultStatus::FAILED);
            $this->result->setError($this->getStatusMessage());
        }
        if ($this->driver->getConfig()->storeResult()) (new ElasticsearchStorageImpl())->store($this->result);
    }

    protected function onNotSuccessfulTest(Throwable $e)
    {
        $this->driver->log(LogLevel::ERROR, "{$e->getMessage()} \n{$e->getTraceAsString()}");
        parent::onNotSuccessfulTest($e);
    }

}
