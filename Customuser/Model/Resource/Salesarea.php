<?php
class Axaltacore_Customuser_Model_Resource_Salesarea extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('axaltacore_customuser/salesarea', 'salesarea_id');
    }

    /**
    * This Function will return All Sales Area Ids of Entity Id
    * @param entity_id
    * @return array of Sales Area Ids
    */
    public function getSalesAreaIds($userEmail)
    {
            $entity = Mage::getSingleton('core/resource')->getConnection('core_read');
            $entityresult = $entity->query(
                'SELECT entity_id,role_id,role_code FROM `customer_entity` ce LEFT JOIN axaltacore_user_role aur ON ce.entity_id = aur.user_id LEFT JOIN axaltacore_role ar ON aur.role_id = ar.axaltacore_role_id 
                where ce.email = "'.$userEmail.'" and ar.role_code="la_key_user"'
            );
            while ($entityRow = $entityresult->fetch()) {
                $entityId = $entityRow['entity_id'];
            }
        
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $readresult = $read->query(
            'SELECT salesarea_id FROM `sap_customer_user` scu 
            where scu.cust_id = "'.$entityId.'" group by scu.salesarea_id'
        );
        while ($row = $readresult->fetch()) {
            $salesAreaIds[] = $row['salesarea_id'];
        }
        
       return $salesAreaIds;
    }
}