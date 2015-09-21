<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Serialnumbers extends Mage_Core_Model_Mysql4_Abstract{
   	protected function _construct(){
		$this->_init('payperrentals/serialnumbers', 'id');
	}

	public function updateStatusBySerial($id, $sr) {
		$this->_getWriteAdapter()->update($this->getMainTable(), array('status' => $sr),'sn="'.$id.'"');
		return $this;
	}

	public function deleteByEntityId($id){
		$condition =   'entity_id='.intval($id);

		$this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
		 
		 return $this;
	}
}
