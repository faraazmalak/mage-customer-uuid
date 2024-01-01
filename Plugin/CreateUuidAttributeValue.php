<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Plugin;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\Notice;
use Quarry\CustomerUuid\Exception\DuplicateUuidException;
use Quarry\CustomerUuid\Exception\UuidException;
use Quarry\CustomerUuid\Exception\InvalidUuidException;
use Quarry\CustomerUuid\Logger\Logger;
use Ramsey\Uuid\Uuid;

/**
 * Intercepts customer save operation,
 * to create and assign a value to the uuid customer attribute, if the attribute is null or an invalid UUID
 */
class CreateUuidAttributeValue
{
    private $customerCollection;
    private Logger $logger;
    private ManagerInterface $messageManager;
    private string $originalUuid = '';
    private bool $isNewCustomer = true;

    /**
     * @param CollectionFactory $customerCollectionFactory
     * @param Logger $logger
     * @param ManagerInterface $messageManager
     */
    public function __construct(CollectionFactory $customerCollectionFactory, Logger $logger, ManagerInterface $messageManager)
    {
        $this->customerCollection = $customerCollectionFactory->create();
        $this->logger = $logger;
        $this->messageManager = $messageManager;
    }

    /**
     *  This method is triggered before save() in Magento\Customer\Api\CustomerRepositoryInterface
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterface $customer
     * @return CustomerInterface[]
     * @throws DuplicateUuidException
     * @throws UuidException
     * @throws InvalidUuidException
     */
    public function beforeSave(CustomerRepositoryInterface $customerRepository, CustomerInterface $customer): array
    {
        // $customer->getId() returns null for new customers
        $this->isNewCustomer = $customer->getId() === null;
        if($this->isNewCustomer){
            $uuid = $this->generateUuid();
            $customer->setCustomAttribute('uuid', $uuid);
        }else{
            $this->originalUuid = $customer->getCustomAttribute('uuid')?->getValue() ?? '';
            $isUuidValid = @(Uuid::isValid($this->originalUuid)) ?? false;
            if (!$isUuidValid) {
                $uuid = $this->generateUuid();
                $customer->setCustomAttribute('uuid', $uuid);
            }
        }
        return [$customer];
    }

    /**
     * This method is triggered after save() in Magento\Customer\Api\CustomerRepositoryInterface
     * It displays a message, in case uuid is changed for an existing customer
     *
     * @param CustomerRepositoryInterface $subject
     * @param CustomerInterface $customer
     * @return CustomerInterface
     */
    public function afterSave(CustomerRepositoryInterface $subject, CustomerInterface $customer): CustomerInterface
    {

        $newUuid = $customer->getCustomAttribute('uuid')?->getValue();
        if (!$this->isNewCustomer && $this->originalUuid !== $newUuid) {
            $noticeMessage = new Notice(__("Customer UUID has changed, since the previous one was invalid."));
            $this->messageManager->addMessage($noticeMessage);
        }
        return $customer;
    }

    /**
     * Generates a UUID for customer
     *
     * @return string
     * @throws DuplicateUuidException
     * @throws UuidException
     * @throws InvalidUuidException
     */
    private function generateUuid(): string
    {
        try {
            $uuid = Uuid::uuid4()->toString();
        } catch (Exception $e) {
            $message = __('Error generating a UUID. Try submitting the form again.');
            throw new UuidException($message, $this->logger, $e);
        }

        try {
            $this->customerCollection->getSelect()->limit(1);
            $this->customerCollection->addAttributeToFilter('uuid', $uuid);
            $isUuidDuplicate = $this->customerCollection->getSize() > 0;
        } catch (LocalizedException $e) {
            $errorMessage = __("Unable to validate uniqueness of UUID $uuid. Try resubmitting the form.");
            throw new InvalidUuidException($errorMessage, $this->logger, $e);
        }

        if ($isUuidDuplicate) {
            $message = __("UUID $uuid has already been assigned to another customer. Try resubmitting the form.");
            throw new DuplicateUuidException($message, $this->logger);
        }
        return $uuid;
    }
}
