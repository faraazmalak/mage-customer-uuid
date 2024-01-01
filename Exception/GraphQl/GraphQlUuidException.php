<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Exception\GraphQl;

use Exception;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Phrase;
use Quarry\CustomerUuid\Logger\Logger;

/**
 * Exception thrown when there is an graphQl error resolving customer uuid
 */
class GraphQlUuidException extends GraphQlInputException
{
    /**
     * @param Phrase $phrase
     * @param Logger|null $logger
     * @param Exception|null $cause
     * @param int $code
     */
    public function __construct(Phrase $phrase, Logger $logger = null, Exception $cause = null, $code = 0)
    {
        parent::__construct($phrase, $cause, $code);
        if ($logger !== null) {
            $cause = $cause ?? $this;
            $logger->logError("{$phrase->render()}", $cause);
        }
    }
}
