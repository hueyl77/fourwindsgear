<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Serialnumbersdetails_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	
	protected $_storeId = null;
	
	public function _construct(){
		parent::_construct();
		$this->_init('payperrentals/serialnumbersdetails');

	}

	public function addEntityIdFilter($id){
		$this->getSelect()
			->where('entity_id=?', $id);
		return $this;	
	}

	public function addSelectFilter($select){
		$this->getSelect()->where($select);
		return $this;
	}

	public function load($printQuery = false, $logQuery = false){
		//$this->_beforeLoad();
		return  parent::load($printQuery, $logQuery);
    }	
    


}
