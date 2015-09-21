<?php

class ITwebexperts_Rshipping_Test_Controller_ProductController extends EcomDev_PHPUnit_Test_Case_Controller
{
    public function _assertResponseBodyContains($string)
    {
        $body = $this->getResponse()->getOutputBody();
        //$constraint = $this->stringContains($string);
        if(strpos($body, $string) !== false){
            $this->assertEquals(1, 1);
        }else{
            $this->assertEquals(0, 1);
        }

    }

    public function _assertResponseBodyNotContains($string)
    {
        $body = $this->getResponse()->getOutputBody();
        if(strpos($body, $string) !== false){
            $this->assertEquals(0, 1);
        }else{
            $this->assertEquals(1, 1);
        }
    }
    /**
     * Register mock object for helper payperrentals/config
     */
    protected function registerConfigHelperMockObject($_configArgs = array())
    {
        if (count($_configArgs)) {
            $_config = $this->getHelperMock('rshipping/config', array_keys($_configArgs));
            foreach ($_configArgs as $_methodName => $_returnVal) {
                $_config->expects($this->any())
                    ->method($_methodName)
                    ->will($this->returnValue($_returnVal));
            }
            $this->replaceByMock('helper', 'rshipping/config', $_config);
        }
    }

    /**
     * Register mock object for model catalog/product
     */
    protected function registerProductModelMockObject($_isSalable = true)
    {
        $_product = $this->getModelMock('catalog/product', array('isSalable'));
        $_product->expects($this->any())
            ->method('isSalable')
            ->will($this->returnValue($_isSalable));
        $this->replaceByMock('model', 'catalog/product', $_product);
    }

    /**
     * productShippingBlock for viewAction test
     *
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
//    public function testProductShippingBlock($_testId, $_getParams, $_isShippingEnabled, $_isSalable)
//    {
//        $this->getRequest()->setMethod('GET');
//        $this->getRequest()->setQuery($_getParams);
//        $_expected = $this->expected('testId' . $_testId);

        /** Register configuration*/
//        $_configArgs = array(
//            'isEnabled' => $_isShippingEnabled,
//        );
//        $this->registerConfigHelperMockObject($_configArgs);
//        $this->registerProductModelMockObject($_isSalable);

//        $this->dispatch('catalog/product/view');

        /** Show Day Details on Product Page Test*/
//        if ($_expected->getIsNotFound()) {
//            $this->assertResponseHeaderContains('Status', '404 File not found');
//        } else {
//            if ($_expected->hasIsContain()) {
//                $this->_assertResponseBodyContains($_expected->getSearchKey());
//            } else {
//                $this->_assertResponseBodyNotContains($_expected->getSearchKey());
//            }
//        }
//    }
}