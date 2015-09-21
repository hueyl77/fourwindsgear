<?php

class JoomlArt_JmProducts_Model_Observer {

    public function cleanCache(Varien_Event_Observer $observer){
       if ($observer->getData('type') == "joomlart_jmproducts"){
           try {
            //clean by type
            Mage::app()->getCacheInstance()->cleanType('joomlart_jmproducts');
            
           }catch (Exception $e) {
            echo $e->getMessage();die();
          }
        }
        
        return $this;
    }
}
