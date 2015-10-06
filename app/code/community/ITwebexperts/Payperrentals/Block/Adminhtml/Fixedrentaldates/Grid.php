<?php


class ITwebexperts_Payperrentals_Block_Adminhtml_Fixedrentaldates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('fixedrentalGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return this
     */
    protected function _prepareCollection()
    {
        $_collection = Mage::getModel('payperrentals/fixedrentalnames')->getCollection();
        $this->setCollection($_collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header' => Mage::helper('payperrentals')->__('Name'),
            'index' => 'name',
            'type'  =>  'text'
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