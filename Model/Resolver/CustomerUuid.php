<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Model\Resolver;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Quarry\CustomerUuid\Exception\GraphQl\GraphQlUuidException;
use Quarry\CustomerUuid\Exception\GraphQl\GraphQlInvalidUuidException;
use Quarry\CustomerUuid\Logger\Logger;
use Quarry\CustomerUuid\Helper\CustomerUuid As CustomerUuidHelper;

/**
 * Resolver to retrieve uuid based on auth token
 */
class CustomerUuid implements ResolverInterface
{
    private CustomerRepositoryInterface $customerRepository;
    private Logger $logger;
    private CustomerUuidHelper $customerUuidHelper;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param Logger $logger
     */
    public function __construct(CustomerRepositoryInterface $customerRepository, Logger $logger, CustomerUuidHelper $customerUuidHelper){
        $this->customerRepository = $customerRepository;
        $this->logger=$logger;
        $this->customerUuidHelper = $customerUuidHelper;
    }

    /**
     * Resolve customer uuid based on auth token
     *
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return void
     * @throws GraphQlUuidException
     * @throws LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();
        try{
            $customer = $this->customerRepository->getById($customerId);
        }catch(NoSuchEntityException $e){
            throw new GraphQlUuidException(__("Customer ID $customerId not found."), $this->logger, $e);
        }

        $uuid = $customer->getCustomAttribute('uuid')?->getValue();
        if($uuid === null){
            throw new GraphQlInvalidUuidException(__("UUID cannot be null"), $this->logger);
        }else{
            if(!$this->customerUuidHelper->isUuidValid($uuid)){
                throw new GraphQlInvalidUuidException(__("UUID '$uuid' has an invalid format."), $this->logger);
            }
        }
        return $uuid ;
    }
}
