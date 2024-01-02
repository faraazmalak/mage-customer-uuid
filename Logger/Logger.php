<?php

namespace Quarry\CustomerUuid\Logger;

use \Exception;

/**
 * Logger to log extension data to log file: var/log/quarry_customeruuid.log
 */
class Logger extends \Monolog\Logger
{
    public function __construct(string $name='logger', array $handlers = [])
    {
        parent::__construct($name, $handlers);
    }

    /**
     * @param string $message
     * @param Exception|null $e
     * @return void
     */
    public function logCritical(string $message, Exception $e = null): void
    {
        try{
            $message = $e !== null ? $this->getExceptionDetails($message, $e) : $message;
            $this->critical($message);
        }catch(Exception $e){
            error_log("Error writing to log file: " . $e->getMessage());
        }

    }

    /**
     * @param string $message
     * @param Exception|null $e
     * @return void
     */
    public function logError(string $message, Exception $e = null): void
    {
        try{
            $message = $e !== null ? $this->getExceptionDetails($message, $e) : $message;
            $this->error($message);
        }catch(Exception $e){
            error_log("Error writing to log file: " . $e->getMessage());
        }
    }

    /**
     * @param string $message
     * @param Exception|null $e
     * @return void
     */
    public function logWarning(string $message, Exception $e = null): void
    {
        try{
            $message = $e !== null ? $this->getExceptionDetails($message, $e) : $message;;
            $this->warning($message);
        }catch(Exception $e){
            error_log("Error writing to log file: " . $e->getMessage());
        }
    }

    /**
     * @param string $message
     * @param Exception|null $e
     * @return void
     */
    public function logInfo(string $message, Exception $e = null): void
    {
        try{
            $message = $e !== null ? $this->getExceptionDetails($message, $e) : $message;
            $this->info($message);
        }catch(Exception $e){
            error_log("Error writing to log file: " . $e->getMessage());
        }
    }

    /**
     * @param string $message
     * @param Exception|null $e
     * @return void
     */
    public function logDebug(string $message, Exception $e=null): void
    {
        try{
            $message = $e !== null ? $this->getExceptionDetails($message, $e) : $message;
            $this->debug($message);
        }catch(Exception $e){
            error_log("Error writing to log file: " . $e->getMessage());
        }

    }

    /**
     * Create a message, with technical details about the exception
     *
     * @param string $message
     * @param Exception $e
     * @return string
     */
    private function getExceptionDetails(string $message, Exception $e): string
    {
        return "$message.\nError in file {$e->getFile()} on line {$e->getLine()}\nStack Trace:\n{$e->getTraceAsString()}";
    }
}
