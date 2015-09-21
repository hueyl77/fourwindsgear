<?php
class ITwebexperts_Maintenance_Model_Snippets extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('simaintenance/snippets');
        parent::_construct();
    }

    /**
     * get templates array for edit maintenance form
     *
     * @return array
     */

    public function getTemplates(){
        $maintenanceTemplates = Mage::getModel('simaintenance/snippets')->getCollection();
        $templateVal = array('-1'=>Mage::helper('simaintenance')->__('Please Select...'));
        foreach($maintenanceTemplates as $template){
            $templateVal[$template->getId()] = $template->getTitle();
        }
        return $templateVal;
    }

}