<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Setup\Patch\Data;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Quarry\CustomerUuid\Exception\DuplicateUuidException;
use Quarry\CustomerUuid\Exception\UuidException;
use Quarry\CustomerUuid\Logger\Logger;
use Ramsey\Uuid\Uuid;


/**
 * Patch to populate UUID for existing customers, when setup:upgrade is run
 */
class CreateUuidAttributeValue implements DataPatchInterface
{
    private $moduleDataSetup;
    private $customerCollection;
    private $customerRepository;
    private $logger;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CollectionFactory $customerCollectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Logger $logger
     */
    public function __construct(
        ModuleDataSetupInterface    $moduleDataSetup,
        CollectionFactory           $customerCollectionFactory,
        CustomerRepositoryInterface $customerRepository,
        Logger                      $logger
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerCollection = $customerCollectionFactory->create();
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    /**
     * This patch will execute, after the patch for creating Customer UUID attribute is executed.
     *
     *
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [
            \Quarry\CustomerUuid\Setup\Patch\Data\AddCustomerUuidAttribute::class
        ];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        // No aliases
        return [];
    }

    /**
     * Create uuid for existing customers
     *
     * @return void
     * @throws DuplicateUuidException
     * @throws UuidException
     * @throws \Magento\Framework\Exception\InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->customerCollection->addAttributeToFilter('uuid', ['null' => true]);
        $customersWithoutUuid = $this->customerCollection->getItems();
        $customersWithDuplicateUuid = [];
        $customersWithError = [];

        foreach ($customersWithoutUuid as $customer) {
            $customerId = $customer->getId();
            try {
                $uuid = Uuid::uuid4()->toString();
                $customer = $this->customerRepository->getById($customerId);

                $this->customerCollection->clear();
                // Optimize database query
                $this->customerCollection->getSelect()->limit(1);
                $this->customerCollection->addAttributeToFilter('uuid', $uuid);

                $isUuidDuplicate = $this->customerCollection->getSize() > 0;
                if ($isUuidDuplicate) {
                    $customersWithDuplicateUuid[] = $customerId;
                } else {
                    $customer->setCustomAttribute('uuid', $uuid);
                    $this->customerRepository->save($customer);
                    $this->logger->info("UUID $uuid created for customer ID $customerId");
                }
            } catch (NoSuchEntityException $e) {
                $this->logger->warning("Customer ID $customerId does not exist.");
            } catch (LocalizedException $e) {
                $customersWithError[] = $customerId;
                $this->logger->critical("Failed to assign UUID for customer ID: $customerId. {$e->getMessage()}");
            }
        }
        if (count($customersWithDuplicateUuid) > 0) {
            $customerList = implode(',', $customersWithDuplicateUuid);
            $errorMessage = __("Duplicate UUIDs generated for these customers IDs: \n$customerList.\nTry running setup:upgrade again.");
            throw new DuplicateUuidException($errorMessage, $this->logger);
        } else if (count($customersWithError) > 0) {
            $customerList = implode(',', $customersWithError);
            $errorMessage = __("Error creating UUID for these customers IDs: \n$customerList.\nTry running setup:upgrade again.");
            throw new UuidException($errorMessage, $this->logger);
        }
        $this->moduleDataSetup->endSetup();
    }
}
