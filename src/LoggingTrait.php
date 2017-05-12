<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 12.5.17
 * Time: 16:51
 */

namespace Sofico\Webdriver;


trait LoggingTrait
{

    /**
     * @param $level
     * @param $message
     */
    public function log($level, $message)
    {
        $isActive = $this->getWebdriver()->isReportingActive();
        $context = get_class($this);
        $isActive ? $this->getWebdriver()->getLogger()->log($level, "$context: $message") : "";
    }
}
