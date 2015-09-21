<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Printlabels
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Printlabels extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row){
		$data = $row->getData($this->getColumn()->getIndex());

		return '<input type="checkbox" id="sendreturn_'.$data.'" name="labelids[]" value="'.$data.'" />';

	}

}
