<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/

class Magentothem_Customerbudget_Model_Resource_Customerbudget extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {    
        // Note that the customerbudget_id refers to the key field in your database table.
        $this->_init('magentothem_customerbudget/customerbudget', 'customerbudget_id');
    }
}