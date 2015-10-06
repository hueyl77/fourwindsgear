<?php


class ITwebexperts_Payperrentals_Model_Mysql4_Fixedrentaldates extends Mage_Core_Model_Mysql4_Abstract{

    protected function _construct(){
        $this->_init('payperrentals/fixedrentaldates', 'id');
    }

    public function deleteById($id){
        $condition =   'nameid='.intval($id);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);

        return $this;
    }
}
