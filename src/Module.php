<?php

namespace Sofico\Webdriver;

use Facebook\WebDriver\Remote\RemoteExecuteMethod;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;

/**
 * Class Module encapsulates other modules and elements.
 * @package Sofico\Webdriver
 */
abstract class Module extends RemoteElement implements Context
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

    /**
     * Module constructor.
     * @param RemoteExecuteMethod $executor
     * @param string $id
     */
    public function __construct(RemoteExecuteMethod $executor, string $id, RemoteDriver $webdriver)
    {
        parent::__construct($executor, $id, $webdriver);
        $this->initializeElements();
    }

    /**
     * Override to initialize elements.
     */
    protected abstract function initializeElements();

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
     * @param WebDriverBy $by
     * @return RemoteWebElement
     */
    public function findElement(WebDriverBy $by, bool $throwEx = true)
    {
        return $this->traitFindElement($by, $throwEx, true);
    }

    /**
     * @param WebDriverBy $by
     * @param int $timeout in s (default 5)
     * @return mixed
     */
    public function waitForElement(WebDriverBy $by, int $timeout = 5)
    {
        return $this->traitWaitForElement($by, $timeout, true);
    }

    /**
     * @param WebDriverBy $by
     * @return RemoteWebElement[]
     */
    public function findElements(WebDriverBy $by)
    {
        return $this->traitFindElements($by, true);
    }


    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return mixed
     */
    public function findModule(WebDriverBy $by, string $class, bool $throwEx = true)
    {
        return $this->traitFindModule($by, $class, $throwEx, true);
    }

    /**
     * @param WebDriverBy $by
     * @param string $class
     * @param int $timeout
     * @return mixed
     */
    public function waitForModule(WebDriverBy $by, string $class, int $timeout = 5)
    {
        return $this->traitWaitForModule($by, $class, $timeout, true);
    }

    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return array
     */
    public function findModules(WebDriverBy $by, string $class)
    {
        return $this->traitFindModules($by, $class, true);
    }

    /**
     * @param string $id
     * @return RemoteWebElement
     */
    protected function newElement($id)
    {
        return new RemoteWebElement($this->getExecuteMethod(), $id);
    }

    /**
     * @return RemoteDriver
     */
    public function getWebdriver()
    {
        return $this->webdriver;
    }

    /**
     * @return RemoteExecuteMethod
     */
    public function getExecuteMethod()
    {
        return $this->executor;
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
