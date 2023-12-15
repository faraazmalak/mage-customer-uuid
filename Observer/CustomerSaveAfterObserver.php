<?php
namespace Quarry\CustomerUuid\Observer;

use Laminas\ReCaptcha\Exception;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerSaveAfterObserver implements ObserverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    public function execute(Observer $observer)
    {
        $customer = $observer->getData('customer');

        if (!$customer->getCustomAttribute('uuid')) {
            $uuid = $this->generateUuid();
            $customer->setCustomAttribute('uuid', $uuid);
            $this->customerRepository->save($customer);
        }
    }

    private function generateUuid()
    {
        // Generate and return a UUID (you can use any suitable method)
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}
