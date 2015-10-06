<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Snippets_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('snippetGrid');
        $this->setDefaultSort('title');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $_collection = Mage::getModel('simaintenance/snippets')->getCollection();
        $this->setCollection($_collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('title',array(
            'header'    =>  Mage::helper('simaintenance')->__('Title'),
            'index'     =>  'title',
            'type'      =>  'text',
            'renderer'  =>  ''
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit',array(
            'id'    =>  $row->getId()));
    }
}