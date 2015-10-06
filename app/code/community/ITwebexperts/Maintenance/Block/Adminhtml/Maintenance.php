<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Maintenance extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_maintenance';
        $this->_blockGroup = 'itwebexperts_maintenance';
        $this->_headerText = Mage::helper('simaintenance')->__('Maintenance Records');
        parent::__construct();
    }


}