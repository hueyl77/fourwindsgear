<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Maintenance_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_countTotals = true;

    public function __construct()
    {
        parent::__construct();
        $this->setId('maintenanceGrid');
        $this->setDefaultSort('date_added');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        /** @var $_collection ITwebexperts_Maintenance_Model_Mysql4_Items_Collection */
        $_collection = Mage::getModel('simaintenance/items')->getCollection()->joinMaintainers()->joinStatus();
        $this->setCollection($_collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('date_added',array(
            'header'    =>  Mage::helper('simaintenance')->__('Date Added'),
            'index'     =>  'date_added',
            'type'      =>  'date',
            'totals_label' => $this->__('Total'),
        ));

        $this->addColumn('product_id',array(
            'header'    =>  Mage::helper('simaintenance')->__('Product'),
            'index'     =>  'product_id',
            'type'      =>  'text',
            'renderer'  =>  'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Productname'
        ));

        $this->addColumn('quantity',array(
            'header'    =>  Mage::helper('simaintenance')->__('Quantity'),
            'index'     =>  'quantity',
            'type'      =>  'number'
        ));

        $this->addColumn('cost',array(
            'header'    =>  Mage::helper('simaintenance')->__('Cost'),
            'index'     =>  'cost',
            'type'      =>  'number'
        ));

        $this->addColumn('summary',array(
            'header'    =>  Mage::helper('simaintenance')->__('Summary'),
            'index'     =>  'summary',
            'type'      =>  'text'
        ));

        $this->addColumn('maintainer',array(
            'header'    =>  Mage::helper('simaintenance')->__('Maintainer'),
            'index'     =>  'maintainer_id',
            'type'      =>  'text',
            'renderer'  =>  'ITwebexperts_Maintenance_Block_Adminhtml_Maintainers_Render_Adminname'
        ));

        $this->addColumn('status',array(
            'header'    =>  Mage::helper('simaintenance')->__('Status'),
            'index'     =>  'status',
            'type'      =>  'text'
        ));

        $this->addColumn('added_from',array(
            'header'    =>  Mage::helper('simaintenance')->__('Added Type'),
            'index'     =>  'added_from',
            'type'      =>  'text'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit',array(
            'id'    =>  $row->getId()));
    }

    protected function _prepareMassaction(){
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('item');
        $statuses = Mage::getModel('simaintenance/status')->getStatusArray(true);
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status',array(
            'label' =>  Mage::helper('simaintenance')->__('Change Status'),
            'totals_label' => '',
            'url'   =>  $this->getUrl('*/*/massStatus',array('_current'=>true)),
            'additional'    =>  array(
                'visibility'    =>  array(
                     'name'          =>  'status',
                     'type'          =>  'select',
                     'class'         =>  'required-entry',
                     'label'         =>  Mage::helper('simaintenance')->__('Status'),
                    'totals_label' => '',
                     'values'        =>  $statuses
            )
            )
        ));

        $this->getMassactionBlock()->addItem('delete',array(
            'label' =>  Mage::helper('simaintenance')->__('Delete'),
            'url'   =>  $this->getUrl('*/*/massDelete',array(''=>'')),
            'confirm'   =>  Mage::helper('simaintenance')->__('Are you sure?')
        ));
        return $this;
    }

    public function getTotals(){
        $totals = new Varien_Object();
        $fields = array(
            'quantity'  =>  0,
            'cost'      =>  0
        );
        foreach($this->getCollection() as $item){
            foreach($fields as $field=>$value){
                $fields[$field] += $item->getData($field);
            }
        }
        $totals->setData($fields);
        return $totals;
    }
}