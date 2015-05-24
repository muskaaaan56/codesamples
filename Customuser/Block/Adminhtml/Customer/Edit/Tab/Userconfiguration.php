<?php
/**
* @category Axaltacore
* @package Customuser
* @author Digitales
*/

class Axaltacore_Customuser_Block_Adminhtml_Customer_Edit_Tab_Userconfiguration extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
     /**
     * Initialize form object
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses
     */
    protected function _prepareForm()
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customermodel = Mage::registry('current_customer');
        $disabled = FALSE;

        // Table for mapping data
        $customerId = $customermodel->getEntityId();
        $parentId = $customermodel->getParentId();
        $websiteId = $customermodel->getWebsiteId();
        $_websites = Mage::app()->getWebsites();

        if ($customerId) {
            $partnerInfo = Mage::getModel('axaltacore_customuser/partnerinfo')->getCollection()->addFieldToFilter('customer_id',$customerId)->getData();
        }

        foreach($_websites as $website){
            if($websiteId == $website->getId())
            {
                $websiteCode = $website->getCode();
            }
        }
        
        $require = TRUE;
        if ($parentId) {
            $require = FALSE;
            $disabled = TRUE;
        }
        else if ($customerId) {
            $model = Mage::getModel('axaltacore_customuser/custusermap')->getCollection()->addFieldToFilter('cust_id',$customerId);
            $count = $model->count();
            if (!$model->count()) {
                $require = TRUE;
            }
            else {
                $require = FALSE;
            }
        }

        $form = new Varien_Data_Form();

        if ($websiteCode == Axaltacore_Usermanagement_Helper_Data::LA_WEBSITE) {
            $associationFieldset = $form->addFieldset(
                'association_fieldset',
                array('legend' => Mage::helper('customer')->__('Association'))
            );
            
            
            $salesOrg = array();
            array_unshift($salesOrg, array('label' => 'Please Select', 'value' => ''));

            /**
             * 
             * Called Function for mapping of sales area with User Role
             * @param array of User Information, Role Information
             */
            $salesCollection = Mage::getModel('axaltacore_customuser/salesarea')->getSalesAreas($websiteId);
            $salesData = $salesCollection->getData();
           foreach($salesData as $key => $value) {
                $salesId = $value['salesarea_id'];
                $salesCompany = $value['company_name'];
                $salesOrg[$salesId] = $salesCompany;
           }

            $url = Mage::helper('adminhtml')->getUrl('adminhtml/customer/fetchsoldto');
            $associationFieldset->addField(
                'salesarea_id', 'select', 
                array(
                    'name'     => 'salesarea_id',
                    'label'    => Mage::helper('axaltacore_customuser')->__('Sales Organization'),
                    'title'    => Mage::helper('axaltacore_customuser')->__('Sales Organization'),
                    'values' => $salesOrg,
                    'onchange' => 'fetchsoldto(this.value,\''.$url.'\',\''.$customerId.'\',\''.$websiteCode.'\')',
                    'required' => $require,
                    'disabled' => $disabled,
                )
            );

          $autoUrl = Mage::helper('adminhtml')->getUrl('adminhtml/customer/fetchsoldtoauto');
          $associationFieldset->addField(
              'sap_cust_id',
              'text',
              array(
                'label'     => Mage::helper('axaltacore_customuser')->__('Sold To Id'),
                'required' => $require,
                'name'      => 'sap_cust_id',
                'disabled'  => TRUE,
 //             'after_element_html' => '<input type="hidden" id="autourl" value="'.$autoUrl.'"/>',
                'after_element_html' => '<input type="hidden" id="sold_to_id" name="sold_to_id" value=""/><div id="note_text" style="display:none">No result found for selected sales organization please select other sales organization</div>',
              )
          );

          $soldTo = array();
          $soldTo[''] = 'Please Select';
        }

        if ($websiteCode == Axaltacore_Usermanagement_Helper_Data::NA_WEBSITE && !empty($partnerInfo) && $partnerInfo[0]['user_type'] == 'partner')
        {
            $associationFieldset = $form->addFieldset(
                'association_fieldset',
                array('legend' => Mage::helper('customer')->__('Association'))
            );

            $fetchSoldTo = Mage::getModel('axaltacore_customuser/partnerinfo')->getNaSoldTo($customerId);
            if (!$fetchSoldTo['soldto']) {
              $display = 'display:""';
            }
            else {
              $display = 'display:none';
            }

            $associationFieldset->addField(
                'sap_cust_id',
                'text',
                array(
                  'label'     => Mage::helper('axaltacore_customuser')->__('Sold To'),
                  'required' => $require,
                  'name'      => 'sap_cust_id',
                  'after_element_html' => '<input type="hidden" id="sold_to_id" name="sold_to_id" value="'.$fetchSoldTo['soldto'].'"/><input type="hidden" id="salesarea_id" name="salesarea_id" value="'.$fetchSoldTo['salesarea_id'].'"/><div id="note_text" style="'.$display.'">No result found for selected sales organization please select other sales organization</div>',
                )
            );

        }

        if ($websiteCode == Axaltacore_Usermanagement_Helper_Data::NA_WEBSITE) {
            $form->addField(
                'unchecked_create_order',
                'hidden',
                array(
                  'name' => 'unchecked_create_order',
                  'id'   => 'unchecked_create_order',
                )
            );

            $form->addField(
                'checked_create_order',
                'hidden',
                array(
                  'name' => 'checked_create_order',
                  'id'   => 'checked_create_order',
                )
            );

            $form->addField(
                'unchecked_view_order',
                'hidden',
                array(
                  'name' => 'unchecked_view_order',
                  'id'   => 'unchecked_view_order',
                )
            );

            $form->addField(
                'checked_view_order',
                'hidden',
                array(
                  'name' => 'checked_view_order',
                  'id'   => 'checked_view_order',
                )
            );
        }

            $customerData = $customermodel->getData();
            $roleData = Mage::getModel('axaltacore_userrole/role')->getCollection()->addFieldToFilter('website_id',array('eq' => $websiteId))->getData();
            foreach ($roleData as $role) {
                $id = $role['axaltacore_role_id'];
                $name = $role['role_name'];
                if ($id != 0) {
                    $result[] = array(
                                    'value' => $id,
                                    'label' => $name
                                );
                }
            }
        
        /* Not showing role for Partner */
        if (($websiteCode == Axaltacore_Usermanagement_Helper_Data::LA_WEBSITE) || ($websiteCode == Axaltacore_Usermanagement_Helper_Data::NA_WEBSITE && !empty($partnerInfo) && $partnerInfo[0]['user_type'] == 'user')) {
            $roleFieldset = $form->addFieldset(
                'role_fieldset',
                array('legend' => Mage::helper('customer')->__('Role'))
            );

            if($websiteCode == Axaltacore_Usermanagement_Helper_Data::LA_WEBSITE) {
                $disabled = 'disabled';
            }
            else {
                $disabled = '';
            }

            $roleFieldset->addField(
                'role_name', 'multiselect', 
                array(
                    'name'     => 'role_name',
                    'label'    => Mage::helper('axaltacore_customuser')->__('Role'),
                    'title'    => Mage::helper('axaltacore_customuser')->__('Role'),
                    'required' => TRUE,
                    'values'   => $result,
                    'disabled' => $disabled,
                )
            );

            $roleFieldset->addField(
                'role_map_id', 'hidden', 
                array(
                    'name' => 'role_map_id'
                )
            );
        }

       if(!empty($customerData)) {
            $userRoleModel = Mage::getModel('axaltacore_userrole/userrole')->getCollection()->addFieldToFilter('user_id',array('eq' => $customerId));
            $userRoleModelData = $userRoleModel->getData();
            $count = 0;
            foreach($userRoleModelData as $k => $v) {
              if ($count == 0) {
                  $customerData['role_map_id'] = $v['user_role_id'].':'. $v['role_id'];
              }
              else {
                  $customerData['role_map_id'] .= ','.$v['user_role_id'].':'.$v['role_id'];
              }

              $customerData['role_name'][] = $v['role_id'];
              $count++;
            }
       }

        $form->setValues($customerData);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('axaltacore_customuser')->__('User Configuration');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('axaltacore_customuser')->__('User Configuration');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return TRUE
     */
    public function canShowTab()
    {
        return TRUE;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return TRUE
     */
    public function isHidden()
    {
        return FALSE;
    }

}