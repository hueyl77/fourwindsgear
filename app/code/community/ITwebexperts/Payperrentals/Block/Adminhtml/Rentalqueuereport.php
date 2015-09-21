<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Rentalqueuereport
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Rentalqueuereport extends Mage_Core_Block_Template
{

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @var
     */
    protected $_myCollection;
    /**
     * @var
     */
    protected $_pager;

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPager()
    {
        return $this->_pager;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return mixed
     */
    protected function getMyCollection()
    {

        return $this->_myCollection;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    function cmp($a, $b)
    {
        $return = $b['tobeSent'] - $a['tobeSent'];
        if ($return === 0) {
            return strcmp($a['name'], $b['name']);
        }
        return $return;
//		return ($b['tobeSent'] - $a['tobeSent']);
    }

    public function deliveryDatesHeader(){
        if(ITwebexperts_Payperrentals_Helper_Data::isDeliveryDatesInstalled()) {
            return '<th>' . Mage::helper('deliverydates')->__("Delivery Date") . '</th>';
        } else {
            return '';
        }
    }

    public function getDeliveryDateTableRow($sendItemsHtml,$member){
        if(ITwebexperts_Payperrentals_Helper_Data::isDeliveryDatesInstalled()) {
            if($member['deliverydate'] == null) {
                $deliverydate = '';
            } else {
                $deliverydate = Mage::helper('payperrentals/date')->formatDbDate($member['deliverydate']);
            }
            $sendItemsHtml .= '<td>' . $deliverydate . '</td>';
            return $sendItemsHtml;
        } else {
            return $sendItemsHtml;
        }
    }

    /**
     * @return string
     */
    public function getRentalQueueTable()
    {

        $sendItemsHtml = '<table class="data" cellspacing="0">
			<thead>
				<tr class="headings">
					<th width="100px">' . Mage::helper('payperrentals')->__("Has Empty Queue") . '</th>
					<th>' . Mage::helper('payperrentals')->__("Customer Name") . '</th>
					<th>' . Mage::helper('payperrentals')->__("Product Allowed") . '</th>
					<th>' . Mage::helper('payperrentals')->__("Products In Queue") . '</th>
					<th>' . Mage::helper('payperrentals')->__("Products Sent") . '</th>
					<th>' . Mage::helper('payperrentals')->__("Products To Send") . '</th>' .
            $this->deliveryDatesHeader() . '
					<th>' . Mage::helper('payperrentals')->__("Rental Queue") . '</th>
				</tr>
			</thead>
		';
        $sendItemsHtml .= '<tbody>';

        $collection = Mage::getResourceModel('sales/recurring_profile_collection')
            ->addFieldToFilter('state', 'active')
            ->addFieldToFilter('customer_id',array('neq'=>'null'));

        if (urldecode($this->getRequest()->getParam('store'))) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        }

        $membersArr = array();
        $customersColl = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('*');
        foreach($customersColl as $customer) {
            $customer_id = $customer->getId();
            $prodAllowed = Mage::helper('payperrentals/membership')->getNumberAllowedForMembershipManual($customer_id);
            if($prodAllowed>=1){
                $customerArray[] = array(
                    'id'=>$customer_id,
                    'name' =>$customer->getName(),
                    'deliverydate'=>$customer->getDeliveryDate()
                );
            }
        }

        foreach ($collection as $coll) {
            $itemSerialized = unserialize($coll->getOrderItemInfo());
            $orderSerialized = unserialize($coll->getOrderInfo());
            //Zend_Debug::dump($orderSerialized);
            if ($itemSerialized['product_type'] == 'membershippackage') {
                $customer = Mage::getModel('customer/customer')->load($orderSerialized['customer_id']);
                $customerArray[] = array(
                    'id' => $orderSerialized['customer_id'],
                    'name' => $orderSerialized['customer_firstname'] . ' ' . $orderSerialized['customer_lastname'],
                    'deliverydate'=>$customer->getDeliveryDate()
                );
            }
        }
        foreach($customerArray as $customer){

            $_queueCollection = Mage::getModel('payperrentals/rentalqueue')
                ->getCollection()
                ->addCustomerIdFilter($customer['id'])
                ->addSelectFilter('sendreturn_id = "0"')
                ->addOrderFilter('sort_order ASC');


            $prodAllowed = Mage::helper('payperrentals/membership')->getNumberAllowedForMembership($customer['id']);
            $prodSent = ITwebexperts_Payperrentals_Helper_Data::getSentItemsForCustomer($customer['id']);
            $member['deliverydate'] = $customer['deliverydate'];
            $member['empty_queue'] = (count($_queueCollection) == 0)?true:false;
            $member['name'] = $customer['name'];
            $member['prodAllowed'] = $prodAllowed;
            $member['in_queue'] = count($_queueCollection);
            $member['prodSent'] = $prodSent;
            $member['tobeSent'] = ($prodAllowed - $prodSent);
            $member['link'] = '<a target="_blank" href="' . Mage::getUrl('adminhtml/customer/edit/', array('id' => $customer['id'], 'tab' => 'customer_info_tabs_rentalqueue', 'key' => Mage::getSingleton('adminhtml/url')->getSecretKey('customer', 'edit'))) . '">View</a>';

            $membersArr[] = $member;
        }


        usort($membersArr, array($this, "cmp"));
        foreach ($membersArr as $member) {

            $sendItemsHtml .= '<tr' . ($member['empty_queue'] ? ' style="background:#fad0d0;"' : '') . '>';
            $sendItemsHtml .= '<td>' . ($member['empty_queue'] ? 'Yes' : 'No') . '</td>';
            $sendItemsHtml .= '<td>' . $member['name'] . '</td>';
            $sendItemsHtml .= '<td>' . $member['prodAllowed'] . '</td>';
            $sendItemsHtml .= '<td>' . $member['in_queue'] . '</td>';
            $sendItemsHtml .= '<td>' . $member['prodSent'] . '</td>';
            $sendItemsHtml .= '<td>' . $member['tobeSent'] . '</td>';
            $sendItemsHtml = $this->getDeliveryDateTableRow($sendItemsHtml,$member);
            $sendItemsHtml .= '<td>' . $member['link'] . '</td>';
            $sendItemsHtml .= '</tr>';

        }

        $sendItemsHtml .= '</tbody></table>';
        return $sendItemsHtml;
    }


}
