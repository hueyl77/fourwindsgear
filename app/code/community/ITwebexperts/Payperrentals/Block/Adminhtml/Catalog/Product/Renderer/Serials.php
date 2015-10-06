<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Catalog_Product_Renderer_Serials extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Price
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $_row)
{

    $serialsColl = Mage::getModel('payperrentals/serialnumbers')->getCollection()->addEntityIdFilter($_row->getId());
    $data = '';
    if($serialsColl->count()){

     foreach($serialsColl as $serial){
         $data .= '<a href="' . Mage::helper('adminhtml')->getUrl('payperrentals_admin/adminhtml_serialreport',array("serialName"=>$serial->getSn())) . '">' . $serial->getSn() . '</a><br />';
     }
    }

    return $data;
}
}