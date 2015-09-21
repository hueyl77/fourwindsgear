<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Bundle option checkbox type renderer
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
if(class_exists('GetSomeMojo_Bundledmojo_Block_Catalog_Product_View_Type_Bundle_Option_Checkbox')){
    class ITwebexperts_Payperrentals_Block_Bundle_Catalog_Product_View_Type_Bundle_Option_Checkbox
        extends ITwebexperts_Payperrentals_Block_Bundle_Catalog_Product_View_Type_Bundle_Option
    {
        /**
         * Product Type
         */
        const PROD_TYPE = GetSomeMojo_Bundledmojo_Helper_Data::XML_MOJO_TYPE_CHECKBOX;

        /**
         * Set template
         *
         * @return void
         */
        public function __construct()
        {

            parent::__construct();

            $this->setCurrentStore(Mage::app()->getStore())
                ->setMojoHelper($this->helper('bundledmojo'))
                ->setYeaH4yeaH($this->getMojoHelper()->getSystemConfigValue(GetSomeMojo_Bundledmojo_Helper_Data::XML_MOJO_GENERAL, GetSomeMojo_Bundledmojo_Helper_Data::XML_MOJO_GENERAL_ACTIVATE, $this->getCurrentStore()))
                ->setJquery($this->getMojoHelper()->getSystemConfigValue(GetSomeMojo_Bundledmojo_Helper_Data::XML_MOJO_GENERAL, GetSomeMojo_Bundledmojo_Helper_Data::XML_MOJO_GENERAL_JQUERY, $this->getCurrentStore()))
                ->setYeaH4checkbox($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_MOJO_TYPE_ENABLE, $this->getCurrentStore()));

            if ($this->getYeaH4yeaH() && $this->getYeaH4checkbox()) {
                $this->setTemplate('payperrentals/catalog/product/view/type/bundle/option/mojo' . self::PROD_TYPE . '.phtml');
            } else {
                $this->setTemplate('payperrentals/catalog/product/view/type/bundle/option/checkbox.phtml');
            }

        }

        public function _beforeToHtml()
        {

            parent::_beforeToHtml();

            if (!$this->getMojo() && $this->getYeaH4yeaH()) {

                $mojoDev = str_replace('.', '', $_SERVER['SERVER_NAME']);
                if (!is_numeric($mojoDev)) {
                    $this->setYeaH4yeaH(0);
                    $this->initiateMojo();
                } else {
                    $this->initiateMojoDev();
                }
            }

            if ($this->getYeaH4yeaH() && $this->getYeaH4checkbox()) {
                /* User-Defined Qty */
                $this->setEnableQty($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_ENABLEQTY, $this->getCurrentStore()));
                /* Product Name & Price */
                $this->setProdNameUrl($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_PRODNAMEURL, $this->getCurrentStore()))
                    ->setProdPrice($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_PRODPRICE, $this->getCurrentStore()));
                /* Product Short Description */
                $this->setShortDesc($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_SHORTDESC, $this->getCurrentStore()))
                    ->setShortDescInit($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_SHORTDESCINIT, $this->getCurrentStore()))
                    ->setShortDescText($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_SHORTDESCTEXT, $this->getCurrentStore()))
                    ->setShortDescTextDefault(GetSomeMojo_Bundledmojo_Helper_Data::XML_SHORTDESCTEXT_DEFAULT);
                /* Product Image */
                $this->setBaseImage($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_BASEIMAGE, $this->getCurrentStore()))
                    ->setThumbSize($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_THUMBSIZE, $this->getCurrentStore()));
                $_imageFloat = ($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_IMAGEFLOAT, $this->getCurrentStore()) ? 'right' : 'left');
                $this->setImageFloat($_imageFloat);
                if (!$this->getBaseImage()) $this->setImageFloat(GetSomeMojo_Bundledmojo_Helper_Data::XML_IMAGEFLOAT_DEF);
                /* Product Image Zoom */
                $this->setImageZoom($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_IMAGEZOOM, $this->getCurrentStore()))
                    ->setZoomWidth($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_ZOOMWIDTH, $this->getCurrentStore()));
                if (!$this->getZoomWidth()) $this->setZoomWidth(GetSomeMojo_Bundledmojo_Helper_Data::XML_IMAGEZOOM_DEFWIDTH);
                $this->setZoomImagePadding($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_ZOOMIMAGEPADDING, $this->getCurrentStore()))
                    ->setZoomBorderSize($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_ZOOMBORDERSIZE, $this->getCurrentStore()))
                    ->setZoomBorderColor($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_ZOOMBORDERCOLOR, $this->getCurrentStore()))
                    ->setZoomBgColor($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_ZOOMBGCOLOR, $this->getCurrentStore()))
                    ->setZoomCaption($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_ZOOMCAPTION, $this->getCurrentStore()))
                    ->setZoomCaptionSize($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_ZOOMCAPTIONSIZE, $this->getCurrentStore()))
                    ->setZoomCaptionPadding($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_ZOOMCAPTIONPADDING, $this->getCurrentStore()))
                    ->setZoomFontColor($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_ZOOMFONTCOLOR, $this->getCurrentStore()));
                /* Wishlist & Add to Compare Links */
                $this->setWishlist($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_WISHLIST, $this->getCurrentStore()))
                    ->setCompare($this->getMojoHelper()->getSystemConfigValue(self::PROD_TYPE, GetSomeMojo_Bundledmojo_Helper_Data::XML_COMPARE, $this->getCurrentStore()));
            }

        }

        /**
         * Returns the selection qty for a checkbox selection
         *
         * @param Mage_Bundle_Model_Selection $selection
         * @return number
         */
        protected function _getSelectionQty($selection)
        {

            if ($this->getProduct()->hasPreconfiguredValues()) {

                $selectedQty = $this->getProduct()->getPreconfiguredValues()
                    ->getData('bundle_option_qty/' . $this->getOption()->getId());

                if (is_array($selectedQty)) {
                    if (isset($selectedQty[$selection->getSelectionId()])) {
                        $selectedQty = $selectedQty[$selection->getSelectionId()];
                    } else {
                        $selectedQty = 0;
                    }
                }

                $selectedQty = (float)$selectedQty;

                if ($selectedQty < 0) {
                    $selectedQty = 0;
                }

            } else {
                $selectedQty = 0;
            }

            return $selectedQty;

        }

        /**
         * Gets the default values for a checkbox selection
         *
         * @param Mage_Bundle_Model_Selection $selection
         */
        protected function _getSelectionDefaultValues($selection)
        {

            $selectedOptions = $this->_getSelectedOptions();
            $_canChangeQty = (bool)$selection->getSelectionCanChangeQty();

            if ((empty($selectedOptions) && $selection->getIsDefault()) || !$_canChangeQty) {
                $_defaultQty = $selection->getSelectionQty()*1;
            } else {
                $_defaultQty = $this->_getSelectionQty($selection)*1;
            }

            return array($_defaultQty, $_canChangeQty);

        }

        function getMojo()
        {

            $this->setTempMojo($this->getMojoHelper()->getSystemConfigValue(GetSomeMojo_Bundledmojo_Helper_Data::XML_MOJO_GENERAL, base64_decode('c2VyaWFs'), $this->getCurrentStore()));
            return $this->getSome($this->getTempMojo());

        }

        function haveMojo()
        {

            $_haveMojo = $_SERVER['SERVER_NAME'];
            $_temp = explode('.', $_haveMojo);
            $_exceptions = array(
                'co.uk',
                'com.au'
            );

            $_count = count($_temp);
            $_last = $_temp[($_count-2)] . '.' . $_temp[($_count-1)];
            if (in_array($_last, $_exceptions))    {
                $_newDomain = $_temp[($_count-3)] . '.' . $_temp[($_count-2)] . '.' . $_temp[($_count-1)];
            } else {
                $_newDomain = $_temp[($_count-2)] . '.' . $_temp[($_count-1)];
            }

            return $_newDomain;

        }

        function checkMojo($domain, $mojo)
        {

            $_tempKey = sha1(base64_decode('Z2V0c29tZWJ1bmRsZWRtb2pv'));

            if (sha1($_tempKey . $domain) == $mojo)   {
                return true;
            }

            return false;

        }

        function getSome($mojo)
        {

            $_original = $this->checkMojo($_SERVER['SERVER_NAME'], $mojo);
            $_wildcard = $this->checkMojo($this->haveMojo(), $mojo);

            if (!$_original && !$_wildcard) {
                return false;
            }

            return true;

        }

        function initiateMojo()
        {

            $_mojoVibe =  "<div style=\"clear:both;\">&nbsp;</div>\n";
            $_mojoVibe .= base64_decode('PGRpdiBzdHlsZT0iYm9yZGVyOiAzcHggc29saWQgcmVkOyBwYWRkaW5nOiA1cHg7IG1hcmdpbi1ib3R0b206IDE1cHg7IG1hcmdpbi10b3A6IDE1cHg7Ij5QbGVhc2UgZW50ZXIgYSB2YWxpZCBzZXJpYWwgZm9yIEJ1bmRsZWQgTW9qbyBpbiB5b3VyIGFkbWluaXN0cmF0aW9uIGNvbnRyb2wgcGFuZWwuIElmIHlvdSBkb24ndCBoYXZlIG9uZSwgcGxlYXNlIHB1cmNoYXNlIGEgdmFsaWQgbGljZW5zZSBmcm9tIDxhIGhyZWY9Imh0dHA6Ly93d3cuZ2V0c29tZW1vam8ubmV0Ij53d3cuR2V0U29tZU1vam8ubmV0PC9hPjwvZGl2Pg==');
            echo $_mojoVibe;

        }

        function initiateMojoDev()
        {

            $_mojoVibe =  "<div style=\"clear:both;\">&nbsp;</div>\n";
            $_mojoVibe .= base64_decode('PGRpdiBzdHlsZT0iYm9yZGVyOiAzcHggc29saWQgcmVkOyBwYWRkaW5nOiA1cHg7IG1hcmdpbi1ib3R0b206IDE1cHg7IG1hcmdpbi10b3A6IDE1cHg7Ij48c3BhbiBzdHlsZT0iZm9udC13ZWlnaHQ6IGJvbGQ7IGNvbG9yOiByZWQ7Ij5XQVJOSU5HOjwvc3Bhbj48cD5JdCBzZWVtcyB5b3VyIHdlYnNpdGUgaXMgaW4gZGV2ZWxvcG1lbnQgbW9kZS4gV2hlbiB5b3UgbW92ZSB5b3VyIHdlYnNpdGUgZnJvbSBkZXZlbG9wbWVudCB0byBwcm9kdWN0aW9uIHRoaXMgd2FybmluZyBtZXNzYWdlIHdpbGwgZGlzYXBwZWFyIGlmIHlvdSBoYXZlIGEgdmFsaWQgc2VyaWFsLjwvcD48L2Rpdj4=');
            echo $_mojoVibe;

        }
    }

}else {
    class ITwebexperts_Payperrentals_Block_Bundle_Catalog_Product_View_Type_Bundle_Option_Checkbox
        extends ITwebexperts_Payperrentals_Block_Bundle_Catalog_Product_View_Type_Bundle_Option
    {
        /**
         * Set template
         *
         * @return void
         */
        protected function _construct()
        {
            $this->setTemplate('payperrentals/catalog/product/view/type/bundle/option/checkbox.phtml');
        }
    }
}