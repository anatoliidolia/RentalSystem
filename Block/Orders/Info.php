<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Block\Orders;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use PeachCode\RentalSystem\Model\Order;

class Info extends Template
{

    /**
     * @param Order                  $order
     * @param PriceCurrencyInterface $priceCurrency
     * @param TemplateContext        $context
     * @param RequestInterface       $request
     * @param array                  $data
     */
    public function __construct(
        private readonly Order $order,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly TemplateContext $context,
        private readonly RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     */
    protected function _prepareLayout(): void
    {
        $this->pageConfig->getTitle()->set(__('Rental Order # %1', $this->getOrder()->getId()));
    }

    /**
     * Retrieve current order model instance
     *
     * @return Order
     * @throws NoSuchEntityException
     */
    public function getOrder()
    {
        $orderId = $this->request->getParams('rent_order_id');
        return $this->order->loadById($orderId);
    }

    /**
     * Price converter
     *
     * @param int $value
     *
     * @return string
     */
    public function convertValue(int $value = 0): string{
        return $this->priceCurrency->format($value);
    }
}
