<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Maintainers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('maintainersGrid');
        $this->setDefaultSort('date_added');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        /** @var $_collection ITwebexperts_Maintenance_Model_Mysql4_Periodic_Collection */
        $_collection = Mage::getModel('simaintenance/maintainers')->getCollection();
        $_collection->getSelect()->joinLeft(array('users'=>Mage::getSingleton('core/resource')->getTableName('admin/user')),'main_table.admin_id=users.user_id');
        $this->setCollection($_collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname',array(
            'header'    =>  Mage::helper('simaintenance')->__('First Name'),
            'index'     =>  'firstname',
            'type'      =>  'text'
        ));

        $this->addColumn('lastname',array(
            'header'    =>  Mage::helper('simaintenance')->__('First Name'),
            'index'     =>  'lastname',
            'type'      =>  'text'
        ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit',array(
            'id'    =>  $row->getMaintainerId()));
    }
}