<?php

class ITwebexperts_Payperrentals_Model_Source_Themestyle
{
    const DEFAULT_STYLE = 'default';
    const START_STYLE = 'start';
    const RTRW_STYLE = 'flicker';
    const LIGHTNESS_STYLE = 'lightness';
    const DARKNESS_STYLE = 'darkness';
    const SUNNY_STYLE = 'sunny';
    const REDMOND_STYLE = 'redmond';
    const LEFROG_STYLE = 'lefrog';
    const EXCITEBIKE_STYLE = 'excitebike';
    const SWANKYPURSE_STYLE = 'swankypurse';
    const PEPPERGRINDER_STYLE = 'peppergrinder';
    const BLITZER_STYLE = 'blitzer';
    const CUPERTINO_STYLE = 'cupertino';
    const OVERCAST_STYLE = 'overcast';
    const BLACKTIE_STYLE = 'blacktie';
    const DOTLOVE_STYLE = 'dotlove';
    const HOTSNEAKS_STYLE = 'hotsneaks';
    const CUSTOM1_STYLE = 'custom1';
    const CUSTOM2_STYLE = 'custom2';

    public function toOptionArray()
    {
        return array(
            array('value' => self::DEFAULT_STYLE, 'label' => Mage::helper('payperrentals')->__('Default')),
            array('value' => self::START_STYLE, 'label' => Mage::helper('payperrentals')->__('Start')),
            array('value' => self::RTRW_STYLE, 'label' => Mage::helper('payperrentals')->__('RTRW')),
            array('value' => self::LIGHTNESS_STYLE, 'label' => Mage::helper('payperrentals')->__('Lightness')),
            array('value' => self::DARKNESS_STYLE, 'label' => Mage::helper('payperrentals')->__('Darkness')),
            array('value' => self::SUNNY_STYLE, 'label' => Mage::helper('payperrentals')->__('Sunny')),
            array('value' => self::REDMOND_STYLE, 'label' => Mage::helper('payperrentals')->__('Redmond')),
            array('value' => self::LEFROG_STYLE, 'label' => Mage::helper('payperrentals')->__('Le Frog')),
            array('value' => self::EXCITEBIKE_STYLE, 'label' => Mage::helper('payperrentals')->__('Excite Bike')),
            array('value' => self::SWANKYPURSE_STYLE, 'label' => Mage::helper('payperrentals')->__('Swanky Purse')),
            array('value' => self::PEPPERGRINDER_STYLE, 'label' => Mage::helper('payperrentals')->__('Pepper Grinder')),
            array('value' => self::BLITZER_STYLE, 'label' => Mage::helper('payperrentals')->__('Blitzer')),
            array('value' => self::CUPERTINO_STYLE, 'label' => Mage::helper('payperrentals')->__('Cupertino')),
            array('value' => self::OVERCAST_STYLE, 'label' => Mage::helper('payperrentals')->__('Overcast')),
            array('value' => self::BLACKTIE_STYLE, 'label' => Mage::helper('payperrentals')->__('Black Tie')),
            array('value' => self::DOTLOVE_STYLE, 'label' => Mage::helper('payperrentals')->__('Dot Love')),
            array('value' => self::HOTSNEAKS_STYLE, 'label' => Mage::helper('payperrentals')->__('Hot Sneaks')),
            array('value' => self::CUSTOM1_STYLE, 'label' => Mage::helper('payperrentals')->__('Custom Style 1')),
            array('value' => self::CUSTOM2_STYLE, 'label' => Mage::helper('payperrentals')->__('Custom Style 2')),
        );
    }
}