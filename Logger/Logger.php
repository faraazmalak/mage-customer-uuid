<?php

namespace Quarry\CustomerUuid\Logger;

use Exception;

/**
 * Logger to log extension data to log file: var/log/quarry_customeruuid.log
 */
class Logger extends \Monolog\Logger
{
    public function logCritical(string $message, Exception $e = null)
    {
        $message = $e !== null ? $this->getExceptionDetails($message, $e) : $message;
        $this->critical($message);
    }

    public function logError(string $message, Exception $e = null)
    {
        $message = $e !== null ? $this->getExceptionDetails($message, $e) : $message;
        $this->error($message);
    }

    public function logWarning(string $message, Exception $e = null)
    {
        $message = $e !== null ? $this->getExceptionDetails($message, $e) : $message;;
        $this->warning($message);
    }

    public function logInfo(string $message, Exception $e = null)
    {
        $message = $$e !== null ? $this->getExceptionDetails($message, $e) : $message;
        $this->info($message);
    }

    public function logDebug(string $message, Exception $e=null)
    {
        $message = $e !== null ? $this->getExceptionDetails($message, $e) : $message;
        $this->debug($message);
    }

    private function getExceptionDetails(string $message, Exception $e)
    {
        return "$message.\nError in file {$e->getFile()} on line {$e->getLine()}\nStack Trace:\n{$e->getTraceAsString()}";
    }
}
