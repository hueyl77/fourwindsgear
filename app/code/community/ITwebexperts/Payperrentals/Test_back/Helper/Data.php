<?php




class ITwebexperts_Payperrentals_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case {

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

//    /**
//     * Check price calculations
//     *
//     * @param string $expectation
//     * @param int $product_id
//     * @param datetime $start_date
//     * @param datetime $end_date
//     * @param int $qty
//     * @param int $customerGroup
//     * @test
//     * @loadFixture
//     * @loadExpectation
//     * @dataProvider dataProvider
//     */
//    public function priceNonrated($expectation, $product_id, $start_date, $end_date, $qty, $customerGroup){
//        //$customerSessionMock = $this->getModelMock('customer/session', array('renewSession'));
//        // $this->replaceByMock('singleton', 'customer/session', $customerSessionMock);
//
//        $product = Mage::getModel('catalog/product')->load($product_id);
//        $iPriceCalculated = ITwebexperts_Payperrentals_Helper_Price::calculatePrice($product, $start_date, $end_date, $qty, $customerGroup);
//        $iExpectedPrice = $this->expected($expectation)->getPrice();
//        //$this->_getExpectations()->getData($testNr . '/price')
//
//        $this->assertEquals($iExpectedPrice, $iPriceCalculated);
//    }
//
//    /**
//     * Check price calculations
//     *
//     * @param string $expectation
//     * @param int $product_id
//     * @param datetime $start_date
//     * @param datetime $end_date
//     * @param int $qty
//     * @param int $customerGroup
//     * @param string $bundleArr
//     * @param string $bundleQty
//     * @param string $bundleQty1
//     * @test
//     * @loadFixture
//     * @loadExpectation
//     * @dataProvider dataProvider
//     */
//    public function priceBundles2($expectation, $product_id, $start_date, $end_date, $qty, $customerGroup, $bundleArr, $bundleQty, $bundleQty1){
//        /*
//         $bundleOptions = array(1=>array(1,90),2=>array(3,4)); //normally passed via dataProvider
//         $oBundleType = Mage::getModel('catalog/product')->load($product_id)->getTypeInstance();
//
//        $cSelections = $oBundleType->getSelectionsCollection(array_keys($bundleOptions));
//        $bundleOptionBuyRequest = array();
//        foreach($bundleOptions as $optionId => $selection){
//            $bundleOptionBuyRequest[$optionId] = $cSelections->getItemByColumnValue('product_id',$selection)->getSelectionId();
//        }
//         */
//
//
//        $Product = Mage::getModel('catalog/product')->load($product_id);
//        if($Product->getBundlePricingtype() == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_FORALL){
//            $iPriceCalculated = ITwebexperts_Payperrentals_Helper_Price::calculatePrice($Product, $start_date, $end_date, $qty, $customerGroup);
//            $iExpectedPrice = $this->expected($expectation)->getPrice();
//
//            $this->assertEquals($iExpectedPrice, $iPriceCalculated);
//        }else{
//
//            eval('$bundle_option = ' . $bundleArr . ';');
//            eval('$bundle_option_qty = ' . $bundleQty . ';');
//            eval('$bundle_option_qty1 = ' . $bundleQty1 . ';');
//
//            $iPriceCalculated = ITwebexperts_Payperrentals_Helper_Price::getBundlePricing($Product, $bundle_option, $bundle_option_qty, $bundle_option_qty1, $qty, $start_date, $end_date, $customerGroup);
//            $iExpectedPrice = $this->expected($expectation)->getPrice();
//            $this->assertEquals($iExpectedPrice, $iPriceCalculated);
//        }
//    }

    /**
     * Check inventory
     *
     * @param string $_testId
     * @param int $product_id
     * @param datetime $start_date
     * @param datetime $end_date
     * @test
     * @loadFixture
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function checkAvailableQty($_testId, $product_id, $start_date, $end_date){
        $availableQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($product_id, $start_date, $end_date);
        $_expected = $this->expected('testId' . $_testId);

        $this->assertEquals($_expected->getQty(), $availableQty);
    }

    /**
     * Check inventory adding
     *
     * @param string $_testId
     * @param int $product_id
     * @param int $new_qty
     * @test
     * @loadFixture
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function checkAddInventory($_testId, $product_id, $new_qty){
        $_expected = $this->expected('testId' . $_testId);

        $start_date1 = '2014-01-05 00:00:00';
        $end_date1   = '2014-01-10 00:00:00';
        $start_date2 = '2014-03-05 00:00:00';
        $end_date2   = '2014-03-10 00:00:00';

        $availableQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($product_id, $start_date1, $end_date1);
        $this->assertEquals($_expected->getOldqty1(), $availableQty);
        $availableQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($product_id, $start_date2, $end_date2);
        $this->assertEquals($_expected->getOldqty2(), $availableQty);

        $product = Mage::getModel('catalog/product')->load($product_id);
        $product->setData('payperrentals_quantity', $new_qty)->getResource()->saveAttribute($product, 'payperrentals_quantity');

        $availableQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($product_id, $start_date1, $end_date1);
        $this->assertEquals($_expected->getNewqty1(), $availableQty);
        $availableQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($product_id, $start_date2, $end_date2);
        $this->assertEquals($_expected->getNewqty2(), $availableQty);
    }

    /**
     * Check min and max rental period
     *
     * @param string $_testId
     * @param int $product_id
     * @test
     * @loadFixture
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function checkMinMax($_testId, $product_id){
        $product = Mage::getModel('catalog/product')->load($product_id);

        $_expected = $this->expected('testId' . $_testId);

        list($_minRentalNumber, $_minRentalType, $_maxRentalNumber, $_maxRentalType) = ITwebexperts_Payperrentals_Helper_Data::getMinMaxRental($product);

        $_minRentalPeriod = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
            $_minRentalNumber, $_minRentalType
        );
        $_maxRentalPeriod = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
            $_maxRentalNumber, $_maxRentalType
        );

        $this->assertEquals($_expected->getMin(), $_minRentalPeriod);
        $this->assertEquals($_expected->getMax(), $_maxRentalPeriod);
    }

    /**
     * Check rental for disabled days
     *
     * @param string $_testId
     * @param int $product_id
     * @test
     * @loadFixture
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function checkDisabledDays($_testId, $product_id){
        $product = Mage::getModel('catalog/product')->load($product_id);
        $_expected = $this->expected('testId' . $_testId);

        $disabledDays = ITwebexperts_Payperrentals_Helper_Data::getDisabledDays($product);

        $this->assertCount(count($_expected->getValues()), $disabledDays );
        foreach ($_expected->getValues() as $value)
            $this->assertContains($value, $disabledDays);
    }

    /**
     * Check store open|close hours
     *
     * @param string $_testId
     * @param int $_storeId
     * @test
     * @loadFixture
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function checkStoreHours($_testId, $_storeId){
        $_expected = $this->expected('testId' . $_testId);

        $storeHours = ITwebexperts_Payperrentals_Helper_Config::getStoreTime($_storeId);

        $this->assertEquals($storeHours, $_expected->getHours());
    }

    /**
     * Check hotel mode
     *
     * @param string $_testId
     * @param int $product_id
     * @param int $hotel_mode
     * @param datetime $start_date
     * @param datetime $end_date
     * @test
     * @loadFixture
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function checkHotelMode($_testId, $product_id, $hotel_mode, $start_date, $end_date){

        Mage::getConfig()->saveConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_HOTEL_MODE, $hotel_mode);
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

        $availableQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($product_id, $start_date, $end_date);
        $_expected = $this->expected('testId' . $_testId);

        $this->assertEquals($_expected->getQty(), $availableQty);
    }

    /**
     * Check reserve inventory for send/return date
     *
     * @param string $_testId
     * @param int $product_id
     * @param int $reserve4send_return
     * @param datetime $start_date
     * @param datetime $end_date
     * @test
     * @loadFixture
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function reserveSendReturn($_testId, $product_id, $reserve4send_return, $start_date, $end_date){

        Mage::getConfig()->saveConfig(
            ITwebexperts_Payperrentals_Helper_Config::XML_PATH_USE_RESERVE_INVENTORY_SEND_RETURN, $reserve4send_return);
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

        $availableQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($product_id, $start_date, $end_date);
        $_expected = $this->expected('testId' . $_testId);

        $this->assertEquals($_expected->getQty(), $availableQty);
    }

    /**
     * Check closed times
     *
     * @param string $_testId
     * @param int $product_id
     * @test
     * @loadFixture
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function checkClosedTimes($_testId, $product_id){
        $product = Mage::getModel('catalog/product')->load($product_id);
        $_expected = $this->expected('testId' . $_testId);

        $closedTimes = ITwebexperts_Payperrentals_Helper_Data::getDisabledDates($product);

        if (empty($closedTimes))
            $this->assertEquals($_expected->getValues(), '');
        elseif(count($closedTimes) > 10)
        {
            foreach ($_expected->getValues() as $value) 
                $this->assertContains('"' . $value . '"', $closedTimes);
        }
        else
        {
            $this->assertCount(count($_expected->getValues()), $closedTimes );
            foreach ($_expected->getValues() as $value) 
                $this->assertContains('"' . $value . '"', $closedTimes);
        }
    }

    /**
     * Check padding days
     *
     * @param string $_testId
     * @param int $product_id
     * @param int $timestamp
     * @test
     * @loadFixture
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function checkPaddingDays($_testId, $product_id, $timestamp){
        $product = Mage::getModel('catalog/product')->load($product_id);
        $_expected = $this->expected('testId' . $_testId);

        $paddingDays = ITwebexperts_Payperrentals_Helper_Data::getProductPaddingDays($product, $timestamp);

        if (empty($paddingDays))
            $this->assertEquals($_expected->getValues(), '');
        else
        {
            $this->assertCount(count($_expected->getValues()), $paddingDays );
            foreach ($_expected->getValues() as $value) 
                $this->assertContains('"' . $value . '"', $paddingDays);
        }
    }

	/*public function testDefaultAllowed(){
		$oSessionMock = $this->getModelMock('customer/session', array('isLoggedIn'));

		$oSessionMock->expects($this->any())
			->method('isLoggedIn')
			->will($this->returnValue(true));

		$this->replaceByMock('singleton', 'customer/session', $oSessionMock);

		$oHelper = Mage::helper('b2bprofessional');
		$bLoggedIn = $oHelper->checkLoggedIn();

		$this->assertEquals(true, $bLoggedIn);

	}*/

}