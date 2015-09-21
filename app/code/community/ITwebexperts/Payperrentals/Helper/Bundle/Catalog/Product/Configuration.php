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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper for fetching properties by product configurational item
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class ITwebexperts_Payperrentals_Helper_Bundle_Catalog_Product_Configuration extends Mage_Bundle_Helper_Catalog_Product_Configuration
{
    /**
     * Get selection quantity
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $selectionId
     * @return decimal
     */
    public function getSelectionQty($product, $selectionId)
    {
        $selectionQty = $product->getCustomOption('selection_qty_' . $selectionId);
        if ($selectionQty) {
            return $selectionQty->getValue();
        }
        return 0;
    }

    /**
     * Obtain final price of selection in a bundle product
     *
     * @param Mage_Catalog_Model_Product_Configuration_Item_Interface $item
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @return decimal
     */
    public function getSelectionFinalPrice(Mage_Catalog_Model_Product_Configuration_Item_Interface $item,
        $selectionProduct)
    {
        $selectionProduct->unsetData('final_price');
        return $item->getProduct()->getPriceModel()->getSelectionFinalTotalPrice(
            $item->getProduct(),
            $selectionProduct,
            $item->getQty() * 1,
            $this->getSelectionQty($item->getProduct(), $selectionProduct->getSelectionId()),
            false,
            true
        );
    }

    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @return array
     */
    public function getBundleOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $options = array();
        $product = $item->getProduct();

        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $product->getTypeInstance(true);

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption ? unserialize($optionsQuoteItemOption->getValue()) : array();
        if ($bundleOptionsIds) {
            /**
            * @var Mage_Bundle_Model_Mysql4_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $selectionsCollection = $typeInstance->getSelectionsByIds(
                unserialize($selectionsQuoteItemOption->getValue()),
                $product
            );

            $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            foreach ($bundleOptions as $bundleOption) {
                if ($bundleOption->getSelections()) {
                    $option = array(
                        'label' => $bundleOption->getTitle(),
                        'value' => array()
                    );

                    $bundleSelections = $bundleOption->getSelections();

                    foreach ($bundleSelections as $bundleSelection) {
                        $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                        if ($qty) {
                            $val = $qty . ' x ' . $this->escapeHtml($bundleSelection->getName())
                                . ' ';

							if($bundleSelection->getTypeId() == 'reservation'){
								if($product->getBundlePricingtype() == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_PERPRODUCT){
									$customerGroup = ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup();
									if(!is_object($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION))){
										$source = unserialize($product->getCustomOption('info_buyRequest')->getValue());
										if(isset($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION])){
											$start_date = $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION];
											$end_date = $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION];
										}
									}
									else{
										$start_date = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION)->getValue();
										$end_date = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION)->getValue();
									}

                                    $data = new Varien_Object(array(
                                        'start_date' => $start_date,
                                        'end_date' => $end_date,
                                    ));

                                    Mage::dispatchEvent('payperrentals_bundle_option_calculate_price_before', array(
                                        'data' => $data,
                                        'item' => $item,
                                        'selection' => $bundleSelection,
                                    ));

                                    extract($data->getData(), EXTR_OVERWRITE);

                                    $priceAmount = ITwebexperts_Payperrentals_Helper_Price::calculatePrice($bundleSelection, $start_date, $end_date, $qty, $customerGroup);
									$val .= Mage::helper('core')->currency($priceAmount);
								}else{

								}
							}else{
								$val .= Mage::helper('core')->currency($this->getSelectionFinalPrice($item, $bundleSelection));
							}

							$option['value'][] = $val;
                        }
                    }

                    if ($option['value']) {
                        $options[] = $option;
                    }
                }
            }
        }

        return $options;
    }


}
