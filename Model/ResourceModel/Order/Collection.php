<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('PeachCode\RentalSystem\Model\Order', 'PeachCode\RentalSystem\Model\ResourceModel\Order');
    }
}
