<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 29.5.17
 * Time: 16:20
 */

namespace Sofico\Webdriver\Storage;


interface Storage
{
    public function store(Result $result);
}
