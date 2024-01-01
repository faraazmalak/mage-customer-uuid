<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Exception;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Quarry\CustomerUuid\Logger\Logger;
use Exception;

/**
 * Exception thrown when there is an error validating UUID.
 */
class InvalidUuidException extends LocalizedException
{
    /**
     * @param Phrase $phrase
     * @param LoggerInterface|null $logger
     * @param Exception|null $cause
     */
    public function __construct(Phrase $phrase, Logger $logger = null, Exception $cause=null)
    {
        parent::__construct($phrase, $logger, $cause);
        if ($logger !== null) {
            $cause = $cause ?? $this;
            $logger->logCritical("{$phrase->render()}", $cause);
        }
    }
}
