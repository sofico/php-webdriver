<?php

namespace Sofico\Webdriver\Storage;


class Result
{

    protected $projectName;
    protected $environment;
    protected $testname;
    protected $severity;
    protected $browser;
    protected $started;
    protected $ended;
    protected $status;
    protected $error = '';
    protected $logPath;
    protected $screenPath;

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * @param mixed $projectName
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param mixed $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return mixed
     */
    public function getTestname()
    {
        return $this->testname;
    }

    /**
     * @param mixed $testname
     */
    public function setTestname($testname)
    {
        $this->testname = $testname;
    }

    /**
     * @return mixed
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param mixed $severity
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    /**
     * @return mixed
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @param mixed $browser
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    /**
     * @return mixed
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * @param mixed $started
     */
    public function setStarted($started)
    {
        $this->started = $started;
    }

    /**
     * @return mixed
     */
    public function getEnded()
    {
        return $this->ended;
    }

    /**
     * @param mixed $ended
     */
    public function setEnded($ended)
    {
        $this->ended = $ended;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getLogPath()
    {
        return $this->logPath;
    }

    /**
     * @param mixed $logPath
     */
    public function setLogPath($logPath)
    {
        $this->logPath = $logPath;
    }

    /**
     * @return mixed
     */
    public function getScreenPath()
    {
        return $this->screenPath;
    }

    /**
     * @param mixed $screenPath
     */
    public function setScreenPath($screenPath)
    {
        $this->screenPath = $screenPath;
    }


}
