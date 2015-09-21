<?php
if(ITwebexperts_Payperrentals_Helper_Data::isFoomanInstalled()){
    class ITwebexperts_Payperrentals_Model_Order_Pdf_Creditmemo extends Fooman_PdfCustomiser_Model_Creditmemo
    {
    }
}else {
class ITwebexperts_Payperrentals_Model_Order_Pdf_Creditmemo extends Mage_Sales_Model_Order_Pdf_Creditmemo
{
    /**
     * Return PDF document
     *
     * @param  array $creditmemos
         *
     * @return Zend_Pdf
     */
    public function getPdf($creditmemos = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('creditmemo');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($creditmemos as $creditmemo) {
            if ($creditmemo->getStoreId()) {
                Mage::app()->getLocale()->emulate($creditmemo->getStoreId());
                Mage::app()->setCurrentStore($creditmemo->getStoreId());
            }
            $page  = $this->newPage();
            $order = $creditmemo->getOrder();
            /* Add image */
            $this->insertLogo($page, $creditmemo->getStore());
            /* Add address */
            $this->insertAddress($page, $creditmemo->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Credit Memo # ') . $creditmemo->getIncrementId()
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
            /* Add table head */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($creditmemo->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $creditmemo);
        }
        $this->_afterGetPdf();
        if ($creditmemo->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }

}
}