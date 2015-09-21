<?php

class Wavethemes_Jmbasetheme_Block_Adminhtml_System_Config_Form_Profile extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$output = '<script type="text/javascript" src="'. $this->getJsUrl('jquery/jquery.1.9.1.min.js') .'"> </script>';
		$output .= '<script type="text/javascript" src="'. $this->getJsUrl('jquery/jquery.noConflict.js') .'"> </script>';
		//jquery alert
		$output .= '<script type="text/javascript" src="'. $this->getJsUrl('joomlart/lib/jquery.alerts.js') .'"> </script>';
		//script on form back end
		$output .= '<script type="text/javascript" src="'. $this->getJsUrl('joomlart/jmbasetheme/jmbasetheme_backend.js') .'"></script>';

		$output .= '<button style="float:left" class="disabled" type="button" id="clone-profile" name="Clone"  >'.Mage::helper('jmbasetheme')->__('Clone this profile').'</button>';
		$output .= '<button style="float:left" class="disabled" type="button" id="restore-profile" name="Restore" >'.Mage::helper('jmbasetheme')->__('Restore this profile').'</button>';
		$output .= '<button style="float:left" class="disabled" type="button" id="delete-profile" name="Delete" >'.Mage::helper('jmbasetheme')->__('Delete this profile').'</button>';

		$output .= '<div id="basetheme-process-bar"></div>';

        $websiteCode = Mage::app()->getRequest()->getParam("website", null);
        $storeCode = Mage::app()->getRequest()->getParam("store", null);
        $profiles = Mage::helper("jmbasetheme")->getProfiles($storeCode, 'backend', $websiteCode);
        if ($profiles) {
            $output .= '<script type="text/javascript"> ';
            $output .= 'var profiles = '.json_encode($profiles).';';
            $output .= ' </script>';
        }

		return $output;
	}
}
