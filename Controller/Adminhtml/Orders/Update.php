<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Controller\Adminhtml\Orders;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Controller\ResultInterface;
use PeachCode\RentalSystem\Controller\Adminhtml\Grid;
use PeachCode\RentalSystem\Model\Api\Status\StatusResolverInterface;

class Update extends Grid
{

    /**
     * @param RequestInterface $request
     * @param Context          $context
     * @param PageFactory      $resultPageFactory
     */
    public function __construct(
        private readonly StatusResolverInterface $orderStatusResolver,
        private readonly RequestInterface $request,
        Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    /**
     * Prepare page header
     *
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute(): Page|ResultInterface|ResponseInterface
    {
        $resultPage = $this->resultPageFactory->create();
        $orderId = (int)$this->request->getParam('entity_id');
        $currentStatus = (int)$this->request->getParam('current_status');

        $this->orderStatusResolver->resolve($orderId, $currentStatus);

        $resultPage->getConfig()->getTitle()->prepend((__('Rental System Orders - Update Status order %1', $orderId)));

        return $resultPage;
    }
}
