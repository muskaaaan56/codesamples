<?php

class Axaltacore_Customuser_Model_Cron extends Mage_Core_Model_Abstract
{
    public function expireCustomerAction()
    {
        $currentDate = date('Y-m-d H:i:s');
        $lastDate = date('Y-m-d H:i:s', strtotime('-1 day'));
        $customers = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('expiration_date', array('gteq' => $lastDate))->addFieldToFilter('expiration_date', array('lteq' => $currentDate))->getData();

        $optionId = Mage::helper('axaltacore_customuser')->getUserStatusId('Inactive');

        foreach ($customers as $key => $value)
        {
            $updateCustomer = Mage::getModel('customer/customer')->load($value['entity_id']);
            $updateCustomer->setId($value['entity_id'])
                           ->setUserStatus($optionId)
                           ->save();
        }
    }
}