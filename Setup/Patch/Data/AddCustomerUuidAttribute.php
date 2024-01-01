<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Setup\Patch\Data;

use Exception;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Quarry\CustomerUuid\Logger\Logger;
use Quarry\CustomerUuid\Exception\UuidException;

/**
 * Create and configure customer UUID attribute
 */
class AddCustomerUuidAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private $customerSetup;
    private $attributeResource;
    private $attributeSetFactory;
    private $logger;

    private const ATTRIBUTE_NAME ='uuid';

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeResource $attributeResource
     * @param Logger $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeResource $attributeResource,
        AttributeSetFactory $attributeSetFactory,
        Logger $logger
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetup = $customerSetupFactory->create(['setup' => $moduleDataSetup]);
        $this->attributeResource = $attributeResource;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->logger = $logger;
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     *
     * @return string[]
     */
    public static function getDependencies(): array
    {
        // No dependencies
        return [];
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
     * Create and configure customer uuid attribute
     *
     * @return void
     * @throws UuidException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        try {
            // Add customer attribute with settings
            $this->customerSetup->addAttribute(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                self::ATTRIBUTE_NAME,
                [
                    'type' => 'varchar',
                    'label' => strtoupper(self::ATTRIBUTE_NAME),
                    'input' => 'text',
                    'required' => true,
                    'unique' => true,
                    'visible' => false,
                    'user_defined' => 1,
                    'position' => 100,
                    'system' => 0,
                    'is_used_in_grid' => 1,
                    'is_visible_in_grid' => 1,
                    'is_filterable_in_grid' => 1,
                    'is_searchable_in_grid' => 1
                ]
            );
            $customerEntity = $this->customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();
            $attributeSet = $this->attributeSetFactory->create();

            // Add attribute to default attribute set and group
            $this->customerSetup->addAttributeToSet(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                $customerEntity->getDefaultAttributeSetId(),
                $attributeSet->getDefaultGroupId($attributeSetId),
                self::ATTRIBUTE_NAME
            );

            // Get the newly created attribute's model
            $attribute = $this->customerSetup->getEavConfig()
                ->getAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, self::ATTRIBUTE_NAME);

            // Save attribute using its resource model
            $this->attributeResource->save($attribute);
        } catch (Exception $e) {
            throw new UuidException(__($e->getMessage()), $this->logger);
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
