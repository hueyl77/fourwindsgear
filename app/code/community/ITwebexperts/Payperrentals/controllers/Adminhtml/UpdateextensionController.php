<?php
/**
 * Class ITwebexperts_Payperrentals_Adminhtml_UpdateextensionController
 */
class ITwebexperts_Payperrentals_Adminhtml_UpdateextensionController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return true;
    }

    public $_publicActions = array('index');
    /**
     *
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }
}