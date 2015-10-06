<?php


class ITwebexperts_Payperrentals_Block_Adminhtml_Reservationpricesdates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('pricesGrid');
        $this->setDefaultSort('description');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return this
     */
    protected function _prepareCollection()
    {
        $_collection = Mage::getModel('payperrentals/reservationpricesdates')->getCollection();
        $this->setCollection($_collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('description', array(
            'header' => Mage::helper('payperrentals')->__('Description'),
            'index' => 'description',
            'type'  =>  'text'
        ));

        $this->addColumn('disabled_type', array(
            'header' => Mage::helper('payperrentals')->__('Repeat Type'),
            'index' => 'disabled_type',
        ));

        $this->addColumn('date_from', array(
            'header' => Mage::helper('payperrentals')->__('Start Date'),
            'renderer'  => 'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime',
            'index' => 'date_from',
        ));
        $this->addColumn('date_to', array(
            'header' => Mage::helper('payperrentals')->__('End Date'),
            'renderer'  => 'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime',
            'index' => 'date_to',
        ));

        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array
        ('id'   =>  $row->getId()));
    }
}