<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 29.5.17
 * Time: 14:31
 */

namespace Sofico\Webdriver;

/**
 * @Annotation
 * @Target("METHOD")
 */
class TestConfig
{
    /** @Required */
    public $severity;

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


}
