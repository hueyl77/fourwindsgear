<?php
/*------------------------------------------------------------------------
# $JA#PRODUCT_NAME$ - Version $JA#VERSION$ - Licence Owner $JA#OWNER$
# ------------------------------------------------------------------------
# Copyright (C) 2004-2009 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: J.O.O.M Solutions Co., Ltd
# Websites: http://www.joomlart.com - http://www.joomlancers.com
# This file may not be redistributed in whole or significant part.
-------------------------------------------------------------------------*/ 

class JoomlArt_JmProducts_Model_System_Config_Source_SlideTransition
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'fade', 'label'=>Mage::helper('joomlart_jmproducts')->__('Fade In & Fade Out')),
            array('value'=>'rtl', 'label'=>Mage::helper('joomlart_jmproducts')->__('Slide Right To Left')),
            array('value'=>'ltr', 'label'=>Mage::helper('joomlart_jmproducts')->__('Slide Left To Right'))
        );
    }    
}
