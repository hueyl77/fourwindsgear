<?php
class ITwebexperts_Maintenance_Adminhtml_MaintainersController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/maintenance/maintainers');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        return $this;
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id',null);
        $tableItem = Mage::getModel('simaintenance/maintainers');

        if($id){
            $tableItem->load((int) $id);
            if($tableItem->getId()){
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if($data){
                    $tableItem->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simaintenance')->__('The Record does not exist.'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('form_data',$tableItem);

        $this->loadLayout()->renderLayout();
    }

    public function saveAction()
    {
        if($this->getRequest()->getPost())
        {
            try {
                $data = $this->getRequest()->getPost();
                $id = $this->getRequest()->getParam('id');
                if($data){
                    /** @var $tableItem ITwebexperts_Maintenance_Model_Maintainers */
                    $tableItem = Mage::getModel('simaintenance/maintainers')->load($id);
                    $tableItem->setAdminId($data['admin_id']);
                    if($id){
                        $tableItem->setId($id);
                    }
                    $tableItem->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess('Record Saved');
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
                    $this->_redirect('*/*/index',array('id'=>$id));
                }
            } catch (Exception $e){
                $this->_getSession()->addError(
                    Mage::helper('simaintenance')->__('An error occurred while saving the record. Please review the log and try again'));
                Mage::logException($e);
                $this->_redirect('*/*/edit',array('id'=>$id));
                return $this;
            }
        }
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        if($id !=0 || $id != null){
            Mage::getModel('simaintenance/maintainers')->load($id)->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simaintenance')->__('The record has been deleted'));
            $this->_redirect('*/*/');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simaintenance')->__('The record does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }
}