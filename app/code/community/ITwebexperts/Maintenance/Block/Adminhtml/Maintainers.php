<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Maintainers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_maintainers';
        $this->_blockGroup = 'itwebexperts_maintenance';
        $this->_headerText = Mage::helper('simaintenance')->__('Maintenance Technicians');
        parent::__construct();
    }
}