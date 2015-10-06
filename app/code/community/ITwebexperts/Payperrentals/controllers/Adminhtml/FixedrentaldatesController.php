<?php


class ITwebexperts_Payperrentals_Adminhtml_FixedrentaldatesController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/fixedrentaldates');
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
        $fixedDatesLoad = Mage::getModel('payperrentals/fixedrentalnames');

        if ($id) {
            $fixedDatesLoad->load($id);
            if ($fixedDatesLoad->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $fixedDatesLoad->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('payperrentals')->__('The price by date entry does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('registry_data', $fixedDatesLoad);
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
                $fixedrental = Mage::getModel('payperrentals/fixedrentalnames');
                if($id){
                    $fixedrental->load($id);
                }else{
                    unset($data['id']);
                }

                if ($data) {
                    $fixedrental->setData($data);
                    $fixedrental->save();
                    $countFixed = 1;
                    $countNotSet = 0;
                    Mage::getResourceModel('payperrentals/fixedrentaldates')->deleteById($data['id']);
                    while(true){
                        if(!isset($data['start_date'.$countFixed])) {
                            $countNotSet++;
                            $countFixed++;
                            if($countNotSet >= 5) {
                                break;
                            }
                            continue;
                        }

                        $fixedDate = Mage::getModel('payperrentals/fixedrentaldates')
                            ->setNameid($fixedrental->getId())
                            ->setStartDate($data['start_date'.$countFixed])
                            ->setEndDate($data['end_date'.$countFixed])
                            ->setRepeatType($data['repeat_type'.$countFixed])
                            ->setRepeatDays(implode(',', $data['repeat_days'.$countFixed]));
                        $fixedDate->save();
                        $countFixed++;
                    }

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
            Mage::getModel('payperrentals/fixedrentalnames')->load($id)->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('payperrentals')->__('The fixed rental has been deleted.'));
            $this->_redirect('*/*/');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('payperrentals')->__('The fixed rental record does not exist.'));
            $this->_redirect('*/*/');
        }
    }
}