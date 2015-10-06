<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Manualreserve_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $data = null;
        $form = new Varien_Data_Form(
            array(
                'id'      => 'edit_form',
                'action'  => $this->getUrl(
                    '*/*/save', array
                    ('id' => $this->getRequest()->getParam('id'))
                ),
                'method'  => 'post',
                'enctype' => 'multipart/form-data',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        } elseif (Mage::registry('registry_data')) {
            $data = Mage::registry('registry_data')->getData();
        }

        $fieldset = $form->addFieldset(
            'registry_form', array('legend' => Mage::helper('payperrentals')->__('Reservation Information'))
        );

        $productsInOrder = (null !== (Mage::registry('productsInOrder')) ? Mage::registry('productsInOrder') : 1);
        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $prodCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('name')->addAttributeToFilter('type_id', array('eq' => 'reservation'));
        if(Mage::helper('itwebcommon')->isVendorAdmin()){
           $prodCollection->addAttributeToFilter('vendor_id',array('eq'=>Mage::getSingleton('vendors/session')->getId()));
        }

        /**
         * Setup product names array
         */
        $prodArray = array('-1' => 'Please Select');
        foreach ($prodCollection as $productItem) {
            $prodArray[$productItem->getId()] = $productItem->getName() . ' - ' . $productItem->getSku();
        }
        asort($prodArray);

        $valueData = array();
        $fieldset->addField(
            'pricenote', 'note', array(
                'text'  => Mage::helper('payperrentals')->__(
                    '** Note: Editing rental dates here will not change the order price. '
                ),
                'class' => 'notedates'
            )
        );

        if ($productsInOrder>1) {

            $fieldset->addField(
                'note2', 'note', array(
                    'text' => Mage::helper('payperrentals')->__(
                        'To globally update rental dates, use the global start and end date and click Update All Dates'
                    )
                )
            );

            $fieldset->addField(
                'global_start_date', 'text', array(
                    'label'        => Mage::helper('payperrentals')->__('Global Start Date'),
                    'name'         => 'global_start_date'
                )
            );

            $fieldset->addField(
                'global_end_date', 'text', array(
                    'label'        => Mage::helper('payperrentals')->__('Global End Date'),
                    'name'         => 'global_end_date'
                )
            );
        }

        for ($i = 0; $i < $productsInOrder; $i++) {

            /**
             * Custom code to be used for $form->setValues as an array from $data
             * $valueData['data_key' . $index] = value
             */

            if (isset($data[$i]) && is_array($data[$i])) {
                $valArr = $data[$i];
            } else {
                $valArr = $data;
            }
            foreach ($valArr as $datakey => $dataitem) {
                $valueData[$datakey . $i] = $dataitem;
            }


            // for showing heading above each product
            if (isset($data[$i]['product_id'])) {
                $productid = $data[$i]['product_id'];
                $productName = Mage::getModel('catalog/product')->load($data[$i]['product_id'])->getName();
            } else {
                $productName = "New Reservation";
            }

            if (isset($data['product_id'])) {
                $productid = $data['product_id'];
                $productName = Mage::getModel('catalog/product')->load($data['product_id'])->getName();
            }


            $fieldset->addField(
                'noteproduct' . $i, 'note', array(
                    'text' => "<b>" . Mage::helper('payperrentals')->__('Product: ') . $productName . "</b>",
                )
            );


            $fieldset->addField(
                'id' . $i, 'hidden', array(
                    'readonly' => true,
                    'name'     => 'entity_id' . $i
                )
            );

            /** used for inventory calculation to know if we should add original reserved quantiy
             * to returned quantity in ajax controller getquantity */

            $valueData['originalprodid' . $i] = $productid;
            $valueData['originalstart_date' . $i] = $valueData['start_date' . $i];
            $valueData['originalend_date' . $i] = $valueData['end_date' . $i];

            $fieldset->addField(
                'originalprodid' . $i, 'hidden', array(
                    'readonly' => true,
                    'name'     => 'originalprodid' . $i,
                )
            );

            $fieldset->addField(
                'originalstart_date' . $i, 'hidden', array(
                    'name'         => 'originalstart_date' . $i,
                )
            );

            $fieldset->addField(
                'originalend_date' . $i, 'hidden', array(
                    'name'         => 'originalend_date' . $i,
                )
            );

            $fieldset->addField(
                'start_date' . $i, 'text', array(
                    'label'        => Mage::helper('payperrentals')->__('Start Date'),
                    'class'        => 'required-entry startdate',
                    'name'         => 'start_date' . $i,
                    'title'        => $i
                )
            );

            $fieldset->addField(
                'end_date' . $i, 'text', array(
                    'label'        => Mage::helper('payperrentals')->__('End Date'),
                    'class'        => 'required-entry enddate',
                    'required'     => 'true',
                    'name'         => 'end_date' . $i,
                    'title'        => $i
                )
            );

            $fieldset->addField(
                'qty' . $i, 'text', array(
                    'label'    => Mage::helper('payperrentals')->__('Qty'),
                    'class'    => 'required-entry qty',
                    'required' => 'true',
                    'style'    => 'width:40px',
                    'name'     => 'qty' . $i,
                    'title'    => $i
                )
            );

            /**
             * Used to hold returned available quantity for start/end dates
             * manually set value since it is not in $data. Add to the available quantity
             * the already reserved quantity for this record.
             */

            if (isset($data[$i]['product_id'])) {
                $valueData['available_qty' . $i] = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity(
                        $data[$i]['product_id'], $data[$i]['start_date'], $data[$i]['end_date']
                    ) + $data[$i]['qty'];
            }

            if (isset($data['product_id'])) {
                $valueData['available_qty' . $i] = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity(
                        $data['product_id'], $data['start_date'], $data['end_date']
                    ) + $data['qty'];
            }


            if (isset($data[$i]['product_id']) || isset($data['product_id'])) {

                $fieldset->addField(
                    'available_qty' . $i, 'hidden', array(
                        'name' => 'available_qty' . $i
                    )
                );

                if (isset($data[$i]['product_id'])) {
                    $valueData['original_qty' . $i] = $data[$i]['qty'];
                }
                if (isset($data['product_id'])) {
                    $valueData['original_qty' . $i] = $data['qty'];
                }

                $fieldset->addField(
                    'original_qty' . $i, 'hidden', array(
                        'name' => 'original_qty' . $i
                    )
                );
            }


            $fieldset->addField(
                'product_id' . $i, 'select', array(
                    'label'    => Mage::helper('payperrentals')->__('Product - Sku'),
                    'class'    => 'required-entry productdrop',
                    'required' => 'true',
                    'name'     => 'product_id' . $i,
                    'values'   => $prodArray,
                    'value'    => 'product_id',
                    'title'    => $i
                )
            );


            $fieldset->addField(
                'comments' . $i, 'text', array(
                    'label'    => Mage::helper('payperrentals')->__('Comments'),
                    'required' => false,
                    'name'     => 'comments' . $i
                )
            );


            $fieldset->addField(
                'order_id' . $i, 'text', array(
                    'label'    => Mage::helper('payperrentals')->__('Order Id'),
                    'required' => false,
                    'readonly' => true,
                    'style'    => 'width:40px',
                    'name'     => 'order_id' . $i
                )
            );

            $fieldset->addField(
                'order_item_id' . $i, 'hidden', array(
                    'readonly' => true,
                    'name'     => 'order_item_id' . $i
                )
            );
        }

        $fieldset->addField(
            'number_of_products', 'hidden', array(
                'readonly' => true,
                'name'     => 'number_of_products'
            )
        );


        $valueData['number_of_products'] = $productsInOrder;

        $form->setValues($valueData);
        return parent::_prepareForm();


    }

}