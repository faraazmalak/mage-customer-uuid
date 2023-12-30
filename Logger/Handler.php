<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Logger;

use Magento\Framework\App\Filesystem\DirectoryList;
use Monolog\Handler\StreamHandler;

/**
 * Handler for quarry logger
 */
class Handler extends StreamHandler
{
    /**
     * @param DirectoryList $directoryList
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(DirectoryList $directoryList)
    {
        $varLogFolderPath = @$directoryList->getPath(DirectoryList::LOG) ?? 'var/log';
        $logFile = $varLogFolderPath . '/quarry_customeruuid.log';
        parent::__construct($logFile);
    }
}

