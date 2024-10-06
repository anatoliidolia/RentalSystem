<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Block\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\Template;
use PeachCode\RentalSystem\Model\ResourceModel\RentProductIndex\CollectionFactory as RentProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class RentProducts extends Template
{
    /**
     * @param Template\Context             $context
     * @param RentProductCollectionFactory $rentProductCollectionFactory
     * @param ProductCollectionFactory     $productCollectionFactory
     * @param array                        $data
     */
    public function __construct(
        Template\Context $context,
        private readonly RentProductCollectionFactory $rentProductCollectionFactory,
        private readonly ProductCollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get product from index
     *
     * @return Collection
     */
    public function getRentProducts(): Collection
    {
        $rentProductCollection = $this->rentProductCollectionFactory->create();
        $productIds = $rentProductCollection->getColumnValues('product_id');

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $productIds]);

        return $productCollection;
    }
}
