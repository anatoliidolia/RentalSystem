<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Indexer;

use Magento\Framework\Model\AbstractModel;
use PeachCode\RentalSystem\Model\ResourceModel\RentProduct;

class RentProductIndex extends AbstractModel
{

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(RentProduct::class);
    }
}
