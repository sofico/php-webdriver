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
use Facebook\WebDriver\Remote\JsonWireCompat;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Psr\Log\LogLevel;

trait CommonTrait
{
    /**
     * Find the first WebDriverElement using the given mechanism.
     *
     * @param WebDriverBy $by
     * @return RemoteWebElement NoSuchElementException is thrown in HttpCommandExecutor if no element is found.
     * @see WebDriverBy
     */
    public function findElement(WebDriverBy $by, bool $throwEx, bool $nested)
    {
        try {
            $this->log(LogLevel::INFO, "Looking for element with [{$by->getMechanism()}: '{$by->getValue()}']");
            $params = JsonWireCompat::getUsing($by, true);
            if ($nested) $params[':id'] = $this->id;
            $command = $nested ? DriverCommand::FIND_CHILD_ELEMENT : DriverCommand::FIND_ELEMENT;
            $raw_element = $this->getWebdriver()->execute($command, $params);

            return $this->newElement(JsonWireCompat::getElement($raw_element));
        } catch (NoSuchElementException $e) {
            if ($throwEx) throw $e;
            return null;
        }
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

        $params = JsonWireCompat::getUsing($by, true);
        if ($nested) $params[':id'] = $this->id;
        $command = $nested ? DriverCommand::FIND_CHILD_ELEMENTS : DriverCommand::FIND_ELEMENTS;
        $raw_elements = $this->getWebdriver()->execute($command, $params);

        $elements = [];
        foreach ($raw_elements as $raw_element) {
            $elements[] = $this->newElement(JsonWireCompat::getElement($raw_element));
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
        return new RemoteElement($this->getExecuteMethod(), $id, $this->getWebdriver());
    }


    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return mixed
     */
    private function findModule(WebDriverBy $by, string $class, bool $throwEx, bool $nested)
    {
        try {
            $this->log(LogLevel::INFO, "Looking for module with [{$by->getMechanism()}: '{$by->getValue()}']");
            $params = JsonWireCompat::getUsing($by, true);
            if ($nested) $params[':id'] = $this->id;
            $command = $nested ? DriverCommand::FIND_CHILD_ELEMENT : DriverCommand::FIND_ELEMENT;
            $raw_element = $this->getWebdriver()->execute($command, $params);

            return $this->newModule(JsonWireCompat::getElement($raw_element), $class);
        } catch (NoSuchElementException $e) {
            if ($throwEx) throw $e;
            return null;
        }
    }

    /**
     * @param WebDriverBy $by
     * @param string $class
     * @return array
     */
    private function findModules(WebDriverBy $by, string $class, bool $nested)
    {
        $this->log(LogLevel::INFO, "Looking for module with [{$by->getMechanism()}: '{$by->getValue()}']");
        $params = JsonWireCompat::getUsing($by, true);
        if ($nested) $params[':id'] = $this->id;
        $command = $nested ? DriverCommand::FIND_CHILD_ELEMENTS : DriverCommand::FIND_ELEMENTS;
        $raw_elements = $this->getWebdriver()->execute($command, $params);

        $elements = [];
        foreach ($raw_elements as $raw_element) {
            $elements[] = $this->newModule(JsonWireCompat::getElement($raw_element), $class);
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
     * @param int $timeout in s (default 5)
     * @param bool $nested
     * @return mixed
     */
    public function waitForElement(WebDriverBy $by, int $timeout, bool $nested)
    {
        return $this->waitForDOMElement($by, $timeout, $nested, 'findElement');
    }

    /**
     * @param WebDriverBy $by
     * @param int $timeout in s (default 5)
     * @param bool $nested
     * @return mixed
     */
    public function waitForModule(WebDriverBy $by, string $class, int $timeout, bool $nested)
    {
        return $this->waitForDOMElement($by, $timeout, $nested, 'findModule', $class);
    }

    /**
     * @param WebDriverBy $by
     * @param int $timeout in s
     * @param bool $nested
     * @param $method
     * @return mixed
     * @throws TimeOutException
     */
    private function waitForDOMElement(WebDriverBy $by, int $timeout, bool $nested, $method, string $class = '')
    {
        $end = microtime(true) + $timeout;
        while ($end > microtime(true)) {
            try {
                return $method === 'findElement' ? $this->$method($by, $nested) : $this->$method($by, $class, $nested);
            } catch (NoSuchElementException $e) {
            }
        }
        throw new TimeOutException("Timeout exception waiting for presence of [{$by->getMechanism()}: '{$by->getValue()}']");
    }

    /**
     * @param int $timeout_in_second
     * @param int $interval_in_millisecond
     * @return WebDriverWait
     */
    public function wait($timeout_in_second = 30, $interval_in_millisecond = 250): WebDriverWait
    {
        if ($this instanceof RemoteDriver) return parent::wait($timeout_in_second, $interval_in_millisecond);
        else return $this->getWebdriver()->wait($timeout_in_second, $interval_in_millisecond);
    }

    /**
     * @param By $by
     * @return bool
     */
    public function containsElement(WebDriverBy $by): bool
    {
        return !is_null($this->findElement($by, false));
    }
}
