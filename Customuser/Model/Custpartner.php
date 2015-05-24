<?php

class Axaltacore_Customuser_Model_Custpartner extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('axaltacore_customuser/custpartner');
    }
}