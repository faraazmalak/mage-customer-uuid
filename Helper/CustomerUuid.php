<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Helper;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Quarry\CustomerUuid\Exception\DuplicateUuidException;
use Quarry\CustomerUuid\Exception\InvalidUuidException;
use Quarry\CustomerUuid\Logger\Logger;
use Ramsey\Uuid\Uuid;

class CustomerUuid extends AbstractHelper
{
    private $customerCollection;
    private Logger $logger;

    public function __construct(CollectionFactory $customerCollectionFactory, Logger $logger)
    {
        $this->customerCollection = $customerCollectionFactory->create();
        $this->logger = $logger;
    }

    public function createUuid()
    {
        $uuid = Uuid::uuid4()->toString();
        if ($this->isUuidDuplicate($uuid)) {
            $message = __("UUID $uuid has already been assigned to another customer. Try resubmitting the form.");
            throw new DuplicateUuidException($message, $this->logger);
        }
        return true;
    }

    public function isUuidDuplicate($uuid)
    {
        try {
            $this->customerCollection->clear();
            $this->customerCollection->getSelect()->limit(1);
            $this->customerCollection->addAttributeToFilter('uuid', $uuid);
            $isUuidDuplicate = $this->customerCollection->getSize() > 0;
        } catch (LocalizedException $e) {
            $errorMessage = __("Unable to validate uniqueness of UUID $uuid. Try resubmitting the form.");
            throw new InvalidUuidException($errorMessage, $this->logger, $e);
        }
        return $isUuidDuplicate;
    }

    public function isUuidValid($uuid)
    {
        return @(Uuid::isValid($uuid)) ?? false;
    }


}
