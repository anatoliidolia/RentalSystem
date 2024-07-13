<?php

namespace PeachCode\RentalSystem\Model\ResourceModel\CustomEntity;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('PeachCode\RentalSystem\Model\CustomEntity', 'PeachCode\RentalSystem\Model\ResourceModel\CustomEntity');
    }
}
