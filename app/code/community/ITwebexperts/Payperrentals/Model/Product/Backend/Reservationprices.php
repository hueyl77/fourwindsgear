<?php



class ITwebexperts_Payperrentals_Model_Product_Backend_Reservationprices extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    protected $_rates;


    protected function _getResource()
    {
        return Mage::getResourceSingleton('payperrentals/reservationprices');
    }

    protected function _getProduct()
    {
        return Mage::registry('product');
    }

    public function _getWebsiteRates()
    {
        if (is_null($this->_rates)) {
            $this->_rates = array();
            $baseCurrency = Mage::app()->getBaseCurrencyCode();
            foreach (Mage::app()->getWebsites() as $website) {
                /* @var $website Mage_Core_Model_Website */
                if ($website->getBaseCurrencyCode() != $baseCurrency) {
                    $rate = Mage::getModel('directory/currency')
                        ->load($baseCurrency)
                        ->getRate($website->getBaseCurrencyCode());
                    if (!$rate) {
                        $rate = 1;
                    }
                    $this->_rates[$website->getId()] = array(
                        'code' => $website->getBaseCurrencyCode(),
                        'rate' => $rate
                    );
                } else {
                    $this->_rates[$website->getId()] = array(
                        'code' => $baseCurrency,
                        'rate' => 1
                    );
                }
            }
        }
        return $this->_rates;
    }

    public function validate($object)
    {
        $periods = $object->getData($this->getAttribute()->getName());
        if (empty($periods)) {
            return $this;
        }


        return $this;
    }

    public function afterSave($object)
    {
        $generalStoreId = $object->getStoreId();
        $periods = $object->getData($this->getAttribute()->getName());
        Mage::getResourceSingleton('payperrentals/reservationprices')->deleteByEntityId($object->getId(), $generalStoreId);
        if (is_null($periods)) {
            return $this;
        }

        if (is_array($periods)) {
            foreach ($periods as $k => $period) {

                if (!is_numeric($k)) continue;

                if (array_key_exists('use_default_value', $period) && $period['use_default_value']) {
                    $storeId = $generalStoreId;
                    $checkCollection = Mage::getModel('payperrentals/reservationprices')->getCollection()
                        ->addFieldToFilter('entity_id', $object->getId())
                        ->addFieldToFilter('store_id', $storeId)
                        ->addFieldToFilter('numberof', $period['numberof'])
                        ->addFieldToFilter('ptype', $period['ptype'])
                        ->addFieldToFilter('price', $period['price'])
                        ->addFieldToFilter('reservationpricesdates_id', $period['reservationpricesdates_id'])
                        ->addFieldToFilter('qty_start', $period['qtystart'])
                        ->addFieldToFilter('qty_end', $period['qtyend'])
                        ->addFieldToFilter('customers_group', $period['custgroup'])
                        ->addFieldToFilter('ptypeadditional', $period['priceadditional'])
                        ->addFieldToFilter('ptypeadditional', $period['ptypeadditional'])
                        ->addFieldToFilter('damage_waiver', $period['damage_waiver']);

                    if (count($checkCollection)) continue;
                } else {
                    $storeId = $generalStoreId;
                }

                $myRes = Mage::getModel('payperrentals/reservationprices')
                    ->setEntityId($object->getId())
                    ->setStoreId($storeId)
                    ->setNumberof($period['numberof'])
                    ->setPtype($period['ptype'])
                    ->setPrice($period['price'])
                    ->setQtyStart($period['qtystart'])
                    ->setQtyEnd($period['qtyend'])
                    ->setCustomersGroup($period['custgroup'])
                    ->setPtypeadditional($period['ptypeadditional'])
                    ->setPriceadditional($period['priceadditional'])
                    ->setDamageWaiver($period['damage_waiver'])
                    ->setReservationpricesdatesId($period['reservationpricesdates_id']);

                $myRes->save();
            }
        } elseif ($object->getIsDuplicate() == true) {
            $priceCollection = Mage::getModel('payperrentals/reservationprices')
                ->getCollection()
                ->addFieldToFilter('entity_id', $object->getOriginalId());
            foreach ($priceCollection AS $priceItem) {
                $priceItem
                    ->setId(null)
                    ->setEntityId($object->getId())
                    ->save();
            }
        }

        return $this;
    }


}
