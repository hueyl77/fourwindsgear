<?php

class JoomlArt_JmProductsSlider_Model_Observer {

    public function cleanCache(Varien_Event_Observer $observer){
       if ($observer->getData('type') == "joomlart_jmproductsslider"){
           try {
            //clean by type
            Mage::app()->getCacheInstance()->cleanType('joomlart_jmproductsslider');
            
           }catch (Exception $e) {
            echo $e->getMessage();die();
          }
        }
        
        return $this;
    }
}
