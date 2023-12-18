<?php

namespace Quarry\CustomerUuid\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;
    private $attributeSetFactory;
    private const ATTRIBUTE_NAME ='uuid';

    public function __construct(CustomerSetupFactory $customerSetupFactory, AttributeSetFactory $attributeSetFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->addAttribute(Customer::ENTITY, self::ATTRIBUTE_NAME, [
            'type' => 'varchar',
            'label' => strtoupper(self::ATTRIBUTE_NAME),
            'input' => 'text',
            'required' => true,
            'unique' => true,
            'visible' => true,
            'user_defined' => true,
            'position' => 999,
            'system' => 0,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => true
        ]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'uuid')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId
            ]);
        echo 'Quarry UUID ' . ' ' . $attributeGroupId . ' ' . $attributeSetId;
        $attribute->save();
    }
}
