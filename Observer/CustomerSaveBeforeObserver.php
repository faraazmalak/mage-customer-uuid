<?php
/**
 * Short description...
 *
 * Long description
 * Broken down into several lines
 *
 * License notice...
 */

namespace Quarry\CustomerUuid\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Quarry\CustomerUuid\Logger\Logger;
use Ramsey\Uuid\Uuid;


class CustomerSaveBeforeObserver implements ObserverInterface
{
    private $logger;
    private $customerRepository;

    /**
     * Constructor
     */
    public function __construct(CustomerRepositoryInterface $customerRepository, Logger $logger)
    {
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Observer
     */
    public function execute(Observer $observer)
    {
        //try {
        $customer = $observer->getEvent()->getCustomer();
        $this->logger->info('NEW EVENT TEST 123');
        $uuid = '12333';
        $customer->setUuid('743d9480-9f59-11ee-9b11-0242ac160004');
        /* } catch (Exception $e) {
             $this->logger->info($e);
         }*/

    }

}
