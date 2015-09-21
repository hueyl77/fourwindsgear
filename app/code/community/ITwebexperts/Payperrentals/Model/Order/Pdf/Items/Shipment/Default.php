<?php
class ITwebexperts_Payperrentals_Model_Order_Pdf_Items_Shipment_Default extends Mage_Sales_Model_Order_Pdf_Items_Shipment_Default
{
    /**
     * Draw item line
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();

        // draw Product name
        $name = array();
        $name[] = $item->getName();

        //get order and check if it has items with different dates
        $isSingle = ITwebexperts_Payperrentals_Helper_Data::isSingleOrder($order);
        if ($options = $item->getOrderItem()->getProductOptions()) {
            //$startDateLabel = $this->getItem()->getIsVirtual() ? $this->__("Subscription start:") : $this->__("First delivery:");
            if(isset($options['info_buyRequest'])) {
                if(isset($options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION])){
                    $start_date = $options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION];
                    $end_date =  $options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION];
                }
                if(!$isSingle['bool']){
                    if(isset($start_date)) {
                        $name[] = 'Start date: '.$start_date;
                    }
                    if(isset($end_date)) {
                        $name[] = 'End date: '.$end_date;
                    }
                }
            }
        }

        foreach($name as $j1 => $namen){
            if($order->getIsSingle() == 0 && $j1 > 0){
                $lines[$j1] = array(array(
                    'text' => Mage::helper('core/string')->str_split($namen, 60, true, true),
                    'feed' => 100,
                ));
            }elseif($j1 == 0){
                $lines[$j1] = array(array(
                    'text' => Mage::helper('core/string')->str_split($namen.($order->getIsSingle() == 1 ?'(reservation)':''), 60, true, true),
                    'feed' => 100,
                ));
            }
        }

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty()*1,
            'feed'  => 35
        );

        // draw SKU
        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 25),
            'feed'  => 565,
            'align' => 'right'
        );

        // Custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 110
                );

                // draw options value
                if ($option['value']) {
                    $_printValue = isset($option['print_value'])
                        ? $option['print_value']
                        : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                            'feed' => 115
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}
