<?php
class ITwebexperts_Payperrentals_Helper_Sso extends Mage_Core_Helper_Abstract {

    /**
     *
     */
    const XML_PATH_USE_SSO = 'payperrentals/appearance_sso/use_sso';
    /**
     *
     */
    const XML_PATH_SSO_ENCRYPTION_KEY = 'payperrentals/appearance_sso/sso_encryption_key';
    /**
     *
     */
    const XML_PATH_SSO_RENT_LINK = 'payperrentals/appearance_sso/sso_rent_link';

    /**
     * Function to check if sso is enabled
     * @return bool
     */
    public static function useSSO()
    {
        if (Mage::getStoreConfig(self::XML_PATH_USE_SSO) != 0) {
            return true;
        }
        return false;
    }

    /**
     * Function used for sso to see if member allows renting
     * @return bool
     */
    public static function isAllowedRenting()
    {

        if (!self::useSSO()) return true;

        $customer = Mage::helper('customer')->getCustomer();

        $client = new SoapClient(Mage::getStoreConfig(self::XML_PATH_SSO_RENT_LINK) . "?WSDL");
        $strRental = $client->GetRentalActive(array('dealerCode' => $customer->getUsername()))->GetRentalActiveResult;

        return ($strRental == 1) ? true : false;
    }

}