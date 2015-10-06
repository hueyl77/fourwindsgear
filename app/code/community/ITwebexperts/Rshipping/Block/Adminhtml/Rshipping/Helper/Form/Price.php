<?php

class ITwebexperts_Rshipping_Block_Adminhtml_Rshipping_Helper_Form_Price extends Varien_Data_Form_Element_Text
{

    public function __construct($_attributes = array())
    {
        parent::__construct($_attributes);
        $this->addClass('validate-zero-or-greater');
    }

    public function getEscapedValue($_index = null)
    {
        $_value = $this->getValue();

        if (!is_numeric($_value)) {
            return null;
        }

        return number_format($_value, 2, null, '');
    }

}

