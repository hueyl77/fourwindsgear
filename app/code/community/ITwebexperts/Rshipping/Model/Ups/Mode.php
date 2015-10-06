<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://snowcode.info/ for more information
 *
 *
 * @date       10.08.13
 * @copyright  Copyright (c) 2013 Snowcode (http://snowcode.info/)
 * @license    http://snowcode.info/LICENSE-1.0.html
 */

class ITwebexperts_Rshipping_Model_Ups_Mode
{
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => 'Test'),
            array('value' => 1, 'label' => 'Live')
        );
    }
}