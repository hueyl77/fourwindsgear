<?php

class ITwebexperts_Payperrentals_Test_Controller_Adminhtml_ShipmentController extends EcomDev_PHPUnit_Test_Case_Controller
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
     * test sendSerialNumbers for save shipment in backend
     *
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function testSendSerialNumbersBeforeSaveShipment($_testId, $_orderId, $_productId, $_itemId, $_serialNumbers = array())
    {
        $this->getRequest()->setQuery('order_id', $_orderId);
        $this->getRequest()->setQuery('shipment', array(
            'items' => array(
                $_itemId => 1
            )
        ));
        $_newSerials = array(
            $_itemId => $_serialNumbers
        );
        $_expected = $this->expected('testId' . $_testId);
        if (count($_serialNumbers)) {
            $this->getRequest()->setQuery('sn', $_newSerials);
        }
        $this->dispatch('adminhtml/sales_order_shipment/save');
        $this->assertRequestDispatched();

        $_serialsCollection = Mage::getModel('payperrentals/serialnumbers')
            ->getCollection()
            ->addEntityIdFilter($_productId)
            ->addSelectFilter("status='O'");
        $this->assertEquals($_expected->getSerialCount(), count($_serialsCollection));
        if ($_expected->hasExpectedSerials()) {
            foreach ($_serialsCollection as $_serialsItem) {
                $this->assertTrue(in_array($_serialsItem->getSn(), $_expected->getExpectedSerials()));
            }
        }

        $_reservationOrderCollection = Mage::getResourceModel('payperrentals/reservationorders_collection')->addOrderIdFilter($_orderId);
        foreach ($_reservationOrderCollection as $_reservationOrder) {
            $_checkSendReturn = $_reservationOrder->hasSendreturnId() && $_reservationOrder->getSendreturnId();
            $this->assertTrue($_checkSendReturn);
            if ($_checkSendReturn && $_expected->hasExpectedSerials()) {
                $_sendReturnModel = Mage::getModel('payperrentals/sendreturn')->load($_reservationOrder->getSendreturnId());
                $this->assertTrue(in_array($_sendReturnModel->getSn(), $_expected->getExpectedSerials()));
            }
        }
    }
}