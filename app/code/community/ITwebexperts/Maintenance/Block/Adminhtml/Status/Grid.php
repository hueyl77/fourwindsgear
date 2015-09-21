<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Status_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('statusGrid');
        $this->setDefaultSort('status');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $_collection = Mage::getModel('simaintenance/status')->getCollection();
        $this->setCollection($_collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('status',array(
            'header'    =>  Mage::helper('simaintenance')->__('Status'),
            'index'     =>  'status',
            'type'      =>  'text'
        ));

        $this->addColumn('status_system',array(
            'header'    =>  Mage::helper('simaintenance')->__('System Status'),
            'index'     =>  'status_system',
            'type'      =>  'text'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit',array(
            'id'    =>  $row->getId()));
    }
}