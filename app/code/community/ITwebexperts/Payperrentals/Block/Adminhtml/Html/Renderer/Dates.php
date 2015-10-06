<?php


class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Dates extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $data = $row->getData($this->getColumn()->getIndex());
        $coll = Mage::getModel('payperrentals/ordertodates')
            ->getCollection()
            ->addSelectFilter("orders_id='" . $data . "'");
        $resp = '';
        foreach($coll as $item){
            $resp .= $item->getEventDate().', ';
        }
        return $resp;
    }

}
