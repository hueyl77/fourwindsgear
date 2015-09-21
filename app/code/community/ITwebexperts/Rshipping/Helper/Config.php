<?php

class ITwebexperts_Rshipping_Helper_Config extends Mage_Core_Helper_Abstract
{
    const RSHIPPING_GENERAL_ENABLE = 'payperrentals/rshipping_general/enabled';
    const RSHIPPING_GENERAL_ALLOW_ALL_METHODS = 'payperrentals/rshipping_general/allow_all_methods';
    const RSHIPPING_GENERAL_ALLOW_CUSTOM_PRICE = 'payperrentals/rshipping_general/allow_custom_price';
    const RSHIPPING_GENERAL_PRODUCT_PAGE_SHIPPING_METHODS = 'payperrentals/rshipping_general/product_page_shipping_methods';
    const RSHIPPING_UPS_ENABLED = 'payperrentals/rshipping_ups/enabled';
    const RSHIPPING_UPS_MODE = 'payperrentals/rshipping_ups/ups_mode';

    public function isEnabled($_storeId = null)
    {
        if ($_storeId) {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_GENERAL_ENABLE, $_storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_GENERAL_ENABLE);
        }
    }

    /**
     * Get product page shipping methods global configuration
     * @param mixed $_storeId
     * @return bool
     * */
    public function isShippingGlobalConfiguration($_storeId = null)
    {
        if ($_storeId) {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_GENERAL_PRODUCT_PAGE_SHIPPING_METHODS, $_storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_GENERAL_PRODUCT_PAGE_SHIPPING_METHODS);
        }
    }

    /**
     * Is allow all methods for all products. Product configuration ignored
     * @param mixed $_storeId
     * @return bool
     * */
    public function isAllowAllMethods($_storeId = null)
    {
        if ($_storeId) {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_GENERAL_ALLOW_ALL_METHODS, $_storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_GENERAL_ALLOW_ALL_METHODS);
        }
    }

    /**
     * Is use custom shipping price for shipping methods
     * @param mixed $_storeId
     * @return bool
     * */
    public function isCustomShippingPrice($_storeId = null)
    {
        if ($_storeId) {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_GENERAL_ALLOW_CUSTOM_PRICE, $_storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_GENERAL_ALLOW_CUSTOM_PRICE);
        }
    }

    /**
     * Is live transit API enabled
     * @param mixed $_storeId
     * @return bool
     * */
    public function isLiveTransitApi($_storeId = null)
    {
        if ($_storeId) {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_UPS_ENABLED, $_storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_UPS_ENABLED);
        }
    }

    /**
     * Is live ups mode
     * @param mixed $_storeId
     * @return bool
     * */
    public function isLiveUpsMode($_storeId = null)
    {
        if ($_storeId) {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_UPS_MODE, $_storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::RSHIPPING_UPS_MODE);
        }
    }
}