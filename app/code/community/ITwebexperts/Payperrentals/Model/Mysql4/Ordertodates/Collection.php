<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Ordertodates_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	
	public function _construct(){
		parent::_construct();
		$this->_init('payperrentals/ordertodates');
	}

	public function addSelectFilter($select){
		$this->getSelect()->where($select);
		return $this;
	}

	public function addOrderIdFilter($id){
		$this->getSelect()
				->where('main_table.orders_id=?', $id);
		return $this;
	}
}
