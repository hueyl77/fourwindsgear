<?php

class ITwebexperts_Rshipping_Model_System_Config_Source_Shipping_Allmethods
{
    /**
     * Return array of carriers.
     * If $isActiveOnlyFlag is set to true, will return only active carriers
     *
     * @param bool $isActiveOnlyFlag
     * @return array
     */
    public function toOptionArray($isActiveOnlyFlag = false)
    {
        $methods = array(array('value' => '', 'label' => 'Please select shipping methods'));
        $carriers = Mage::getModel('shipping/config')->getAllCarriers();
        foreach ($carriers as $carrierCode => $carrierModel) {
            if (!$carrierModel || ((bool)$isActiveOnlyFlag === true && !$carrierModel->isActive())) {
                continue;
            }
            $carrierMethods = $carrierModel->getAllowedMethods();
            if (!$carrierMethods) {
                continue;
            }
            $carrierTitle = Mage::getStoreConfig('carriers/' . $carrierCode . '/title');
            $methods[$carrierCode] = array(
                'label' => $carrierTitle,
                'value' => array(),
            );
            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $methods[$carrierCode]['value'][] = array(
                    'value' => $carrierCode . '_' . $methodCode,
                    'label' => '[' . $carrierCode . '] ' . $methodTitle,
                );
            }
        }

        return $methods;
    }
}
