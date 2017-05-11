<?php

namespace Sofico\Webdriver;

use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\RemoteExecuteMethod;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;

/**
 * Class Module encapsulates other modules and elements.
 * @package Sofico\Webdriver
 */
class Module extends RemoteWebElement implements Context
{
    use FindModuleTrait {
        findModules as traitFindModules;
        findModule as traitFindModule;
    }

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
    protected function initializeElements()
    {
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
        return new RemoteWebElement($this->executor, $id);
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
        $this->getWebdriver()->getProperty($propertyName);
    }
}
