<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel\Cart\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('PeachCode\RentalSystem\Model\Cart\Item', 'PeachCode\RentalSystem\Model\ResourceModel\Cart\Item');
    }
}
