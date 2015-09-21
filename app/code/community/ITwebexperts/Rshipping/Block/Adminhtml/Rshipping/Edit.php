<?php

/**
 * @category   Shipping Extension
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */
class ITwebexperts_Rshipping_Block_Adminhtml_Rshipping_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'rshipping';
        $this->_controller = 'adminhtml_rshipping';

        $this->_updateButton('save', 'label', Mage::helper('rshipping')->__('Save Method'));
        $this->_updateButton('delete', 'label', Mage::helper('rshipping')->__('Delete Method'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('rshipping_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'rshipping_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'rshipping_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }

            MaskedInput.definitions['a'] = '[0-9]';
            MaskedInput.definitions['b'] = '[0-9]';
            MaskedInput.definitions['c'] = '[0-9]';

            new MaskedInput('#shipping_cutoff_time', 'aa:bb:cc');
        ";

        if (Mage::getStoreConfig(Itwebexperts_Rshipping_Helper_Data::RSHIPPING_UPS_API_ENABLE)) {
            $this->_formScripts[] = "var ups_api = true;";
        } else {
            $this->_formScripts[] = "var ups_api = false;";
        }

        $this->_formScripts[] = "
            Event.observe(window, 'load', function() {
                if(!$('shipping_method').value.toLowerCase().include('ups')) {
                    $('ignore_turnover_day').up(1).hide();
                    if(ups_api) {
                        $('use_live_ups_api').up(1).hide();
                    }
                }

                Event.observe($('shipping_method'), 'change', function() {
                    if($('shipping_method').value.toLowerCase().include('ups')) {
                        $('ignore_turnover_day').up(1).show();
                        if(ups_api) {
                            $('use_live_ups_api').up(1).show();
                        }
                    } else {
                        $('ignore_turnover_day').up(1).hide();
                        if(ups_api) {
                            $('use_live_ups_api').up(1).hide();
                        }
                    }
                });
            })
            ";

        if (Mage::helper('rshipping/config')->isCustomShippingPrice()) {
            $this->_formScripts[] = "
            Event.observe(window, 'load', function() {
                if(!parseInt($('use_custom_shipping_amount').value)) {
                    $('shipping_amount').up(1).hide();
                    $('custom_shipping_amount_type').up(1).hide();
                }

                Event.observe($('use_custom_shipping_amount'), 'change', function() {
                    if(parseInt($('use_custom_shipping_amount').value)) {
                        $('shipping_amount').up(1).show();
                        $('custom_shipping_amount_type').up(1).show();
                    } else {
                        $('shipping_amount').up(1).hide();
                        $('custom_shipping_amount_type').up(1).hide();
                    }
                });
            })
            ";
        }
    }

    public function getHeaderText()
    {
        if (Mage::registry('rshipping_data') && Mage::registry('rshipping_data')->getId()) {
            return Mage::helper('rshipping')->__("Edit Method '%s'", $this->htmlEscape(Mage::registry('rshipping_data')->getShippingTitle()));
        } else {
            return Mage::helper('rshipping')->__('Add Method');
        }
    }

}