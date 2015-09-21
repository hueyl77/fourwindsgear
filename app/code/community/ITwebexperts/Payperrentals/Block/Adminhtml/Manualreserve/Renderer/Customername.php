<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Manualreserve_Renderer_Customername extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){
        $customername = $row->getCustomerFirstname() . ' ' . $row->getCustomerLastname();
        return $customername;
    }
}