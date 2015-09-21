<?php
/**
 * @category   Itwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */

class ITwebexperts_Rshipping_Block_Adminhtml_Rshipping extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_rshipping';
        $this->_blockGroup = 'rshipping';
        $this->_headerText = Mage::helper('rshipping')->__('Rental Shipping Methods').'<br/><span style="font-size:11px;color:#000000">Reservation shipping is for using turnover time with ship methods. So say you have a 2 day ship method, a 5 day ship method, etc and you want to dynamically add to the calendar reservation turnover time (padding) based on the ship method, rather than using a global turnover time. That is what this setting is for.
If you use Reservation Ship Methods – your customer will see on the product page a zip code entry field to get the shipping quote and ship method turnover time.
You can add your ship methods under Rentals > Reservation Ship Methods. You will need to make sure that the ship methods you use are also enabled in System >config> ship methods.
</span>';
        $this->_addButtonLabel = Mage::helper('rshipping')->__('Add Shipping Method');
        parent::__construct();
    }

}