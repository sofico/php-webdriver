<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 25.5.17
 * Time: 14:34
 */

namespace Sofico\Webdriver;


use Facebook\WebDriver\Remote\RemoteExecuteMethod;
use Facebook\WebDriver\Remote\RemoteWebElement;

class RemoteElement extends RemoteWebElement
{

    /**
     * @var RemoteDriver
     */
    protected $webdriver;

    /**
     * RemoteElement constructor.
     */
    public function __construct(RemoteExecuteMethod $executor, string $id, RemoteDriver $driver)
    {
        parent::__construct($executor, $id);
        $this->webdriver = $driver;
    }
}
