<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Controller\Order;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use PeachCode\RentalSystem\Model\OrderFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use PeachCode\RentalSystem\Model\Order;

class View extends Action
{
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        private readonly RequestInterface $request,
        private readonly Session $customerSession,
        private readonly OrderFactory $orderFactory,
        private readonly Registry $coreRegistry,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    /**
     * Order view page
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $result = $this->loadValidOrder();
        if ($result instanceof ResultInterface) {
            return $result;
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }

    /**
     * Try to load valid order by $_POST or $_COOKIE
     *
     * @param RequestInterface $request
     * @return Redirect|bool
     */
    public function loadValidOrder()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->resultRedirectFactory->create()->setPath('/');
        }

        $params = $this->request->getParams();

        /** @var $order Order */
        $order = $this->orderFactory->create();

        if (!empty($params) && isset($params['rent_order_id'])) {

            $orderId = $params['rent_order_id'];

            $order = $order->loadById($orderId);

            if ($order->getId() && $order->getCustomerId() == $this->customerSession->getCustomerId()) {
               // $this->coreRegistry->register('current_rent_order', $order);
                return true;
            }
        }

        $this->messageManager->addError(__('You entered incorrect data. Please try again.'));
        return $this->resultRedirectFactory->create()->setPath('rent/order/history');
    }

}
