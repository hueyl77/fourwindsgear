<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Sendreturn extends Mage_Core_Model_Mysql4_Abstract{
   	protected function _construct(){
		$this->_init('payperrentals/sendreturn', 'id');
	}

	public function updateReturndateById($id, $retdate) {
		$this->_getWriteAdapter()->update($this->getMainTable(), array('return_date' => $retdate),'id='.$id);
		return $this;
	}

	public function deleteByCustomerId($id, $storeId = null){
		$condition =   'customer_id='.intval($id);
		if(!is_null($storeId)){
			$condition .= ' AND store_id='.intval($storeId);
		}
		$this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
		return $this;
	}
	public function deleteByOrderId($id){
		//order with sent elements cannot be cancelled
		$condition =   'order_id='.intval($id);

		$this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
		 
		 return $this;
	}
}
