<?php

namespace Sofico\Webdriver;

use Facebook\WebDriver\Remote\RemoteExecuteMethod;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;

abstract class Page implements Context
{
    use FindContextTrait {
        findElement as traitFindElement;
        findElements as traitFindElements;
        findModules as traitFindModules;
        findModule as traitFindModule;
    }
    use LoggingTrait;

    protected $webdriver;
    protected $address;
    protected $executeMethod;

    /**
     * Page constructor.
     * @param RemoteDriver $webdriver
     * @param bool $init
     */
    public function __construct(RemoteDriver $webdriver, bool $init = true, RemoteExecuteMethod $executeMethod)
    {
        $this->executeMethod = $executeMethod;
        $this->webdriver = $webdriver;
        $this->address = $this->getBaseUrl() . $this->getUrl();
        if ($init) $this->initializeElements();
    }

    /**
     * @param string $pageClass
     * @param bool $initElements
     * @return mixed
     */
    public function initPage(string $pageClass, bool $initElements = true)
    {
        return $this->webdriver->initPage($pageClass, $initElements);
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
    protected abstract function initializeElements();

    /**
     * @param WebDriverBy $by
     * @return RemoteWebElement
     */
    public function findElement(WebDriverBy $by)
    {
        return $this->traitFindElement($by, false);
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
    protected function getBaseUrl(): string
    {
        return $this->webdriver->getConfig()->getBaseUrl();
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
