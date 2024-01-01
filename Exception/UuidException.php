<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Exception;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Quarry\CustomerUuid\Logger\Logger;
use Exception;

/**
 * Generic class, for catching customer uuid exceptions
 */
class UuidException extends LocalizedException{
    /**
     * @param Phrase $phrase
     * @param Logger|null $logger
     * @param Exception|null $cause
     * @param int $code
     */
    public function __construct(Phrase $phrase, Logger $logger=null, Exception $cause = null, $code = 0)
    {
        parent::__construct($phrase, $cause, $code);
        if ($logger !== null) {
            $cause = $cause ?? $this;
            $logger->logCritical("{$phrase->render()}", $cause);
        }
    }
}
