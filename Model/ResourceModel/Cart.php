<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Cart extends AbstractDb
{
    public const TABLE_NAME = 'peach_code_rental_cart';
    public const CART_ID = 'cart_id';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME,self::CART_ID);
    }
}
