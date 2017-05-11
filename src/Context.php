<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 10.5.17
 * Time: 9:15
 */

namespace Sofico\Webdriver;


interface Context
{

    public function getWebdriver();

    public function getExecutor();

    public function getProperty(string $propertyName);
}
