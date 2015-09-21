<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Queuepopularity_Grid
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Queuepopularity_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('reportGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return this
     */
    protected function _prepareCollection()
    {
        $_collection = Mage::getModel('catalog/product')->getCollection();
        $_collection->addAttributeToSelect(array('name', 'payperrentals_quantity'));
        /*$_collection->addAttributeToFilter('type_id', array('in' => 'reservation'));*/
        $_collection->addAttributeToFilter('is_reservation', array('in' => array(ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_RENTAL, ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_RENTALANDRESERVATION)));

        $_resource = Mage::getSingleton('core/resource');

        $_collection->getSelect()->columns(new Zend_Db_Expr("(SELECT COUNT(*) FROM " . $_resource->getTableName('payperrentals/rentalqueue') . " WHERE product_id=e.entity_id) as number_in_queue"));
        $_collection->getSelect()->columns(new Zend_Db_Expr("(SELECT SUM(qty) FROM " . $_resource->getTableName('payperrentals/sendreturn') . " WHERE product_id=e.entity_id AND return_date='0000-00-00 00:00:00') as checked_out"));

        $this->setCollection($_collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('payperrentals')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('payperrentals')->__('Product Name'),
            'align' => 'left',
            'width' => '300px',
            'index' => 'name',
        ));
        $this->addColumn('number_in_queue', array(
            'header' => Mage::helper('payperrentals')->__('Number In Customer Queue'),
            'align' => 'left',
            'width' => '50px',
            'type' => 'number',
            'index' => 'number_in_queue',
        ));
        $this->addColumn('payperrentals_quantity', array(
            'header' => Mage::helper('payperrentals')->__('Total Inventory'),
            'align' => 'left',
            'width' => '50px',
            'type' => 'number',
            'index' => 'payperrentals_quantity',
        ));
        $this->addColumn('checked_out', array(
            'header' => Mage::helper('payperrentals')->__('Checked Out Inventory'),
            'align' => 'left',
            'width' => '50px',
            'sortable' => false,
            'filter' => false,
            'index' => 'checked_out',
            'renderer' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Queuepopularity_Renderer_Checkedout'
        ));
        $this->addColumn('available_inventory', array(
            'header' => Mage::helper('payperrentals')->__('Available Inventory'),
            'align' => 'left',
            'width' => '50px',
            'sortable' => false,
            'filter' => false,
            'renderer' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Queuepopularity_Renderer_Available'
        ));


        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }
}