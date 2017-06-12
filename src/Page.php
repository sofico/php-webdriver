<?php

namespace Sofico\Webdriver;

use Facebook\WebDriver\Remote\RemoteExecuteMethod;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use function get_class;
use function sleep;

abstract class Page implements Context
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

    protected $webdriver;
    protected $domain;
    protected $address;
    protected $executeMethod;

    /**
     * Page constructor.
     * @param RemoteDriver $webdriver
     * @param bool $init
     */
    public function __construct(RemoteDriver $webdriver, RemoteExecuteMethod $executeMethod)
    {
        $this->executeMethod = $executeMethod;
        $this->webdriver = $webdriver;
        $this->domain = $webdriver->getConfig()->getDomain();
        $this->address = $this->domain . $this->getUrl();
    }

    /**
     * @param string $pageClass
     * @param bool $initElements
     * @return mixed
     */
    public function initPage(string $pageClass)
    {
        return $this->webdriver->initPage($pageClass);
    }

    public function initThisPage()
    {
        return $this->webdriver->initPage(get_class($this));
    }

    /**
     * Navigate to this page.
     * @return $this
     */
    public function goTo()
    {
        $this->webdriver->get($this->address);
        $this->initializeElements();
        return $this;
    }

    /**
     * @param String $path
     */
    public function takeScreenshot(String $path)
    {
        $this->webdriver->takeScreenshot($path);
    }

    /**
     * Override this to initialize elements
     */
    public function initializeElements()
    {
        sleep($this->webdriver->getConfig()->getWaitBeforeElInit());
        $this->beforeInitializeElements();
    }

    /**
     * Override this to perform action before element initialization
     */
    protected function beforeInitializeElements()
    {

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

    /**
     * Override this to get full path of page. Will be concatenated with baseUrl.
     * @return string
     */
    protected function getUrl(): string
    {
        return "";
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    protected function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return bool
     */
    protected function isCurrentPage(): bool
    {
        return $this->getAddress() === explode("?", $this->webdriver->getCurrentURL())[0];
    }


    /**
     * @return RemoteDriver
     */
    public function getWebdriver(): RemoteDriver
    {
        return $this->webdriver;
    }

    /**
     * @return RemoteExecuteMethod
     */
    public function getExecuteMethod(): RemoteExecuteMethod
    {
        return $this->executeMethod;
    }


    /**
     * @param string $propertyName
     * @return string
     */
    public function getProperty(string $propertyName): string
    {
        $this->getWebdriver()->getProperty($propertyName);
    }

}
