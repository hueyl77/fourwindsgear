<?php

/**
 * Temporarily solution. Need remove when all dates will be
 * storing in same format in system
 *
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_DatetimeLocalize
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_DatetimeLocalize extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $data = $row->getData($this->getColumn()->getIndex());
        if ($data != '') {
            $data = ITwebexperts_Payperrentals_Helper_Date::formatDbDate($data, false, false);
        } else {
            /** @var $sOrder Mage_Sales_Model_Order */
            $sOrder = Mage::getModel('sales/order')->load($row->getId());
            $items = $sOrder->getItemsCollection();
            /** @var $item Mage_Sales_Model_Order_Item */
            foreach ($items as $item) {
                /** @var $Product Mage_Catalog_Model_Product */
                $Product = Mage::getModel('catalog/product')->load($item->getProductId());
                if ($Product->getTypeId() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE && $Product->getTypeId() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE) {
                    continue;
                }

                $data1 = $item->getProductOptionByCode('info_buyRequest');
                if ($this->getColumn()->getIndex() == 'start_date') {
                    $data = $data1[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION];
                    break;
                }
                if ($this->getColumn()->getIndex() == 'end_date') {
                    $data = $data1[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION];
                    break;
                }
            }
        }

        return $data;
    }

}