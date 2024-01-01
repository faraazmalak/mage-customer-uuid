<?php

namespace Quarry\CustomerUuid\Logger;

use \Exception;

/**
 * Logger to log extension data to log file: var/log/quarry_customeruuid.log
 */
class Logger extends \Monolog\Logger
{
    public function logCritical(string $message, Exception $e)
    {
        $message = $this->getFormattedMessage($message, $e);
        $this->critical($message);
    }

    public function logError(string $message, Exception $e)
    {
        $message = $this->getFormattedMessage($message, $e);
        $this->error($message);
    }

    public function logWarning(string $message, Exception $e)
    {
        $message = $this->getFormattedMessage($message, $e);
        $this->warning($message);
    }

    public function logInfo(string $message, Exception $e)
    {
        $message = $this->getFormattedMessage($message, $e);
        $this->info($message);
    }

    public function logDebug(string $message, Exception $e)
    {
        $message = $this->getFormattedMessage($message, $e);
        $this->debug($message);
    }

    private function getFormattedMessage(string $message, Exception $e)
    {
        return "$message.\nError in file {$e->getFile()} on line {$e->getLine()}\nStack Trace:\n{$e->getTraceAsString()}";
    }
}
