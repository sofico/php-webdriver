<?php

namespace Sofico\Webdriver;

class Page
{

    protected $webdriver;
    protected $address;

    /**
     * Page constructor.
     * @param RemoteDriver $webdriver
     * @param bool $init
     */
    public function __construct(RemoteDriver $webdriver, bool $init = true)
    {
        $this->webdriver = $webdriver;
        $this->address = $this->getBaseUrl() . $this->getUrl();
        if ($init) $this->initializeElements();
    }

    /**
     * Navigate to this page.
     * @return $this
     */
    public function goTo()
    {
        $this->webdriver->get($this->address);
        $this->initializeElements();
        return $this;
    }

    public function takeScreenshot(String $path)
    {
        $this->webdriver->takeScreenshot($path);
    }

    /**
     * Override this to initialize elements
     */
    protected function initializeElements()
    {
    }

    /**
     * Override this to get full path of page. Will be concatenated with baseUrl.
     * @return string
     */
    protected function getUrl(): string
    {
        return "";
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return $this->webdriver->getConfig()->getBaseUrl();
    }

    /**
     * @return bool
     */
    protected function isCurrentPage(): bool
    {
        return $this->getAddress() === explode("?", $this->webdriver->getCurrentURL())[0];
    }


}
