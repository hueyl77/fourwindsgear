<?php

class JoomlArt_JmSlideshow_Block_Adminhtml_System_Config_Form_ListImages extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$output = '<script type="text/javascript" src="'. $this->getJsUrl('jquery/jquery.1.9.1.min.js') .'"> </script>';
		$output .= '<script type="text/javascript" src="'. $this->getJsUrl('jquery/jquery.noConflict.js') .'"> </script>';

		//script on form back end
		$output .= '<script type="text/javascript" src="'. $this->getJsUrl('joomlart/jmslideshow/backend.js') .'"></script>';

        $output .= '<input type="hidden" id="latest_image_folder" name="latest_image_folder" value="" />';
        $output .= '<div id="jm-slide-images" style="display: none;"></div>';

		return $output;
	}
}
