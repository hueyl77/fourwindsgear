<?php

class ITwebexperts_Payperrentals_Block_Html_Calendar extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payperrentals/calendar/global_calendar.phtml');
    }

    public function isAdmin(){
        return Mage::app()->getStore()->isAdmin();
    }

}