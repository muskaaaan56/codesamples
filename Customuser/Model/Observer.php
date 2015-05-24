<?php
/**
 * Extension Customuser
 *
 * @category   Customuser
 * @package    Magentothem
 * @author     Digitales
 */

class Magentothem_Customuser_Model_Observer extends Varien_Object
{
    /**
     * Update customer association
     * @param Varien_Event_Observer $observer
     */

    const XML_PATH_FORGOT_EMAIL_IDENTITY    = 'customer/password/forgot_email_identity';
    const XML_PATH_REMIND_EMAIL_TEMPLATE = 'customer/password/remind_email_template';

    public function customerSaveAfter()
    {
        $customer = Mage::registry('current_customer');
        if(isset($customer) && !empty($customer)) {
            if ($customer && !$customer->getId())
                return $this;

            if(Mage::registry('customer_save_observer_executed_'.$customer->getId()))
                return $this; 

            
            //form post data
            //$observer;
            
            $soldToAss = Mage::app()->getRequest()->getPost();
            $soldToAss['partnerinfo']['customer_id'] = $customer->getId();
            $uniqueSoldTo = array();

            if($customer && $customer->getId()) {
                $magentoCustId = $customer->getId();
            }

            if (isset($soldToAss['sap_cust_id'])) {
                $sapCustId = $soldToAss['sap_cust_id'];
                $uniqueSoldTo = array_unique(explode(',', $sapCustId));
                $uniqueSoldTo = array_filter($uniqueSoldTo);
                $uniqueSoldTo = array_filter($uniqueSoldTo);
            }

            if(isset($soldToAss['salesarea_id'])) {
                $salesareaId = $soldToAss['salesarea_id'];
            }

            $selectSapCustId = array();
            $diffSapCustId = array();
            $filterSapCustId = array();
            if((isset($soldToAss['sap_cust_id']) && !empty($soldToAss['sap_cust_id'])) && (isset($soldToAss['sold_to_id']) && !empty($soldToAss['sold_to_id'])) ) {
                $selectSapCustId = explode(',', $soldToAss['sap_cust_id']);
                $existSapCustId = explode('/', $soldToAss['sold_to_id']);
                $diffSapCustId = array_diff($selectSapCustId, $existSapCustId);
                $filterSapCustId = array_filter($diffSapCustId);

                if(!empty($filterSapCustId)) {
                  $sapcustnotmatch = implode(',', $filterSapCustId).' - Sold-To not match with selected Salesarea.';
                  Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('adminhtml')->__($sapcustnotmatch));
                }
            }

            //$isDefault = $soldToAss['is_default'];

            // Insert New association in parent and subusers
            if(($uniqueSoldTo) && ($salesareaId != '')) {
                foreach ($uniqueSoldTo as $key => $soldToIds) {
                    if ($soldToIds && (!in_array($soldToIds, $filterSapCustId)) ) {
                        $custUserMap = Mage::getModel('magentothem_customuser/custusermap')
                        ->setCustId($magentoCustId)
                        ->setSapCustId($soldToIds)
                        ->setSalesareaId($salesareaId)
                        ->save();

                        $subUsers = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('parent_id',$magentoCustId);
                        $subUserData = $subUsers->getData();
                        foreach ($subUserData as $key => $custSubId) {
                            $custSubUserMap = Mage::getModel('magentothem_customuser/custusermap')
                                ->setCustId($custSubId['entity_id'])
                                ->setSapCustId($soldToIds)
                                ->setSalesareaId($salesareaId)
                                ->save();
                        }
                    }
                }
            }

            // If assigned new sub users then insert association of Parent
            if ($soldToAss['in_customers']) {
                $parent = Mage::getModel('magentothem_customuser/custusermap')->getCollection()->addFieldToFilter('cust_id',$magentoCustId);
                foreach ($soldToAss['in_customers'] as $key => $subUserId) {
                    $subCustMap = Mage::getModel('magentothem_customuser/custusermap')->getCollection();
                    $subCustMapColl = $subCustMap->addFieldToFilter('cust_id',$subUserId);
                    foreach ($subCustMapColl->getData() as $key => $value) {
                        Mage::getModel('magentothem_customuser/custusermap')->setId($value['cust_user_map_id'])
                        ->delete();
                    }

                    foreach ($parent->getData() as $key => $value) {
                        $model = Mage::getModel('magentothem_customuser/custusermap');
                        $model->setCustId($subUserId)
                            ->setSapCustId($value['sap_cust_id'])
                            ->setSalesareaId($value['salesarea_id']);
                            $model->save();
                    }
                }
            }


            // For LA and subuser save default and permission information
            else if(isset($soldToAss['isdefault'])) {
                if (isset($soldToAss['unchecked_create_order']) && $soldToAss['unchecked_create_order'] != '') {
                    $uncheckSoldTo = explode(',',$soldToAss['unchecked_create_order']);
                }

                if (isset($soldToAss['checked_create_order']) && $soldToAss['checked_create_order'] != '') {
                    $checkSoldTo = explode(',',$soldToAss['checked_create_order']);
                }

                if (isset($soldToAss['unchecked_view_order']) && $soldToAss['unchecked_view_order'] != '') {
                    $uncheckViewSoldTo = explode(',',$soldToAss['unchecked_view_order']);
                }

                if (isset($soldToAss['checked_view_order']) && $soldToAss['checked_view_order'] != '') {
                    $checkViewSoldTo = explode(',',$soldToAss['checked_view_order']);
                }

                $parent = Mage::getModel('magentothem_customuser/custusermap')->getCollection()->addFieldToFilter('cust_id',$magentoCustId);
                foreach ($parent->getData() as $key => $value) {
                    $model = Mage::getModel('magentothem_customuser/custusermap');
                    $model->setId($value['cust_user_map_id'])
                        ->setCustId($magentoCustId)
                        ->setSapCustId($value['sap_cust_id'])
                        ->setSalesareaId($value['salesarea_id']);
                        if (in_array($value['sap_cust_id'],$soldToAss['can_create_order']) || in_array($value['sap_cust_id'],$checkSoldTo)) {
                            $model->setCanCreateOrder(1);
                        }
                        else if (in_array($value['sap_cust_id'],$uncheckSoldTo)) {
                            $model->setCanCreateOrder(0);
                        }

                        if (in_array($value['sap_cust_id'],$soldToAss['can_view_order']) || in_array($value['sap_cust_id'],$checkViewSoldTo)) {
                            $model->setCanViewOrder(1);
                        }
                        else if (in_array($value['sap_cust_id'],$uncheckViewSoldTo)) {
                            $model->setCanViewOrder(0);
                        }

                        if (in_array($value['cust_user_map_id'],$soldToAss['isdefault'])) {
                            $model->setIsDefault(1);
                        }
                        else {
                            $model->setIsDefault(0);
                        }

                        $model->save();
                }
            }

            // Save Role Mapping
            if (!empty($soldToAss['role_name'])) {
                    $cuRoleId = Mage::getModel('magentothem_userrole/role')->getCollection()->addFieldToFilter('role_code',array('eq' => 'standard'))->getData();
                    $defaultRoleId = $cuRoleId[0]['magentothem_role_id'];
                    $flagChkStandard = FALSE;
                if ($soldToAss['role_map_id']) {
                    $roleMapArray = explode(',',$soldToAss['role_map_id']);
                    foreach ($roleMapArray as $key => $value) {
                        $data = explode(':',$value);
                        $dbData[$data[0]] = $data[1];
                    }

                    foreach ($soldToAss['role_name'] as $key => $value) {
                        if (in_array($value,$dbData) && count($soldToAss['role_name']) == count($dbData)) {
                            continue;
                        }

                        else {
                            foreach ($dbData as $userroleid => $roleid) {
                                Mage::getModel('magentothem_userrole/userrole')
                                    ->setId($userroleid)
                                    ->delete();
                            }

                         Mage::getModel('magentothem_userrole/userrole')
                            ->setUserId($magentoCustId)
                            ->setRoleId($value)
                            ->save();
                        }

                        if($value == $defaultRoleId ) {
                        $flagChkStandard = TRUE;
                        }
                    }

                   if(!$flagChkStandard)
                   {
                            Mage::getModel('magentothem_userrole/userrole')
                            ->setUserId($magentoCustId)
                            ->setRoleId($defaultRoleId)
                            ->save();
                   }
                }
                else {
                    foreach ($soldToAss['role_name'] as $key => $value) {
                       if($value == $defaultRoleId ) {
                        $flagChkStandard = TRUE;
                       }

                        Mage::getModel('magentothem_userrole/userrole')
                            ->setUserId($magentoCustId)
                            ->setRoleId($value)
                            ->save();
                    }

                   if(!$flagChkStandard)
                   {
                            Mage::getModel('magentothem_userrole/userrole')
                            ->setUserId($magentoCustId)
                            ->setRoleId($defaultRoleId)
                            ->save();
                   }
                }
            }

            /** ADMIN KEY USER ROLE **/
              /*
              take current selected role id
              check it's name in magentothem_role  == "key user"
              if match found : 
              [
                    entry should be in 2 tables : admin_user
                                                  admin_role
                    check if already exists or not
                    if not exists
                      add as a admin user as per done in Mage/Adminhtml/controllers/Permission/save action.
                    then update it.
              ]
              else // means unassigned 
              [

                entry should be in 2 tables : admin_user
                                              admin_role
                check if already exists or not

                if exists
                          delete entry from  2 tables : admin_user
                                                        admin_role
              ]
              */


                $keyUserAssign = 'Key User';
                $keyUserFlag = FALSE;
                $kuRoleId = '';

                //Store posted values in some variables
                $firstname = $soldToAss['account']['firstname'];
                $lastname = $soldToAss['account']['lastname'];
                $email = $soldToAss['account']['email'];
                $username = $customer->getUid();

                //Check If role name has been passed or not..

                if (!empty($soldToAss['role_name'])) {
                        //Check if passed role name is == Key User..
                        foreach ($soldToAss['role_name'] as $key => $value) {
                            $selRoleId = $value;
                            $usrRoleData = Mage::getModel('magentothem_userrole/role')->load($selRoleId)->getData();

                            //Check if Selected Role Id == "Key User" role id and role name == 'Key User', if yes then set flag = TRUE
                            if($usrRoleData['role_name'] == $keyUserAssign) {
                                //Set Key User Flag = TRUE , if "Key User" role has been assigned and posted
                                $keyUserFlag = TRUE;
                                break;
                            }
                        }
                }

                //Get User Id if already exists with the same role id
                $userId = '';
                $userExists = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username', array('like' => '%'.$username.'%'))->getData();

                //If modifying user is admin himself,  then do nothing.
                $allowModify = FALSE;
                if(!empty($userExists)) {
                  $userId = $userExists[0]['user_id'];
                  if($userId == 1) {
                    $allowModify = FALSE;
                  }
                  else {
                    $allowModify = TRUE;
                    $keyUserFlag = TRUE;
                  }
                }
                else {
                    $allowModify = TRUE;
                    //$keyUserFlag = TRUE;
                }

                //Get Admin Role Id for where Role Name == Key User..
                $adminRoleData = Mage::getModel('admin/role')->getCollection()->addFieldToFilter('role_name', $keyUserAssign)->getData();

                $kuRoleId = array();
                $kuRoleId[] = $adminRoleData[0]['role_id'];
                $adminUsrMdl = Mage::getModel('admin/user');
                try{
                  if($allowModify) {
                    if($keyUserFlag) {
                      $adminUsrData = array();
                      if($firstname == '' || $firstname == NULL) {
                        $adminUsrData['firstname'] = $userExists[0]['firstname'];
                      }
                      else {
                          $adminUsrData['firstname'] = $firstname;
                      }

                      if($lastname == '' || $lastname == NULL) {
                        $adminUsrData['lastname'] = $userExists[0]['lastname'];
                      }
                      else {
                        $adminUsrData['lastname'] = $lastname;
                      }

                      if($email == '' || $email == NULL) {
                          $adminUsrData['email'] = $userExists[0]['email'];
                      }
                      else {
                        $adminUsrData['email'] = $email;
                      }

                      if($username == '' || $username == NULL) {
                          $adminUsrData['username'] = $userExists[0]['username'];
                      }
                      else {
                        $adminUsrData['username'] = $username;
                      }

                      $createStatusId = Mage::helper('magentothem_customuser')->getUserStatusId('Created');
                      $activeStatusId = Mage::helper('magentothem_customuser')->getUserStatusId('Active');
                      $inactiveStatusId = Mage::helper('magentothem_customuser')->getUserStatusId('Inactive');

                      if($soldToAss['account']['user_status'] == $createStatusId || $soldToAss['account']['user_status'] == $inactiveStatusId) {
                        $adminUsrData['is_active'] = 0;
                      }
                      else if($soldToAss['account']['user_status'] == $activeStatusId) {
                        $adminUsrData['is_active'] = 1;
                      }

                      $logpsw = '';

                      if(empty($userExists) && !empty($soldToAss['account']['password']) && !empty($soldToAss['account']['password_confirmation'])) {
                        $psw = $soldToAss['account']['password'];
                        $pswConfirm = $soldToAss['account']['password_confirmation'];
                        $adminUsrData['password'] = $psw;
                        $adminUsrData['password_confirmation'] = $pswConfirm;
                        $logpsw = $psw;
                      }

                      // Remove User Exist condition as it not require as per current implementation
                      else if(!empty($soldToAss['account']['new_password']) && !empty($soldToAss['account']['new_password'])) {
                        $pswNew = $soldToAss['account']['new_password'];
                        $pswConfirm = $soldToAss['account']['password_confirmation'];
                        $adminUsrData['new_password'] = $pswNew;
                        $adminUsrData['password_confirmation'] = $pswConfirm;
                        $logpsw = $pswNew;
                      }
                      else if(empty($soldToAss['account']['password']) && empty($soldToAss['account']['new_password']) && ($soldToAss['account']['user_status'] == $activeStatusId) && empty($userExists[0]['password']) ) {
                        $custObj = Mage::getModel('customer/customer');
                        $psw = $custObj->generatePassword();
                        $adminUsrData['password'] = $psw;
                        $logpsw = $psw;
                      }


                      if(!empty($userId)) {
                        $adminUsrData['user_id'] = $userId;
                        $adminUsrMdl = Mage::getModel('admin/user')->load($userId);
                      }

                      /*
                       * Unsetting new password and password confirmation if they are blank
                       */
                      if ($adminUsrMdl->hasNewPassword() && $adminUsrMdl->getNewPassword() === '') {
                          $adminUsrMdl->unsNewPassword();
                      }

                      if ($adminUsrMdl->hasPasswordConfirmation() && $adminUsrMdl->getPasswordConfirmation() === '') {
                          $adminUsrMdl->unsPasswordConfirmation();
                      }

                      $adminUsrMdl->setData($adminUsrData);

                      if(isset($soldToAss['partnerinfo']['partner_type'])) {
                          $result = $adminUsrMdl->validate();
                          if (is_array($result)) {
                            Mage::getSingleton('adminhtml/session')->setUserData($adminUsrData);
                            foreach ($result as $message) {
                                Mage::getSingleton('adminhtml/session')->addError($message);
                            }

                            $this->_redirect('*/*/edit', array('_current' => TRUE));
                            return $this;
                          }
                      }

                      $adminUsrMdl->save();

                      $adminUsrMdl->setRoleIds($kuRoleId)->setRoleUserId($adminUsrMdl->getUserId())->saveRelations();

                      $websiteId = $soldToAss['account']['website_id'];


                      if($logpsw != '') {
                        $magelogstr = 'Username:'.$username.' --- psw: '.$logpsw."\n";
                        Mage::log($magelogstr, NULL, 'mylogfile.log');
                        $this->sendKeyUserPasswordEmail($adminUsrMdl, $websiteId, $logpsw);
                      }
                    }

                    /** @TODO Please remove comments once everything works fine
                    else{
                      $getKeyUser = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username', array('like' => '%'.$username.'%'));
                      $getKeyUser->getSelect()->join( array('ar' => 'admin_role'), 'ar.parent_id = '.$kuRoleId[0].' and ar.user_id = main_table.user_id and ar.role_type = "U" ', 'ar.role_id');
                      if($getKeyUser->count() > 0) {
                            $model = Mage::getModel('admin/user');
                            $model->setId($userId);
                            $model->delete();
                      }
                    }
                    */
                  }
                }
                catch(Exception $e){
                  echo $e->getMessage();
                }
                //}

            Mage::register('customer_save_observer_executed_'.$customer->getId(),TRUE);

        }


        /** END : ADMIN KEY USER ROLE **/
    }


    /**
     * Send email with reset password confirmation link
     *
     * @return Mage_Admin_Model_User
     */
    public function sendKeyUserPasswordEmail($keyUsrMdl, $websiteId, $psw)
    {
      $keyuser = $keyUsrMdl->getData();
      $email = $keyuser['email'];
      $name = $keyuser['name'];

      $keyUsrMdl->setPassword($psw);

        $storeId = Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId();

        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($email, $name);
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_IDENTITY));

        $mailer->setStoreId($storeId);
        $mailer->setTemplateId(Mage::getStoreConfig(self::XML_PATH_REMIND_EMAIL_TEMPLATE));
        $mailer->setTemplateParams(array('customer' => $keyUsrMdl));
        $mailer->send();
        //return $this;
    }
}