<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_Create_Changecustomer extends Mage_Adminhtml_Block_Template
{
    public static function getCollection(){
        $collection = Mage::getModel('customer/customer')
                ->getCollection()
                ->addAttributeToSelect(array('firstname', 'lastname'))
                ->addOrder('firstname', 'ASC')
                ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left');

        return $collection;
    }

    public static function getCustomersDropdown(){
        $collection = self::getCollection();
        if(!is_object($collection)){
            $collection = array();
        }
        $htmlDropdown = '<select id="select-new-customer">
        <option value="#" selected="selected">--Select Customer--</option>';

            foreach ($collection as $customer){
                $company = ($customer->getCompany()) ? ' (' . $customer->getCompany() . ')' : '';
                $htmlDropdown .= '<option value="'. $customer->getId().'">'. $customer->getFirstname() . ' ' . $customer->getLastname() . $company.'</option>';
            }
       $htmlDropdown .=  '</select>';
       return $htmlDropdown;
    }
}