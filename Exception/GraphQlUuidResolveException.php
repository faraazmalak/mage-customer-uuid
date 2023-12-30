<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Exception;

use Magento\Framework\Phrase;

/**
 * Exception thrown when there is an graphQl error fetching customer uuid
 */
class GraphQlUuidResolveException extends UuidException {
    /**
     * @param Phrase $phrase
     * @param $logger
     * @param $cause
     */
    public function __construct(Phrase $phrase, $logger=null, $cause=null)
    {
        parent::__construct($phrase, $logger, $cause);
    }
}
