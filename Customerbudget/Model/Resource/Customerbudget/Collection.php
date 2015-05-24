<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/

class Magentothem_Customerbudget_Model_Resource_Customerbudget_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('magentothem_customerbudget/customerbudget');
    }
}