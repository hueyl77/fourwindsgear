<?php
class ITwebexperts_Payperrentals_Test_Helper_Price extends EcomDev_PHPUnit_Test_Case {
    /**
     * Retrieves list of order ids for some purpose
     *
     * @test
     */
    public function setUp(){
        $sessionMock = $this->getModelMockBuilder('adminhtml/session_quote')
            ->disableOriginalConstructor() // This one removes session_start and other methods usage
            ->setMethods(null) // Enables original methods usage, because by default it overrides all methods
            ->getMock();
        $this->replaceByMock('singleton', 'adminhtml/session_quote', $sessionMock);
    }

    /**
     * Check price calculations
     *
     * @param string $expectation
     * @param int $product_id
     * @param datetime $start_date
     * @param datetime $end_date
     * @param int $qty
     * @param int $customerGroup
     * @test
     * @loadFixture
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function priceCalculations($expectation, $product_id, $start_date, $end_date, $qty, $customerGroup){
        //$customerSessionMock = $this->getModelMock('customer/session', array('renewSession'));
        // $this->replaceByMock('singleton', 'customer/session', $customerSessionMock);

        $product = Mage::getModel('catalog/product')->load($product_id);
        $iPriceCalculated = ITwebexperts_Payperrentals_Helper_Price::calculatePrice($product, $start_date, $end_date, $qty, $customerGroup);
        $iExpectedPrice = $this->expected($expectation)->getPrice();
        //$this->_getExpectations()->getData($testNr . '/price')

        $this->assertEquals($iExpectedPrice, $iPriceCalculated);
    }

    /**
     * Check price calculations
     *
         * @param string $expectation
         * @param int $product_id
         * @param datetime $start_date
         * @param datetime $end_date
         * @param int $qty
         * @param int $customerGroup
         * @param string $bundleArr
         * @param string $bundleQty
         * @param string $bundleQty1
         * @test
         * @loadFixture
         * @loadExpectation
         * @dataProvider dataProvider
         */
    public function priceBundles($expectation, $product_id, $start_date, $end_date, $qty, $customerGroup, $bundleArr, $bundleQty, $bundleQty1){

         $bundleOptions = array(1=>array(1,90),2=>array(3,4)); //normally passed via dataProvider
         $oBundleType = Mage::getModel('catalog/product')->load($product_id)->getTypeInstance();

        $cSelections = $oBundleType->getSelectionsCollection(array_keys($bundleOptions));
        $bundleOptionBuyRequest = array();
        foreach($bundleOptions as $optionId => $selection){
            $bundleOptionBuyRequest[$optionId] = $cSelections->getItemByColumnValue('product_id',$selection)->getSelectionId();
        }

        $Product = Mage::getModel('catalog/product')->load($product_id);
        if($Product->getBundlePricingtype() == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_FORALL){
            $iPriceCalculated = ITwebexperts_Payperrentals_Helper_Price::calculatePrice($Product, $start_date, $end_date, $qty, $customerGroup);
            $iExpectedPrice = $this->expected($expectation)->getPrice();

            $this->assertEquals($iExpectedPrice, $iPriceCalculated);
        }else{

            eval('$bundle_option = ' . $bundleArr . ';');
            eval('$bundle_option_qty = ' . $bundleQty . ';');
            eval('$bundle_option_qty1 = ' . $bundleQty1 . ';');

            $iPriceCalculated = ITwebexperts_Payperrentals_Helper_Price::getBundlePricing($Product, $bundle_option, $bundle_option_qty, $bundle_option_qty1, $qty, $start_date, $end_date, $customerGroup);

            $iExpectedPrice = $this->expected($expectation)->getPrice();
            $this->assertEquals($iExpectedPrice, $iPriceCalculated);
        }
    }
}