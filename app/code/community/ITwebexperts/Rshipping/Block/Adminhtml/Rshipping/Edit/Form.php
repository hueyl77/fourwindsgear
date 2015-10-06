<?php

/**
 * @category   ITwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */
class ITwebexperts_Rshipping_Block_Adminhtml_Rshipping_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}