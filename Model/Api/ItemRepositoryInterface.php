<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Api;

use PeachCode\RentalSystem\Api\Data\ItemInterface;
use PeachCode\RentalSystem\Model\Cart\Item;

interface ItemRepositoryInterface
{

    /**
     * Get OneClickOrder object.
     *
     * @param int $entity_id
     * @return ItemInterface
     */
    public function get(int $entity_id): ItemInterface;

    /**
     * Save Item Cart object.
     *
     * @param ItemInterface $cartItemData
     *
     * @return ItemInterface|Item
     */
    public function save(ItemInterface $cartItemData): ItemInterface|Item;
}
