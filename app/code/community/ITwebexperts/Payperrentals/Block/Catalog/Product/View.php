<?php


class ITwebexperts_Payperrentals_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{

    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        if (!$this->getTemplate()) {
            $this->setTemplate('payperrentals/catalog/product/view.phtml');
        }
    }

    public function getCalendar(){
        return $this->getLayout()
            ->createBlock('payperrentals/html_calendar', 'my.front.calendar')
            ->setData(
                array(
                    'product' => $this->getProduct()
                )
            )
            ->toHtml();
    }


}