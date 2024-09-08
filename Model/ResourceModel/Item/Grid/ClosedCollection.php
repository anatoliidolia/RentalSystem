<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel\Item\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use PeachCode\RentalSystem\Model\ResourceModel\Item;
use PeachCode\RentalSystem\Model\ResourceModel\Order;
use Psr\Log\LoggerInterface as Logger;

class ClosedCollection extends SearchResult
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = Order::TABLE_NAME,
        $resourceModel = Item::class
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * Filter by order status
     *
     * @return $this|ActiveCollection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('status', 'closed');
        return $this;
    }
}
