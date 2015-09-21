<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Customer_Edit_Tab_Rentalqueue
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Customer_Edit_Tab_Rentalqueue extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('payperrentals/customer/edit/tab/rentalqueue.phtml');
    }


    /**
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('payperrentals')->__('Rental Queue');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('payperrentals')->__('Rentalqueue');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getSentItems()
    {
        $filter = "res_startdate='0000-00-00 00:00:00' AND return_date='0000-00-00 00:00:00' AND customer_id='" . Mage::registry('current_customer')->getId() . "'";

        $returnItemsHtml = '<table class="data" cellspacing="0">
			<thead>
				<tr class="headings">
					<th>Customer Name</th>
					<th>Product Name</th>
					<th>Send Date</th>
					<th>Serial Numbers</th>
					<th>' . Mage::helper('payperrentals')->__('Mark For Return') . '<br/>
						<button class="returnSelected" type="button"><span>' . Mage::helper('payperrentals')->__('Return Selected') . '</span></button>
					</th>

				</tr>
			</thead>
		';
        $returnItemsHtml .= '<tbody>';

        $coll = Mage::getModel('payperrentals/sendreturn')
            ->getCollection()
            ->addSelectFilter($filter);

        $customerId = 0;
        foreach ($coll as $item) {
            $returnItemsHtml .= '<tr id="resorder_' . $item->getId() . '">';
            if ($customerId != $item->getCustomerId()) {
                $customerId = $item->getCustomerId();
                $customer = Mage::getModel('customer/customer')->load($customerId);
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
                $returnItemsHtml .= '<td>' . $customerName;
                $returnItemsHtml .= '<input type="hidden" name="customer_id" value="' . $customerId . '"/>';
                $returnItemsHtml .= '</td>';
            } else {
                $returnItemsHtml .= '<td>' . '' . '</td>';
            }

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $productName = $product->getName();
            $returnItemsHtml .= '<td>' . $productName . '</td>';
            $returnItemsHtml .= '<td>' . Mage::helper('core')->formatDate($item->getSendDate(), 'medium', false) . '</td>';

            $returnItemsHtml .= '<td>';
            $snArr = explode(',', $item->getSn());
            foreach ($snArr as $sn) {
                $returnItemsHtml .= $sn . '<br/>';
            }
            $returnItemsHtml .= '</td>';
            $returnItemsHtml .= '<td><input type="checkbox" name="returnRes[]" class="returnRes" value="' . $item->getId() . '"/></td>';
            $returnItemsHtml .= '</tr>';
        }

        $returnItemsHtml .= '</tbody></table>';
        return $returnItemsHtml;
    }
}
