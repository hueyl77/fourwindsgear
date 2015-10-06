<?php

/**
 * Class ITwebexperts_Payperrentals_Helper_Extend
 */
class ITwebexperts_Payperrentals_Helper_Extend extends Mage_Core_Helper_Abstract {

    public static function isExtensibleOrder($orderId){
        return count(self::getExtensibleProductsFromOrder($orderId)) > 0;
    }

    public static function getExtendHtml($orderId){

        $params = array(
            'order_id'   =>  $orderId
        );
        if(Mage::app()->getStore()->isAdmin()) {
            $extendControllerUrl = Mage::helper("adminhtml")->getUrl('payperrentals_admin/adminhtml_extend', $params);
        }else{
            $extendControllerUrl = Mage::app()->getStore()->getUrl('payperrentals_front/extend', $params);
        }
        $html = '<form name="formProducts" id="formProducts" action="'.$extendControllerUrl.'">';
        $html .= 'Extend Order To: '. '<input id="new_date" class="input-text required-entry" type="datetime" name="new_date"> <br />';
        $html .=  '<script type="text/javascript">'.
            '//<![CDATA[';
        if(Mage::app()->getStore()->isAdmin()){
            $html .= "var extendForm = new varienForm('formProducts', true);";
        }else{
            $html .= "var extendForm = new VarienForm('formProducts', true);";
        }
        $html .= '//]]>'.
        '</script>';
        $html .=
<<<END2

    <input type="checkbox" class="selectAll" name="select_all"/>Select/Deselect All products<br /><div id="extendProducts">
END2;

        $html .= self::getExtendProductsHtml($orderId);
        $html .= '</div>'.
<<<END2
    <button type="submit" class="button" title="Extend Order"><span><span>Extend Order</span></span></button>
    </form>
END2;

        return $html;
    }

    public static function getExtendProductsHtml($orderId, $date = null){

        $productsArr = self::getExtensibleProductsFromOrder($orderId, $date);
        $html = '';
        foreach($productsArr as $iProduct) {
            $html
                .= '<input type="checkbox" checked="checked" class="elemCheck" name="product[]" value="'.$iProduct['oId'].'"/>'.$iProduct['name'].'<br />';

        }
        return $html;
    }

    public static function getExtensibleProductsFromOrder($orderId, $date = null){
        $order = Mage::getModel('sales/order')->load($orderId);
        if(!is_null($date)){
            $date = ITwebexperts_Payperrentals_Helper_Date::toMysqlDate($date, true);
        }
        $productsArr = array();
        foreach ($order->getAllItems() as $_item) {
            if (is_object($_item->getOrderItem())) {
                $item = $_item->getOrderItem();
            } else {
                $item = $_item;
            }
            if ($item->getParentItem()) {
                continue;
            }
            if(is_null($date)){
                if(Mage::helper('payperrentals/config')->hasExtendEnabled($_item->getId(), $_item->getChildren())){
                    $productsArr[] = array(
                        'name' => $_item->getProduct()->getName(),
                        'oId' => $_item->getId(),
                    );
                }
            }else {
                if ($options = $item->getProductOptions()) {
                    if (isset($options['info_buyRequest'])) {
                        if (isset($options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION])) {
                            $start_date
                                = $options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION];
                            $end_date
                                = $options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION];
                            $diffSeconds = ITwebexperts_Payperrentals_Helper_Date::getDifferenceInSeconds($start_date, $date);
                            $maxLength = Mage::helper('payperrentals/config')->getMaximumExtensionLength();
                            $isExtendEnabled = Mage::helper('payperrentals/config')->hasExtendEnabled($_item->getProduct()->getId());
                            $isAvailable = (ITwebexperts_Payperrentals_Helper_Inventory::getQuantityForAnyProductTypeFromOptions($_item->getProduct()->getId(), $end_date, $date, $options['info_buyRequest']) > 0);
                            if($diffSeconds < $maxLength && strtotime($end_date) < strtotime($date) && $isExtendEnabled && $isAvailable){
                                $productsArr[] = array(
                                  'name' => $_item->getProduct()->getName(),
                                  'oId' => $_item->getId(),
                                );
                            }
                        }

                    }
                }
            }
        }
        return $productsArr;
    }

    /**
     * @return string
     */
    public static function getExtendUrl($orderId)
    {
        return Mage::getUrl('payperrentals_front/customer_extendorder/index', array('order_id' => $orderId));
    }

}