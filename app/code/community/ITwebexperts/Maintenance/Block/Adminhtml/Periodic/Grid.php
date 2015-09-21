<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Periodic_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        /** @var $_collection ITwebexperts_Maintenance_Model_Mysql4_Periodic_Collection */
        $_collection = Mage::getModel('simaintenance/periodic')->getCollection();
        $_collection->getSelect()->joinLeft(array('snippets'=>Mage::getSingleton('core/resource')->getTableName('simaintenance/snippets')),'main_table.snippet_id=snippets.snippet_id',array());
        $this->setCollection($_collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id',array(
            'header'    =>  Mage::helper('simaintenance')->__('Product'),
            'index'     =>  'product_id',
            'type'      =>  'text',
            'renderer'  =>  'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Productname'
        ));

        $this->addColumn('frequency_type',array(
            'header'    =>  Mage::helper('simaintenance')->__('Maintenance Period'),
            'index'     =>  'frequency_type',
            'type'      =>  'text',
            'renderer'  =>  'ITwebexperts_Maintenance_Block_Adminhtml_Periodic_Render_Frequency'
        ));

        $this->addColumn('title',array(
            'header'    =>  Mage::helper('simaintenance')->__('Maintenance Template'),
            'index'     =>  'title',
            'type'      =>  'text',
            'renderer'  =>  'ITwebexperts_Maintenance_Block_Adminhtml_Snippets_Render_Templatename'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit',array(
            'id'    =>  $row->getId()));
    }
}