<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Queuepopularity_Renderer_Available
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Queuepopularity_Renderer_Available extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return int|string
     */
    public function render(Varien_Object $row)
    {
        $_checked = (int)$row->getCheckedOut();
        $_total = (int)$row->getPayperrentalsQuantity();
        return (!($_total - $_checked)) ? '0' : ($_total - $_checked);
    }
}