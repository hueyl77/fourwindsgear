<?php




class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Returndate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
        $column = $this->getColumn()->getIndex();
		$data = $row->getData($column);

		if($data == '0000-00-00 00:00:00' || $data == '1970-01-01 00:00:00' || $data == null){
			if($column == 'send_date'){
                $data = "Not Sent";
            } else {
                $data = "Not Returned";
            }
		}else{
			$data = ITwebexperts_Payperrentals_Helper_Date::formatDbDate($data, false, true);
		}
		return $data;
	}

}
