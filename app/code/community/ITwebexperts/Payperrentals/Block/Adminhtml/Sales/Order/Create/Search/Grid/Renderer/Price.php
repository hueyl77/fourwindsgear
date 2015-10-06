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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales create order product search grid price column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_Create_Search_Grid_Renderer_Price extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Price
{
    /**
     * Render price for products
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $_data = $row->getData($this->getColumn()->getIndex());
        $_priceHtml = false;
        if ($_data) {
            $_priceHtml = $_data;
        } elseif (is_null($_data)) {
            $_product = $row->load($row->getId());
            if ($_product) {
                $_priceHtml = ITwebexperts_Payperrentals_Helper_Price::getPriceListHtml($_product, 2);
            }
        }
        if ($_priceHtml !== false) {
            return $_priceHtml;
        } else {
            return $this->getColumn()->getDefault();
        }
    }

}
