<?php
class ITwebexperts_Payperrentals_Adminhtml_Sales_ReturnlateController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/sales/payperrentals/returnlate');
    }

    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }

}