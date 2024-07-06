<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use PeachCode\RentalSystem\Model\Cart\ItemFactory;
use PeachCode\RentalSystem\Model\Config\Config;
use PeachCode\RentalSystem\Model\ResourceModel\Cart\Item\CollectionFactory;
use PeachCode\RentalSystem\Model\Api\ConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use PeachCode\RentalSystem\Model\ResourceModel\Cart as ResourceCart;

class Cart extends AbstractModel implements  IdentityInterface
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ResourceCart::class);
    }

    /**
     * @param Context             $context
     * @param Registry            $registry
     * @param ItemFactory         $rentCartItemFactory
     * @param ResourceCart        $resourceCart
     * @param CollectionFactory   $rentCartItemCollectionFactory
     * @param Config              $config
     * @param SerializerInterface $serializer
     * @param array               $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly ItemFactory $rentCartItemFactory,
        private readonly \PeachCode\RentalSystem\Model\Api\ItemRepositoryInterface $itemRepository,
        private readonly ResourceCart $resourceCart,
        private readonly CollectionFactory $rentCartItemCollectionFactory,
        private readonly Config $config,
        private readonly SerializerInterface $serializer,
        $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            null,
            null,
            $data
        );
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [ConfigInterface::XML_CART_CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Add to rent cart Item
     *
     * @param $productId
     * @param $startDate
     * @param $endDate
     * @param $rentPrice
     *
     * @return void
     * @throws LocalizedException
     */
    public function addCartItem($productId, $startDate, $endDate, $rentPrice): void
    {
        if($this->getAllItems()->getSize() == $this->getCartItemsLimit()){
            throw new LocalizedException(__("Rent cart is full."));
        }

        $discountPercent = $this->calculateDiscountPercent($startDate, $endDate);

        $cartItem = $this->rentCartItemFactory->create();

        $cartItem->setCartId($this->getId());
        $cartItem->setStartDate($startDate);
        $cartItem->setEndDate($endDate);
        $cartItem->setFullDays($this->getFinalFullDays($startDate, $endDate));
        $cartItem->setRentPrice($rentPrice);
        $cartItem->setDiscount($discountPercent);
        $cartItem->setProductId($productId);

        $this->itemRepository->save($cartItem);
    }

    /**
     * Get full days value
     *
     * @param $startDate
     * @param $endDate
     *
     * @return int
     */
    public function getFinalFullDays($startDate, $endDate): int
    {
        $dateDiff = strtotime($endDate) - strtotime($startDate);

        $days = intval($dateDiff / (60 * 60 * 24));

        return !$days ? 1 : $days;
    }

    /**
     * @return int|mixed
     */
    private function getCartItemsLimit(): mixed
    {
        return $this->config->getCartItemsLimit();
    }

    /**
     * @return ResourceModel\Cart\Item\Collection
     */
    public function getAllItems(): ResourceModel\Cart\Item\Collection
    {
        $collection = $this->rentCartItemCollectionFactory->create();

        $collection->addFieldToFilter('cart_id',['eq' => $this->getId()]);
        $collection->load();

        return $collection;
    }

    /**
     * Calculate discount percent
     *
     * @param $startDate
     * @param $endDate
     *
     * @return int
     */
    private function calculateDiscountPercent($startDate, $endDate): int
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $dateDiff = $endDate - $startDate;

        $days =  intval($dateDiff / (60 * 60 * 24));

        $discountConfig = $this->serializer->unserialize($this->config->getDiscountMatrix());

        foreach ($discountConfig as $range) {
            $start = intval($range['start']);
            $end = intval($range['end']);
            $discount = intval($range['discount']);

            if ($days >= $start && $days < $end) {
                return $discount;
            }
        }

        return 0;
    }
}
