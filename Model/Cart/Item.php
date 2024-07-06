<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Cart;

use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use PeachCode\RentalSystem\Model\Api\ConfigInterface;
use PeachCode\RentalSystem\Model\Cart;
use PeachCode\RentalSystem\Model\ResourceModel\Cart\CollectionFactory;
use PeachCode\RentalSystem\Model\ResourceModel\Cart\Item as ItemResource;

class Item extends AbstractModel implements IdentityInterface
{

    /**
     * @param CollectionFactory $rentCartCollectionFactory
     * @param Context           $context
     * @param Registry          $registry
     * @param array             $data
     */
    public function __construct(
        private readonly CollectionFactory $rentCartCollectionFactory,
        Context $context,
        Registry $registry,
        array $data = []
    ){
        parent::__construct($context, $registry, null, null, $data);
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ItemResource::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [ConfigInterface::XML_CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $id
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    public function loadById($id): static
    {
        $this->getResource()->load($this, $id);
        if (!$this->getId()) {
            throw new NoSuchEntityException(__('Unable to find rent cart item with ID "%1"', $id));
        }
        return $this;
    }

    /**
     * @param $customerId
     *
     * @return Cart|DataObject
     */
    public function getCurrentCart($customerId): Cart|DataObject
    {
        $collection = $this->rentCartCollectionFactory->create();

        $collection->addFieldToFilter('customer_id', ['eq' => $customerId]);

        return $collection->getFirstItem();
    }
}
