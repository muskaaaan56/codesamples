<?php

class Magentothem_Customuser_Model_Custusermap extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('magentothem_customuser/custusermap');
    }

    public function setUserSessionData($custId)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('cust_id', $custId)->addFieldToFilter('is_default', 1);
        $websiteCode = $_SERVER['MAGE_RUN_CODE'];

        if ($websiteCode == Magentothem_Usermanagement_Helper_Data::LA_WEBSITE) {
            $collection->getSelect()->join(array('as' => 'magentothem_salesarea'),'as.salesarea_id = main_table.salesarea_id', array('as.sales_organization_id','as.division','as.distribution_channel'));

            $collection->getSelect()->join(array('scm' => 'sap_customer_master'), 'scm.sap_cust_id = main_table.sap_cust_id and scm.sales_organization_id = as.sales_organization_id and scm.division = as.division and scm.distribution_channel = as.distribution_channel', array('scm.sales_organization_id', 'scm.division', 'scm.distribution_channel', 'scm.name','scm.server'));

            $collection->getSelect()->joinLeft(array('scp' => 'sap_customer_partner'), 'main_table.sap_cust_id = scp.sap_cust_id AND scp.address_type="WE"', array('partner_id'));
        }
        else {
            $collection->getSelect()->join(array('scm' => 'sap_customer_master'), 'scm.sap_cust_id = main_table.sap_cust_id', array('scm.sales_organization_id', 'scm.division', 'scm.distribution_channel', 'scm.name','scm.server'));
            $collection->getSelect()->joinLeft(array('scp' => 'sap_customer_partner'), 'main_table.sap_cust_id = scp.sap_cust_id AND scp.address_type="WE"', array('partner_id'));
        }

        $sapCustUserMapData = $collection->getData();

        if ($sapCustUserMapData) {
            $sapCustId = $sapCustUserMapData[0]['sap_cust_id'];
            $shipToId = $sapCustUserMapData[0]['partner_id'];
            $salesAreaId = $sapCustUserMapData[0]['salesarea_id'];
            $salesOrgId = $sapCustUserMapData[0]['sales_organization_id'];
            $divisionId = $sapCustUserMapData[0]['division'];
            $distrChnnel = $sapCustUserMapData[0]['distribution_channel'];
            $serverCode = $sapCustUserMapData[0]['server'];
            $canViewOrder = $sapCustUserMapData[0]['can_view_order'];
            $canCreateOrder = $sapCustUserMapData[0]['can_create_order'];
            
            Mage::getSingleton('customer/session')->setSapCustomerId($sapCustId);
            Mage::getSingleton('customer/session')->setShipTo($shipToId);
            Mage::getSingleton('customer/session')->setSalesAreaId($salesAreaId);
            Mage::getSingleton('customer/session')->setSalesOrgId($salesOrgId);
            Mage::getSingleton('customer/session')->setDivisionId($divisionId);
            Mage::getSingleton('customer/session')->setDistrChannel($distrChnnel);
            Mage::getSingleton('customer/session')->setServerCode($serverCode);
            Mage::getSingleton('customer/session')->setCanViewOrder($canViewOrder);
            Mage::getSingleton('customer/session')->setCanCreateOrder($canCreateOrder);
        }
    }

    public function changeUserSessionData($data)
    {
        $custId = Mage::getSingleton('customer/session')->getId();
        $collection = $this->getCollection();
        $collection->addFieldToFilter('cust_id', $custId)->addFieldToFilter('main_table.sap_cust_id', $data['sap_cust_id']);
        $collection->addFieldToSelect(array('can_view_order'));
        $collection->addFieldToSelect(array('can_create_order'));
        $collection->getSelect()->join(array('scm' => 'sap_customer_master'), 'scm.sap_cust_id = main_table.sap_cust_id', array('scm.server'));
        $sapCustUserMapData = $collection->getData();
		//$title = Mage::helper('magentothem_customuser')->getCustomerTitle();
		$rolecodes = Mage::getSingleton('customer/session')->getRoles();
		$websiteCode = $_SERVER['MAGE_RUN_CODE'];

		if (strtolower($websiteCode) == 'na' && in_array('axaltacsr', $rolecodes) ) {
			Mage::getSingleton('customer/session')->setServerCode('R');
            Mage::getSingleton('customer/session')->setCanViewOrder(1);
			Mage::getSingleton('customer/session')->setCanCreateOrder(1);
        } else if ($sapCustUserMapData) {
            $serverCode = $sapCustUserMapData[0]['server'];
            $canViewOrder = $sapCustUserMapData[0]['can_view_order'];
            $canCreateOrder = $sapCustUserMapData[0]['can_create_order'];
            Mage::getSingleton('customer/session')->setServerCode($serverCode);
            Mage::getSingleton('customer/session')->setCanViewOrder($canViewOrder);
            Mage::getSingleton('customer/session')->setCanCreateOrder($canCreateOrder);
        }
        
        $salesAreaData = Mage::getModel('magentothem_customuser/salesarea')->load($data['salesarea_id']);
        $salesOrgId = $salesAreaData->getSalesOrganizationId();
        $divisionId = $salesAreaData->getDivision();
        $distrChnnel = $salesAreaData->getDistributionChannel();

        Mage::getSingleton('customer/session')->setSapCustomerId($data['sap_cust_id']);
        Mage::getSingleton('customer/session')->setShipTo($data['shipto']);
        Mage::getSingleton('customer/session')->setSalesAreaId($data['salesarea_id']);
        Mage::getSingleton('customer/session')->setSalesOrgId($salesOrgId);
        Mage::getSingleton('customer/session')->setDivisionId($divisionId);
        Mage::getSingleton('customer/session')->setDistrChannel($distrChnnel);
        return array(
                'salesOrgId' => $salesOrgId,
                'serverCode' => $serverCode,
               );
    }
    
    public function getUserOptionValue($optionId, $attributeCode)
    {
        $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setCodeFilter($attributeCode)
                ->getFirstItem();
        $attributeId = $attributeInfo->getAttributeId();

        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')  
                ->setPositionOrder('asc')
                ->setAttributeFilter($attributeId)
                ->setStoreFilter()
                ->load();
        foreach ($optionCollection as $option) {
             if($option->getId() == $optionId) {
                $value = $option->getValue();
             }
        }

        return $value;
    }
    
    public function removeAssc($data)
    {
        $sapCustId = $data['sap_cust_id'];
        $customerId = $data['customer_id'];
        $custUserMapId = $data['id'];
        
        $customerCollection = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('parent_id', array('eq' => $customerId));
        $customerData = $customerCollection->getData();
        $totalChild = count($customerData);
        
        if ($totalChild > 0) {
            $customerArray = array();
            foreach ($customerData as $customer) {
                $customerArray[] = $customer['entity_id'];
            }
            
            $collection = Mage::getModel('magentothem_customuser/custusermap')->getCollection()->addFieldToSelect('cust_user_map_id');
            $collection->addFieldToFilter('sap_cust_id', array('eq' => $sapCustId))
                       ->addFieldToFilter('cust_id', array('in' => $customerArray));
            $custUserData = $collection->getData();
            if (count($custUserData) > 0) {
                $custUserIds = array();
                foreach ($custUserData as $custUser) {
                    $custUserIds[] = $custUser['cust_user_map_id'];
                }
                
                $custUserIds[] = $custUserMapId;
                foreach ($custUserIds as $id) {
                    $model = Mage::getModel('magentothem_customuser/custusermap')->load($id);
                    $model->delete();
                }
            }
        } else {
            $model = Mage::getModel('magentothem_customuser/custusermap')->load($custUserMapId);
            $model->delete();
        }

    }



    public function removeSubuser($data)
    {
        $parentId = $data['parent_id'];
        $subuserId = $data['subuser_id'];
        $custUserMapId = $data['id'];

        $customerCollection = Mage::getModel('customer/customer')->load($subuserId);
        $customerCollection->setParentId(0)->save();

        $model = Mage::getModel('magentothem_customuser/custusermap')->getCollection()->addFieldToFilter('cust_id',$subuserId);
        $custUserData = $model->getData();
        if (count($custUserData) > 0) {
            $custUserIds = array();
            foreach ($custUserData as $custUser) {
                $custUserIds[] = $custUser['cust_user_map_id'];
            }

            $custUserIds[] = $custUserMapId;
            foreach ($custUserIds as $id) {
                $model = Mage::getModel('magentothem_customuser/custusermap')->load($id);
                $model->delete();
            }
        }
    }
}