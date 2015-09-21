<?php
if(ITwebexperts_Payperrentals_Helper_Data::isFoomanInstalled()){
    class ITwebexperts_Payperrentals_Model_Order_Pdf_Shipment extends Fooman_PdfCustomiser_Model_Shipment
    {
    }
}else {
class ITwebexperts_Payperrentals_Model_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment
{
    /**
     * Return PDF document
     *
     * @param  array $shipments
         *
     * @return Zend_Pdf
     */
    public function getPdf($shipments = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
                Mage::app()->setCurrentStore($shipment->getStoreId());
            }
            $page  = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId()
            );
            $isSingle = ITwebexperts_Payperrentals_Helper_Data::isSingleOrder($order);
            if($isSingle['bool']){
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                    $page->drawText(
                        Mage::helper('payperrentals')->__('Start Date:')
                . ' ' . $isSingle['start_date']
                . ' ' . Mage::helper('payperrentals')->__('End Date:')
                        . ' ' . $isSingle['end_date'], 25, $this->y, 'UTF-8'
                    );
                $this->y -=10;
            }
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }
        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }

}
}