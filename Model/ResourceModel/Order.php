<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Order extends AbstractDb
{
    public const TABLE_NAME = 'peach_code_rental_order';

    public const ORDER_ID = 'order_id';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME,self::ORDER_ID);
    }
}
