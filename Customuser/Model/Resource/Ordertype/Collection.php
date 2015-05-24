<?php
/**
 * Customuser Ordertype
 *
 * @category    Axaltacore
 * @package     Axaltacore_Customuser
 * @author      Digitales
 */
class Axaltacore_Customuser_Model_Resource_Ordertype_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('axaltacore_customuser/ordertype');
    }
}