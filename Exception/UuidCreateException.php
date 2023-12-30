<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Exception;

use Magento\Framework\Phrase;

/**
 * Exception thrown when there is an error creating a new UUID
 */
class UuidCreateException extends UuidException
{
    /**
     * @param Phrase $phrase
     * @param $logger
     * @param $cause
     */
    public function __construct(Phrase $phrase, $logger = null, $cause=null)
    {
        parent::__construct($phrase, $logger, $cause);
    }
}
