<?php

namespace Sofico\Webdriver\HtmlValidation\Modules;

use Sofico\Webdriver\By;
use Sofico\Webdriver\Module;

/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 22.6.17
 * Time: 13:12
 */
class MessageModule extends Module
{

    public static $ERROR = 'Error';
    public static $WARNING = 'Warning';

    protected $status;
    protected $description;
    protected $line;


    protected function initializeElements()
    {
        $this->status = $this->findElement(By::cssSelector('p:nth-of-type(1) strong'))->getText();
        $this->description = $this->findElement(By::cssSelector('p:nth-of-type(1) span'))->getText();
        $this->line = $this->findElement(By::cssSelector('p.location .last-line'))->getText();
    }


    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }


    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * @return string
     */
    public function getLine(): string
    {
        return $this->line;
    }


}
