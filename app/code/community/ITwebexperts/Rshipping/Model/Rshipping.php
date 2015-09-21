<?php

/**
 * Shipping Methods Model
 *
 * @method int getRshippingId()
 * @method string getShippingTitle()
 * @method string setShippingTitle(string $_title)
 * @method string getShippingMethod()
 * @method string setShippingMethod(string $_method)
 * @method int getUseCustomShippingAmount()
 * @method int setUseCustomShippingAmount(int $_useAmount)
 * @method float getShippingAmount()
 * @method float setShippingAmount(float $_amount)
 * @method string getTurnoverBeforePeriod()
 * @method string setTurnoverBeforePeriod(string $_turnoverBeforePeriod)
 * @method int getTurnoverBeforeType()
 * @method int setTurnoverBeforeType(int $_turnoverBeforeType)
 * @method string getTurnoverAfterPeriod()
 * @method string setTurnoverAfterPeriod(string $_turnoverAfterPeriod)
 * @method int getTurnoverAfterType()
 * @method int setTurnoverAfterType(int $_turnoverAfterType)
 * @method int getStatus()
 * @method int setStatus(int $_status)
 * @method string getCreatedTime()
 * @method string setCreatedTime(string $_createTime)
 * @method string getUpdatedTime()
 * @method string setUpdateTime(string $_updateTime)
 * @method string getMinRentalPeriod()
 * @method string setMinRentalPeriod(string $_minRentalPeriod)
 * @method int getMinRentalType()
 * @method int setMinRentalType(int $_minRentalType)
 * @method string getStartDisabledDays()
 * @method string setStartDisabledDays(string $_disabledDays)
 * @method string getEndDisabledDays()
 * @method string setEndDisabledDays(string $_disabledDays)
 * @method string getCutoffTime()
 * @method string setCutoffTime(string $_cutoffTime)
 * @method int getUseLiveUpsApi()
 * @method int setUseLiveUpsApi(int $_useUpsApi)
 * @method string getIgnoreTurnoverDay()
 * @method string setIgnoreTurnoverDay(string $_ignoredDays)
 * @method int getIsLocalPickup()
 * @method int setIsLocalPickup(int $_isLocal)
 * @method int getIsDefaultMethod()
 * @method int setIsDefaultMethod(int $_isDefault)
 * @method int getCustomShippingAmountType()
 * @method int setCustomShippingAmountType(int $shippingAmountType)
 *
 * @category   Shipping Extension
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2014
 *
 */
class ITwebexperts_Rshipping_Model_Rshipping extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rshipping/rshipping');
    }
}