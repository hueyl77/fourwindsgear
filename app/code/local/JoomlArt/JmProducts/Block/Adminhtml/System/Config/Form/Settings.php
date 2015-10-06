<?php

class JoomlArt_JmProducts_Block_Adminhtml_System_Config_Form_Settings extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	public function _prepareLayout()
	{
		$headBlock = $this->getLayout()->getBlock('head');
		//jquery
		$headBlock->addItem('js', 'jquery/jquery.1.9.1.min.js');
		$headBlock->addItem('js', 'jquery/jquery.noConflict.js');
		//script on form backend
		$headBlock->addItem('js', 'joomlart/jmproducts/jmproduct_backend.js');

		return parent::_prepareLayout();
	}

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		return '';
	}
}
