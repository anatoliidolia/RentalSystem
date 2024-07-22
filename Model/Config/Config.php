<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Config;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PeachCode\RentalSystem\Model\Api\ConfigInterface;

class Config
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Get custom option
     *
     * @return mixed|int
     */
    public function getCartItemsLimit(): mixed
    {
        return $this->scopeConfig->getValue(ConfigInterface::XML_CART_ITEM_LIMIT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get custom option
     *
     * @return mixed
     */
    public function getRentAttribute(): mixed
    {
        return $this->scopeConfig->getValue(ConfigInterface::XML_PRODUCT_RENTAL_ATTRIBUTE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check is Rend module enabled for Guest
     *
     * @return bool
     */
    public function getRendForGuest(): bool
    {
        return $this->scopeConfig->isSetFlag(ConfigInterface::XML_MODULE_STATUS_GUEST, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check is Rend module enabled
     *
     * @return bool
     */
    public function getRendEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(ConfigInterface::XML_MODULE_STATUS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get custom option
     *
     * @return mixed|int
     */
    public function getDiscountMatrix(): mixed
    {
        return $this->scopeConfig->getValue(ConfigInterface::XML_RENTAL_DISCOUNT_MATRIX, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get custom option
     *
     * @return bool
     */
    public function getRentOrderEmail(): bool
    {
        return $this->scopeConfig->isSetFlag(ConfigInterface::XML_RENTAL_EMAIL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get custom option
     *
     * @return bool
     */
    public function isStoreLocatorActive(): bool
    {
        return $this->scopeConfig->isSetFlag(ConfigInterface::XML_STORE_LOCATOR_STATUS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get custom option
     *
     * @return mixed|int
     */
    public function getStoreLocatorAddress(): mixed
    {
        return $this->scopeConfig->getValue(ConfigInterface::XML_STORE_LOCATOR_ADDRESS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get custom option
     *
     * @return mixed
     */
    public function getRentOrderEmailTemplate(): mixed
    {
        return $this->scopeConfig->getValue(ConfigInterface::XML_RENT_ORDER_TEMPLATE, ScopeInterface::SCOPE_STORE) ?? 'rental_module_template';
    }

    /**
     * Get custom option
     *
     * @return mixed
     */
    public function getRentOrderEmailSender(): mixed
    {
        return $this->scopeConfig->getValue(ConfigInterface::XML_RENT_ORDER_SENDER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get custom option
     *
     * @return mixed
     */
    public function getRentOrderEmailSendTo(): mixed
    {
        return $this->scopeConfig->getValue(ConfigInterface::XML_RENT_ORDER_SEND_TO, ScopeInterface::SCOPE_STORE);
    }
}
