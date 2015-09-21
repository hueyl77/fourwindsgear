<?php
/**
 * Class ITwebexperts_Payperrentals_SsoController
 * used when someone from the parent site logs in
 */
class ITwebexperts_Payperrentals_SsoController extends Mage_Core_Controller_Front_Action
{

    /**
     * Function used to decypt values in sso
     * @param $encrypt_value
     * @return string
     */
    private function decrypt($encrypt_value)
    {

        $concatString = '';
        $decrypt_key = Mage::getStoreConfig(self::XML_PATH_SSO_ENCRYPTION_KEY);
        $keyArray = array();
        $nrDecryptedKey = strlen($decrypt_key);
        for ($i = 0; $i < $nrDecryptedKey; $i++) {
            $keyLetter = substr($decrypt_key, $i, 1);
            $keyArray[$i] = ord($keyLetter);
        }

        $p = 0;
        $lenStr = strlen($encrypt_value);
        for ($x = 0; $x < $lenStr; $x++) {
            $ascValue = substr($encrypt_value, $x, 3);
            if ($p >= strlen($decrypt_key)) {
                $p = 0;
            }
            $keyResult = $ascValue - $keyArray[$p];
            $concatString = $concatString . chr($keyResult);

            $x = $x + 2;
            $p = $p + 1;
        }

        $leftRemove = intval(substr($concatString, 0, 1));
        $rightRemove = intval(substr($concatString, 1, 2));
        $decryptValue = substr($concatString, 2 + $leftRemove);
        $decryptValue = substr($decryptValue, 0, strlen($decryptValue) - $rightRemove);
        return $decryptValue;
    }

	/**
	 * @return bool
	 */
	public function indexAction()
	{

		$dealerNumber = $this->decrypt($this->getRequest()->getParam('dealer_number'));
		$dealerName = $this->decrypt($this->getRequest()->getParam('dealer_name'));
		$dealerCountry = $this->decrypt($this->getRequest()->getParam('dealer_country'));
		$dealerAddress = $this->decrypt($this->getRequest()->getParam('address'));
		$dealerCity = $this->decrypt($this->getRequest()->getParam('city'));
		$dealerState = $this->decrypt($this->getRequest()->getParam('state'));
		$dealerZip = $this->decrypt($this->getRequest()->getParam('zipcode'));
		$dealerPhone = $this->decrypt($this->getRequest()->getParam('phone'));
		$firstName = $this->decrypt($this->getRequest()->getParam('first_name'));
		$lastName = $this->decrypt($this->getRequest()->getParam('last_name'));
		$emailAddress = $this->decrypt($this->getRequest()->getParam('email_address'));


		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
		$customer->loadByEmail($emailAddress);
		/*
		* Check if the email exist on the system.
		* If YES,  it will not create a user account.
		*/

		if(!$customer->getId()) {

			//setting data such as email, firstname, lastname, and password

			$customer->setEmail($emailAddress);
			$customer->setUsername($dealerNumber);
			$customer_group = new Mage_Customer_Model_Group();
			$allGroups  = $customer_group->getCollection()->toOptionHash();
			if($dealerCountry == 'USA'){
				foreach($allGroups as $key=>$allGroup){
					if(strtolower($allGroup) == 'usa' ){
						$customer->setData( 'group_id', $key);
						break;
					}
				}
			}else if($dealerCountry == 'Canada'){
				foreach($allGroups as $key=>$allGroup){
					if(strtolower($allGroup) == 'canada' ){
						$customer->setData( 'group_id', $key);
						break;
					}
				}
			}else{
				foreach($allGroups as $key=>$allGroup){
					if(strtolower($allGroup) == 'europe' ){
						$customer->setData( 'group_id', $key);
						break;
					}
				}
			}
			$customer->setFirstname($firstName);
			$customer->setLastname($lastName);
			$customer->setPassword($customer->generatePassword(8));

		}
		try{
			//the save the data and send the new account email.
			$customer->save();
			$customer->setConfirmation(null);
			$customer->save();
			//$customer->sendNewAccountEmail();
		}

		catch(Exception $ex){
		  return false;
		}
		if($dealerCountry == "USA"){
			$dealerCountry = 'United States';
		}else{
			$dealerCountry = 'Canada';
		}
		$collection = Mage::getModel('directory/country')->getCollection();
		foreach ($collection as $country){
			$cname = $country->getName();
			if($cname == $dealerCountry){
				$cid = $country->getId();
				//$ccode = $country->getCode();
				break;
			}
		}
		$regionModel = Mage::getModel('directory/region')->loadByCode($dealerState, $cid);
		$regionId = $regionModel->getId();
		//$addresses = Mage::getModel("customer/address")->getCollection();
		if(count($customer->getAddresses()) == 0){
			$address = Mage::getModel("customer/address");
			$address->setCustomerId($customer->getId());
			$address->setFirstname($customer->getFirstname());
			$address->setLastname($customer->getLastname());
			$address->setCountryId($cid); //Country code here
			$address->setStreet($dealerAddress);
			$address->setPostcode($dealerZip);
			$address->setCity($dealerCity);
			$address->setRegionId($regionId);
			$address->setTelephone($dealerPhone);
			$address->setIsDefaultShipping(true);
			$address->setIsDefaultBilling(true);
			$address->save();
		}
		$session = Mage::getSingleton( 'customer/session' );

		try
		{
			//echo 'fff'.$customer->getEmail().'----'.$customer->getPassword();
			$session->loginById( $customer->getId());
			$session->setCustomerAsLoggedIn( $session->getCustomer() );
		}
		catch( Exception $e )
		{
			return false;
		}
		Mage::app()->getResponse()->setRedirect(Mage::getUrl('customer/account/index'));
		return true;
	}
}
