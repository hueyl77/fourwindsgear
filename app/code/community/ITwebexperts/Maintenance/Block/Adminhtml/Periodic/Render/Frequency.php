<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Periodic_Render_Frequency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function render(Varien_Object $row){
       $frequency = $row->getFrequencyQuantity() . " " . Mage::getModel('simaintenance/periodic')->getTypeById($row->getFrequencyType());
        return $frequency;
    }
}