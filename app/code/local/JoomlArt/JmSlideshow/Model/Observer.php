<?php

class JoomlArt_JmSlideshow_Model_Observer {

    public function cleanCache(Varien_Event_Observer $observer){
       if ($observer->getData('type') == "joomlart_jmslideshow"){
           try {
            //clean by type
            Mage::app()->getCacheInstance()->cleanType('joomlart_jmslideshow');

            }catch (Exception $e) {
            echo $e->getMessage();die();
          }
        }
        
        return $this;
    }
}
