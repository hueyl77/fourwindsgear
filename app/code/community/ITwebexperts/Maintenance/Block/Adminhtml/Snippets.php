<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Snippets extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_snippets';
        $this->_blockGroup = 'itwebexperts_maintenance';
        $this->_headerText = Mage::helper('simaintenance')->__('Maintenance Templates');
        parent::__construct();
    }
}