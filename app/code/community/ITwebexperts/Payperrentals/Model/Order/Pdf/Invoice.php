<?php

/**
 * Inchoo PDF rewrite to add products images
 * Original: Sales Order Invoice PDF model
 *
 * @category   Inchoo
 * @package    Inhoo_Invoice
 * @author     Mladen Lotar - Inchoo <mladen.lotar@inchoo.net>
 */
if(ITwebexperts_Payperrentals_Helper_Data::isFoomanInstalled()){
    class ITwebexperts_Payperrentals_Model_Order_Pdf_Invoice extends Fooman_PdfCustomiser_Model_Invoice
    {

    }
}else{
class ITwebexperts_Payperrentals_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $page  = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
            );

            $isSingle = ITwebexperts_Payperrentals_Helper_Data::isSingleOrder($order);
            if($isSingle['bool']){
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                $page->drawText(Mage::helper('payperrentals')->__('Start Date:')
                    . ' ' . $isSingle['start_date']
                    . ' ' . Mage::helper('payperrentals')->__('End Date:')
                    . ' ' . $isSingle['end_date'], 25, $this->y, 'UTF-8');
                $this->y -=10;
            }
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

}
}