<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Snippets_Render_Templatename extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function render(Varien_Object $row){
        $snippetname = Mage::getModel('simaintenance/snippets')->load($row->getSnippetId())->getTitle();
        return $snippetname;
    }
}