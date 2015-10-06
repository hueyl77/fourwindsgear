<?php

class ITwebexperts_Maintenance_Model_Source_Percentoptions
{

    public function toOptionArray()
    {
        return array(
            array('value' => .1, 'label'=>"10%"),
            array('value' => .2, 'label'=>"20%"),
            array('value' => .3, 'label'=>"30%"),
            array('value' => .4, 'label'=>"40%"),
            array('value' => .5, 'label'=>"50%"),
            array('value' => .6, 'label'=>"60%"),
            array('value' => .7, 'label'=>"70%"),
            array('value' => .8, 'label'=>"80%"),
            array('value' => .9, 'label'=>"90%"),
            array('value' => 1, 'label'=>"100%"),
        );
    }

}
