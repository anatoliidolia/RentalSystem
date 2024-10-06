<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel\RentProductIndex;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PeachCode\RentalSystem\Model\Indexer\RentProductIndex as Model;
use PeachCode\RentalSystem\Model\ResourceModel\RentProduct as ResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
