<?php

class JoomlArt_JmProducts_ViewallController extends Mage_Core_Controller_Front_Action {

    public function IndexAction() {
        $this->loadLayout();
        $params = Mage::app()->getRequest()->getParams();
        $block = $this->getLayout()->getBlock('viewall.jmproducts.list');
        if ($block) {
            //take params from request
            if(isset($params['p'])){
                $params['page'] = $params['p'];
            }
            foreach ($params as $key => $value) {
                $block->setData($key, $value);
            }
        }

        $this->renderLayout();
    }
}
