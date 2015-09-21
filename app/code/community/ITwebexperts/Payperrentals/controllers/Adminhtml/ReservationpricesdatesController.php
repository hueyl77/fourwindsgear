<?php


class ITwebexperts_Payperrentals_Adminhtml_ReservationpricesdatesController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/reservationpricesdates');
    }


    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $reservationLoad = Mage::getModel('payperrentals/reservationpricesdates');

        if ($id) {
            $reservationLoad->load($id);
            if ($reservationLoad->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $reservationLoad->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('payperrentals')->__('The price by date entry does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('registry_data', $reservationLoad);
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $data = $this->getRequest()->getPost();
                $id = $this->getRequest()->getParam('id');
                $respricedate = Mage::getModel('payperrentals/reservationpricesdates');
                if($id){
                    $respricedate->load($id);
                }else{
                    unset($data['id']);
                }
                if ($data) {
                        if(array_key_exists('repeat_days',$data)){
                            $data['repeat_days'] = implode(',', $data['repeat_days']);
                        }
                        $respricedate->setData($data);
                        $respricedate->save();
                }
                    Mage::getSingleton('adminhtml/session')->addSuccess('Saved');
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
                    $this->_redirect('*/*/index');
                }
             catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('payperrentals')->__('An error occurred while saving the data. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return $this;
            }
        }
    }
    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        if($id != 0 || $id != null){
            Mage::getModel('payperrentals/reservationpricesdates')->load($id)->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('payperrentals')->__('The price by date/time record has been deleted.'));
            $this->_redirect('*/*/');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('payperrentals')->__('The price by date/time record does not exist.'));
            $this->_redirect('*/*/');
        }
    }
}