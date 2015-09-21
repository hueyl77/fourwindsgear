<?php

class Wavethemes_Jmbasetheme_Block_Adminhtml_Button_Export_Cms extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $el)
    {
		$data = $el->getOriginalData();
		if (isset($data['task']))
			$task = $data['task'];
		else
			return '<div>Action was not specified</div>';
		
		$buttonSuffix = '';
		if (isset($data['label']))
			$buttonSuffix = ' ' . $data['label'];

		$url = $this->getUrl('jmbasetheme/adminhtml_demo/' . $task);

		$html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData('id', $data['id'])
			->setType('button')
			->setClass('export-cms')
			->setLabel('Export' . $buttonSuffix)
			->setOnClick("setLocation('$url')")
			->toHtml();
        $html .= '<input type="hidden" id ="'.$data['id'].'-url" value="'.$url.'" />' ;

        return $html;
    }
}
