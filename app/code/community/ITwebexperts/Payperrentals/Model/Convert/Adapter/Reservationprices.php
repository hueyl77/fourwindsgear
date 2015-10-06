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


class ITwebexperts_Payperrentals_Model_Convert_Adapter_Reservationprices
    extends Mage_Catalog_Model_Convert_Adapter_Product
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


		if (empty($importData['store'])) {
			if (!is_null($this->getBatchParams('store'))) {
				$store = $this->getStoreById($this->getBatchParams('store'));
			} else {
				$message = Mage::helper('payperrentals')->__('Skipping import row, required field "%s" is not defined.', 'store');
				Mage::throwException($message);
			}
		} else {
			$store = $this->getStoreByCode($importData['store']);
		}
		$storeId = $store->getId();
		$product = $this->getProductModel()->reset();
		//$product->setStoreId($storeId);
		$productId = $product->getIdBySku($importData['sku']);


		if ($productId) {
			$product->load($productId);
			/*Import Reservation prices*/
			try {
				$resPricesData = explode(';',$importData["resprices"]);

				$stockData = $product->getStockData();
				$stockData['qty'] = $importData['payperrentals_quantity'];
				$stockData['is_in_stock'] = 1;
				$stockData['manage_stock'] = 0;
				$stockData['use_config_manage_stock'] = 0;
				$product->setStockData($stockData);
				$product->setIsMassupdate( true );
				$product->setExcludeUrlRewrite( true );
				$product->save();
				Mage::getResourceSingleton('payperrentals/reservationprices')->deleteByEntityId($product->getId());
				foreach($resPricesData as $resPrice){
					if(!empty($resPrice)){
						$priceData = explode('=', $resPrice);
						$periodType = ITwebexperts_Payperrentals_Model_Product_Periodtype::MINUTES;
						switch($priceData[1]){
							case 'Minute':
								$periodType = ITwebexperts_Payperrentals_Model_Product_Periodtype::MINUTES;
								break;
							case 'Hour':
								$periodType = ITwebexperts_Payperrentals_Model_Product_Periodtype::HOURS;
								break;
							case 'Day':
								$periodType = ITwebexperts_Payperrentals_Model_Product_Periodtype::DAYS;
								break;
							case 'Week':
								$periodType = ITwebexperts_Payperrentals_Model_Product_Periodtype::WEEKS;
								break;
							case 'Month':
								$periodType = ITwebexperts_Payperrentals_Model_Product_Periodtype::MONTHS;
								break;
							case 'Year':
								$periodType = ITwebexperts_Payperrentals_Model_Product_Periodtype::YEARS;
								break;
						}
						$myRes = Mage::getModel('payperrentals/reservationprices')
								->setEntityId($product->getId())
								->setStoreId($storeId)
								->setNumberof($priceData[0])
								->setPtype($periodType)
								->setPrice($priceData[2])
								->setQtyStart($priceData[3])
								->setQtyEnd($priceData[4])
								->setDateFrom($priceData[5])
								->setDateTo($priceData[6])
								->setCustomersGroup($priceData[7])
								->save();
					}
				}

			}
			catch (Exception $e) {

			}

			//import sn numbers
			try{
				$resSnData = explode(';',$importData["snppr"]);
				Mage::getResourceSingleton('payperrentals/serialnumbers')->deleteByEntityId($product->getId());
				$p = $product->getPayperrentalsQuantity();
				foreach($resSnData as $resSn){
					if(!empty($resSn)){
						$snData = explode('=', $resSn);
						if(!isset($snData[1]) || empty($snData[1])){
							$snData[1] = 'A';
						}
						$myRes = Mage::getModel('payperrentals/serialnumbers')
								->setEntityId($product->getId())
								->setSn($snData[0])
								->setStatus($snData[1])
								->save();
						$p--;
						if($p <= 0){
							break;
						}
					}
				}
			}catch(Exception $e){

			}
		}
			/*End Import*/

        return true;
    }

}
