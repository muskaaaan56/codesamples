<?php

class Axaltacore_Customuser_Model_Custaddress extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('axaltacore_customuser/custaddress');
    }
    
    public function checkSoldToXmlData($soldToId)
    {
        $custAddress = Mage::getModel('axaltacore_customuser/custaddress')->getCollection()->addFieldToFilter('partner_id', $soldToId)->getData();
        
        if($custAddress)
        {
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function getTransp($shipToId)
    {
        $data = $this->load($shipToId, 'partner_id');
        $jsonData = $data->getTranspCode();
        $decodeData = json_decode($jsonData);
        foreach ($decodeData as $nameTransp) {
            $lifnr = $nameTransp->LIFNR;
        }
        
        return $lifnr;
    }
}