<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 9.5.17
 * Time: 17:38
 */

namespace Sofico\Webdriver;


use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\RemoteExecuteMethod;
use Facebook\WebDriver\WebDriverBy;

trait FindModuleTrait
{
    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return mixed
     */
    private function findModule(WebDriverBy $by, string $class, bool $nested)
    {
        $params = [
            'using' => $by->getMechanism(),
            'value' => $by->getValue()
        ];
        if ($nested) $params[':id'] = $this->id;
        $command = $nested ? DriverCommand::FIND_CHILD_ELEMENT : DriverCommand::FIND_ELEMENT;
        $raw_element = $this->getWebdriver()->execute($command, $params);

        return $this->newModule($raw_element['ELEMENT'], $class);
    }

    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return array
     */
    private function findModules(WebDriverBy $by, string $class, bool $nested)
    {
        $params = [
            'using' => $by->getMechanism(),
            'value' => $by->getValue()
        ];
        if ($nested) $params[':id'] = $this->id;
        $command = $nested ? DriverCommand::FIND_CHILD_ELEMENTS : DriverCommand::FIND_ELEMENTS;
        $raw_elements = $this->getWebdriver()->execute($command, $params);

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
    private function newModule($id, $class)
    {
        return new $class(new RemoteExecuteMethod($this->getWebdriver()), $id, $this->getWebdriver());
    }
}
