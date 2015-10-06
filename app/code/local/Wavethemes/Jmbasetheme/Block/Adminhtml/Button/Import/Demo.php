<?php

class Wavethemes_Jmbasetheme_Block_Adminhtml_Button_Import_Demo extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $el)
    {
		$data = $el->getOriginalData();
		if (isset($data['task'])){
			$task = $data['task'];
            if ($task == 'setDemo'){
                $model = Mage::getSingleton('jmbasetheme/demo');
                $notSupportMsg = '<div style="color:#f00;">'.Mage::helper('jmbasetheme')->__('Current theme was not support this feature or there are not demo styles defined in current theme.').'</div>';
                if (!$model->hasStyleDefined()){
                    return $notSupportMsg;
                }
            }
        }
		else
			return '<div>Action was not specified</div>';

		$btnLabel = '';
		if (isset($data['label']))
            $btnLabel = $data['label'];

        $url = $this->getUrl('jmbasetheme/adminhtml_demo/' . $task);

        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) // website level
        {
            $url .= "website/".$code;
        }
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) // store level
        {
            $url .= "/store/".$code;
        }

		$html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData('id', $data['id'])
			->setType('button')
			->setClass('import-cms')
			->setLabel($btnLabel)
			->setOnClick("setLocation('$url')")
			->toHtml();
        $html .= '<input type="hidden" id ="'.$data['id'].'-url" value="'.$url.'" />' ;
        
        //Js
        //flag to show/hide export button
        $export   = Mage::app()->getRequest()->getParam('export', 0);
        $html .= '<script type="text/javascript"> ';
        $html .= 'var showBtnExport = '.$export.';';
        $html .= ' </script>';
        
        $html .= '<script type="text/javascript" src="'. $this->getJsUrl('jquery/jquery.1.9.1.min.js') .'"> </script>';
        $html .= '<script type="text/javascript" src="'. $this->getJsUrl('jquery/jquery.noConflict.js') .'"> </script>';
        //script on form in back-end
        $html .= '<script type="text/javascript" src="'. $this->getJsUrl('joomlart/jmbasetheme/demo_backend.js') .'"></script>';

        return $html;
    }
}
