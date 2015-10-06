<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class ITwebexperts_Payperrentals_Model_Customer_Convert_Adapter_Customerqueue
    extends Mage_Customer_Model_Convert_Adapter_Customer
{

    /**
     * Save product (import)
     *
     * @param  array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
		parent::saveRow($importData);

		$customer = $this->getCustomerModel();
		$website = $this->getWebsiteByCode($importData['website']);

		if ($website === false) {
			$message = Mage::helper('payperrentals')->__('Skipping import row, website "%s" field does not exist.', $importData['website']);
			Mage::throwException($message);
		}
		if (empty($importData['email'])) {
			$message = Mage::helper('payperrentals')->__('Skipping import row, required field "%s" is not defined.', 'email');
			Mage::throwException($message);
		}

		$customer->setWebsiteId($website->getId())
				->loadByEmail($importData['email']);

			/*Import Rental queue*/
			try {
				$rentalqueueData = explode(';',$importData["rental_queue"]);
				$sendReturnData = explode(';',$importData["send_return"]);
				$customer->save();

				Mage::getResourceSingleton('payperrentals/rentalqueue')->deleteByCustomerId($customer->getId());
				Mage::getResourceSingleton('payperrentals/sendreturn')->deleteByCustomerId($customer->getId());
				$p = 0;
				foreach($rentalqueueData as $rentalQ){
					if(!empty($rentalQ)){
						$rentalData = explode('=', $rentalQ);
						$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$rentalData[0]);
						if(is_object($product)){
						$p++;
						$myRQ = Mage::getModel('payperrentals/rentalqueue')
								->setCustomerId($customer->getId())
								->setStoreId('0')
								->setProductId($product->getId())
								->setDateAdded($rentalData[1])
								->setSortOrder($p)
								->save();
					}
				}
				}

				$p = 0;
				foreach($sendReturnData as $sendQ){
					if(!empty($sendQ)){
						$rentalData = explode('=', $sendQ);
						$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$rentalData[0]);
						if(is_object($product)){
						$p++;

						$mySQ = Mage::getModel('payperrentals/sendreturn')
								->setCustomerId($customer->getId())
								->setStoreId('0')
								->setProductId($product->getId())
								->setDateAdded($rentalData[1])
								->setSendDate($rentalData[2])
								->setResStartdate('0000-00-00 00:00:00')
								->setResEnddate('0000-00-00 00:00:00')
								->setReturnDate('0000-00-00 00:00:00')
								->setQty(1)//here needs a check this should always be true
								->setSn($rentalData[3])
								->save();

						$myRQ = Mage::getModel('payperrentals/rentalqueue')
								->setCustomerId($customer->getId())
								->setStoreId('0')
								->setProductId($product->getId())
								->setDateAdded($rentalData[1])
								->setSortOrder($p)
								->setSendreturnId($mySQ->getId())
								->save();
					}
				}
				}



			}
			catch (Exception $e) {

			}

			/*End Import*/

        return true;
    }

}
