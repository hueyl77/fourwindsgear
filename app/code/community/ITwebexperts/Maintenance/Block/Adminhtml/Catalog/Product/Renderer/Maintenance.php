<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Catalog_Product_Renderer_Maintenance extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function render(Varien_Object $_row){
        return Mage::helper('simaintenance')->getReportMaintenanceQuantity($_row->getId(),true,true);
    }
}
