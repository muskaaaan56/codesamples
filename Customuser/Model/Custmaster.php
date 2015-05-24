<?php

class Axaltacore_Customuser_Model_Custmaster extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('axaltacore_customuser/custmaster');
    }

    public function loadBySapCustomerId($sapCustId)
    {
        return $this->load($sapCustId, 'sap_cust_id');
    }
    
    public function getCustomerPt($sapCustId)
    {
        $collection = $this->getCollection()->addFieldToFilter('sap_cust_id',$sapCustId)->addFieldToFilter('website_code',Axaltacore_Usermanagement_Helper_Data::NA_WEBSITE)->addFieldToFilter('distribution_channel','10')->getFirstItem();
        $custmasterData = $collection->getData();
        return $custmasterData['paymentterms'];
    }
}