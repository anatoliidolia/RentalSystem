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
use Magento\Framework\Exception\CouldNotDeleteException;

class CartRepository implements ItemRepositoryInterface
{
    /**
     * @var ResourceCartItem
     */
    private ResourceCartItem $resource;

    /**
     * @var ItemFactory
     */
    private ItemFactory $itemFactory;

    /**
     * @param ResourceCartItem $resource
     * @param ItemFactory       $itemFactory
     */
    public function __construct(
        ResourceCartItem $resource,
        ItemFactory $itemFactory
    ) {
        $this->itemFactory = $itemFactory;
        $this->resource = $resource;
    }

    /**
     * Get Information
     *
     * @param int $entity_id
     *
     * @return ItemInterface|Item
     * @throws NoSuchEntityException
     */
    public function get(int $entity_id): ItemInterface|Item
    {
        $object = $this->itemFactory->create();
        $this->resource->load($object, $entity_id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Item value with ID "%1" does not exist.', $entity_id));
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

    /**
     * Delete Information
     *
     * @param ItemInterface|Item $cartItemData
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function delete(ItemInterface|Item $cartItemData): bool
    {
        try {
            $this->resource->delete($cartItemData);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Error: some data can not be deleted.', [
                'exception' => $exception->getMessage(),
                'item' => $cartItemData
            ]));
        }

        return true;
    }

    /**
     * Delete by ID
     *
     * @param int $entity_id
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $entity_id): bool
    {
        try {
            $item = $this->get($entity_id);
            return $this->delete($item);
        } catch (NoSuchEntityException) {
            throw new NoSuchEntityException(__('Error: some data can not be deleted.', $entity_id));
        }
    }
}
