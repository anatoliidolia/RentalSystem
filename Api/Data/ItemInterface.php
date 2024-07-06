<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Api\Data;

interface ItemInterface
{
    public const CART_ITEM_ID = 'cart_item_id';
    public const CART_ID = 'cart_id';
    public const PRODUCT_ID = 'product_id';
    public const START_DATE = 'start_date';
    public const END_DATE = 'end_date';
    public const FULL_DAYS = 'full_days';
    public const RENT_PRICE = 'rent_price';
    public const DISCOUNT = 'discount';

    /**
     * Get Cart Item ID
     *
     * @return string
     */
    public function getCartItemId(): string;

    /**
     * Set Cart Item ID
     *
     * @param string $cartItemId
     *
     * @return void
     */
    public function setCartItemId(string $cartItemId): void;

    /**
     * Get Cart ID
     *
     * @return string
     */
    public function getCartId(): string;

    /**
     * Set Cart ID
     *
     * @param string $cartId
     *
     * @return void
     */
    public function setCartId(string $cartId): void;

    /**
     * Get Product ID
     *
     * @return string
     */
    public function getProductId(): string;

    /**
     * Set Product ID
     *
     * @param string $productId
     *
     * @return void
     */
    public function setProductId(string $productId): void;

    /**
     * Get Start Date
     *
     * @return string
     */
    public function getStartDate(): string;

    /**
     * Set Start Date
     *
     * @param string $startDate
     *
     * @return void
     */
    public function setStartDate(string $startDate): void;

    /**
     * Get End Date
     *
     * @return string
     */
    public function getEndDate(): string;

    /**
     * Set End Date
     *
     * @param string $endDate
     *
     * @return void
     */
    public function setEndDate(string $endDate): void;

    /**
     * Get Full Days
     *
     * @return string
     */
    public function getFullDays(): string;

    /**
     * Set Full Days
     *
     * @param int $fullDays
     *
     * @return void
     */
    public function setFullDays(int $fullDays): void;

    /**
     * Get Rent Price
     *
     * @return string
     */
    public function getRentPrice(): string;

    /**
     * Set Rent Price
     *
     * @param string $rentPrice
     *
     * @return void
     */
    public function setRentPrice(string $rentPrice): void;

    /**
     * Get Discount
     *
     * @return string
     */
    public function getDiscount(): string;

    /**
     * Set Discount
     *
     * @param int $discount
     *
     * @return void
     */
    public function setDiscount(int $discount): void;
}
