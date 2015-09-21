<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Customer_Rentalqueuelist
 */
class ITwebexperts_Payperrentals_Block_Customer_Rentalqueuelist extends Mage_Customer_Block_Account_Dashboard
{
    /**
     *
     */
    protected function _construct()
	{
	}

    /**
     * @return string
     */
    public function getRentalQueue(){
		$rentalQueueHtml = '<ul id="sortable">';
		//$rentalQueueHtml .= '<tbody>';
		$coll = Mage::getModel('payperrentals/rentalqueue')
				->getCollection()
                ->addProductAttributeToSelect('name')
				->addCustomerIdFilter(Mage::getSingleton('customer/session')->getCustomerId())
				->addStoreIdFilter(Mage::app()->getStore()->getId())
				->addSelectFilter('sendreturn_id = "0"')
				->addOrderFilter('sort_order ASC');


		foreach($coll as $item){
			$rentalQueueHtml .= '<li class="ui-state-default" id="item_'.$item->getProductId().'">';
			$productName = $item->getProductName();
			$rentalQueueHtml .= '<span style="display:inline-block" class="ui-icon ui-icon-arrowthick-2-n-s"></span><span style="width:90%;display:inline-block;">'.$productName.'</span>';
			$rentalQueueHtml .= ''.'<span style="display:inline-block" class="remove ui-icon ui-icon-close"></span>'.'';
			$rentalQueueHtml .= '</li>';
		}

		$rentalQueueHtml .= '</ul>';
		return $rentalQueueHtml;
	}

    /**
     * @return string
     */
    public function getSentItems(){
		$filter = "res_startdate='0000-00-00 00:00:00' AND return_date='0000-00-00 00:00:00' AND customer_id='".Mage::getSingleton('customer/session')->getCustomerId()."'";

		$returnItemsHtml = '<table class="data-table" id="sentItemsTable" cellspacing="0">
			<thead>
				<tr class="headings">
					<th>'.Mage::helper('payperrentals')->__('Product Name').'</th>
					<th>'.Mage::helper('payperrentals')->__('Send Date').'</th>
					<th>'.Mage::helper('payperrentals')->__('Serial Numbers').'</th>
				</tr>
			</thead>
		';
		$returnItemsHtml .= '<tbody>';

		$coll = Mage::getModel('payperrentals/sendreturn')
				->getCollection()
                ->addProductAttributeToSelect('name')
				->addSelectFilter($filter)
				->orderByCustomerId()
		;

		$customerId = 0;
		foreach ($coll as $item) {
			$returnItemsHtml .= '<tr id="resorder_'.$item->getId().'">';

			/*if($customerId != $item->getCustomerId()){
				$customerId = $item->getCustomerId();
				$customer = Mage::getModel('customer/customer')->load($customerId);
				$customerName = $customer->getFirstname(). ' '.$customer->getLastname();
				$returnItemsHtml .= '<td>'.$customerName.'</td>';
			}else{
				$returnItemsHtml .= '<td>'.''.'</td>';
			} */

			$productName = $item->getProductName();
			$returnItemsHtml .= '<td>'.$productName.'</td>';
			$returnItemsHtml .= '<td>'.Mage::helper('core')->formatDate($item->getSendDate(), 'medium', false).'</td>';

			$returnItemsHtml .= '<td>';
			$snArr = explode(',', $item->getSn());
			foreach($snArr as $sn){
				$returnItemsHtml .= $sn.'<br/>';
			}
			$returnItemsHtml .= '</td>';
			$returnItemsHtml .= '</tr>';
		}

		$returnItemsHtml .= '</tbody></table>';
		return $returnItemsHtml;
	}

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

}
