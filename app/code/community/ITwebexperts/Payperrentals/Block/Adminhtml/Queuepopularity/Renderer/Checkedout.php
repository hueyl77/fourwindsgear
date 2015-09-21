<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Queuepopularity_Renderer_Checkedout
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Queuepopularity_Renderer_Checkedout extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return mixed|string
     */
    public function render(Varien_Object $row)
    {
        return (is_null($this->_getValue($row))) ? '0' : $this->_getValue($row);
    }
}