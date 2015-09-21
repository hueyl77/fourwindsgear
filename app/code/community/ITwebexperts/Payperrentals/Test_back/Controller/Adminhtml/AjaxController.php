<?php

class ITwebexperts_Payperrentals_Test_Controller_Adminhtml_AjaxController extends EcomDev_PHPUnit_Test_Case_Controller
{
    public function setUp()
    {
        Mage::setIsDeveloperMode(false);
        Mage::getConfig()->setNode('default/dev/log/file', 'phpunit.system.log');
        $this->mockAdminUserSession();
        parent::setUp();
    }

    public function tearDown()
    {
        Mage::unregister('current_shipment');
        $_sendReturnTable = Mage::getSingleton('core/resource')->getTableName('payperrentals/sendreturn');
        Mage::getSingleton('core/resource')->getConnection('core_write')->truncateTable($_sendReturnTable);
    }

    /**
     * test getSerialNumbersByItemId for backend
     *
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function testGetSerialNumbersByItemId($_testId, $_productId, $_queryValue = '')
    {
        $this->getRequest()->setQuery('productId', $_productId);
        $this->getRequest()->setQuery('value', $_queryValue);
        $_expected = $this->expected('testId' . $_testId);

        $this->dispatch('payperrentals_admin/adminhtml_ajax/getSerialNumbersbyItemId');
        $_response = $this->getResponse()->getOutputBody();
        $this->assertJson($_response);
        $_responseDecode = Zend_Json::decode($_response);
        if (!is_array($_responseDecode)) (array)$_responseDecode;
        $this->assertEquals(count($_responseDecode), $_expected->getReturnCount());
        if ($_expected->getReturnCount()) {
            $this->assertJsonMatch($_response, $_expected->getReturnValues());
        }
    }

    /**
     * test sendSelectedAction for save shipment in backend
     *
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function testSendSelectedAction($_testId, $_resId, $_serialNumbers = array())
    {
        $this->getRequest()->setQuery('sendRes', array($_resId));
        $_expected = $this->expected('testId' . $_testId);
        if (count($_serialNumbers)) {
            $_newSerials = array(
                $_resId => $_serialNumbers
            );
            $this->getRequest()->setQuery('sn', $_newSerials);
        }
        $this->dispatch('payperrentals_admin/adminhtml_ajax/sendSelected');
        $this->assertRequestDispatched();

        $_reservationOrder = Mage::getModel('payperrentals/reservationorders')->load($_resId);
        $_serialsCollection = Mage::getModel('payperrentals/serialnumbers')
            ->getCollection()
            ->addEntityIdFilter($_reservationOrder->getProductId())
            ->addSelectFilter("status='O'");
        $this->assertEquals($_expected->getSerialCount(), count($_serialsCollection));
        if ($_expected->hasExpectedSerials()) {
            foreach ($_serialsCollection as $_serialsItem) {
                $this->assertTrue(in_array($_serialsItem->getSn(), $_expected->getExpectedSerials()));
            }
        }

        $_checkSendReturn = $_reservationOrder->hasSendreturnId() && $_reservationOrder->getSendreturnId();
        $this->assertTrue($_checkSendReturn);
        if ($_checkSendReturn && $_expected->hasExpectedSerials()) {
            $_sendReturnModel = Mage::getModel('payperrentals/sendreturn')->load($_reservationOrder->getSendreturnId());
            $this->assertTrue(in_array($_sendReturnModel->getSn(), $_expected->getExpectedSerials()));
        }
    }
}