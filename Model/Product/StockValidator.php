<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use PeachCode\RentalSystem\Model\Config\Config;

class StockValidator{

    /**
     * @param Config                     $config
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        private readonly Config $config,
        private readonly  ProductRepositoryInterface $productRepository)
    {
    }

    /**
     * Stock validator
     *
     * @param $productID
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkIfRentIsAvailable($productID): bool
    {
        $product = $this->productRepository->getById($productID);

        $rentAttributeCode = $this->config->getRentAttribute();

        if ($rentAttributeCode &&
            $product->getCustomAttribute($rentAttributeCode)->getValue() &&
            $product->getCustomAttribute('rent_qty')->getValue() >= 1
        ) {
            return true;
        }

        return false;
    }
}
