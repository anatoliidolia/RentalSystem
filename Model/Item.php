<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model;

use PeachCode\RentalSystem\Api\Data\ItemInterface;
use PeachCode\RentalSystem\Model\ResourceModel\Cart\Item as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel implements ItemInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getCartItemId(): string
    {
        return $this->getData(self::CART_ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCartItemId(string $cartItemId): void
    {
        $this->setData(self::CART_ITEM_ID, $cartItemId);
    }

    /**
     * @inheritdoc
     */
    public function getCartId(): string
    {
        return $this->getData(self::CART_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCartId(string $cartId): void
    {
        $this->setData(self::CART_ID, $cartId);
    }

    /**
     * @inheritdoc
     */
    public function getProductId(): string
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setProductId(string $productId): void
    {
        $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritdoc
     */
    public function getStartDate(): string
    {
        return $this->getData(self::START_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setStartDate(string $startDate): void
    {
        $this->setData(self::START_DATE, $startDate);
    }

    /**
     * @inheritdoc
     */
    public function getEndDate(): string
    {
        return $this->getData(self::END_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setEndDate(string $endDate): void
    {
        $this->setData(self::END_DATE, $endDate);
    }

    /**
     * @inheritdoc
     */
    public function getFullDays(): string
    {
        return $this->getData(self::FULL_DAYS);
    }

    /**
     * @inheritdoc
     */
    public function setFullDays(int $fullDays): void
    {
        $this->setData(self::FULL_DAYS, $fullDays);
    }

    /**
     * @inheritdoc
     */
    public function getRentPrice(): string
    {
        return $this->getData(self::RENT_PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setRentPrice(string $rentPrice): void
    {
        $this->setData(self::RENT_PRICE, $rentPrice);
    }

    /**
     * @inheritdoc
     */
    public function getDiscount(): string
    {
        return $this->getData(self::DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setDiscount(int $discount): void
    {
        $this->setData(self::DISCOUNT, $discount);
    }
}
