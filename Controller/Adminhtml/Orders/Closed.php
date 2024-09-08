<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Controller\Adminhtml\Orders;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Controller\ResultInterface;
use PeachCode\RentalSystem\Controller\Adminhtml\Grid;

class Closed extends Grid
{

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
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
        $resultPage->getConfig()->getTitle()->prepend((__('Rental System - Closed Orders')));

        return $resultPage;
    }
}
