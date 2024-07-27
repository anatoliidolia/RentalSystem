<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Resolver;

use Exception;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\LocalizedException;
use PeachCode\RentalSystem\Model\Cart;
use PeachCode\RentalSystem\Model\Cart\Item;
use PeachCode\RentalSystem\Model\Product\StockValidator;
use PeachCode\RentalSystem\Model\Order\CreateOrder as OrderCreate;

class CreateOrder implements ResolverInterface
{

    /**
     * @param Item                  $item
     * @param EventManagerInterface $eventManager
     * @param StockValidator        $stockValidator
     * @param OrderCreate           $createOrder
     */
    public function __construct(
        private readonly Item $item,
        private readonly EventManagerInterface $eventManager,
        private readonly StockValidator $stockValidator,
        private readonly OrderCreate $createOrder
    ) {
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {

        $graphData = $args['input'];

        $customerId = $graphData['customerId'];
        $cartId = $graphData['cartId'];
        $address = $graphData['htmlAddress'];

        try {
            $cart = $this->item->getCurrentCart($customerId);

            /** @var $cart Cart */
            if (!$cart->getId() && $cartId !== $cart->getId()) {
                throw new LocalizedException(__('Cart not found.'));
            }

            $this->eventManager->dispatch('before_submit_rent_cart', [$cart, $customerId]);

            $interceptionFlag = false;
            $finalPrice = 0;
            foreach ($cart->getAllItems() as $item) {
                $finalPrice = $finalPrice + $this->createOrder->getFinalPrice($item);

                if (!$this->stockValidator->checkIfRentIsAvailable($item->getProductId())) {
                    $interceptionFlag = true;
                    $item->delete();
                }
            }

            if ($interceptionFlag) {
                throw new LocalizedException(__('Some rent product are no longer available. They were removed from your cart.'));
            }

            $this->eventManager->dispatch('before_create_rent_order_from_cart', [$customerId, $cart]);

            $order = $this->createOrder->createOrderFromCart($cart);

            $order->setHtmlAddress($address);
            $order->setTotalSumm($finalPrice);
            $order->save();

            //Delete cart after order is submitted
            $cart->delete();

            $this->eventManager->dispatch('after_create_rent_order', ['customer_id' => $customerId, 'order' => $order]);

            return [
                'customerId' => $customerId,
                'orderId' => $order->getId()
            ];

        } catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
