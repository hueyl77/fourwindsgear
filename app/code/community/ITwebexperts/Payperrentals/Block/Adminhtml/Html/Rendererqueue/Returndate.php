<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Returndate
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Returndate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row){
		$data = $row->getData($this->getColumn()->getIndex());
		if($data == '0000-00-00 00:00:00'){
			$data = "Not Returned";
		}else{
			if(!empty($data)){
				//$myDate= new Zend_Date($data);
				///$data = $myDate->toString(Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG));
				$data = Mage::helper('core')->formatDate($data, 'medium', false);
				//$data = ITwebexperts_Payperrentals_Helper_Data::formatDbDate($data,false,true);
			}else{
				$data = "Not Returned";
			}
			//$data = ITwebexperts_Payperrentals_Helper_Data::strToDateTime($data);
		}
		return $data;
	}

}
