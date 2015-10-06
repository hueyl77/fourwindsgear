<?php

class ITwebexperts_Rshipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    const RSHIPPING_UPS_API_ENABLE = 'payperrentals/rshipping_ups/enabled';
    const RSHIPPING_UPS_ACCESS_KEY = 'payperrentals/rshipping_ups/ups_access_token';
    const RSHIPPING_UPS_USERNAME = 'payperrentals/rshipping_ups/ups_username';
    const RSHIPPING_UPS_PASSWORD = 'payperrentals/rshipping_ups/ups_password';
    const RSHIPPING_STORE_POST_CODE = 'payperrentals/rshipping_ups/store_post_code';

    const RSHIPPING_GEOCODE_URL = 'http://maps.googleapis.com/maps/api/geocode/xml?address={{address}}&sensor=false';
    const RSHIPPING_UPS_ENDPOINT_URL_LIVE = 'https://onlinetools.ups.com/ups.app/xml/TimeInTransit';
    const RSHIPPING_UPS_ENDPOINT_URL_TEST = 'https://wwwcie.ups.com/webservices/TimeInTransit';

    private $_cacheTurnover = array();
    public function getGeoData($_address)
    {

        $_xmlContent = file_get_contents(str_replace('{{address}}', $_address, self::RSHIPPING_GEOCODE_URL));
        if (empty($_xmlContent)) return false;
        $_geoData = new Zend_Config_Xml($_xmlContent);
        if ($_geoData->status != 'OK') return false;
        $_address = array();
        foreach ($_geoData->result->address_component as $_addressComponent) {
            if (!is_object($_addressComponent->type)) {
                if ($_addressComponent->type == 'postal_code') {
                    $_address['PostalCode'] = $_addressComponent->short_name;
                }
            } else {
                foreach ($_addressComponent->type as $_type) {
                    if ($_type == 'locality') {
                        $_address['City'] = $_addressComponent->short_name;
                    } elseif ($_type == 'country') {
                        $_address['CountryCode'] = $_addressComponent->short_name;
                    } elseif ($_type == 'administrative_area_level_1') {
                        $_address['StateProvinceCode'] = $_addressComponent->short_name;
                    }
                }
            }
        }
        return array('Address' => $_address);
    }

    public function getTimeInTransit($_zip, $_product, $_method = null)
    {
        if (!empty($this->_cacheTurnover) && array_key_exists($_method, $this->_cacheTurnover)) {
            return $this->_cacheTurnover[$_method];
        }
        /*Varien_Profiler::start('ups_get_time_in_transit');*/
        if ($_zip == '' || !Mage::getStoreConfig(self::RSHIPPING_STORE_POST_CODE)) return 0;

        /**
         * Get Store GeoData for ups
         * */
        $_storeGeoData = $this->getGeoData(Mage::getStoreConfig(self::RSHIPPING_STORE_POST_CODE));
        if (!$_storeGeoData) return 0;

		$pWeight = $_product->getWeight();
        /**
         * Get Customer GeoData for ups
         * */
        $_customerGeoData = $this->getGeoData($_zip);
        if (!$_customerGeoData) return 0;

        /**
         * UPS API Transit Time
         * */
        $_accessKey = Mage::helper('core')->decrypt(Mage::getStoreConfig(self::RSHIPPING_UPS_ACCESS_KEY));
        $_userId = Mage::getStoreConfig(self::RSHIPPING_UPS_USERNAME);
        $_pass = Mage::helper('core')->decrypt(Mage::getStoreConfig(self::RSHIPPING_UPS_PASSWORD));

        $_wsdl = Mage::getModuleDir('etc', 'ITwebexperts_Rshipping') . DS . "upsApi" . DS . "tntws.wsdl";
        $_operation = "ProcessTimeInTransit";
        $_endPointUrl = Mage::helper('rshipping/config')->isLiveUpsMode(Mage::app()->getStore()->getId()) ? self::RSHIPPING_UPS_ENDPOINT_URL_LIVE : self::RSHIPPING_UPS_ENDPOINT_URL_TEST;
        try {
            /* Initialize soap client*/
            $_client = new SoapClient($_wsdl, array(
                'soap_version' => 'SOAP_1_1', // use soap 1.1 client
                'trace' => 1
            ));
            $_client->__setLocation($_endPointUrl);

            /* Create soap header*/
            $_upss = array(
                'UsernameToken' => array(
                    'Username' => $_userId,
                    'Password' => $_pass
                ),
                'ServiceAccessToken' => array(
                    'AccessLicenseNumber' => $_accessKey
                )
            );

            $_header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0', 'UPSSecurity', $_upss);
            $_client->__setSoapHeaders($_header);

            /**
             * TODO: get weight code from product
             * Max UPS weight = 31kg (70lbs)
             * Create soap request
             */
            $_request = array(
                'Request' => array(
                    'RequestOption' => 'TNT'
                ),
                'ShipFrom' => $_storeGeoData,
                'ShipTo' => $_customerGeoData,
                'Pickup' => array(
                    'Date' => Mage::getModel('core/date')->date('Ymd')
                ),
                'ShipmentWeight' => array(
                    'Weight' => (($pWeight && (int)$pWeight < 75) ? $pWeight : 75),
                    'UnitOfMeasurement' => array(
                        'Code' => 'KGS',
                        'Description' => 'Kilograms'
                    )
                ),
                'TotalPackagesInShipment' => '1',
                'InvoiceLineTotal' => array(
                    'CurrencyCode' => Mage::app()->getStore()->getCurrentCurrencyCode(),
                    'MonetaryValue' => '1'
                ),
                'MaximumListSize' => '1'

            );
            $_response = $_client->__soapCall($_operation, array($_request));


            /*Varien_Profiler::stop('ups_get_time_in_transit');
            $_timers = Varien_Profiler::getTimers();*/

            /**
             * Check selected method in response
             * */
            if ($_response->Response->ResponseStatus->Code != 1) {
                Mage::log($_response->Response->ResponseStatus->Description, null, 'rshipping.log');
                $this->_cacheTurnover[$_method] = 0;
                return $this->_cacheTurnover[$_method];
            }
            $_serviceSummaryAr = $_response->TransitResponse->ServiceSummary;
            if (!is_array($_serviceSummaryAr)) {
                Mage::log('Error in response parsing', null, 'rshipping.log');
                $this->_cacheTurnover[$_method] = 0;
                return $this->_cacheTurnover[$_method];
            }
            foreach ($_serviceSummaryAr as $_serviceSummary) {
                if (mb_stripos($_method, $_serviceSummary->Service->Code) !== false) {
                    $this->_cacheTurnover[$_method] = (int)$_serviceSummary->EstimatedArrival->BusinessDaysInTransit;
                    return $this->_cacheTurnover[$_method];
                }
            }
        } catch (Exception $_e) {
            Mage::log($_e->getMessage(), null, 'rshipping.log');
            $this->_cacheTurnover[$_method] = 0;
            return $this->_cacheTurnover[$_method];
        }

        $this->_cacheTurnover[$_method] = 0;
        return $this->_cacheTurnover[$_method];
    }

    public function getLastAddedZip()
    {
        $_cartCollection = Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection();
        $_quote = Mage::getSingleton('checkout/session')->getQuote();
        $_address = $_quote->getShippingAddress();
        if (!$_cartCollection->getSize()) {
            $_address->setPostcode('');
            $_address->setCollectShippingRates(true)->collectShippingRates();
            return false;
        }
        $_postcode = ($_address->getPostcode() != '') ? $_address->getPostcode() : false;
        return $_postcode;
    }

    public function getLastAddedMethod()
    {
        $_cartCollection = Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection();
        $_quote = Mage::getSingleton('checkout/session')->getQuote();
        $_address = $_quote->getShippingAddress();
        if (!$_cartCollection->getSize()) {
            $_address->setShippingMethod('');
            $_address->setCollectShippingRates(true)->collectShippingRates();
            $_quote->setShippingMethod('')->save();
            return false;
        }
        $_shippingMethods = ($_address->getShippingMethod() != '') ? $_address->getShippingMethod() : false;
        return $_shippingMethods;
    }

    public static function initAllProductAttributes($_productId)
    {
        if ($_productId instanceof Mage_Catalog_Model_Product) {
            $_productId = $_productId->getId();
        }
        $product = Mage::getModel('catalog/product')->load($_productId);
        return $product;
    }
}