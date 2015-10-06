<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Mailinglog_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{

	
	public function _construct(){
		parent::_construct();
		$this->_init('payperrentals/mailinglog');

	}

	public function load($printQuery = false, $logQuery = false){
		//$this->_beforeLoad();
		return  parent::load($printQuery, $logQuery);
    }	
    


}
