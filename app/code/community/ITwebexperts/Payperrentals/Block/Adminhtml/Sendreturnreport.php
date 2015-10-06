<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Sendreturnreport
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sendreturnreport extends Mage_Core_Block_Template
{
    /**
     *
     */
    public function __construct()
    {

    }

    /**
     * @var
     */
    protected $_myCollection;
    /**
     * @var
     */
    protected $_pager;

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPager()
    {
        return $this->_pager;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return mixed
     */
    protected function getMyCollection()
    {

        return $this->_myCollection;
    }

    public function getCalendar($date, $name)
    {
        return $this->getLayout()->createBlock('core/html_date')
            ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
            ->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT))
            ->setName($name)
            ->setValue($date)
            ->setId($name)
            ->setClass('datetime-picker input-text ' . $name)
            ->toHtml();
    }


}
