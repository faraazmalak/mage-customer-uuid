<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Exception;

use Psr\Log\LoggerInterface;
use Exception;
use Magento\Framework\Phrase;

/**
 * Exception thrown when there is an error creating a new UUID
 */
class UuidCreateException extends UuidException
{
    /**
     * @param Phrase $phrase
     * @param LoggerInterface|null $logger
     * @param Exception|null $cause
     */
    public function __construct(Phrase $phrase, LoggerInterface $logger = null, Exception $cause=null)
    {
        parent::__construct($phrase, $logger, $cause);
    }
}
