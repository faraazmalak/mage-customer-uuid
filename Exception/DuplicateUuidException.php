<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Exception;

use Magento\Framework\Phrase;
use Psr\Log\LoggerInterface;
use Exception;


/**
 * Exception thrown when a newly generated UUID is already in-use by another customer
 */
class DuplicateUuidException extends UuidException {
    /**
     * @param Phrase $phrase
     * @param LoggerInterface|null $logger
     * @param Exception|null $cause
     */
    public function __construct(Phrase $phrase, LoggerInterface $logger=null, Exception $cause=null)
    {
        parent::__construct($phrase, $logger, $cause);
    }
}
