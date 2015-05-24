<?php

class Axaltacore_Customuser_Model_Resource_Custmaster_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('axaltacore_customuser/custmaster');
    }
}