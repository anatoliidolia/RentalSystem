<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Api;

interface ConfigInterface
{
    public const XML_CART_ITEM_LIMIT = 'rental/module/item_limit';
    public const XML_MODULE_STATUS = 'rental/module/enabled';
    public const XML_MODULE_STATUS_GUEST = 'rental/module/enabled_for_guest';
    public const XML_PRODUCT_RENTAL_ATTRIBUTE = 'rental/product/rental_attribute';
    public const XML_RENTAL_DISCOUNT_MATRIX = 'rental/discount/percent_matrix';
    public const XML_RENTAL_EMAIL = 'rental/module/email';
    public const XML_RENT_ORDER_SENDER = 'rental/module/email_sender';
    public const XML_RENT_ORDER_SEND_TO = 'rental/module/email_to';
    public const XML_RENT_ORDER_TEMPLATE = 'rental/module/template';
    public const XML_STORE_LOCATOR_STATUS = 'rental/locations/is_enabled';
    public const XML_STORE_LOCATOR_ADDRESS = 'rental/locations/stores';
    public const XML_RENT_ADD_URL = 'rent/cart/add';
    public const XML_RENT_REMOVE_URL = 'rent/cart/remove';
    public const XML_RENT_SUBMIT_URL = 'rent/cart/submit';
    public const XML_RENT_VIEW_URL = 'rent/cart/view';
    public const XML_RENT_PRODUCT_PRICE = 'base_rent_price';
    public const XML_CACHE_TAG = 'peach_code_rent_cart_item';
    public const XML_CART_CACHE_TAG = 'peach_code_rent_cart';
    public const XML_ORDER_CACHE_TAG = 'peach_code_rent_order';
    public const XML_ORDER_ITEM_CACHE_TAG = 'peach_code_rent_order_item';
    public const XML_RENT_ITEM_ID = 'rent_item_id';
    public const PRODUCT_ID_FROM_TEMPLATE = 'rent_product_id';
    public const RENT_DATE_START = 'start_date';
    public const RENT_DATE_END = 'end_date';
}
