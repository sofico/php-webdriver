<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 13.6.17
 * Time: 11:08
 */

namespace Sofico\Webdriver\HtmlValidation\Pages;

use Sofico\Webdriver\Page;

class W3cPage extends Page
{
    protected function getDomain(): string
    {
        return "https://validator.w3.org/";
    }


}
