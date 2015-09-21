<?php
class ITwebexperts_Maintenance_Model_Mysql4_Items_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('simaintenance/items');
        parent::_construct();
    }

    public function joinMaintainers()
    {
        $this->getSelect()->joinLeft(array('maintainers'=>Mage::getSingleton('core/resource')->getTableName('simaintenance/maintainers')),'main_table.maintainer_id=maintainers.maintainer_id');
        $this->getSelect()->joinLeft(array('users'=>Mage::getSingleton('core/resource')->getTableName('admin/user')),'maintainers.admin_id=users.user_id');
        return $this;
    }

    public function joinStatus()
    {
        $this->getSelect()->joinLeft(array('status'=>Mage::getSingleton('core/resource')->getTableName('simaintenance/status')),'main_table.status=status.status_id');
        return $this;
    }
}