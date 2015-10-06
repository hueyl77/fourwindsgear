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


class JoomlArt_JmProductsDeal_Model_System_Config_Source_ListType
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'', 'label'=>Mage::helper('joomlart_jmproductsdeal')->__('-- Please select --')),
            array('value'=>'list', 'label'=>Mage::helper('joomlart_jmproductsdeal')->__('List')),
            array('value'=>'grid', 'label'=>Mage::helper('joomlart_jmproductsdeal')->__('Grid'))
        );
    }    
}
