<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RentProduct extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('rent_product_index', 'product_id');
    }
}
