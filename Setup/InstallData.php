<?php
// app/code/Custom/CustomerAttribute/Setup/InstallData.php

namespace Quarry\CustomerUuid\Setup;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;

    public function __construct(CustomerSetupFactory $customerSetupFactory)
    {
        echo 'QUARRY UUID';
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        echo 'QUARRY UUID';
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(Customer::ENTITY, 'uuid', [
            'type' => 'varchar',
            'label' => 'UUID',
            'input' => 'text',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'system' => false,
            'position' => 100,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => true,
            'is_html_allowed_on_front' => false,
            'visible_on_front' => false,
            'used_in_forms' => ['adminhtml_customer'],
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'uuid');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $attribute->save();

        // Assign UUID to existing customers
        $this->assignUuidToExistingCustomers();
    }

    private function assignUuidToExistingCustomers()
    {
        // Implement logic to assign unique UUIDs to existing customers
    }
}


?>
