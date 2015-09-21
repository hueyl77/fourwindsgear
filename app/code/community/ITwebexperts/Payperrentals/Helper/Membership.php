<?php
class ITwebexperts_Payperrentals_Helper_Membership extends Mage_Core_Helper_Abstract {


    /**
     * Function to return the number of items the customer is allowed to add to queue for the bought membership
     * @param $customerId
     * @return int|string
     */
    public function getNumberAllowedForMembership($customerId)
    {
        $numbermanual = intval($this->getNumberAllowedForMembershipManual($customerId));
        if($numbermanual != 0){
            return $numbermanual;
        }
        $collection = Mage::getResourceModel('sales/recurring_profile_collection')
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('state', 'active');
        $numberAllowed = 0;
        foreach ($collection as $coll) {
            $itemSerialized = unserialize($coll->getOrderItemInfo());
            if ($itemSerialized['product_type'] == 'membershippackage') { //here I check if productssku contains $itemserialize['sku']
                $infoBuyRequest = unserialize($itemSerialized['info_buyRequest']);
                $prod = Mage::getModel('catalog/product')->load($infoBuyRequest['product']);
                if ($prod) {
                    $numberAllowed += intval($prod->getNumberItems());
                }
            }
        }
        return $numberAllowed;
    }

    /**
     * Function to return the number of items the customer is allowed to add to queue for memberships manually
     * set on edit customer page
     * @param $customerId
     * @return int|string
     */
    public function getNumberAllowedForMembershipManual($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $enabled = $customer->getMembershippackageEnabled();
        if($enabled){
            $membershipid = $customer->getMembershippackageName();;
            $prod = Mage::getModel('catalog/product')->load($membershipid);
            if($prod){
                return $prod->getNumberItems();
            }
        }
        return 0;
    }


    /**
     * Function used to add product to queue
     * @param Mage_Catalog_Model_Product $product
     * @param Varien_Object $buyRequest
     * @param bool $isGroup
     * @return string
     */
    public function addProductToQueue($product, Varien_Object $buyRequest, $isGroup = false)
    {
        if ($this->hasMembership($product->getId())) {
            if ($isGroup) {
                $success = false;
                $url = $product->getProductUrl();
                foreach ($product->getTypeInstance(true)->getAssociatedProducts($product) as $iProduct) {
                    $collItem2 = Mage::getModel('payperrentals/rentalqueue')
                        ->getCollection()
                        ->addCustomerIdFilter(Mage::getSingleton('customer/session')->getCustomerId())
                        ->addSelectFilter('sendreturn_id="0"')
                        ->addProductIdFilter($iProduct->getId());
                    if ($collItem2->getSize() > 0) {
                        Mage::getSingleton('core/session')->addError(Mage::helper('payperrentals')->__('Product') . ' ' . $iProduct->getName() . ' ' . Mage::helper('payperrentals')->__('is already in queue'));
                        continue;
                    }

                    $collItem = Mage::getModel('payperrentals/rentalqueue')
                        ->getCollection()
                        ->addCustomerIdFilter(Mage::getSingleton('customer/session')->getCustomerId())
                        ->addSelectFilter('sendreturn_id="0"')
                        ->addOrderFilter('sort_order DESC');
                    if ($collItem->getSize() > 0) {
                        $sortMax = $collItem->getFirstItem()->getSortOrder() + 1;
                    } else {
                        $sortMax = 0;
                    }

                    /** @var $ex ITwebexperts_Payperrentals_Model_Rentalqueue */
                    $ex = Mage::getModel('payperrentals/rentalqueue')
                        ->setProductId($iProduct->getId())
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->setSortOrder($sortMax)
                        ->setDateAdded(date('Y-m-d H:i:s'))
                        ->setCustomOptions(serialize($buyRequest));

                    try {
                        $ex->save();
                    } catch (Exception $_e) {
                        Mage::logException($_e);
                    }

                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('payperrentals')->__('Product') . ' ' . $iProduct->getName() . ' ' . Mage::helper('payperrentals')->__('was added to queue'));
                    $success = true;
                }

                if ($success) {
                    $url = Mage::getUrl('payperrentals_front/customer_rentalqueue/index');
                }

                Mage::app()->getFrontController()->getResponse()->setRedirect($url)->sendResponse();
                exit;
            } else {
                $url = Mage::getUrl('payperrentals_front/customer_rentalqueue/index');
                $collItem2 = Mage::getModel('payperrentals/rentalqueue')
                    ->getCollection()
                    ->addCustomerIdFilter(Mage::getSingleton('customer/session')->getCustomerId())
                    ->addSelectFilter('sendreturn_id="0"')
                    ->addProductIdFilter($product->getId());
                if ($collItem2->getSize() > 0) {
                    Mage::getSingleton('core/session')->addError(Mage::helper('payperrentals')->__('Product is already in queue'));

                    $grouped_product_model = Mage::getModel('catalog/product_type_grouped');
                    $groupedParentId = $grouped_product_model->getParentIdsByChild($product->getId());
                    //todo check $groupedParentId[0] to be reservation product although in 99% will be. add checking type for parents and redirect if parent is reservation
                    if (isset($groupedParentId[0])) {
                        $product = Mage::getModel('catalog/product')->load($groupedParentId[0]);
                    }

                    Mage::app()->getFrontController()->getResponse()->setRedirect($product->getProductUrl())->sendResponse();
                    exit;
                }

                $collItem = Mage::getModel('payperrentals/rentalqueue')
                    ->getCollection()
                    ->addCustomerIdFilter(Mage::getSingleton('customer/session')->getCustomerId())
                    ->addSelectFilter('sendreturn_id="0"')
                    ->addOrderFilter('sort_order DESC');
                if ($collItem->getSize() > 0) {
                    $sortMax = $collItem->getFirstItem()->getSortOrder() + 1;
                } else {
                    $sortMax = 0;
                }

                /** @var $ex ITwebexperts_Payperrentals_Model_Rentalqueue */
                $ex = Mage::getModel('payperrentals/rentalqueue')
                    ->setProductId($product->getId())
                    ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->setSortOrder($sortMax)
                    ->setDateAdded(date('Y-m-d H:i:s'))
                    ->setCustomOptions(serialize($buyRequest));
                try {
                    $ex->save();
                } catch (Exception $_e) {
                    Mage::logException($_e);
                }

                Mage::getSingleton('core/session')->addSuccess(Mage::helper('payperrentals')->__('Product was added to queue'));
                Mage::app()->getFrontController()->getResponse()->setRedirect($url)->sendResponse();
                exit;
            }
        } else {
            Mage::getSingleton('core/session')->addError(Mage::helper('payperrentals')->__('You do not have a rental membership for this product or you are not logged in. Please either login ') . '<a href="' . Mage::getUrl('customer/account/login') . '">' . Mage::helper('payperrentals')->__('here') . '</a>, ' . Mage::helper('payperrentals')->__('or purchase a rental membership below.'));
            $_categoryId = Mage::helper('payperrentals/config')->getMembershipCategoryId(Mage::app()->getStore()->getId());
            if ($_categoryId) {
                $_category = Mage::getModel('catalog/category')->load($_categoryId);
                /** @method Mage_Catalog_Model_Category getUrl() */
                /** @var $_category Mage_Catalog_Model_Category */
                $_url = $_category->getUrl();
                Mage::app()->getFrontController()->getResponse()->setRedirect($_url)->sendResponse();
                exit;
            }
            return '';
        }
    }

    /**
     * @param $customer_id
     * @return bool
     */
    public function hasEmptyQueue($customer_id)
    {
        return false;
    }

    /**
     * Checks if membership sku is allowed to rent the product
     *
     * @param $product
     */

    public function membershipAllowsProduct($productId,$membershipSku){
            $_resExcludedMembership = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'res_excluded_membership');
            $disabledMemberships = explode(',', $_resExcludedMembership);
            if(!in_array($membershipSku,$disabledMemberships)){
                return true;
            } else {
                return false;
            }
        }



    /**
     * Checks if a customer has a manual membership
     *
     * @param $customer
     * @return bool
     */

    public function isManualMembership($customer){
        if(($customer->getMembershippackageEnabled() != 0) && ($customer->getMembershippackageName() != null)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function which return true if logged in customer has a membership
     * which allows him to add to queue the productId
     * @param $productId
     * @return bool
     */
    public function hasMembership($productId)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customerId);

        if($this->isManualMembership($customer)){
            $membershipSku = Mage::getModel('catalog/product')->load($customer->getMembershippackageName())->getSku();
            if($this->membershipAllowsProduct($productId,$membershipSku)){
                return true;
            }
        }

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $collection = $this->getActiveMemberships($customerId);
            foreach ($collection as $coll) {
                $itemSerialized = unserialize($coll->getOrderItemInfo());
                if ($itemSerialized['product_type'] == 'membershippackage' && $this->membershipAllowsProduct($productId,$itemSerialized['sku'])) {
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }

    /**
     * Gets active memberships collection
     *
     * @param $customerId
     * @return Mage_Sales_Model_Resource_Recurring_Profile_Collection
     */

    public function getActiveMemberships($customerId){
        return Mage::getResourceModel('sales/recurring_profile_collection')
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('state', 'active');
    }



    /**
     * Function to return the order which the customer bought the membership package
     * @param $customerId
     * @return int
     */
    public static function getOrderFromCustomer($customerId)
    {
        $collection = Mage::getResourceModel('sales/recurring_profile_collection')
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('state', 'active');

        foreach ($collection as $coll) {
            $itemSerialized = unserialize($coll->getOrderItemInfo());
            $orderSerialized = unserialize($coll->getOrderInfo());
            if ($itemSerialized['product_type'] == 'membershippackage') { //here I check if productssku contains $itemserialize['sku']
                return $orderSerialized['orig_order_id'];
            }
        }
        return 0;
    }

}