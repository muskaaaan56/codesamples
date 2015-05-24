<?php
/**
 * Customer entity resource model
 *
 * @category    Axaltacore
 * @package     Axaltacore_Customuser
 * @author      Ameri & Partner
 */
class Axaltacore_Customuser_Model_Resource_Customer extends Mage_Customer_Model_Resource_Customer
{
    /**
     * Retrieve customer entity default attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array(
                'entity_type_id',
                'attribute_set_id',
                'created_at',
                'updated_at',
                'increment_id',
                'store_id',
                'parent_id',
                'website_id',
                'partner_key',
                'uid',
                'user_key',
                'sfdc_user_id',
                'addemail',
               );
    }

    /**
     * Check customer scope, email and confirmation key before saving
     *
     * @param Mage_Customer_Model_Customer $customer
     * @throws Mage_Customer_Exception
     * @return Mage_Customer_Model_Resource_Customer
     */
    protected function _beforeSave(Varien_Object $customer)
    {
        Mage_Eav_Model_Entity_Abstract::_beforeSave($customer);
        if (!$customer->getEmail()) {
            throw Mage::exception('Mage_Customer', Mage::helper('customer')->__('Customer email id is required'));
        }

        return $this;
    }
}
