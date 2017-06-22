<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 8.6.17
 * Time: 17:37
 */

namespace Sofico\Webdriver\HtmlValidation\Pages;


use Sofico\Webdriver\By;
use Sofico\Webdriver\HtmlValidation\Modules\MessageModule;

class ValidationResultPage extends W3cPage
{
    protected $messageEls;

    public function initializeElements()
    {
        parent::initializeElements();
        $this->messageEls = $this->webdriver->findModules(By::cssSelector('#results li'), MessageModule::class);
    }

    /**
     * @return string[]
     */
    public function getMessageEls()
    {
        return $this->messageEls;
    }


}
