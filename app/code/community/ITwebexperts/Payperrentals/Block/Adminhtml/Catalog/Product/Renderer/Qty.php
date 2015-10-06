<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Catalog_Product_Renderer_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Price
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $_row)
    {
        if (ITwebexperts_Payperrentals_Helper_Data::isReservationType($_row->getId())) {
            $data = intval(ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                $_row->getId(), 'payperrentals_quantity'));
        }else{
            $data = intval($_row->getQty());
        }


        return $data;
    }
}