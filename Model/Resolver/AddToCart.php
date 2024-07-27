<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Resolver;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\LocalizedException;
use PeachCode\RentalSystem\Model\Api\ConfigInterface;
use PeachCode\RentalSystem\Model\Cart;
use PeachCode\RentalSystem\Model\CartFactory;
use PeachCode\RentalSystem\Model\Config\Config;
use PeachCode\RentalSystem\Model\ResourceModel\Cart\CollectionFactory;

class AddToCart implements ResolverInterface
{
    /**
     * @param CollectionFactory          $rentCartCollectionFactory
     * @param Config                     $config
     * @param ProductRepositoryInterface $productRepository
     * @param CartFactory                $rentCartFactory
     */
    public function __construct(
        private readonly CollectionFactory $rentCartCollectionFactory,
        private readonly Config $config,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CartFactory $rentCartFactory
    ) {}

    /**
     * @param Field       $field
     * @param             $context
     * @param ResolveInfo $info
     * @param array|null  $value
     * @param array|null  $args
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        $graphData = $args['input'];

        $productId = (int)$graphData['productId'] ?? '';
        $startDate = $graphData['startDate'] ?? '';
        $endDate = $graphData['endDate'] ?? '';
        $customerId = $graphData['customerId'] ?? '';

        $product = $this->productRepository->getById($productId);

        $rentAttributeCode = $this->config->getRentAttribute();
        $rentPrice = $product->getCustomAttribute(ConfigInterface::XML_RENT_PRODUCT_PRICE)->getValue();

        if (!$rentAttributeCode || !$product->getCustomAttribute($rentAttributeCode)->getValue()) {
            throw new LocalizedException(__('Rent feature not available for product'
                .$product->getSku()));
        }

        $cart = $this->getCurrentCart($customerId);

        /** @var $cart Cart */
        $cart->addCartItem($productId, $startDate, $endDate, $rentPrice);

        return [
            'cartId' => $cart->getId(),
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }

    /**
     * @param $customerId
     *
     * @return DataObject|Cart
     */
    private function getCurrentCart($customerId): Cart|DataObject
    {
        $collection = $this->rentCartCollectionFactory->create();

        $collection->addFieldToFilter('customer_id', ['eq' => $customerId]);
        $customerCart = $collection->getFirstItem();

        if ($customerCart->getId()) {
            return $customerCart;
        }

        $customerCart = $this->rentCartFactory->create();
        $customerCart->setCustomerId($customerId)->save();

        return $customerCart;
    }
}
