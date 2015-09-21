<?php
class ITwebexperts_Maintenance_Helper_Emails extends Mage_Core_Helper_Abstract
{
    public function sendMaintenanceReportEmail ($maintenanceReportId)
    {
        /** @var  $report ITwebexperts_Maintenance_Model_Items */
        $report = Mage::getModel('simaintenance/items')->load($maintenanceReportId);
        $maintainerId = $report->getMaintainerId();
        $status = Mage::getModel('simaintenance/status')->load($report->getStatus())->getStatus();
        $adminUser = Mage::getModel('admin/user')->load($maintainerId);
        $product = Mage::getModel('catalog/product')->load($report->getProductId())->getName();
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('maintenance_report_email');
        $maintenanceurl = Mage::helper("adminhtml")->getUrl('adminhtml/maintenance/edit',array('id'=>$maintenanceReportId));
        $vars = array(
            'product' => $product,
            'summary'   =>  $report->getSummary(),
            'description'   =>  $report->getDescription(),
            'maintenanceurl'    =>  $maintenanceurl,
            'status'    =>  $status
        );
        $subject = Mage::helper('simaintenance')->__('Product Maintenance Report');
        //Appending the Custom Variables to Template.
        $processedTemplate = $emailTemplate->getProcessedTemplate($vars);
        $mail = Mage::getModel('core/email')
            ->setToName($adminUser->getFirstname() . $adminUser->getLastname())
            ->setToEmail($adminUser->getEmail())
            ->setBody($processedTemplate)
            ->setSubject($subject)
            ->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'))
            ->setFromName(Mage::getStoreConfig('trans_email/ident_general/name'))
            ->setType('html');
        try{
            //Confimation E-Mail Send
            $mail->send();
        }
        catch(Exception $error)
        {
            Mage::getSingleton('core/session')->addError($error->getMessage());
            return false;
        }
    }
}