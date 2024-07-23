<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Resolver;

use Magento\Customer\Model\Customer as MagentoCustomer;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use PeachCode\RentalSystem\Model\ResourceModel\Order\Collection;
use PeachCode\RentalSystem\Model\ResourceModel\Order\CollectionFactory;

/**
 * Customers field resolver, used for GraphQL request processing.
 */
class Customer implements ResolverInterface
{
    /**
     * @param CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        private readonly CollectionFactory $orderCollectionFactory
    ) {
    }

    /**
     * @param Field $field
     * @param [type] $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return array
     * @throws GraphQlAuthorizationException|GraphQlNoSuchEntityException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): array
    {
        if (!isset($args['customerId'])) {
            throw new GraphQlAuthorizationException(
                __('Customer ID should be specified', [MagentoCustomer::ENTITY])
            );
        }

        try {
            $orders = $this->getCustomerOrders($args['customerId']);
            return $orders->toArray()['items'];
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlNoSuchEntityException(__($exception->getMessage()));
        }
    }

    /**
     * Return Customer Orders by customer ID
     *
     * @param int $customerId
     *
     * @return Collection
     * @throws NoSuchEntityException
     */
    private function getCustomerOrders(int $customerId): Collection
    {
        try {
            return $this->orderCollectionFactory->create()
                ->addFieldToFilter('customer_id', ['eq' => $customerId])
                ->setOrder('created_at', 'desc');
        } catch (LocalizedException $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }
    }
}
