<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Order;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\Data\ProductInterface;
use PeachCode\RentalSystem\Model\Api\ConfigInterface;
use PeachCode\RentalSystem\Block\Cart\View;
use PeachCode\RentalSystem\Model\Config\Config;

class Item extends AbstractModel implements IdentityInterface
{

    /**
     * @param Context                    $context
     * @param Registry                   $registry
     * @param ProductRepositoryInterface $productRepository
     * @param View                       $view
     * @param array                      $data
     */
    public function __construct(
        private readonly Config $config,
        Context $context,
        Registry $registry,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly View $view,
        array $data = []
    )
    {
        parent::__construct($context, $registry, null, null, $data);
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('PeachCode\RentalSystem\Model\ResourceModel\Order\Item');
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [ConfigInterface::XML_ORDER_ITEM_CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $cartItem
     * @param $orderId
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function createFromCartItem($cartItem, $orderId): static
    {
        /** @var $cartItem \PeachCode\RentalSystem\Model\Cart\Item */
        if (!$cartItem->getId()) {
            throw new LocalizedException(__('Cannot overwrite order.'));
        }

        $sourceId = $cartItem->getData('source_id') ?? '';

        $this->setOrderId($orderId);

        $product = $this->productRepository->getById($cartItem->getProductId());

        $rentSku = $this->getFormattedSku($product);

        $this->setDiscount($cartItem->getData('discount'));
        $this->setStartDate($cartItem->getData('start_date'));
        $this->setSourceId($sourceId);
        $this->setEndDate($cartItem->getData('end_date'));
        $this->setFullDays($this->view->getFinalFullDays($cartItem));

        $this->setPrice('Free');

        if($product->getCustomAttribute(ConfigInterface::XML_RENT_PRODUCT_PRICE)){
            $this->setPrice($product->getCustomAttribute(ConfigInterface::XML_RENT_PRODUCT_PRICE)->getValue());
        }

        $this->setSku($rentSku);
        $this->setName($product->getName());

        $this->save();

        // here I need to check is MSI configuration enabled for module

        if($this->config->isSourcesEnabled()){

            $sourceUpdate = $sourceId;
            //  reduce item qty from selected warehouse

            return $this;
        }

        /**
         * Update product rent SKU
         */
        $rentQty = $product->getCustomAttribute('rent_qty')->getValue();
        // TODO: need to update this - now always we removed only 1 item
        $product->setCustomAttribute('rent_qty', $rentQty - 1);
        $this->productRepository->save($product);

        return $this;
    }

    /**
     * @param $product Product|ProductInterface
     *
     * @return array|string|string[]
     */
    private function getFormattedSku(ProductInterface|Product $product
    ): array|string {
        $type = $product->getTypeId();
        $sku = $product->getSku();

        if ($type == "grouped") {
            $sku = str_replace("-GROUPED", "", $sku);
        }

        return $sku;
    }
}
