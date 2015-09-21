<?php
class Wavethemes_Jmbasetheme_Model_Config_Demo_Style
{

    public function toOptionArray()
    {
        //list all styles
        $helper = Mage::helper('jmbasetheme');
        $websiteCode = Mage::app()->getRequest()->getParam('website', null);
        $storeCode   = Mage::app()->getRequest()->getParam('store', null);
        $designPackage = $helper->getDesignPackage($storeCode, 'backend', $websiteCode);
        $styleSrc = Mage::getBaseDir("design") . DS . "frontend" . DS . $designPackage . DS . 'demo' . DS . 'styles' .DS;
        $files = $helper->listFiles($styleSrc);

        $options = array();
        if ($files){
            foreach ($files as $fileName){
                $fileName = substr($fileName, 0, strpos($fileName, '.xml'));
                $options[] = array(
                    'value' => $fileName,
                    'label' => Mage::helper('jmbasetheme')->__(ucfirst($fileName))
                );
            }
        }else{
            $options[] = array(
                'value' => '',
                'label' => Mage::helper('jmbasetheme')->__('Demo Styles was not defined yet.')
            );
        }

        return $options;
    }
}