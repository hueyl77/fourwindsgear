<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Catalog_Product_Edit_Tab_Payperrentals_Serialform extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {

        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('payperrentals')->__('Add Serial'),
                    'onclick' => 'addNewSerialNumber(this)',
                    'class' => 'add'
                )));

        $this->setChild('generate_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('payperrentals')->__('Generate'),
                    'class' => 'generateBut'
                ))
        );

        return parent::_prepareLayout();
    }
}