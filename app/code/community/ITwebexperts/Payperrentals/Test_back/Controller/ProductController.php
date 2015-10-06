<?php

class ITwebexperts_Payperrentals_Test_Controller_ProductController extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * Register mock object for helper payperrentals/config
     */
    protected function registerConfigHelperMockObject($_configArgs = array())
    {
        if (count($_configArgs)) {
            $_config = $this->getHelperMock('payperrentals/config', array_keys($_configArgs));
            foreach ($_configArgs as $_methodName => $_returnVal) {
                $_config->expects($this->any())
                    ->method($_methodName)
                    ->will($this->returnValue($_returnVal));
            }
            $this->replaceByMock('helper', 'payperrentals/config', $_config);
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
     * dayDetailGrid for viewAction test
     *
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function testDayDetailGrid($_testId, $_getParams, $_isHotelMode, $_showTimeGrid, $_isSalable)
    {
        $this->getRequest()->setMethod('GET');
        $this->getRequest()->setQuery($_getParams);
        $_expected = $this->expected('testId' . $_testId);

        /** Register configuration*/
        $_configArgs = array(
            'isHotelMode' => $_isHotelMode,
            'isShowTimeGrid' => $_showTimeGrid
        );
        $this->registerConfigHelperMockObject($_configArgs);
        $this->registerProductModelMockObject($_isSalable);

        $this->dispatch('catalog/product/view');

        /** Show Day Details on Product Page Test*/
        if ($_expected->getIsNotFound()) {
            $this->assertResponseHeaderContains('Status', '404 File not found');
        } else {
            if ($_expected->hasIsContain()) {
                $this->assertResponseBodyContains($_expected->getSearchKey());
            } else {
                $this->assertResponseBodyNotContains($_expected->getSearchKey());
            }
        }
    }

    /**
     * test globalExcludeDate for viewAction test
     *
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function testGlobalExcludeDate($_testId, $_getParams)
    {
        $this->getRequest()->setMethod('GET');
        $this->getRequest()->setQuery($_getParams);
        $_expected = $this->expected('testId' . $_testId);

        /** Register configuration*/
        $_configArgs = array(
            'isHotelMode' => false,
            'isShowTimeGrid' => false
        );
        $this->registerConfigHelperMockObject($_configArgs);
        $this->registerProductModelMockObject();

        $this->dispatch('catalog/product/view');

        /** Use Global Config for Exclude Days Test*/
        if ($_expected->getIsNotFound()) {
            $this->assertResponseHeaderContains('Status', '404 File not found');
        } else {
            if ($_expected->hasContain()) {
                $_expectedDates = is_array($_expected->getContain()) ? $_expected->getContain() : array($_expected->getContain());
                foreach ($_expectedDates as $_expectedDate) {
                    $this->assertResponseBodyContains($_expectedDate);
                }
            }
            if ($_expected->hasNotContain()) {
                $_notExpectedDates = is_array($_expected->getNotContain()) ? $_expected->getNotContain() : array($_expected->getNotContain());
                foreach ($_notExpectedDates as $_notExpectedDate) {
                    $this->assertResponseBodyNotContains($_notExpectedDate);
                }
            }
        }
    }

    /**
     * test minMaxRentalPeriod for viewAction test
     *
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function testMinMaxRentalPeriod($_testId, $_getParams)
    {
        $this->getRequest()->setMethod('GET');
        $this->getRequest()->setQuery($_getParams);
        $_expected = $this->expected('testId' . $_testId);

        /** Register configuration*/
        $_configArgs = array(
            'isHotelMode' => false,
            'isShowTimeGrid' => false
        );
        $this->registerConfigHelperMockObject($_configArgs);
        $this->registerProductModelMockObject();

        $this->dispatch('catalog/product/view');

        /** Use Global Config for Exclude Days Test*/
        if ($_expected->getIsNotFound()) {
            $this->assertResponseHeaderContains('Status', '404 File not found');
        } else {
            if ($_expected->hasContain()) {
                $_expectedDates = is_array($_expected->getContain()) ? $_expected->getContain() : array($_expected->getContain());
                foreach ($_expectedDates as $_expectedDate) {
                    $this->assertResponseBodyContains($_expectedDate);
                }
            }
        }
    }

    /**
     * test useTimesOnProductPage for viewAction test
     *
     * @test
     * @loadFixture testDayDetailGrid
     * @dataProvider dataProvider
     */
    public function testUseTimesOnProductPage($_testId, $_getParams, $_isHotelMode, $_isSalable)
    {
        $this->getRequest()->setMethod('GET');
        $this->getRequest()->setQuery($_getParams);
        $_expected = $this->expected('testId' . $_testId);

        /** Register configuration*/
        $_configArgs = array(
            'isHotelMode' => $_isHotelMode,
        );
        $this->registerConfigHelperMockObject($_configArgs);
        $this->registerProductModelMockObject($_isSalable);

        $this->dispatch('catalog/product/view');

        /** Show Day Details on Product Page Test*/
        if ($_expected->getIsNotFound()) {
            $this->assertResponseHeaderContains('Status', '404 File not found');
        } else {
            if ($_expected->hasIsContain() && $_expected->getIsContain()) {
                $this->assertResponseBodyContains($_expected->getSearchKey());
            } else {
                $this->assertResponseBodyNotContains($_expected->getSearchKey());
            }
        }
    }

    /**
     * Execute after each function call
     * */
    public function tearDown()
    {
        Mage::unregister('current_product');
        Mage::unregister('product');
        Mage::unregister('current_category');
    }
}