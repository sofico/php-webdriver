<?php

namespace Sofico\Webdriver;

use Facebook\WebDriver\Remote\RemoteExecuteMethod;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;

/**
 * Class Module encapsulates other modules and elements.
 * @package Sofico\Webdriver
 */
abstract class Module extends RemoteWebElement implements Context
{
    use FindContextTrait {
        findElement as traitFindElement;
        findElements as traitFindElements;
        findModules as traitFindModules;
        findModule as traitFindModule;
    }
    use LoggingTrait;

    protected $webdriver;


    /**
     * Module constructor.
     * @param RemoteExecuteMethod $executor
     * @param string $id
     * @param RemoteDriver $webdriver
     */
    public function __construct(RemoteExecuteMethod $executor, string $id, RemoteDriver $webdriver = null)
    {
        parent::__construct($executor, $id);
        $isModule = !is_null($webdriver);
        if ($isModule) {
            $this->webdriver = $webdriver;
            $this->initializeElements();
        }
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
        return $this->traitFindModule($by, $class, true);
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
