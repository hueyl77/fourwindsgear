<?php
class ITwebexperts_Payperrentals_Adminhtml_Sales_ReturnController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/sales/payperrentals/returnhistory');
    }

    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }

}