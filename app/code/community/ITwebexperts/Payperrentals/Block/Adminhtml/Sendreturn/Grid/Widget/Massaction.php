<?php
/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Sendreturn_Grid_Widget_Massaction
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sendreturn_Grid_Widget_Massaction
	extends Mage_Adminhtml_Block_Widget_Grid_Massaction
{

    /**
     *
     */
    protected function _construct(){
		//$this->setUseAjax(true);
		parent::_construct();
	}

    /**
     * @return string
     */
    public function getJavaScript()
	{
		return "jQuery(document).ready(function (){ setupMassAction({\n" .
			"\tjsObjectName: '" . $this->getJsObjectName() . "',\n" .
			"\tgridJsObject: " . $this->getGridJsObjectName() . ",\n" .
			"\thtmlId: '" . $this->getHtmlId() . "',\n" .
			"\tselectedJson: '" . $this->getSelectedJson() . "',\n" .
			"\tformFieldNameInternal: '" . $this->getFormFieldNameInternal() . "',\n" .
			"\tformFieldName: '" . $this->getFormFieldName() . "',\n" .
			"\titemsJson: " . $this->getItemsJson() . ",\n" .
			"\tgridIdsJson: '" . $this->getGridIdsJson() . "',\n" .
			"\terrorText: '" . $this->getErrorText() . "',\n" .
			"\tprintUrl: '" . $this->getUrl('*/*/massPrint', array('' => '')) . "'\n" .
			"});});\n";
	}
}
