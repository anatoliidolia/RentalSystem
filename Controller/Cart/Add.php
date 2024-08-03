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
use Magento\Framework\DataObject;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use PeachCode\RentalSystem\Model\Config\Config;
use PeachCode\RentalSystem\Model\ResourceModel\Cart\CollectionFactory;
use PeachCode\RentalSystem\Logger\Logger;
use PeachCode\RentalSystem\Model\Cart;
use PeachCode\RentalSystem\Model\CartFactory;
use PeachCode\RentalSystem\Model\Api\ConfigInterface;

class Add implements ActionInterface
{

    /**
     * @param EventManagerInterface      $eventManager
     * @param RequestInterface           $request
     * @param ResultFactory              $resultFactory
     * @param RedirectInterface          $redirect
     * @param Logger                     $logger
     * @param ManagerInterface           $messageManager
     * @param CartFactory                $rentCartFactory
     * @param CollectionFactory          $rentCartCollectionFactory
     * @param Session                    $customerSession
     * @param Config                     $config
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        private readonly EventManagerInterface $eventManager,
        private readonly RequestInterface $request,
        private readonly ResultFactory $resultFactory,
        private readonly RedirectInterface $redirect,
        private readonly Logger $logger,
        private readonly ManagerInterface $messageManager,
        private readonly CartFactory $rentCartFactory,
        private readonly CollectionFactory $rentCartCollectionFactory,
        private readonly Session $customerSession,
        private readonly Config $config,
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @return ResultInterface|ResponseInterface|Redirect
     * @throws NotFoundException
     */
    public function execute(): ResultInterface|ResponseInterface|Redirect
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$this->request->isPost()) {
            throw new NotFoundException(__('Not Found.'));
        }

        $statusFlag = false;
        $post = $this->request->getParams();

        try {

            if (!$this->customerSession->isLoggedIn() || !($customerId = $this->customerSession->getCustomerId())
            ) {
                throw new LocalizedException(__('User not logged in.'));
            }

            /**
             * Before add to cart event
             */
            $this->eventManager->dispatch('before_add_rent_product', [$post, $customerId]);

            $sourceId = $post[ConfigInterface::SOURCE_ID] ?? '';

            $productId = (int)$post[ConfigInterface::PRODUCT_ID_FROM_TEMPLATE] ?? '';
            $startDate = $post[ConfigInterface::RENT_DATE_START] ?? '';
            $endDate = $post[ConfigInterface::RENT_DATE_END] ?? '';

            $product = $this->productRepository->getById($productId);

            $rentAttributeCode = $this->config->getRentAttribute();
            $rentPrice = $product->getCustomAttribute(ConfigInterface::XML_RENT_PRODUCT_PRICE)->getValue();

            if (!$rentAttributeCode || !$product->getCustomAttribute($rentAttributeCode)->getValue()) {
                throw new LocalizedException(__('Rent feature not available for product'
                    .$product->getSku()));
            }

            $cart = $this->getCurrentCart($customerId);

            /** @var $cart Cart */
            $cart->addCartItem($productId, $startDate, $endDate, $rentPrice, $sourceId);

            $this->messageManager->addSuccessMessage(__('Product added to rent cart'));

            $statusFlag = true;

        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not add product to cart. '
                .$e->getMessage()));
            $this->logger->info($e->getMessage());
        }

        /**
         * After add to cart event with status flag
         */
        $this->eventManager->dispatch('after_add_rent_product', [$statusFlag, $post]);

        $resultRedirect->setUrl($this->redirect->getRefererUrl());

        return $resultRedirect;
    }

    /**
     * @param $customerId
     *
     * @return DataObject|Cart
     */
    private function getCurrentCart($customerId): Cart|DataObject
    {
        $collection = $this->rentCartCollectionFactory->create();

        $collection->addFieldToFilter('customer_id', ['eq' => $customerId]);
        $customerCart = $collection->getFirstItem();

        if ($customerCart->getId()) {
            return $customerCart;
        }

        $customerCart = $this->rentCartFactory->create();
        $customerCart->setCustomerId($customerId)->save();

        return $customerCart;
    }
}
