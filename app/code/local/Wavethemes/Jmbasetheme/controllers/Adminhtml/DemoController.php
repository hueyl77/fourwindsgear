<?php 
class Wavethemes_Jmbasetheme_Adminhtml_DemoController extends Mage_Adminhtml_Controller_Action{
    public function indexAction() {
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/wavethemes_demo/"));
    }
    public function importBlocksAction() {
        $is_overwrite = $this->getRequest()->getParam('is_overwrite', 0);
        $model = Mage::getSingleton('jmbasetheme/demo');
        if ($model){
            $model->importCms('cms/block', 'blocks', $is_overwrite);

            //save config
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');
            Mage::helper('jmbasetheme')->saveConfig('wavethemes_demo/import_demo/overwrite_blocks', $is_overwrite, $store, $website);

        }
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/wavethemes_demo/"));
    }

    public function importPagesAction() {
        $is_overwrite = $this->getRequest()->getParam('is_overwrite', 0);
        $model = Mage::getSingleton('jmbasetheme/demo');
        if ($model){
            $model->importCms('cms/page', 'pages', $is_overwrite);

            //save config
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');
            Mage::helper('jmbasetheme')->saveConfig('wavethemes_demo/import_demo/overwrite_pages', $is_overwrite, $store, $website);
        }
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/wavethemes_demo/"));
    }

    public function importMenuAction() {
        $model = Mage::getSingleton('jmbasetheme/demo');
        if ($model){
            $model->importMenu();
        }
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/wavethemes_demo/"));
    }

    public function exportCMSDataAction() {
        $model = Mage::getSingleton('jmbasetheme/demo');
        if ($model){
            $model->exportCMSData('cms/block', 'blocks');
            $model->exportCMSData('cms/page', 'pages');
        }
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/wavethemes_demo/export/1"));
    }

    public function exportMenuDataAction() {
        $model = Mage::getSingleton('jmbasetheme/demo');
        if ($model){
            $model->exportMenuData();
        }
        $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/wavethemes_demo/export/1"));
    }

    public function setDemoAction() {
        $model = Mage::getSingleton('jmbasetheme/demo');
        $style = $this->getRequest()->getParam('style', null);

        if ($model && $style){
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');
            $model->setDemo($style, $store, $website);
        }
        
        if ($store){
            $url = $this->getUrl("adminhtml/system_config/edit/section/wavethemes_demo/website/{$website}/store/{$store}");
        } else if ($website){
            $url = $this->getUrl("adminhtml/system_config/edit/section/wavethemes_demo/website/{$website}");
        } else {
            $url = $this->getUrl("adminhtml/system_config/edit/section/wavethemes_demo");
        }
        
        $this->getResponse()->setRedirect($url);
    }
}
