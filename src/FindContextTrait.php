<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 9.5.17
 * Time: 17:38
 */

namespace Sofico\Webdriver;


use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Psr\Log\LogLevel;

trait FindContextTrait
{
    /**
     * Find the first WebDriverElement using the given mechanism.
     *
     * @param WebDriverBy $by
     * @return RemoteWebElement NoSuchElementException is thrown in HttpCommandExecutor if no element is found.
     * @see WebDriverBy
     */
    public function findElement(WebDriverBy $by, bool $nested)
    {
        $this->log(LogLevel::INFO, "Looking for element with [{$by->getMechanism()}: '{$by->getValue()}']");
        $params = [
            'using' => $by->getMechanism(),
            'value' => $by->getValue()
        ];
        if ($nested) $params[':id'] = $this->id;
        $command = $nested ? DriverCommand::FIND_CHILD_ELEMENT : DriverCommand::FIND_ELEMENT;
        $raw_element = $this->getWebdriver()->execute($command, $params);

        return $this->newElement($raw_element['ELEMENT']);
    }

    /**
     * Find all WebDriverElements within the current page using the given mechanism.
     *
     * @param WebDriverBy $by
     * @return RemoteWebElement[] A list of all WebDriverElements, or an empty array if nothing matches
     * @see WebDriverBy
     */
    public function findElements(WebDriverBy $by, bool $nested)
    {
        $this->log(LogLevel::INFO, "Looking for elements with [{$by->getMechanism()}: '{$by->getValue()}']");

        $params = [
            'using' => $by->getMechanism(),
            'value' => $by->getValue()
        ];
        if ($nested) $params[':id'] = $this->id;
        $command = $nested ? DriverCommand::FIND_CHILD_ELEMENTS : DriverCommand::FIND_ELEMENTS;
        $raw_elements = $this->getWebdriver()->execute($command, $params);

        $elements = [];
        foreach ($raw_elements as $raw_element) {
            $elements[] = $this->newElement($raw_element['ELEMENT']);
        }

        return $elements;
    }

    /**
     * Return the WebDriverElement with the given id.
     *
     * @param string $id The id of the element to be created.
     * @return RemoteWebElement
     */
    protected function newElement($id)
    {
        return new RemoteWebElement($this->getExecuteMethod(), $id);
    }


    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return mixed
     */
    private function findModule(WebDriverBy $by, string $class, bool $nested)
    {
        $this->log(LogLevel::INFO, "Looking for module with [{$by->getMechanism()}: '{$by->getValue()}']");
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
        $this->log(LogLevel::INFO, "Looking for module with [{$by->getMechanism()}: '{$by->getValue()}']");
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
        return new $class($this->getExecuteMethod(), $id, $this->getWebdriver());
    }

    /**
     * @param WebDriverBy $by
     * @param int $timeout
     * @param bool $nested
     * @return mixed
     */
    public function waitForElement(WebDriverBy $by, int $timeout, bool $nested)
    {
        return $this->waitFor($by, $timeout, $nested, 'findElement');
    }

    /**
     * @param WebDriverBy $by
     * @param int $timeout
     * @param bool $nested
     * @return mixed
     */
    public function waitForModule(WebDriverBy $by, int $timeout, bool $nested)
    {
        return $this->waitFor($by, $timeout, $nested, 'findModule');
    }

    /**
     * @param WebDriverBy $by
     * @param int $timeout
     * @param bool $nested
     * @param $method
     * @return mixed
     * @throws TimeOutException
     */
    private function waitFor(WebDriverBy $by, int $timeout, bool $nested, $method)
    {
        $end = microtime(true) + $timeout;
        while ($end > microtime(true)) {
            try {
                return $this->$method($by, $nested);
            } catch (NoSuchElementException $e) {
            }
        }
        throw new TimeOutException("Timeout exception waiting for presence of [{$by->getMechanism()}: '{$by->getValue()}']");
    }
}
