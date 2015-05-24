<?php
class Axaltacore_Customuser_Model_Resource_Partnerinfo extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('axaltacore_customuser/partnerinfo', 'partnerinfo_id');
    }

    /**
    * This will return entity id based on email address.
    */
    public function getEntityIdByEmail($email)
    {
            $read = Mage::getSingleton('core/resource')->getConnection('core_read');
            $readresult = $read->query(
                'SELECT entity_id,role_id,role_code FROM `customer_entity` ce LEFT JOIN axaltacore_user_role aur ON ce.entity_id = aur.user_id LEFT JOIN axaltacore_role ar ON aur.role_id = ar.axaltacore_role_id 
                where ce.email = "'.$email.'" and ar.role_code="la_key_user"'
            );
            while ($row = $readresult->fetch()) {
                $entityId = $row['entity_id'];
            }
            
            return $entityId;
    }


    /*
    To unassign parent from User
    */
    public function unassginedUser($customerId)
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $query = 'UPDATE customer_entity SET parent_id = 0 WHERE parent_id = '. $customerId;
        $writeConnection->query($query);
    }

}