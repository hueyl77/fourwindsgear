<?php
class ITwebexperts_Maintenance_Block_Adminhtml_Status extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_status';
        $this->_blockGroup = 'itwebexperts_maintenance';
        $this->_headerText = Mage::helper('simaintenance')->__('Maintenance Status');
        parent::__construct();
    }

}