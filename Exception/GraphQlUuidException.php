<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Exception;

use Exception;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Phrase;
use Psr\Log\LoggerInterface;

/**
 * Exception thrown when there is an graphQl error fetching customer uuid
 */
class GraphQlUuidException extends GraphQlInputException
{
    /**
     * @param Phrase $phrase
     * @param LoggerInterface|null $logger
     * @param Exception|null $cause
     * @param int $code
     */
    public function __construct(Phrase $phrase, LoggerInterface $logger = null, Exception $cause = null, $code = 0)
    {
        parent::__construct($phrase, $cause, $code);
        if ($logger !== null) {
            $technicalError = $cause instanceof Exception ? $cause->getMessage() : null;
            $logger->critical(__("$phrase $technicalError"));
        }
    }
}
