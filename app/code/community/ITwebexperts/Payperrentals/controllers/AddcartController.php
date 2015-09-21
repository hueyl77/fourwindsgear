<?php
class ITwebexperts_Payperrentals_AddcartController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        echo 'test';
        $id = 4;
        $qty = '1';
        $product = Mage::getModel('catalog/product')->load($id);
        $cart = Mage::getSingleton('checkout/cart');
        $cart->init();

        $params = array(
            'product'   =>  $id,
            'qty'       =>  $qty,
            'start_date'=>  '2014-09-01',
            'end_date'  =>  '2014-09-05',
        );

        $additionalOptions[] = array(
            'label' =>  'test label',
            'value' =>  'test value'
        );

        $request = new Varien_Object();
        $request->setData($params);
        $product->addCustomOption('additional_options', serialize($additionalOptions));
        $cart->addProduct($product,$request);
        $cart->save();
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        $this->_redirect('checkout/cart');
    }
}