<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Catalog_Product_Renderer_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Price
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $_row)
    {
        $data = $_row->getData($this->getColumn()->getIndex());
        $priceAmount = 0;
        if ($data && $_row->getTypeId() != 'reservation') {
            $priceAmount = $data;
        } elseif ($_row->hasData('reservation_price')) {
            $priceAmount = $_row->getData('reservation_price');
        } elseif (is_null($data) || $_row->getTypeId() == 'reservation') {
            $product = $_row->load($_row->getId());
            if ($product->getTypeId() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE || $product->getBundlePricingtype() == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_FORALL) {
                /** TODO move calculation to mysql join first record */
                $priceCollection = Mage::getModel('payperrentals/reservationprices')->getCollection()->addFieldToFilter('entity_id', array('eq' => $_row->getData('entity_id')));
                if (count($priceCollection)) {
                    $firstRecord = $priceCollection->getFirstItem();
                    $priceAmount = $firstRecord->getPrice();
                    $_row->setData('reservation_number', $firstRecord->getNumberof());
                    $_row->setData('price_type', $firstRecord->getPtype());
                }
            } elseif ($product->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {
                $priceAmount = 0;
            }
        }
        if ($priceAmount) {
            $data = floatval($priceAmount) * $this->_getRate($_row);
            $currency_code = $this->_getCurrencyCode($_row);

            if (!$currency_code) {
                return $data;
            }
            $data = sprintf("%f", $data);
            $data = Mage::app()->getLocale()->currency($currency_code)->toCurrency($data);

            if ($_row->hasData('reservation_number') && $_row->hasData('price_type')) {
                $periodAr = Mage::getModel('payperrentals/product_periodtype')->getOptionArray($_row->getData('reservation_number'));
                $data .= '/<b>' . $_row->getData('reservation_number') . ' ' . $periodAr[$_row->getData('price_type')] . '</b>';
            }
            return $data;
        }

        return $this->getColumn()->getDefault();
    }
}