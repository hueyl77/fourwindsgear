<?php

/**
 * Class ITwebexperts_Payperrentals_Adminhtml_QueuepopularityController
 */
class ITwebexperts_Payperrentals_Adminhtml_QueuepopularityController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/rentalqueue/queue_popularity');
    }


    /**
     *
     */
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
}