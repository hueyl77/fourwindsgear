<?php

/**
 * @category   Itwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */

class ITwebexperts_Rshipping_Block_Adminhtml_Rshipping_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('rshippingGrid');
        $this->setDefaultSort('rshipping_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rshipping/rshipping')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rshipping_id', array(
            'header' => Mage::helper('rshipping')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'rshipping_id',
        ));

        $this->addColumn('shipping_title', array(
            'header' => Mage::helper('rshipping')->__('Shipping Title'),
            'align' => 'left',
            'index' => 'shipping_title',
        ));

        $this->addColumn('shipping_method', array(
            'header' => Mage::helper('rshipping')->__('Shipping Method'),
            'align' => 'left',
            'index' => 'shipping_method',
        ));


        $this->addColumn('status', array(
            'header' => Mage::helper('rshipping')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('rshipping')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('rshipping')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('rshipping_id');
        $this->getMassactionBlock()->setFormFieldName('rshipping');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('rshipping')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('rshipping')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('rshipping/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('rshipping')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('rshipping')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}