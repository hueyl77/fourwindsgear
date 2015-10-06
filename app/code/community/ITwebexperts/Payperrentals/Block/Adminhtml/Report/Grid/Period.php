<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Report_Grid_Period extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return mixed
     */
    public function _getValue(Varien_Object $row)
    {

        // If no format and it column not filtered specified return data as is.
        $_data = (int)parent::_getValue($row);
        $_string = is_null($_data) ? 0 : (int)$_data;

        $_w = floor($_string/10080);
        $_d = floor(($_string - $_w * 10080)/1440);
        $_h = floor(($_string - $_w * 10080 - $_d * 1440)/60);
        $_m = $_string - $_w * 10080 - $_d * 1440 - $_h * 60;
        $_gridString = '';
        if($_w) $_gridString .= $_w . ' weeks ';
        if($_d) $_gridString .= $_d . ' days ';
        if($_h) $_gridString .= $_h . ' hours ';
        if($_m) $_gridString .= $_m . ' minutes';

        return $this->escapeHtml($_gridString);
    }
}