<?php
/**
 * Extension   Customuser
 *
 * @category   Customuser
 * @package    Magentothem
 * @author     Digitales
 * @copyright
 * @license
 */

class Magentothem_Customuser_Controller_Observer extends Varien_Object
{
    //Event: adminhtml_controller_action_predispatch_start
    public function overrideTheme()
    {
        $request = Mage::app()->getRequest();
        $fullActionName = $request->getRouteName().'_'.$request->getControllerName().'_'.$request->getActionName();
        $customerData = array();
        $adminUser = Mage::getSingleton('admin/session');

        if ($fullActionName == 'adminhtml_customer_save') {
            $customerData = Mage::app()->getRequest()->getPost();

            $customerId = $customerData['customer_id'];

            $customerInfoData = Mage::getModel('customer/customer')->load($customerData['customer_id'])->getData();

            $sapCustUserColl = Mage::getModel('magentothem_customuser/custusermap')->getCollection()->addFieldToFilter('cust_id',$customerId)->addFieldToFilter('is_default','1');
            $sapCustUserArr = $sapCustUserColl->getData();
            $sapCustUserData = $sapCustUserArr[0];

            $customerInfoData['salesarea_id'] = $sapCustUserData['salesarea_id'];
            $customerInfoData['sap_cust_id'] = $sapCustUserData['sap_cust_id'];


              $oldExpDate = date('Y-m-d', strtotime($customerInfoData['expiration_date']));
              $newExpDate = date('Y-m-d', strtotime($customerData['account']['expiration_date']));

              $oldOrdLby = $customerInfoData['orders_lobby'];
              $newOrdLby = $customerData['account']['orders_lobby'];

              $oldInvLby = $customerInfoData['invoices_lobby'];
              $newInvLby = $customerData['account']['invoices_lobby'];

              $oldOrdHstrDys = $customerInfoData['orders_history_days'];
              $newOrdHstrDys = $customerData['account']['orders_history_days'];

              $oldInvHstrDys = $customerInfoData['invoices_days'];
              $newInvHstrDys = $customerData['account']['invoices_days'];

              $oldAddEmails = $customerInfoData['addemail'];
              $newAddEmails = $customerData['account']['addemail'];

              $oldUserStatus = $customerInfoData['user_status'];
              $newUserStatus = $customerData['account']['user_status'];

              $actionName = array();
              if($oldExpDate != $newExpDate) {
                $actionName[] = Mage::helper('adminhtml')->__('Customer Expired Date Update');
              }

              if($oldOrdLby != $newOrdLby || $oldInvLby != $newInvLby || $oldOrdHstrDys != $newOrdHstrDys || $oldInvHstrDys != $newInvHstrDys) {
                $actionName[] = Mage::helper('adminhtml')->__('Customer Preferences Update');
              }

              if($oldAddEmails != $newAddEmails) {
                $actionName[] = Mage::helper('adminhtml')->__('Customer Additional Emails Update');
              }


              if ($oldUserStatus != $newUserStatus) {
                  $actionName[] = Mage::helper('adminhtml')->__('Customer Status Update');
              }

              $actNmCnt = count($actionName);

              if($actNmCnt > 0) {
                $area = Mage::helper('adminhtml')->__('backend');
                $adminUserId = $adminUser->getUser()->getUserId();
                $custDefSaId = $customerInfoData['salesarea_id'];
                $custDefSapCustId = $customerInfoData['sap_cust_id'];

                foreach($actionName as $actnmK => $actnmV) {
                          if ($actnmV) {
                              $bckendUaArr = array(
                                      'entity_id'        => $customerId,
                                      'admin_user_id'    => $adminUserId,
                                      'salesarea_id'     => $custDefSaId,
                                      'sold_to_id'       => $custDefSapCustId,
                                      'action_name'      => $actnmV,
                                      'area'             => $area,
                                      'log_created_date' => date('d-M-y H:i:s'),
                                      'language_code'    => Mage::app()->getStore()->getCode(),
                                      'firstname'        => $customerInfoData['firstname'],
                                      'lastname'         => $customerInfoData['lastname'],
                                      'username'         => $customerInfoData['uid'],
                                      'email'            => $customerInfoData['email'],
                                             );

                              Mage::getModel('magentothem_usermanagement/useractivity')->setData($bckendUaArr)->save();


                          }
                }
              }


              if ($customerData['salesarea_id']) {
                $actName = Mage::helper('adminhtml')->__('Customer Configured Update');
                $area = Mage::helper('adminhtml')->__('backend');
                $adminUserId = $adminUser->getUser()->getUserId();
                $custSaId = $customerData['salesarea_id'];
                $custSapCustIdArr = explode(',', $customerData['sap_cust_id']);

                $cntSapCustId = count($custSapCustIdArr);

                if($cntSapCustId) {
                    foreach($custSapCustIdArr as $csciK => $csciV) {
                      if(!empty($csciV)) {
                      $bckendUaArr = array(
                              'entity_id'        => $customerId,
                              'admin_user_id'    => $adminUserId,
                              'salesarea_id'     => $custSaId,
                              'sold_to_id'       => $csciV,
                              'action_name'      => $actName,
                              'area'             => $area,
                              'log_created_date' => date('d-M-y H:i:s'),
                              'language_code'    => Mage::app()->getStore()->getCode(),
                              'firstname'        => $customerInfoData['firstname'],
                              'lastname'         => $customerInfoData['lastname'],
                              'username'         => $customerInfoData['uid'],
                              'email'            => $customerInfoData['email'],
                                     );

                        Mage::getModel('magentothem_usermanagement/useractivity')->setData($bckendUaArr)->save();
                      }
                    }
                }
              }
        }

        if ($fullActionName == 'adminhtml_customer_removeassociation') {
            $customerData = Mage::app()->getRequest()->getPost();
            $salesAreaArray = Mage::getModel('magentothem_customuser/custusermap')->load($customerData['id'])->getData();

            $customerInfoData = Mage::getModel('customer/customer')->load($customerData['customer_id'])->getData();
            $customerData['customer_id'] = $salesAreaArray['cust_id'];
            $customerData['salesarea_id'] = $salesAreaArray['salesarea_id'];
            $area = Mage::helper('adminhtml')->__('backend');
            $adminUserId = $adminUser->getUser()->getUserId();
            $actionName = Mage::helper('adminhtml')->__('Remove Customer Association');
            if ($actionName) {
                $bckendUaArr = array(
                        'entity_id'        => $customerData['customer_id'],
                        'admin_user_id'    => $adminUserId,
                        'salesarea_id'     => $customerData['salesarea_id'],
                        'sold_to_id'       => $customerData['sap_cust_id'],
                        'action_name'      => $actionName,
                        'area'             => $area,
                        'log_created_date' => date('d-M-y H:i:s'),
                        'language_code'    => Mage::app()->getStore()->getCode(),
                        'firstname'        => $customerInfoData['firstname'],
                        'lastname'         => $customerInfoData['lastname'],
                        'username'         => $customerInfoData['uid'],
                        'email'            => $customerInfoData['email'],
                               );

                Mage::getModel('magentothem_usermanagement/useractivity')->setData($bckendUaArr)->save();

            }
        }


        Mage::getDesign()->setArea('adminhtml')
            ->setTheme((string)Mage::getStoreConfig('design/admin/theme'));
    }
}