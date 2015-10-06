<?php


/**
 * Class ITwebexperts_Payperrentals_Model_Product_Backend_Excludeddates
 */
class ITwebexperts_Payperrentals_Model_Product_Backend_Excludeddates extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract {

    /**
     * @return object
     */
    protected function _getResource() {
        return Mage::getResourceSingleton('payperrentals/excludeddates');
    }

    /**
     * @return mixed
     */
    protected function _getProduct() {
        return Mage::registry('product');
    }

    /**
     * @param Varien_Object $object
     * @return $this
     */
    public function validate($object) {

        $periods = $object->getData($this->getAttribute()->getName());
        if (empty($periods)) {
            return $this;
        }


        return $this;
    }


    /**
     * @param Varien_Object $object
     * @return $this
     */
    public function afterSave($object) {

        $generalStoreId = $object->getStoreId();

        $periods = $object->getData($this->getAttribute()->getName());


        Mage::getResourceSingleton('payperrentals/excludeddates')->deleteByProductId($object->getId(), $generalStoreId);
        if (is_null($periods)) {
            return $this;
        }
		if(is_array($periods)){
			foreach ($periods as $k=>$period) {
				if(!is_numeric($k)) continue;

				$storeId = @$period['use_default_value'] ? 0 : $object->getStoreId();

				$ex = Mage::getModel('payperrentals/excludeddates')
					->setProductId($object->getId())
					->setStoreId($storeId)
					->setDisabledFrom(ITwebexperts_Payperrentals_Helper_Date::toMysqlDate($period['excludefrom']))
					->setDisabledType($period['repeatperiod'])
                    ->setExcludeDatesFrom($period['excludedaysfrom'])
					->setDisabledTo(ITwebexperts_Payperrentals_Helper_Date::toMysqlDate($period['excludeto']))
					->save();
			}
		}
        return $this;
    }
}
