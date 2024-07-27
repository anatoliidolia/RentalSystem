<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Order;

use Magento\Framework\Exception\LocalizedException;
use PeachCode\RentalSystem\Model\Order;
use PeachCode\RentalSystem\Model\OrderFactory;

class CreateOrder{

    public function __construct(
        private readonly OrderFactory $rentOrderFactory,
    ) {}

    /**
     * @throws LocalizedException
     */
    public function createOrderFromCart($cart): Order
    {
        $order = $this->rentOrderFactory->create();

        $order->createFromCart($cart);

        return $order;
    }

    /**
     * Get final price
     *
     * @param $item
     *
     * @return int
     */
    public function getFinalPrice($item): int
    {
        $finalPrice = ($this->getFinalFullDays($item) * (int)$item->getData('rent_price'));
        $amountToSubtract = ($item->getDiscount() / 100) * $finalPrice;

        $finalPrice = $finalPrice - $amountToSubtract;

        return (int)$finalPrice;
    }

    /**
     * Get full days value
     *
     * @param $item
     *
     * @return int
     */
    public function getFinalFullDays($item): int
    {
        $startDate = strtotime($item->getStartDate());
        $endDate = strtotime($item->getEndDate());
        $dateDiff = $endDate - $startDate;

        $days = intval($dateDiff / (60 * 60 * 24));

        return !$days ? 1 : $days;
    }
}
