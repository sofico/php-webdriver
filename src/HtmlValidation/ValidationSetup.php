<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 8.6.17
 * Time: 16:06
 */

namespace Sofico\Webdriver\HtmlValidation;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Sofico\Webdriver\BasicConfig;
use Sofico\Webdriver\HtmlValidation\Modules\MessageModule;
use Sofico\Webdriver\HtmlValidation\Pages\ValidationPage;
use Sofico\Webdriver\RemoteDriver;
use function fclose;
use function file_exists;
use function fopen;
use function fwrite;
use function is_null;
use function mkdir;
use function strpos;

abstract class ValidationSetup extends TestCase
{
    protected static $BOM; // if not used, file encoding is wrong in created HTML files

    /** @var RemoteDriver */
    protected $driver;
    /** @var String[] */
    protected $ignoredErrorStrings;

    protected function setUp()
    {
        $this->ignoredErrorStrings = parse_ini_file("{$this->getChildDir()}/Config/ignored.ini")['ignored_error'];
        self::$BOM = chr(239) . chr(187) . chr(191);

        $config = new BasicConfig();
        /** @var BasicConfig $config */
        $config->addProperty(BasicConfig::TEST_NAME, $this->getName());
        $this->driver = $this->createDriver($config);
        $this->driver->manage()->window()->maximize();
    }

    private function getChildDir(): string
    {
        $class_info = new ReflectionClass($this);
        return dirname($class_info->getFileName());
    }

    protected function createDriver(BasicConfig $config): RemoteDriver
    {
        $driverDir = $config->getDriverDir();
        putenv("webdriver.chrome.driver=$driverDir/chromedriver");
        $capabilities = DesiredCapabilities::chrome();
        $hub = $config->getHubAddress();
        $driver = RemoteDriver::create($hub, $capabilities);
        /** @var RemoteDriver $driver */
        $driver->withConfig($config);
        return $driver;
    }


    protected function tearDown()
    {
        parent::tearDown();
        $this->driver->quit();
    }


    protected function validateAndLog(string $state = '')
    {
        $url = $this->driver->getCurrentURL();

        $this->createTempFile();
        $this->savePageSourceToReport($state);
        $validationPage = $this->openValidatorInNewTab();
        /** @var ValidationPage $validationPage */
        $resultPage = $validationPage->submitFragmentFromTempFile("{$this->getChildDir()}/temp/source.html");
        $messagesEls = $resultPage->getMessageEls();
        $messageElsWithoutIgnored = $this->filterIgnoredMessages($messagesEls);
        $this->logErrors($url, $state, $messageElsWithoutIgnored);
        $this->closeTab();
        $this->driver->switchTo()->window($this->driver->getWindowHandles()[0]);
    }

    private function createTempFile()
    {
        $tempDirectory = $this->getChildDir() . '/temp';
        $tempFilePath = $tempDirectory . '/source.html';
        if (!file_exists($tempDirectory)) mkdir($tempDirectory, 0777, true);
        $tempSource = fopen($tempFilePath, 'wb');

        fwrite($tempSource, self::$BOM . $this->driver->getPageSource());
        fclose($tempSource);
    }

    private function savePageSourceToReport(string $state)
    {
        $pathToPageSource = "{$this->driver->getTestReportDir()}/$state.html";
        $pageSource = fopen($pathToPageSource, 'wb');
        fwrite($pageSource, self::$BOM . $this->driver->getPageSource());
        fclose($pageSource);
    }

    private function openValidatorInNewTab(): ValidationPage
    {
        $this->driver->executeScript("window.open('https://validator.w3.org/#validate_by_upload+with_options', '_blank');");
        $this->driver->switchTo()->window($this->driver->getWindowHandles()[1]);
        return $this->driver->initPage(ValidationPage::class);
    }

    /**
     * @param $messagesEls
     * @param $messageElsWithoutIgnored
     * @return array
     */
    protected function filterIgnoredMessages($messagesEls): array
    {
        $messageElsWithoutIgnored = [];
        if (!is_null($messagesEls)) {
            foreach ($messagesEls as $message) {
                if (!$this->messageShouldBeIgnored($message)) $messageElsWithoutIgnored[] = $message;
            }
        }
        return $messageElsWithoutIgnored;
    }

    private function messageShouldBeIgnored(MessageModule $message): bool
    {
        $found = false;
        foreach ($this->ignoredErrorStrings as $ignoredErrorString) {
            if (strpos($message->getDescription(), $ignoredErrorString) !== false) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    /**
     * @param $url
     * @param $state
     * @param MessageModule[] $messageEls
     */
    private function logErrors($url, $state, $messageEls)
    {
        $resultFolderPath = "{$this->driver->getTestReportDir()}/";
        $resultFilePath = "$resultFolderPath/results.html";
        if (!file_exists($resultFolderPath)) mkdir($resultFolderPath, 0777, true);
        if (!file_exists($resultFilePath)) {
            $resultFile = fopen($resultFilePath, 'a');
            fwrite($resultFile, self::$BOM);
        } else {
            $resultFile = fopen($resultFilePath, 'a');
        }
        fwrite($resultFile, "<a href='$url'><h3 style='display: inline'>$state</h3></a><br>");
        foreach ($messageEls as $message) {
            $description = $message->getDescription();
            $statusColor = $message->getStatus() === MessageModule::$ERROR ? 'red' : 'orange';
            $line = $message->getLine();
            fwrite($resultFile, "<div style='color: $statusColor; padding-left: 4%'>$line: $description</div>");
        }
    }

    public function closeTab()
    {
        $this->driver->executeScript("close();");
    }
}
