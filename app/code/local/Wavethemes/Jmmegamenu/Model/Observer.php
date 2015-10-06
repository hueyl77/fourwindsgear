<?php

class Wavethemes_Jmmegamenu_Model_Observer {

    public function prepareStoreForm($block) {}

    public function storeEdit($store) {}

    public function cleanCache(Varien_Event_Observer $observer){
       if ($observer->getData('type') == "wavethemes_jmmegamenu"){
           try {
            //clean by type
            Mage::app()->getCacheInstance()->cleanType('wavethemes_jmmegamenu');
            //clean by tags
            //Mage::app()->cleanCache(array(Wavethemes_Jmmegamenu_Block_Jmmegamenu::CACHE_TAG));
           }catch (Exception $e) {
            echo $e->getMessage();die();
          }
        }
        
        return $this;
    }
}
