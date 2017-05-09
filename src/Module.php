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
class Module extends RemoteWebElement
{
    /**
     * Module constructor.
     * @param RemoteExecuteMethod $executor
     * @param string $id
     */
    public function __construct(RemoteExecuteMethod $executor, $id)
    {
        parent::__construct($executor, $id);
        $this->initializeElements();
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
        $params = [
            'using' => $by->getMechanism(),
            'value' => $by->getValue(),
            ':id' => $this->id,
        ];
        $raw_element = $this->executor->execute(
            DriverCommand::FIND_CHILD_ELEMENT,
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
        $params = [
            'using' => $by->getMechanism(),
            'value' => $by->getValue(),
            ':id' => $this->id,
        ];
        $raw_elements = $this->executor->execute(
            DriverCommand::FIND_CHILD_ELEMENTS,
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
        return new $class($this->executor, $id);
    }

}
