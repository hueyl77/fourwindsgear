<?php

class Wavethemes_Jmbasetheme_Block_Adminhtml_Button_Import_Cms extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $el)
    {
		$data = $el->getOriginalData();
        if (isset($data['task'])){
            $task = $data['task'];
            $model = Mage::getSingleton('jmbasetheme/demo');
            $notSupportMsg = '<div style="color:#f00;">'.Mage::helper('jmbasetheme')->__('Current theme was not support this feature.').'</div>';
            if ($task == 'importBlocks'){
                if (!$model->isSupported('blocks')){
                    return $notSupportMsg;
                }
            }else if ($task == 'importPages'){
                if (!$model->isSupported('pages')){
                    return $notSupportMsg;
                }
            }else if ($task == 'importMenu'){
                if (!$model->isSupported('menus')){
                    return $notSupportMsg;
                }
            }
        }
		else
			return '<div>Action was not specified</div>';
		
		$buttonSuffix = '';
		if (isset($data['label']))
			$buttonSuffix = ' ' . $data['label'];

		$url = $this->getUrl('jmbasetheme/adminhtml_demo/' . $task);

		$html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData('id', $data['id'])
			->setType('button')
			->setClass('import-cms')
			->setLabel('Import' . $buttonSuffix)
			->setOnClick("setLocation('$url')")
			->toHtml();
        $html .= '<input type="hidden" id ="'.$data['id'].'-url" value="'.$url.'" />' ;

        return $html;
    }
}
