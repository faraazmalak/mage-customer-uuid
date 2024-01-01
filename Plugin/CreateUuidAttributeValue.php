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
use Quarry\CustomerUuid\Helper\CustomerUuid;
use Ramsey\Uuid\Uuid;

/**
 * Intercepts customer save operation,
 * to create and assign a value to the uuid customer attribute, if the attribute is null or an invalid UUID
 */
class CreateUuidAttributeValue
{
    private Logger $logger;
    private ManagerInterface $messageManager;
    private string $originalUuid = '';
    private bool $isNewCustomer = true;
    private CustomerUuid $customerUuidHelper;

    /**
     * @param CollectionFactory $customerCollectionFactory
     * @param Logger $logger
     * @param ManagerInterface $messageManager
     */
    public function __construct(CustomerUuid $customerUuidHelper, Logger $logger, ManagerInterface $messageManager)
    {
        $this->customerUuidHelper=$customerUuidHelper;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
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
            $this->logger->logDebug('NEW CUSTOMER');
            $uuid = $this->customerUuidHelper->createUuid();
            $customer->setCustomAttribute('uuid', $uuid);
        }else{
            $this->logger->logDebug('REPEAT CUSTOMER');
            $this->originalUuid = $customer->getCustomAttribute('uuid')?->getValue() ?? '';
            if (!$this->customerUuidHelper->isUuidValid($this->originalUuid)) {
                $uuid = $this->customerUuidHelper->createUuid();
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


}
