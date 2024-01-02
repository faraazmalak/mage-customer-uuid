<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Setup\Patch\Data;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Quarry\CustomerUuid\Exception\DuplicateUuidException;
use Quarry\CustomerUuid\Exception\InvalidUuidException;
use Quarry\CustomerUuid\Exception\UuidException;
use Quarry\CustomerUuid\Helper\CustomerUuid;
use Quarry\CustomerUuid\Logger\Logger;


/**
 * Patch to populate UUID for existing customers, when setup:upgrade is run
 */
class CreateUuidAttributeValue implements DataPatchInterface, PatchRevertableInterface
{
    private $moduleDataSetup;
    private $customerCollection;
    private $customerRepository;
    private $logger;
    private $customerUuidHelper;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CollectionFactory $customerCollectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerUuid $customerUuidHelper
     * @param Logger $logger
     */
    public function __construct(
        ModuleDataSetupInterface    $moduleDataSetup,
        CollectionFactory           $customerCollectionFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerUuid                $customerUuidHelper,
        Logger                      $logger
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerCollection = $customerCollectionFactory->create();
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->customerUuidHelper = $customerUuidHelper;
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
     * Create uuid for existing customers, with no uuid assigned
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
        $customersWithError = [];

        foreach ($customersWithoutUuid as $customer) {
            $customerId = $customer->getId();
            try {
                $uuid = $this->customerUuidHelper->createUuid();
                $customer = $this->customerRepository->getById($customerId);

                $customer->setCustomAttribute('uuid', $uuid);
                $this->customerRepository->save($customer);
                $this->logger->logInfo("UUID $uuid created for customer ID $customerId");
            } catch (NoSuchEntityException $e) {
                $this->logger->logWarning("Customer ID $customerId does not exist.");
            } catch (DuplicateUuidException | InvalidUuidException | UuidException) {
                $customersWithError[] = $customerId;
                $this->logger->logCritical("Failed to assign UUID for customer ID: $customerId. {$e->getMessage()}");
            }
        }
        if (count($customersWithError) > 0) {
            $customerList = implode(',', $customersWithError);
            $errorMessage = __("Error creating UUID for these customers IDs: \n$customerList.\nTry running setup:upgrade again.");
            throw new UuidException($errorMessage, $this->logger);
        }
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Adding this method, for this patch to unregister during module uninstallation.
     *
     * @return void
     */
    public function revert(){}
}
