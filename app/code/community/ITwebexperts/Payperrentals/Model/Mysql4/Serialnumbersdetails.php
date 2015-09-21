<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Serialnumbersdetails extends Mage_Core_Model_Mysql4_Abstract{
   	protected function _construct(){
		$this->_init('payperrentals/serialnumbersdetails', 'id');
	}
		
	public function deleteByEntityId($id){
		$condition =   'entity_id='.intval($id);

		$this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
		 
		 return $this;
	}
}
