<?php

class Wavethemes_Jmquickview_Block_Cartreturn extends Mage_Core_Block_Template{

    public function __construct($attributes = array()) {
        parent::__construct ();
    }

    public function _toHtml(){
	    if(Mage::registry('cartreturn')){

            if(!$this->getTemplate()){
                $this->setTemplate("joomlart/jmquickview/cartreturn.phtml");
            }

		    $this->assign('cartreturn', Mage::registry('cartreturn'));

		    return parent::_toHtml();
		}
	}

}