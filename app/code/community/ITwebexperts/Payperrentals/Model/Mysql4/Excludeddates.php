<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Excludeddates extends Mage_Core_Model_Mysql4_Abstract{
   	protected function _construct(){
		$this->_init('payperrentals/excludeddates', 'id');
	}
		
	public function deleteByProductId($id, $storeId = null){
		$condition =   'product_id='.intval($id);
		if(!is_null($storeId)){
			$condition .= ' AND store_id='.intval($storeId);
		}
		$this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
		 
		 return $this;
	}
}
