<?php
/**
 *
 * @author Enrique Piatti
 */
class ITwebexperts_Payperrentals_GlobaldatesController extends Mage_Core_Controller_Front_Action
{

    public function updateAction()
    {
        $params = ITwebexperts_Payperrentals_Helper_Date::filterDates($this->getRequest()->getParams());
        $startDate = array_key_exists('start_date', $params) ? $params['start_date'] : null;
        $endDate = array_key_exists('end_date', $params) ? $params['end_date'] : null;
        if ($startDate && $endDate) {
            $checkoutSession = Mage::getSingleton('checkout/session');
            try {
                ITwebexperts_Payperrentals_Helper_Data::updateCurrentGlobalDates($startDate, $endDate);
                $checkoutSession->addSuccess($this->__('Global Date was updated'));
            } catch (Mage_Core_Exception $e) {
                if ($checkoutSession->getUseNotice(true)) {
                    $checkoutSession->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $checkoutSession->addError(Mage::helper('core')->escapeHtml($message));
                    }
                }
            } catch (Exception $e) {
                $checkoutSession->addException($e, $this->__('Cannot add the item to shopping cart.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('checkout/cart');
    }
}
