<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_System_Renderer_StoreTime
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_System_Renderer_StoreTime extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $_element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $_element)
    {
        $_html = parent::render($_element);
        $_html .= '<tr style="display: none;"><td>';
        $_script = "<script type=\"text/javascript\">new MaskedInput('#" . $_element->getHtmlId() . "', '99:99')</script>";
        $_html .= $_script;
        $_html .= '</td></tr>';

        return $_html;
    }
}