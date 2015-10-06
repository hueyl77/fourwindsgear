<?php

/**
 * @category   ITwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */

class ITwebexperts_Rshipping_Adminhtml_RshippingController extends Mage_Adminhtml_Controller_Action
{

        protected function _isAllowed(){
            return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/rshipping');
        }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('rshipping')->__('Rental Shipping Methods'), Mage::helper('rshipping')->__('Rental Shipping Methods'));
        $this->_title($this->__('Rental'))->_title($this->__('Shipping Methods'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_setActiveMenu('payperrentals/rshipping');

        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('rshipping/rshipping')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('rshipping_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('rshipping/items');

            $this->_addContent($this->getLayout()->createBlock('rshipping/adminhtml_rshipping_edit'))
                ->_addLeft($this->getLayout()->createBlock('rshipping/adminhtml_rshipping_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rshipping')->__('Method does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $data['min_rental_period'] = (int)$data['min_rental_period'];
            $data['is_local_pickup'] = (int)$data['is_local_pickup'];
            $data['is_default_method'] = (int)$data['is_default_method'];

            $data['start_disabled_days'] = isset($data['start_disabled_days']) ? implode(',', $data['start_disabled_days']) : '';
            $data['end_disabled_days'] = isset($data['end_disabled_days']) ? implode(',', $data['end_disabled_days']) : '';

            $data['ignore_turnover_day'] = isset($data['ignore_turnover_day']) ? implode(',', $data['ignore_turnover_day']) : '';

            if (strpos(strtolower($data['shipping_method']), 'ups') === false) {
                $data['use_live_ups_api'] = 0;
            }
            $data['use_live_ups_api'] = (int)$data['use_live_ups_api'];

            $model = Mage::getModel('rshipping/rshipping');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rshipping')->__('Method was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rshipping')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('rshipping/rshipping');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Method was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $rshippingIds = $this->getRequest()->getParam('rshipping');
        if (!is_array($rshippingIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($rshippingIds as $rshippingId) {
                    $rshipping = Mage::getModel('rshipping/rshipping')->load($rshippingId);
                    $rshipping->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($rshippingIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $rshippingIds = $this->getRequest()->getParam('rshipping');
        if (!is_array($rshippingIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($rshippingIds as $rshippingId) {
                    Mage::getSingleton('rshipping/rshipping')
                        ->load($rshippingId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($rshippingIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}