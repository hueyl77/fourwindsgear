<?php

class Wavethemes_Jmbasetheme_DemoController extends Mage_Core_Controller_Front_Action
{
	public function applyAction()
	{
		$model = Mage::getSingleton('jmbasetheme/demo');
        $style = $this->getRequest()->getParam('style', null);
        if ($model && $style){
            $website = Mage::app()->getWebsite()->getCode();
            $store   = Mage::app()->getStore()->getCode();
            $model->setDemo($style, $store, $website);
            
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('jmbasetheme')->__('The demo style with named "%s" was applied.', ucfirst($style)));
        }
        
        $this->getResponse()->setRedirect(Mage::getBaseUrl());
	}
}