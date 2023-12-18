<?php

namespace Quarry\CustomerUuid\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use PharIo\Version\Exception;
use Quarry\CustomerUuid\Logger\Logger;


class CustomerSaveBeforeObserver implements ObserverInterface
{
    private $logger;
    private $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository, Logger $logger)
    {
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
    }

    public function execute(Observer $observer)
    {
        try {
            $customer = $observer->getEvent()->getCustomer();
            $customer->setUuid('test uuid');
        } catch (Exception $e) {
            $this->logger->info($e);
        }

    }

}
