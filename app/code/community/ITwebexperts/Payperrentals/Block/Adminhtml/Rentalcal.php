<?php


class ITwebexperts_Payperrentals_Block_Adminhtml_Rentalcal extends Mage_Core_Block_Template
{
    public function __construct()
    {

    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    /**
     * get collection of all rentals for start & end date
     *
     * @param $startDate
     * @param $endDate
     */

    public function getCollection($startDate, $endDate, $isByOrder = false)
    {
        $reserveOrderCollection = Mage::getModel('payperrentals/reservationorders')->getCollection();
        if(Mage::helper('itwebcommon')->isVendorAdmin()){
            $reserveOrderCollection->addFieldToFilter('main_table.vendor_id',array('eq'=>Mage::getSingleton('vendors/session')->getId()));
        }
        $reserveOrderCollection->addHavingFilter(
            "main_table.start_turnover_before >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($startDate)
            . "' AND main_table.end_turnover_after <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($endDate) . "'"
        );
        $reserveOrderCollection->getSelect()->joinLeft(array('soi' => Mage::getConfig()->getTablePrefix().'sales_flat_order_item'),'soi.item_id=order_item_id',array('name'));
        $reserveOrderCollection->getSelect()->joinLeft(array('so' => Mage::getConfig()->getTablePrefix().'sales_flat_order'),
            'so.entity_id=main_table.order_id',
            array('customer_firstname','customer_lastname','increment_id'));
//        $reserveOrderCollection->getSelect()->join(array('product' => 'catalog_product_flat_1'),'product.entity_id=main_table.product_id','name');
        // $reserveOrderCollection->getSelect()->group('main_table.order_id');
        return $reserveOrderCollection;
    }

    /**
     * Convert the collection to a php array of orders
     * within the orders array is an array of products
     *
     * @param $collection
     * @return array
     */

    public function convertCollectionToEventsArray($collection)
    {
        $eventsArray = array();

        foreach($collection as $reservation)
        {
            /**
             * if no sales_flat_order_item record because is manual reservation, set
             * product name manually
             */
            if(!isset($reservation['name'])){
                $productname = Mage::getModel('catalog/product')->load($reservation['product_id'])->getName();
                $reservation['name'] = $productname;
            }

            $eventsArray[] = array(
                'orderid'       =>  $reservation['order_id'],
                'start_date_turnover'    =>  $reservation['start_turnover_before'],
                'end_date_turnover'      =>  $reservation['end_turnover_after'],
                'start_date'    =>  $reservation['start_date'],
                'end_date'      =>  $reservation['end_date'],
                'product'       =>  $reservation['name'],
                'qty'           =>  $reservation['qty'],
                'order_increment'           =>  $reservation['increment_id'],
                'customer_name' =>  $reservation['customer_firstname'] . " " . $reservation['customer_lastname'],
                'comments'  =>  $reservation['comments']
            );
        }
        // combine array items with same order_id
        $combinedEventsArray = array();
        foreach($eventsArray as $eventsItem)
        {
            $orderid = $eventsItem['orderid'];
            if(isset($combinedEventsArray[$orderid])){
                // add product & qty to products array
                $combinedEventsArray[$orderid]['products'][] = array(
                    'qty'   =>  $eventsItem['qty'],
                    'product'   =>  $eventsItem['product']
                );
            } else {
                $combinedEventsArray[$orderid] = array();
                    $combinedEventsArray[$orderid]['products'][] = array(
                        'qty'   =>  $eventsItem['qty'],
                        'product'   =>  $eventsItem['product']
                    );
                $combinedEventsArray[$orderid]['start_date_turnover'] = $eventsItem['start_date_turnover'];
                $combinedEventsArray[$orderid]['end_date_turnover'] = $eventsItem['end_date_turnover'];
                $combinedEventsArray[$orderid]['start_date'] = $eventsItem['start_date'];
                $combinedEventsArray[$orderid]['end_date'] = $eventsItem['end_date'];
                $combinedEventsArray[$orderid]['customer_name'] = $eventsItem['customer_name'];
                $combinedEventsArray[$orderid]['order_increment'] = $eventsItem['order_increment'];
                $combinedEventsArray[$orderid]['comments'] = $eventsItem['comments'];
            }
        }
        return $combinedEventsArray;
    }

    /**
     * Takes array from convertCollectionToEventsArray function and combines
     * orders that have the same start date with turnover. Return as array based on $byStartorEnd date
     *
     * @param $combinedEventsArray
     * @return mixed
     */

    public function combineStartandEndTurnoverArray($combinedEventsArray,$byStartorEnd)
    {
        $newArrayCombinedStartEnd = array();
        foreach($combinedEventsArray as $orderid=>$eventsItem)
        {
            if($byStartorEnd == "start") {
                $startWithTurnoverWithoutTimes = $eventsItem['start_date_turnover'];
            } else {
                $startWithTurnoverWithoutTimes = $eventsItem['end_date_turnover'];
            }
            $startWithTurnoverWithoutTimes = new DateTime($startWithTurnoverWithoutTimes);
            $startWithTurnoverWithoutTimes = $startWithTurnoverWithoutTimes->format('Y-m-d');
            $newArrayCombinedStartEnd[$startWithTurnoverWithoutTimes][$orderid]['products'] = $eventsItem['products'];
            $newArrayCombinedStartEnd[$startWithTurnoverWithoutTimes][$orderid]['start_date_turnover'] = $eventsItem['start_date_turnover'];
            $newArrayCombinedStartEnd[$startWithTurnoverWithoutTimes][$orderid]['end_date_turnover'] = $eventsItem['end_date_turnover'];
            $newArrayCombinedStartEnd[$startWithTurnoverWithoutTimes][$orderid]['start_date'] = $eventsItem['start_date'];
            $newArrayCombinedStartEnd[$startWithTurnoverWithoutTimes][$orderid]['end_date'] = $eventsItem['end_date'];
            $newArrayCombinedStartEnd[$startWithTurnoverWithoutTimes][$orderid]['customer_name'] = $eventsItem['customer_name'];
            $newArrayCombinedStartEnd[$startWithTurnoverWithoutTimes][$orderid]['order_increment'] = $eventsItem['order_increment'];
            $newArrayCombinedStartEnd[$startWithTurnoverWithoutTimes][$orderid]['comments'] = $eventsItem['comments'];
            }
        return $newArrayCombinedStartEnd;
    }


    /**
     * Convert orders array to a json object for use in fullcalendar
     *
     * @param $eventsArray
     * @return string
     */

    public function convertEventsArrayToJSON($eventsArray){
        $helper = Mage::helper('payperrentals');
        $datehelper = Mage::helper('payperrentals/date');
        $json = '';
        foreach($eventsArray as $orderIdKey=>$eventItem){
            $eventItem['customer_name'] = htmlentities($eventItem['customer_name'],ENT_QUOTES | ENT_HTML5);
            $orderurl = Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view', array('order_id' => $orderIdKey));

            $combinedOrderInfo = "<a href=\"$orderurl\">" . $helper->__('Order #') . ": " . $eventItem['order_increment'] . "</a> " . $eventItem['customer_name'] . "<br />";
            $combinedOrderInfo .= "<h5>" . $helper->__('Reservation Dates') . "</h5>";
            $combinedOrderInfo .= $helper->__('Start Date') . ": " . $datehelper->formatDbDate($eventItem['start_date'], false) . "<br />";
            if ($eventItem['start_date'] != $eventItem['start_date_turnover']){
            $combinedOrderInfo .= $helper->__('Start Date Including Turnover') . ": " . $datehelper->formatDbDate($eventItem['start_date_turnover'], false) . "<br />";
            }
            $combinedOrderInfo .= $helper->__('End Date') . ": " . $datehelper->formatDbDate($eventItem['end_date'], false) . "<br />";
            if ($eventItem['end_date'] != $eventItem['end_date_turnover']){
            $combinedOrderInfo .= $helper->__('End Date Including Turnover') . ": "  . $datehelper->formatDbDate($eventItem['end_date_turnover'], false) . "<br />";
            }
            $combinedOrderInfo .= "<h5>" . $helper->__('Products') . "</h5>";
            foreach($eventItem['products'] as $product){
                $product['product'] = htmlentities($product['product'],ENT_QUOTES | ENT_HTML5);
                $combinedOrderInfo .= $product['qty'] . "x" . $product['product'] . "<br />";
            }
            $json .= <<<END
                {
                    title: 'Order #: {$eventItem['order_increment']} {$eventItem['customer_name']}',
                    start: '{$eventItem['start_date']}',
                    end: '{$eventItem['end_date']}',
                    description: '$combinedOrderInfo'
                },
END;
        }
        return $json;
    }

    /**
     * Used twice, set if start or end using $byStartorEnd flag. Needs to have an array
     * based on start date or end date to work properly
     *
     * @param $eventsArray
     * @param $byStartorEnd
     * @return string
     */

    public function convertEventsArrayToJSONstartend($eventsArray,$byStartorEnd){
        $helper = Mage::helper('payperrentals');
        $datehelper = Mage::helper('payperrentals/date');
        $json = '';

        foreach($eventsArray as $eventItem){
            $combinedOrderInfo = '';
            foreach($eventItem as $orderid=>$order){
            $orderurl = Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view',
                array('order_id' => $orderid));
                if(isset($order['order_increment'])){
                    $order['customer_name'] =  htmlentities($order['customer_name'],ENT_QUOTES | ENT_HTML5);
                    $combinedOrderInfo .= "<a href=\"$orderurl\">" . $helper->__('Order #') . ": " . $order['order_increment'] . "</a> " .
                        $order['customer_name'] . "<br />";} else {
                    $combinedOrderInfo .= $helper->__('Manual Reservation') . '<br />' . $helper->__('Comments: ') . $order['comments'];
                }
                $combinedOrderInfo .= "<h5>" . $helper->__('Reservation Dates') . "</h5>";
            $combinedOrderInfo .= $helper->__('Start Date') . ": " . $datehelper->formatDbDate($order['start_date'], false) . "<br />";
            if ($order['start_date'] != $order['start_date_turnover']){
                $combinedOrderInfo .= $helper->__('Start Date Including Turnover') . ": " . $datehelper->formatDbDate($order['start_date_turnover'],false) . "<br />";
            }
            $combinedOrderInfo .= $helper->__('End Date') . ": " . $datehelper->formatDbDate($order['end_date'], false) . "<br />";
            if ($order['end_date'] != $order['end_date_turnover']){
                $combinedOrderInfo .= $helper->__('End Date Including Turnover') . ": "  . $datehelper->formatDbDate($order['end_date_turnover'],false) . "<br />";
            }
                $combinedOrderInfo .= "<h5>" . $helper->__('Products') . "</h5>";
            foreach($order['products'] as $product){
                $product['product'] = htmlentities($product['product'],ENT_QUOTES | ENT_HTML5);
                $combinedOrderInfo .= $product['qty'] . "x" . $product['product'] . "<br />";
            }
                $combinedOrderInfo .= "<br /><hr><br />";
                $startTurnover = $order['start_date_turnover'];
                $endTurnover = $order['end_date_turnover'];
            }
            $format = 'Y-m-d H:i:s';
            $orderStartText = $helper->__('Orders Starting');
            $ordersEndingText = $helper->__('Orders Ending');
            $startTurnover = DateTime::createFromFormat($format,$startTurnover);
            $startTurnover = $startTurnover->format('Y-m-d');
            $endDate = DateTime::createFromFormat($format,$order['end_date']);
            $endDate = $endDate->format('Y-m-d');
            $endTurnover = DateTime::createFromFormat($format,$endTurnover);
            $endTurnover = $endTurnover->format('Y-m-d');
            $test = '';
            if($byStartorEnd == "start") {
                $json .= <<<END
                {
                    title: '$orderStartText',
                    start: '$startTurnover',
                    end: '$startTurnover',
                    description: '$combinedOrderInfo'
                },
END;
            }
            if($byStartorEnd == "end") {
                if(Mage::helper('payperrentals/config')->useTurnover() == 'turnover'){
                    $end = $endTurnover;
                } else {
                    $end = $endDate;
                }
                $json .= <<<END2
                {
                    title: '$ordersEndingText',
                    start: '$end',
                    end: '$end',
                    description: '$combinedOrderInfo'
                },
END2;
            }
        }
        return $json;
    }

}