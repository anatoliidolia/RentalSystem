<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel\Cart;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Item extends AbstractDb
{
    public const TABLE_NAME = 'peach_code_rental_cart_item';

    public const CART_ITEM = 'cart_item_id';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME,self::CART_ITEM);
    }
}
