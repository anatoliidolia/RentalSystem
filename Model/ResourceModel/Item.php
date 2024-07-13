<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use PeachCode\RentalSystem\Model\ResourceModel\Order;
class Item extends AbstractDb {

    protected function _construct()
    {
        $this->_init(Order::TABLE_NAME, Order::ORDER_ID);
    }
}
