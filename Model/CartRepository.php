<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model;

use PeachCode\RentalSystem\Api\Data\ItemInterface;
use PeachCode\RentalSystem\Model\Cart\Item;
use PeachCode\RentalSystem\Model\Api\ItemRepositoryInterface;
use PeachCode\RentalSystem\Model\Cart\ItemFactory;
use PeachCode\RentalSystem\Model\ResourceModel\Cart\Item as ResourceCartItem;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CartRepository implements ItemRepositoryInterface
{
    /**
     * @var ResourceCartItem
     */
    private ResourceCartItem $resource;

    /**
     * @var ItemFactory
     */
    private ItemFactory $oneClickOrderFactory;

    /**
     * @param ResourceCartItem $resource
     * @param ItemFactory       $oneClickOrderFactory
     */
    public function __construct(
        ResourceCartItem $resource,
        ItemFactory $oneClickOrderFactory
    ) {
        $this->oneClickOrderFactory = $oneClickOrderFactory;
        $this->resource = $resource;
    }

    /**
     * Get Information
     *
     * @param int $entity_id
     *
     * @return ItemInterface
     * @throws NoSuchEntityException
     */
    public function get(int $entity_id): ItemInterface
    {
        $object = $this->oneClickOrderFactory->create();
        $this->resource->load($object, $entity_id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('OneClickOrder value with ID "%1" does not exist.', $entity_id));
        }

        return $object;
    }

    /**
     * Save Information
     *
     * @param ItemInterface|Item $cartItemData
     *
     * @return ItemInterface|Item
     * @throws CouldNotSaveException
     */
    public function save(ItemInterface|Item $cartItemData): ItemInterface|Item
    {
        try {
            $this->resource->save($cartItemData);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()), $exception);
        }

        return $cartItemData;
    }
}
