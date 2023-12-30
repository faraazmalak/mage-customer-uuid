<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Plugin;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Quarry\CustomerUuid\Exception\DuplicateUuidException;
use Quarry\CustomerUuid\Exception\UuidCreateException;
use Quarry\CustomerUuid\Exception\UuidValidateException;
use Quarry\CustomerUuid\Logger\Logger;
use Ramsey\Uuid\Uuid;

/**
 * Intercepts customer save operation,
 * to create and assign a value to the uuid customer attribute, if the attribute is null
 */
class CreateUuidAttributeValue
{
    private $customerCollection;
    private Logger $logger;

    /**
     * @param CollectionFactory $customerCollectionFactory
     * @param Logger $logger
     */
    public function __construct(CollectionFactory $customerCollectionFactory, Logger $logger)
    {
        $this->customerCollection = $customerCollectionFactory->create();
        $this->logger = $logger;
    }

    /**
     * This method is triggered before save() in Magento\Customer\Api\CustomerRepositoryInterface
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterface $customer
     * @return CustomerInterface[]
     * @throws LocalizedException
     */
    public function beforeSave(CustomerRepositoryInterface $customerRepository, CustomerInterface $customer): array
    {
        if ($customer->getCustomAttribute('uuid') === null) {
            try {
                $uuid = Uuid::uuid4()->toString();
            } catch (Exception $e) {
                $message=__('Error generating a UUID. Try submitting the form again.');
                throw new UuidCreateException($message, $this->logger, $e);
            }

            try {
                $this->customerCollection->getSelect()->limit(1);
                $this->customerCollection->addAttributeToFilter('uuid', $uuid);
                $isUuidDuplicate = $this->customerCollection->getSize() > 0;
            } catch (LocalizedException $e) {
                $errorMessage = __("Unable to validate uniqueness of UUID $uuid. Try resubmitting the form.");
                throw new UuidValidateException($errorMessage, $this->logger, $e);
            }

            if ($isUuidDuplicate) {
                $message = __("UUID $uuid has already been assigned to another customer. Try resubmitting the form.");
                throw new DuplicateUuidException($message, $this->logger);
            } else {
                $customer->setCustomAttribute('uuid', $uuid);
            }
        }
        return [$customer];
    }
}
