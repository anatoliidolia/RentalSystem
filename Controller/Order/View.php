<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Controller\Order;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use PeachCode\RentalSystem\Model\OrderFactory;
use Magento\Framework\Controller\Result\Redirect;
use Exception;

class View extends Action
{
    /**
     * @param Context          $context
     * @param RequestInterface $request
     * @param Session          $customerSession
     * @param OrderFactory     $orderFactory
     * @param PageFactory      $resultPageFactory
     */
    public function __construct(
        Context $context,
        private readonly RequestInterface $request,
        private readonly Session $customerSession,
        private readonly OrderFactory $orderFactory,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    /**
     * Order view page
     *
     * @return ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute(): ResultInterface
    {
        $result = $this->loadValidOrder();
        if ($result instanceof ResultInterface) {
            return $result;
        }

        return $this->resultPageFactory->create();
    }

    /**
     * Try to load valid order by $_POST or $_COOKIE
     *
     * @return Redirect|bool
     * @throws NoSuchEntityException
     */
    public function loadValidOrder(): bool|Redirect
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->resultRedirectFactory->create()->setPath('/');
        }

        try {
            $params = $this->request->getParams();

            $order = $this->orderFactory->create();

            if (!empty($params) && isset($params['rent_order_id'])) {

                $orderId = $params['rent_order_id'];

                $order = $order->loadById($orderId);

                if ($order->getId() && $order->getCustomerId() == $this->customerSession->getCustomerId()) {
                    return true;
                }
            }
        } catch (Exception $e) {
            $this->messageManager->addWarningMessage(__('Something went wrong.'));
            return $this->resultRedirectFactory->create()->setPath('rent/order/history');
        }

        $this->messageManager->addWarningMessage(__('Something went wrong.'));
        return $this->resultRedirectFactory->create()->setPath('rent/order/history');
    }
}
