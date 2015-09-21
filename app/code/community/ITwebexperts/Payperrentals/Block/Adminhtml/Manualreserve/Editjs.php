<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Manualreserve_Editjs extends Mage_Adminhtml_Block_Abstract
{
    public function getQtyUrl(){
        if(Mage::helper('itwebcommon')->isVendorAdmin()){
            return Mage::getUrl("vendors/ajax/getqtyavailable/",array('form_key'=> Mage::getSingleton('core/session')->getFormKey()));
        } else {
            return Mage::getUrl("payperrentals_admin/adminhtml_ajax/getqtyavailable/",array('form_key'=> Mage::getSingleton('core/session')->getFormKey()));
        }
    }
}