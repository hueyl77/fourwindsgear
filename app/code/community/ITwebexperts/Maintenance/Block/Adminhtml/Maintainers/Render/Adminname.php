<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Maintainers_Render_Adminname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function render(Varien_Object $row){
        $adminname = $row->getFirstname() . ' ' . $row->getLastname();
        return $adminname;
    }
}