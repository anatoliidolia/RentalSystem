<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model;

use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use PeachCode\RentalSystem\Model\Api\ConfigInterface;
use PeachCode\RentalSystem\Model\Order\ItemFactory;
use PeachCode\RentalSystem\Model\ResourceModel\Cart\Item\Collection;
use PeachCode\RentalSystem\Model\ResourceModel\Order\Item\CollectionFactory;
use PeachCode\RentalSystem\Model\ResourceModel\Order as OrderResource;

class Order extends AbstractModel implements  IdentityInterface
{

    /**
     * @param Context            $context
     * @param Registry           $registry
     * @param ItemFactory        $rentOrderItemFactory
     * @param OrderResource      $orderResource
     * @param CustomerRepository $customerRepository
     * @param CollectionFactory  $rentOrderItemCollectionFactory
     * @param array              $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly ItemFactory $rentOrderItemFactory,
        private readonly OrderResource $orderResource,
        private readonly CustomerRepository $customerRepository,
        private readonly CollectionFactory $rentOrderItemCollectionFactory,
        array $data = []
    ){
        parent::__construct($context, $registry, null, null, $data);
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(OrderResource::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [ConfigInterface::XML_ORDER_CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @throws NoSuchEntityException
     */
    public function loadById($id): static
    {
        $this->orderResource->load($this, $id);
        if (! $this->getId()) {
            throw new NoSuchEntityException(__('Unable to find rent order with ID "%1"', $id));
        }
        return $this;
    }

    /**
     * @param $rentCart Cart
     *
     * @return Order
     * @throws LocalizedException
     */
    public function createFromCart(Cart $rentCart): static
    {
        if ($this->getId()) {
            throw new LocalizedException(__('Cannot overwrite order.'));
        }

        $customer = $this->customerRepository->getById($rentCart->getCustomerId());

        $this->setCustomerId($customer->getId());
        $this->setCustomerEmail($customer->getEmail());


        $cartItems = $rentCart->getAllItems();

        if(!($cartItems && $cartItems->getSize())){
            throw new LocalizedException(__('No items in cart.'));
        }

        $this->setTotalItems($cartItems->getSize());

        $this->save();

        $orderId = $this->getId();

        try {
            //Add items
            foreach ($cartItems as $cartItem) {
                $orderItem = $this->rentOrderItemFactory->create();
                $orderItem->createFromCartItem($cartItem,$orderId);
            }
        }catch (\Exception $e){
            //Deleting order if items were unsuccessful
            $this->delete();
            throw new LocalizedException(__($e->getMessage()));
        }

        return $this;
    }

    /**
     * @return ResourceModel\Order\Item\Collection|Collection
     */
    public function getAllItems(): ResourceModel\Order\Item\Collection|Collection
    {
        $collection = $this->rentOrderItemCollectionFactory->create();

        /** @var $collection Collection */
        $collection->addFieldToFilter('order_id',['eq' => $this->getId()]);
        $collection->load();

        return $collection;
    }
}
