<?php

class JoomlArt_JmProducts_IndexController extends Mage_Core_Controller_Front_Action {

    public function AjaxLoadMoreAction() {
        if (Mage::app()->getRequest()->isAjax()) {
            $params = Mage::app()->getRequest()->getParams();
            $block = $this->getLayout()->createBlock('joomlart_jmproducts/list', 'jmproducts.index.ajaxloadmore' . microtime());
            if ($block) {
                //Take params from request
                foreach ($params as $key => $value) {
                    if ($key != 'template') {
                        $block->setData($key, $value);
                    }
                }

                echo $block->setTemplate('joomlart/jmproducts/list_ajax_more.phtml')->toHtml();
            }
        }
        die();
    }

    public function AjaxViewAction() {
        if (Mage::app()->getRequest()->isAjax()) {
            $productId = Mage::app()->getRequest()->getParam('product_id');
            $landingThumbWidth = Mage::app()->getRequest()->getParam('landing_width');
            $landingThumbHeight = Mage::app()->getRequest()->getParam('landing_height');
            $maxDesc = Mage::app()->getRequest()->getParam('max');

            if ($productId) {
                $product = Mage::getModel('catalog/product')->load($productId);
                $block = Mage::app()->getLayout()->createBlock('joomlart_jmproducts/list');
                if ($block) {
                    $block->setData('area', 'frontend');
                    $block->setData('landingItem', $product);
                    $block->setData('landingThumbWidth', $landingThumbWidth);
                    $block->setData('landingThumbHeight', $landingThumbHeight);
                    $block->setData('maxDesc', $maxDesc);

                    echo $block->setTemplate('joomlart/jmproducts/list_ajax_view_landing_item.phtml')->toHtml();
                }
            } else {
                echo Mage::helper('joomlart_jmproducts')->__('Not specified the product ID.');
            }
        }

        die();
    }

}
