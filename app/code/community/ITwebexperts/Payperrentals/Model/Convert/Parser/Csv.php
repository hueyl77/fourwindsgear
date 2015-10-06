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
 * @package     Mage_Dataflow
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert csv parser
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ITwebexperts_Payperrentals_Model_Convert_Parser_Csv extends Mage_Dataflow_Model_Convert_Parser_Csv
{

	/**
	 * Read data collection and write to temporary file
	 *
	 * @return Mage_Dataflow_Model_Convert_Parser_Csv
	 */
	public function unparse()
	{
		$batchExport = $this->getBatchExportModel()
				->setBatchId($this->getBatchModel()->getId());
		$fieldList = $this->getBatchModel()->getFieldList();
		/*PPR export*/
		$fieldList['resprices'] = 'resprices';
		$fieldList['snppr'] = 'snppr';
		/*PPR Export*/
		$batchExportIds = $batchExport->getIdCollection();

		$io = $this->getBatchModel()->getIoAdapter();
		$io->open();

		if (!$batchExportIds) {
			$io->write("");
			$io->close();
			return $this;
		}

		if ($this->getVar('fieldnames')) {
			$csvData = $this->getCsvString($fieldList);
			$io->write($csvData);
		}

		foreach ($batchExportIds as $batchExportId) {
			$csvData = array();
			$batchExport->load($batchExportId);
			$row = $batchExport->getBatchData();

			/* PPR Export start*/
			$productid = Mage::getModel('catalog/product')->getIdBySku($row['sku']);

			$collectionPrices = Mage::getModel('payperrentals/reservationprices')
					->getCollection()
					->addEntityStoreFilter($productid, $row['store_id']);

			$resprices = array();
			foreach ($collectionPrices as $item) {
				$periodType = '';
				switch($item->getPtype()){
					case ITwebexperts_Payperrentals_Model_Product_Periodtype::MINUTES:
						$periodType = 'Minute';
						break;
					case ITwebexperts_Payperrentals_Model_Product_Periodtype::HOURS:
						$periodType = 'Hour';
						break;
					case ITwebexperts_Payperrentals_Model_Product_Periodtype::DAYS:
						$periodType = 'Day';
						break;
					case ITwebexperts_Payperrentals_Model_Product_Periodtype::WEEKS:
						$periodType = 'Week';
						break;
					case ITwebexperts_Payperrentals_Model_Product_Periodtype::MONTHS:
						$periodType = 'Month';
						break;
					case ITwebexperts_Payperrentals_Model_Product_Periodtype::YEARS:
						$periodType = 'Year';
						break;
				}
				$resprices[] = $item->getNumberof().'='.$periodType.'='.$item->getPrice().'='.$item->getQtyStart().'='.$item->getQtyEnd().'='.$item->getDateFrom().'='.$item->getDateTo().'='.$item->getCustomersGroup();
			}

			$row['resprices'] = implode(';',$resprices);

			//add serial number to export.
			$coll3 = Mage::getModel('payperrentals/serialnumbers')
					->getCollection()
					->addEntityIdFilter($productid)
			;

			$snppr = array();
			foreach ($coll3 as $item) {
				$snppr[] = $item->sn.'='.$item->status;
			}

			$row['snppr'] = implode(';',$snppr);

			 /*PPR Export finish*/

			foreach ($fieldList as $field) {
				$csvData[] = isset($row[$field]) ? $row[$field] : '';
			}
			$csvData = $this->getCsvString($csvData);
			$io->write($csvData);
		}

		$io->close();

		return $this;
	}
}
