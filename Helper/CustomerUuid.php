<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Helper;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Quarry\CustomerUuid\Exception\DuplicateUuidException;
use Quarry\CustomerUuid\Exception\InvalidUuidException;
use Quarry\CustomerUuid\Exception\UuidException;
use Quarry\CustomerUuid\Logger\Logger;
use \Magento\Customer\Model\ResourceModel\Customer\Collection;
use \Exception;
use Ramsey\Uuid\Uuid;

/**
 * This class provides utility methods to work with customer UUIDs
 */
class CustomerUuid extends AbstractHelper
{
    private Collection $customerCollection;
    private Logger $logger;

    public function __construct(CollectionFactory $customerCollectionFactory, Logger $logger)
    {
        $this->customerCollection = $customerCollectionFactory->create();
        $this->logger = $logger;
    }

    /**
     * Create a new uuid
     *
     * @throws DuplicateUuidException
     * @throws InvalidUuidException|UuidException
     */
    public function createUuid(): string
    {
        try{
            $uuid = Uuid::uuid4()->toString();
        }catch(Exception $e){
            throw new UuidException(__("Error generating a UUID."), $this->logger, $e);
        }
        if ($this->isUuidDuplicate($uuid)) {
            $message = __("UUID $uuid has already been assigned to another customer.");
            throw new DuplicateUuidException($message, $this->logger);
        }
        return $uuid;
    }

    /**
     * Check if the supplied UUID is already assigned to another customer
     *
     * @param $uuid
     * @return bool
     * @throws InvalidUuidException
     */
    public function isUuidDuplicate($uuid): bool
    {
        try {
            $this->customerCollection->clear();
            $this->customerCollection->getSelect()->limit(1);
            $this->customerCollection->addAttributeToFilter('uuid', $uuid);
            $isUuidDuplicate = $this->customerCollection->getSize() > 0;
        } catch (LocalizedException $e) {
            $errorMessage = __("Unable to check if UUID $uuid is assigned to another customer.");
            throw new InvalidUuidException($errorMessage, $this->logger, $e);
        }
        return $isUuidDuplicate;
    }

    /**
     * Validate the UUID format
     *
     * @param $uuid
     * @return bool
     */
    public function isUuidValid($uuid): bool
    {
        $uuid = $uuid ?? '';
        return @(Uuid::isValid($uuid)) ?? false;
    }


}
