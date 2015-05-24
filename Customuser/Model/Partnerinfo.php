<?php

class Axaltacore_Customuser_Model_Partnerinfo extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('axaltacore_customuser/partnerinfo');
    }
    
    public function loadByPartnerId($customerId)
    {
        return $this->load($customerId, 'customer_id');
    }

    public function getNaSoldTo($customerId)
    {
        $salesarea = Mage::getModel('axaltacore_customuser/salesarea')->getCollection();
        $salesarea->getSelect()->join(array('cw' => 'core_website'), 'main_table.website_id = cw.website_id',array('cw.code'));
        $salesarea->getSelect()->where('cw.code = "'.Axaltacore_Usermanagement_Helper_Data::NA_WEBSITE.'"');

        $naSalesareaData = $salesarea->getData();
        $salesOrgId = $naSalesareaData[0]['sales_organization_id'];
        $distributionChannel = $naSalesareaData[0]['distribution_channel'];
        $division = $naSalesareaData[0]['division'];
        $salesareaid = $naSalesareaData[0]['salesarea_id'];

        $custUserMap = Mage::getModel('axaltacore_customuser/custusermap')->getCollection();
        // Map sales area, sold to - Admin side
        $custUserMapall = Mage::getModel('axaltacore_customuser/custusermap')->getCollection();
        $custUserMapFilter = $custUserMapall->addFieldToFilter('salesarea_id',$salesareaid);
        if ($customerId) {
            $custUserMapall->addFieldToFilter('cust_id',$customerId);
        }

        $existSoldTo = array();

        foreach ($custUserMapFilter->getData() as $key => $value) {
            $existSoldTo[] = $value['sap_cust_id'];
        }

        $custMasterCollection = Mage::getModel('axaltacore_customuser/custmaster')->getCollection()
                                ->addFieldtoFilter('sales_organization_id',$salesOrgId)
                                ->addFieldtoFilter('distribution_channel',$distributionChannel)
                                ->addFieldtoFilter('division',$division)
                                ->addFieldToFilter('website_code',$naSalesareaData[0]['code']);

        if (!empty($existSoldTo)) {
            $custMasterCollection->addFieldToFilter('sap_cust_id', array('nin' => $existSoldTo));
        }

        $custMasterData = $custMasterCollection->getData();
        if (!empty($custMasterData)) {
            $data = '';
            $count = 0;
            foreach($custMasterData as $key => $value) {
                if ($count > 0) {
                    $data .= '/'.$value['sap_cust_id'];
                }
                else {
                    $data .= $value['sap_cust_id'];
                }

                $count++;
            }
        }
        else {
            $data = '';
        }

        $result['soldto'] = $data;
        $result['salesarea_id'] = $salesareaid;
        return $result;
    }
}