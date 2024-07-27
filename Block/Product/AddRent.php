<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\EncoderInterface;
use PeachCode\RentalSystem\Model\Config\Config;
use PeachCode\RentalSystem\Model\Api\ConfigInterface as RentalApiConfigInterface;

class AddRent extends View
{

    /**
     * @param Context                                  $context
     * @param EncoderInterface                         $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param StringUtils                              $string
     * @param \Magento\Catalog\Helper\Product          $productHelper
     * @param ConfigInterface                          $productTypeConfig
     * @param FormatInterface                          $localeFormat
     * @param Session                                  $customerSession
     * @param ProductRepositoryInterface               $productRepository
     * @param PriceCurrencyInterface                   $priceCurrency
     * @param Config                                   $config
     * @param array                                    $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        private readonly Config $config,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    /**
     * Prepare rent URL
     *
     * @return string
     */
    public function getAddRentUrl(): string
    {
        return $this->getUrl(RentalApiConfigInterface::XML_RENT_ADD_URL);
    }

    /**
     * Check is Rent module Available
     *
     * @param Product $product
     *
     * @return mixed|null
     */
    public function isRentAvailable(Product $product): mixed
    {
        /**
         * Check is module enabled && check is module available gor guests
         */
        if(!$this->config->getRentEnabled() && !$this->customerSession->isLoggedIn()) {
             return false;
        }

        $attributeCode = $this->config->getRentAttribute();
        return $product->getData($attributeCode);
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
        if($product->getCustomAttribute(RentalApiConfigInterface::XML_RENT_PRODUCT_PRICE)){
            return $this->priceCurrency->format(
                $product->getCustomAttribute(RentalApiConfigInterface::XML_RENT_PRODUCT_PRICE)->getValue()
            );
        }
        return '';
    }
}
