<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Indexer;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Indexer\ActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResourceConnection;

class ProductIndexer implements ActionInterface, MviewActionInterface
{

    /**
     * @param CollectionFactory  $productCollectionFactory
     * @param ResourceConnection $resource
     */
    public function __construct(
        private readonly  CollectionFactory $productCollectionFactory,
        private readonly ResourceConnection $resource
    ) {}

    /**
     * Used by mview, allows process indexer in the "Update on schedule" mode
     *
     * @param int[] $ids
     */
    public function execute($ids): void
    {
        $this->reindexByIds($ids);
    }

    /**
     * Will take all the data and reindex.
     * Will run when reindex via command line
     */
    public function executeFull(): void
    {
        // Select all products with is_rent_available = 1 and add them to the index table
        $this->reindexAll();
    }

    /**
     * Works with a set of entity changed (mass actions)
     *
     * @param int[] $ids
     */
    public function executeList(array $ids): void
    {
        $this->reindexByIds($ids);
    }

    /**
     * Works in runtime for a single entity using plugins
     *
     * @param int $id
     */
    public function executeRow($id): void
    {
        $this->reindexByIds([$id]);
    }

    /**
     * Reindex products by their IDs.
     *
     * @param array $ids
     */
    private function reindexByIds(array $ids): void
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('entity_id', ['in' => $ids])
            ->addAttributeToFilter('is_rent_available', 1);

        $this->updateIndexTable($collection);
    }

    /**
     * Reindex all products with is_rent_available = 1.
     */
    private function reindexAll(): void
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('is_rent_available', 1);

        $this->updateIndexTable($collection);
    }

    /**
     * Update the index table with product IDs.
     *
     * @param Collection $collection
     */
    private function updateIndexTable(Collection $collection): void
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('rent_product_index');

        // Clear the table before adding new rows (for full reindex)
        $connection->truncateTable($tableName);

        // Insert new product IDs into the index table
        foreach ($collection as $product) {
            $connection->insert(
                $tableName,
                ['product_id' => $product->getId()]
            );
        }
    }
}
