<?php declare(strict_types=1);

namespace Quarry\CustomerUuid\Model\Resolver;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Quarry\CustomerUuid\Exception\GraphQlUuidResolveException;
use Quarry\CustomerUuid\Logger\Logger;

/**
 * Resolver to retrieve uuid based on auth token
 */
class CustomerUuid implements ResolverInterface
{
    private CustomerRepositoryInterface $customerRepository;
    private Logger $logger;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(CustomerRepositoryInterface $customerRepository, Logger $logger){
        $this->customerRepository = $customerRepository;
        $this->logger=$logger;
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
     * @throws GraphQlUuidResolveException
     * @throws LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();
        try{
            $customer = $this->customerRepository->getById($customerId);
        }catch(NoSuchEntityException $e){
            throw new GraphQlUuidResolveException(__("Customer ID $customerId not found."), $this->logger, $e);
        }
        return $customer->getCustomAttribute('uuid')?->getValue();
    }
}
