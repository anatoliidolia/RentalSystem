<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Controller\Cart;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use PeachCode\RentalSystem\Logger\Logger;
use PeachCode\RentalSystem\Model\Cart\Item;
use PeachCode\RentalSystem\Model\Cart\ItemFactory;
use PeachCode\RentalSystem\Model\ResourceModel\Cart\CollectionFactory;
use PeachCode\RentalSystem\Model\Api\ConfigInterface;

class Remove implements ActionInterface
{

    /**
     * @param Item              $item
     * @param RequestInterface  $request
     * @param ResultFactory     $resultFactory
     * @param ManagerInterface  $messageManager
     * @param RedirectInterface $redirect
     * @param Logger            $logger
     * @param ItemFactory       $rentCartItemFactory
     * @param CollectionFactory $rentCartCollectionFactory
     * @param Session           $customerSession
     */
    public function __construct(
        private readonly Item $item,
        private readonly RequestInterface $request,
        private readonly ResultFactory $resultFactory,
        private readonly ManagerInterface $messageManager,
        private readonly RedirectInterface $redirect,
        private readonly Logger $logger,
        private readonly ItemFactory $rentCartItemFactory,
        private readonly CollectionFactory $rentCartCollectionFactory,
        private readonly Session $customerSession
    ) {
    }

    /**
     * @return ResultInterface|ResponseInterface|Redirect
     * @throws NotFoundException
     */
    public function execute(): ResultInterface|ResponseInterface|Redirect
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if(!$this->request->isPost()){
            throw new NotFoundException(__('Not Found.'));
        }

        try {
            if(!$this->customerSession->isLoggedIn() || !($customerId = $this->customerSession->getCustomerId())){
                throw new LocalizedException(__('User not logged in.'));
            }

            $post = $this->request->getParams();

            $cartItemId = (int)$post[ConfigInterface::XML_RENT_ITEM_ID];

            $cartItem = $this->rentCartItemFactory->create();

            $cartItem->loadById($cartItemId);

            $currentCart = $this->item->getCurrentCart($customerId);

            if(!$currentCart->getId()){
                throw new LocalizedException(__('Rent cart not found.'));
            }

            if($cartItem->getCartId() !== $currentCart->getId()){
                throw new LocalizedException(__('Not allowed.'));
            }

            $cartItem->delete();

            $this->messageManager->addSuccessMessage(__('Product removed from rent cart'));

        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not remove product from rent cart. '.$e->getMessage()));
            $this->logger->info($e->getMessage());
        }

        $resultRedirect->setUrl($this->redirect->getRefererUrl());
        return $resultRedirect;
    }
}
