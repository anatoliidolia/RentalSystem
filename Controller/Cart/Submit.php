<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Controller\Cart;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use PeachCode\RentalSystem\Model\Cart;
use PeachCode\RentalSystem\Model\Order\CreateOrder;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use PeachCode\RentalSystem\Model\Cart\Item;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use PeachCode\RentalSystem\Model\Product\StockValidator;

class Submit implements ActionInterface
{
    /**
     * @var array|string[]
     */
    private array $requiredAddressFields = [
            "name",
            "street",
            "city",
            "postcode",
        ];

    /**
     * @var array|string[]
     */
    private array $allAddressFieldsFull = [
            "name",
            "telephone",
            "street",
            "city",
            "region",
            "postcode",
            "stores",
        ];

    /**
     * @var array|string[]
     */
    private array $allAddressFields = [];

    /**
     * @var array|string[]
     */
    private array $allAddressFieldsPath = [
        "name",
        "telephone"
    ];

    /**
     * @param Item                  $item
     * @param EventManagerInterface $eventManager
     * @param RequestInterface      $request
     * @param ResultFactory         $resultFactory
     * @param ManagerInterface      $messageManager
     * @param RedirectInterface     $redirect
     * @param StockValidator        $stockValidator
     * @param Session               $customerSession
     * @param CreateOrder           $createOrder
     */
    public function __construct(
        private readonly Item $item,
        private readonly EventManagerInterface $eventManager,
        private readonly RequestInterface $request,
        private readonly ResultFactory $resultFactory,
        private readonly ManagerInterface $messageManager,
        private readonly RedirectInterface $redirect,
        private readonly StockValidator $stockValidator,
        private readonly Session $customerSession,
        private readonly CreateOrder $createOrder,
        private readonly \PeachCode\RentalSystem\Model\Config\Config $config
    ) {
    }

    /**
     * @return ResultInterface|ResponseInterface|Redirect
     * @throws LocalizedException
     * @throws NotFoundException
     */
    public function execute(): ResultInterface|ResponseInterface|Redirect
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$this->request->isPost()) {
            throw new NotFoundException(__('Not Found.'));
        }

        if (!$this->customerSession->isLoggedIn() || !($customerId = $this->customerSession->getCustomerId())) {
            throw new LocalizedException(__('User not logged in.'));
        }

        $addressValue = $this->validateAndStringifyAddress($this->request->getPost());

       if(!$addressValue){
           $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

           $this->messageManager->addErrorMessage(__("Please fill all fields"));

           return $resultRedirect->setUrl($this->redirect->getRefererUrl());
       }

        $cart = $this->item->getCurrentCart($customerId);

        /** @var $cart Cart */
        if (!$cart->getId()) {
            throw new LocalizedException(__('Cart not found.'));
        }

        $this->eventManager->dispatch('before_submit_rent_cart', [$cart, $customerId, $addressValue]);

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
        $orderId = $order->getId();

        $order->setHtmlAddress($addressValue);
        $order->setTotalSumm($finalPrice);
        $order->save();

        //Delete cart after order is submitted
        $cart->delete();

        $this->eventManager->dispatch('after_create_rent_order', ['customer_id' => $customerId, 'order' => $order]);

        $this->messageManager->addSuccessMessage(__("Your order was created (ID: $orderId). You can see your Rent order history in your account."));

        $resultRedirect->setUrl($this->redirect->getRefererUrl());

        return $resultRedirect;
    }

    /**
     * Validate address and convert data to string
     *
     * @throws LocalizedException
     */
    private function validateAndStringifyAddress($post): bool|string
    {

        if (empty($post)) {
            throw new LocalizedException(__('No address data found.'));
        }

        $errors = [];

        if($this->config->isSourcesEnabled()){
            $this->allAddressFields = $this->allAddressFieldsPath;
        } else {
            $this->allAddressFields = $this->allAddressFieldsFull;
        }

        $addressData = array_fill_keys($this->allAddressFields, '');

        foreach ($this->allAddressFields as $addressField) {

            if (!isset($post[$addressField]) || $post[$addressField] == '' || !$this->arrayValidator($post[$addressField])
            ) {
                if (in_array($addressField, $this->requiredAddressFields)) {
                    $errors[] = $addressField;
                }
            } else {
                $postedValue = $post[$addressField];

                if (is_array($postedValue)) {
                    for ($i = 0; $i < sizeof($postedValue); $i++) {
                        $postedValue[$i] = htmlspecialchars($postedValue[$i]);
                    }

                    $addressData[$addressField] = $postedValue;
                } else {
                    $addressData[$addressField] = htmlspecialchars($postedValue);

                }
            }
        }

        if (sizeof($errors) > 0) {
            return false;
        }

        $addressValue = '<p>';

        foreach ($addressData as $addrField => $addressDatum) {

            if (is_array($addressDatum)) {
                $addressDatum = $this->removeEmptyRows($addressDatum);
                $rowValue = implode("<br/>", $addressDatum);
            } else {
                $rowValue = $addressDatum;
            }

            if ($addrField == 'telephone') {
                $rowValue = '<span id="telephone">'.$rowValue.'</span>';
            }

            if ($rowValue != '') {
                $addressValue .= $rowValue.'<br/>';
            }
        }

        $addressValue .= '</p>';

        return $addressValue;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    private function arrayValidator($value): bool
    {
        if (!is_array($value) && (trim($value) != '')) {
            return true;
        }

        foreach ($value as $valuePart) {
            if (trim($valuePart) != '') {
                return true;
            }
        }

        return false;
    }


    /**
     * Remove empty rows
     *
     * @param $rows
     *
     * @return array
     */
    private function removeEmptyRows($rows): array
    {
        $newRows = [];

        foreach ($rows as $addressRow) {
            if (trim($addressRow) != '') {
                $newRows[] = $addressRow;
            }
        }

        return $newRows;
    }
}
