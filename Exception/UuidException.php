<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Exception;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Psr\Log\LoggerInterface;
use \Exception;

/**
 * Base exception class, with logging functionality. Other exceptions in this module, extend this class
 */
class UuidException extends LocalizedException{
    /**
     * @param Phrase $phrase
     * @param LoggerInterface|null $logger
     * @param Exception|null $cause
     * @param int $code
     */
    public function __construct(Phrase $phrase, LoggerInterface $logger=null, Exception $cause = null, int $code = 0)
    {
        parent::__construct($phrase, $cause, $code);
        if ($logger !== null) {
            $technicalError = $cause instanceof Exception ? $cause->getMessage() : null;
            $logger->critical(__("$phrase $technicalError"));
        }
    }
}
