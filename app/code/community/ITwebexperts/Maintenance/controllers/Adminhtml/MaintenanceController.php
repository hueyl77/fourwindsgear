<?php
class ITwebexperts_Maintenance_Adminhtml_MaintenanceController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/maintenance/maintenance_history');
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
        $maintenanceItem = Mage::getModel('simaintenance/items');

        if($id){
            $maintenanceItem->load((int) $id);
            if($maintenanceItem->getId()){
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if($data){
                    $maintenanceItem->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simaintenance')->__('The Maintenance Record does not exist.'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('maintenance_data',$maintenanceItem);

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
                    $maintenanceItem = Mage::getModel('simaintenance/items')->load($id);
                    if(array_key_exists('serials',$data)){
                        $serials = $data['serials'];
                        $data['serials'] = Mage::helper('simaintenance')->arrayToString($serials);
                        Mage::helper('simaintenance')->setSerialsStatus($serials,$data);
                    }
                    $maintenanceItem->setData($data);
                    if(!$id){
                        $maintenanceItem->setDateAdded(date('Y-m-d H:i:s'));
                    }
                    if($id){
                        $maintenanceItem->setId($id);
                    }
                    $maintenanceItem->save();
                    $informMaintenanceTech = null;
                    $informMaintenanceTech = $this->getRequest()->getParam('email_maintainer');
                    if(!is_null($informMaintenanceTech) && $data['maintainer_id'] != -1){
                        Mage::helper('simaintenance/emails')->sendMaintenanceReportEmail($maintenanceItem->getId());
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess('Maintenance Record Saved');
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
                    $this->_redirect('*/*/index',array('id'=>$id));
                }

            } catch (Exception $e){
                $this->_getSession()->addError(
                    Mage::helper('simaintenance')->__('An error occured while saving the maintenance record. Please review the log and try again'));
                Mage::logException($e);
                $this->_redirect('*/*/edit',array('id'=>$id));
                return $this;
            }
        }
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        if($id !=0 || $id != null){
            Mage::getModel('simaintenance/items')->load($id)->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simaintenance')->__('The maintenance record has been deleted'));
            $this->_redirect('*/*/');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simaintenance')->__('The maintenance record does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function massStatusAction(){
        $items = $this->getRequest()->getParam('item');
        $status = $this->getRequest()->getParam('status');

        foreach($items as $item){
            $maintenance = Mage::getModel('simaintenance/items')->load($item);
            $maintenance->setStatus($status);
            $maintenance->save();
            /** update serial status */
            $serials = $maintenance->getSerials();
            $data['status'] = $status;
            Mage::helper('simaintenance')->setSerialsStatus($serials,$data);
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simaintenance')
            ->__('Maintenance Ticket Status was updated'));
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $items = $this->getRequest()->getParam('item');

        try {
            foreach ($items as $item) {
                $maintenance = Mage::getModel('simaintenance/items')->load($item)->delete();
            }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simaintenance')->__('Total of %d record(s) were deleted.', count($items)));
        } catch (Exeption $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }
}