<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Item extends AbstractDb
{

    public const TABLE_NAME = 'peach_code_rental_order_item';

    public const ORDER_ITEM = 'order_item_id';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME,self::ORDER_ITEM);
    }

}
