<?php
class Axaltacore_Customuser_Model_Resource_Custusermap extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('axaltacore_customuser/custusermap', 'cust_user_map_id');
    }

    public function getCustUserMap()
    {
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $readresult = $read->query(
            'SELECT cust_id FROM `sap_customer_user`'
        );

        return $readresult->fetchAll();
    }
}