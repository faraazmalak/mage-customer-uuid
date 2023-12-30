<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Exception;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Exception thrown when there is an error validating UUID.
 */
class UuidValidateException extends LocalizedException
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
