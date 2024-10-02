<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Config;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PeachCode\RentalSystem\Model\Api\ConfigInterface;

class Config
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly SerializerInterface $serializer
    ) {}

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

    public function isSourcesEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(ConfigInterface::XML_MODULE_SOURCES_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check is Rent module enabled for Guest
     *
     * @return bool
     */
    public function getRentForGuest(): bool
    {
        return $this->scopeConfig->isSetFlag(ConfigInterface::XML_MODULE_STATUS_GUEST, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check is Rent module enabled
     *
     * @return bool
     */
    public function getRentEnabled(): bool
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

    /**
     * Get order status mapper
     *
     * @return mixed
     */
    public function getRentOrderStatusMapper(): mixed
    {
        return $this->scopeConfig->getValue(ConfigInterface::XML_RENT_ORDER_STATUS_MAPPER, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Get order status mapper
     *
     * @return mixed
     */
    /**
     * Get order status mapper
     *
     * @return array
     */
    public function getRentOrderStatusMapperArray(): array
    {
        $configData = $this->scopeConfig->getValue(ConfigInterface::XML_RENT_ORDER_STATUS_MAPPER, ScopeInterface::SCOPE_WEBSITE);

        $configArray = $this->serializer->unserialize($configData);

        $options = [];

        foreach ($configArray as $value) {
            $options[] = [
                'value' => $value['status_id'],
                'label' => $value['status_text']
            ];
        }

        return $options;
    }
}
