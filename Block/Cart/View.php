<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Block\Cart;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use PeachCode\RentalSystem\Block\Product\AddRent;
use PeachCode\RentalSystem\Logger\Logger;
use PeachCode\RentalSystem\Model\Cart\Item;
use PeachCode\RentalSystem\Model\Config\Config;
use PeachCode\RentalSystem\Model\ResourceModel\Cart\Item\Collection;
use PeachCode\RentalSystem\Model\Api\ConfigInterface as RentalApiConfigInterface;

class View extends Template
{
    public const CATEGORY_PAGE_GRID = 'category_page_grid';


    public function __construct(
        private readonly Item $item,
        private readonly SerializerInterface $serializer,
        private readonly Config $config,
        private readonly AddRent $productAddRent,
        private readonly Template\Context $context,
        private readonly SourceRepositoryInterface $sourceItemRepository,
        private readonly Logger $logger,
        private readonly Session $customerSession,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ImageBuilder $imageBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get Cart Items
     *
     * @return Collection|null
     */
    public function getCartItems(): ?Collection
    {
        if (!$this->customerSession->isLoggedIn() ||
            !($customerId = $this->customerSession->getCustomerId())
        ) {
            return null;
        }

        if ($cart = $this->item->getCurrentCart($customerId)) {
            return $cart->getAllItems();
        }

        return null;
    }

    /**
     * Get Product By ID
     *
     * @param $productId
     *
     * @return ProductInterface|null
     */
    public function getProductById($productId): ?ProductInterface
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * Prepare remove URL
     *
     * @return string
     */
    public function getRemoveUrl(): string
    {
        return $this->getUrl(RentalApiConfigInterface::XML_RENT_REMOVE_URL);
    }

    /**
     * Retrieve product image
     *
     * @param Product $product
     * @param string  $imageId
     * @param array   $attributes
     *
     * @return Image
     */
    public function getImage(
        Product $product,
        string $imageId,
        array $attributes = []
    ): Image {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * Check is mini cart active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->config->getRentEnabled();
    }

    /**
     * @return string
     */
    public function getPostUrl(): string
    {
        return $this->getUrl(RentalApiConfigInterface::XML_RENT_SUBMIT_URL);
    }

    /**
     * Get rent price
     *
     * @param Product $product
     *
     * @return string
     */
    public function getRentPrice(Product $product): string
    {
        return $this->productAddRent->getRentPrice($product);
    }

    /**
     * Get final price
     *
     * @param $item
     *
     * @return string
     */
    public function getFinalPrice($item): string
    {
        $finalPrice = ($this->getFinalFullDays($item) * (int)$item->getData('rent_price'));
        $amountToSubtract = ($item->getDiscount() / 100) * $finalPrice;

        $finalPrice = $finalPrice - $amountToSubtract;

        return (string)$finalPrice;
    }

    /**
     * Get full days value
     *
     * @param $item
     *
     * @return int
     */
    public function getFinalFullDays($item): int
    {
        $startDate = strtotime($item->getStartDate());
        $endDate = strtotime($item->getEndDate());
        $dateDiff = $endDate - $startDate;

        $days = intval($dateDiff / (60 * 60 * 24));

        return !$days ? 1 : $days;
    }

    /**
     * Get full days value for GraphQL
     *
     * @param $startDate
     * @param $endDate
     *
     * @return int
     */
    public function getFinalFullDaysGraphQl($startDate, $endDate): int
    {
        $startDateObj = strtotime($startDate);
        $endDateObj = strtotime($endDate);
        $dateDiff = $endDateObj - $startDateObj;

        $days = intval($dateDiff / (60 * 60 * 24));

        return !$days ? 1 : $days;
    }

    /**
     * Prepare view Url
     *
     * @return string
     */
    public function getViewUrl(): string
    {
        return $this->getUrl(RentalApiConfigInterface::XML_RENT_VIEW_URL);
    }

    /**
     * @return string
     */
    public function getRentItemsCount(): string
    {
        if($this->getCartItems() && $quoteProduct = $this->getCartItems()->getSize()){
            return '('.$quoteProduct.')';
        }

       return '';
    }

    /**
     * Get store locations
     *
     * @return array
     */
    public function prepareStorePickUp(): array
    {
        if(!$this->config->isStoreLocatorActive()){
            return [];
        }

        return $this->serializer->unserialize($this->config->getStoreLocatorAddress());
    }


    public function getSourceAddressById(string $sourceId)
    {
        $address =  $this->sourceItemRepository->get($sourceId);

       return [
           'name' => $address->getName(),
           'frontend_name' => $address->getDescription(),
           'country_id' => $address->getCountryId(),
           'region' => $address->getRegion(),
           'city' => $address->getCity(),
           'street' => $address->getStreet(),
           'postcode' => $address->getPostcode(),
           'phone' => $address->getPhone(),
        ];
    }
}
