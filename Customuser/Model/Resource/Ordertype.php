<?php
/**
 * Customuser Ordertype
 *
 * @category    Axaltacore
 * @package     Axaltacore_Customuser
 * @author      Digitales
 */
class Axaltacore_Customuser_Model_Resource_Ordertype extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('axaltacore_customuser/ordertype', 'ordertype_id');
    }
}