<?php
/**
 * Snowcode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Snowcode EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://snowcode.info/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://snowcode.info/ for more information
 *
 * @category   Snowcode
 * @package    Snowcode
 * @date       02.07.13
 * @copyright  Copyright (c) 2013 Snowcode (http://snowcode.info/)
 * @license    http://snowcode.info/LICENSE-1.0.html
 */

class ITwebexperts_Rshipping_Model_Entity_Attribute_Source_Method extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()
    {
        $_resultAr = array();
        $_rshipping = Mage::getModel('rshipping/rshipping')->getCollection();
        foreach ($_rshipping as $_shippingMethod) {
            $_resultAr[] = array(
                'value' => $_shippingMethod->getData('rshipping_id'),
                'label' => $_shippingMethod->getData('shipping_title') . ' - ' . $_shippingMethod->getData('shipping_method')
            );
        }
        return $_resultAr;
    }
}