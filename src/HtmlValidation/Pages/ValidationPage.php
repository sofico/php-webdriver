<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 8.6.17
 * Time: 16:56
 */

namespace Sofico\Webdriver\HtmlValidation\Pages;


use Facebook\WebDriver\Remote\LocalFileDetector;
use Sofico\Webdriver\By;
use Sofico\Webdriver\RemoteElement;

class ValidationPage extends W3cPage
{

    /** @var RemoteElement */
    protected $uploadInput;
    /** @var RemoteElement */
    protected $submitBtn;

    public function initializeElements()
    {
        parent::initializeElements();
        $this->uploadInput = $this->webdriver->findElement(By::id('uploaded_file'));
        $this->submitBtn = $this->webdriver->findElement(By::cssSelector('#validate-by-upload .submit'));
    }

    public function submitFragmentFromTempFile(string $path): ValidationResultPage
    {
        $this->uploadInput->setFileDetector(new LocalFileDetector());
        $this->uploadInput->sendKeys($path)->submit();
        return $this->initPage(ValidationResultPage::class);
    }

}
