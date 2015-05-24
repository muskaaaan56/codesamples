<?php

class Magentothem_Customuser_Model_Salesarea extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('magentothem_customuser/salesarea');
    }

    /*
    * Fetch the sales area and convert it into options.
    */
    public function toOptionArray()
    {
        $salesarea = Mage::getModel('magentothem_customuser/salesarea')->getCollection();
        $salesarea->getSelect()->join(array('cw' => 'core_website'), 'main_table.website_id = cw.website_id',array('cw.code'));
        //$salesarea->getSelect()->where('cw.code = "'.Magentothem_Usermanagement_Helper_Data::NA_WEBSITE.'"');
        $salesareaData = $salesarea->getData();

        $numOfSalesArea = sizeof($salesareaData);
        for($i = 0; $i < $numOfSalesArea; $i++) {
                $opt[] = array(
                        'value' => $salesareaData[$i]['sales_organization_id'].'_'.$salesareaData[$i]['distribution_channel'].'_'.$salesareaData[$i]['division'],
                        'label' => $salesareaData[$i]['company_name'],
                         );
        }

        $this->_options = $opt;
        return $this->_options;
    }
    
    /**
     * This will retun entity Id of user by email Id
     * @param email_id
     * @return entity id
     */
    public function getSalesAreas($websiteId=NULL)
    {
        $usersDetails = Mage::getSingleton('admin/session')->getUser();
        $userInfo = $usersDetails->getData();
        $rolesDetail = $usersDetails->getRole()->getData();
        $salesCollection = Mage::getModel('magentothem_customuser/salesarea')->getCollection();
        
        if($rolesDetail['role_name'] == 'Key User' && $rolesDetail['role_type'] == 'G') {
            $salesAreaIds = Mage::getResourceModel('magentothem_customuser/salesarea')->getSalesAreaIds($userInfo['email']);
            $salesCollection->addFieldToFilter('salesarea_id', array('in' => $salesAreaIds));
        } else if($rolesDetail['role_name'] == 'naadmin' || $rolesDetail['role_name'] == 'laadmin' || $rolesDetail['role_name'] == 'emeaadmin') {
            
            if(!is_null($rolesDetail['gws_is_all']) && count($rolesDetail['gws_websites']) == 1) {
                $salesCollection->addFieldToFilter('website_id', array('in' => $rolesDetail['gws_websites']));
            }
        } else {
            if($websiteId != NULL) {
                $salesCollection->addFieldToFilter('website_id', array('eq' => $websiteId));
            }
        }
        
        return $salesCollection;
    }
}