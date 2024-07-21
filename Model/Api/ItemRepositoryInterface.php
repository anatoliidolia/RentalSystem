<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Api;

use PeachCode\RentalSystem\Api\Data\ItemInterface;
use PeachCode\RentalSystem\Model\Cart\Item;

interface ItemRepositoryInterface
{

    /**
     * Get Item object.
     *
     * @param int $entity_id
     * @return ItemInterface|Item
     */
    public function get(int $entity_id): ItemInterface|Item;

    /**
     * Save Item Cart object.
     *
     * @param ItemInterface $cartItemData
     *
     * @return ItemInterface|Item
     */
    public function save(ItemInterface $cartItemData): ItemInterface|Item;

    /**
     * Delete Item object.
     *
     * @param ItemInterface|Item $cartItemData
     *
     * @return bool
     */
    public function delete(ItemInterface|Item $cartItemData): bool;

    /**
     * Delete Item Cart object by ID.
     *
     * @param int $entity_id
     *
     * @return bool
     */
    public function deleteById(int $entity_id): bool;
}
