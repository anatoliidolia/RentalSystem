<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel\Cart;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PeachCode\RentalSystem\Model\ResourceModel\Cart;
use PeachCode\RentalSystem\Model\Cart as CartModel;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(CartModel::class, Cart::class);
    }
}
