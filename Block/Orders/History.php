<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Block\Orders;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use PeachCode\RentalSystem\Model\ResourceModel\Order\Collection;
use PeachCode\RentalSystem\Model\ResourceModel\Order\CollectionFactory;

/**
 * Sales order history block
 */
class History extends Template
{
    public Collection $orders;

    /**
     * @param Context                                                              $context
     * @param CollectionFactory $orderCollectionFactory
     * @param Session                                                              $customerSession
     * @param array                                                                $data
     */
    public function __construct(
        private readonly Context $context,
        private readonly CollectionFactory $orderCollectionFactory,
        private readonly Session $customerSession,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Rent Orders'));
    }

    /**
     * @return bool|Collection
     */
    public function getOrders(): bool|Collection
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        $this->orders = $this->orderCollectionFactory->create()
            ->addFieldToFilter(
                'customer_id', ['eq' => $customerId]
            )->setOrder(
                'created_at',
                'desc'
            );


        return $this->orders;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareLayout(): static
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'rent.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml(): string
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $order
     *
     * @return string
     */
    public function getViewUrl(object $order): string
    {
        return $this->getUrl('rent/order/view', ['rent_order_id' => $order->getId()]);
    }

    /**
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('customer/account/');
    }
}
