<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/

require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'CustomerController.php');

class Magentothem_Customuser_Adminhtml_CustomerController extends Mage_Adminhtml_CustomerController
{
    CONST NA_PERFECTION  = 'http://perfectionsoftware.hopto.org/perf/EpoMsgReceiver.external';
    CONST NA_COMCEPT = 'http://biztalk1.comcept.net/DPCServices/OrderUpdates.aspx.external';
    
    protected function _initCustomer($idFieldName='id')
    {
        $this->_title($this->__('Customers'))->_title($this->__('Manage Customers'));

        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        $partnerInfo = Mage::getModel('magentothem_customuser/partnerinfo')->getCollection()->addFieldToFilter('customer_id',$customerId);
        $partnerInfoData = $partnerInfo->getData();

        if (!empty($partnerInfoData)) {
            $existingInfo = Mage::getModel('magentothem_customuser/partnerinfo')->load($partnerInfoData[0]['partnerinfo_id']);
            Mage::register('partner_info', $existingInfo);
        }

        return $this;
    }

    /**
     * Get sold-to for the selected sales-area
    */
    public function fetchsoldtoAction()
    {
        $salesareaid = $this->getRequest()->getPost('salesareaid');
        $magentoCustId = $this->getRequest()->getPost('customerid');
        $website = $this->getRequest()->getPost('websitecode');
        $salesareaData = Mage::getModel('magentothem_customuser/salesarea')->load($salesareaid)->getData();
        $salesOrgId = $salesareaData['sales_organization_id'];
        $distributionChannel = $salesareaData['distribution_channel'];
        $division = $salesareaData['division'];

        $custUserMap = Mage::getModel('magentothem_customuser/custusermap')->getCollection();
        // Map sales area, sold to - Admin side
        $custUserMapall = Mage::getModel('magentothem_customuser/custusermap')->getCollection();
        $custUserMapFilter = $custUserMapall->addFieldToFilter('cust_id',$magentoCustId)->addFieldToFilter('salesarea_id',$salesareaid);
        $existSoldTo = array();

        foreach ($custUserMapFilter->getData() as $key => $value) {
            $existSoldTo[] = $value['sap_cust_id'];
        }

        $custMasterCollection = Mage::getModel('magentothem_customuser/custmaster')->getCollection()
                                ->addFieldtoFilter('sales_organization_id',$salesOrgId)
                                ->addFieldtoFilter('distribution_channel',$distributionChannel)
                                ->addFieldtoFilter('division',$division)
                                ->addFieldToFilter('website_code',$website);

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

            $this->getResponse()->setBody($data);
        }
        else {
            $this->getResponse()->setBody($this->__('No result found for selected sales organization please select other sales organization'));
        }
    }



    /**
    * Prepare sold-to/salesarea association form and grid 
    **/
    public function userconfigurationAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
    * Remove selected sold-to/salesarea association from Database 
    **/
    public function removeassociationAction()
    {
        $data = $this->getRequest()->getPost();
        try {
            $model = Mage::getModel('magentothem_customuser/custusermap')->removeAssc($data);
            echo 'success';
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            echo 'success';
        }
    }

    /**
    * Get selected parent's sold-to/salesarea association
    **/
    public function fetchassociationAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
    * Save customer action
    */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();

        if ($data) {
            $redirectBack = $this->getRequest()->getParam('back', FALSE);
            $this->_initCustomer('customer_id');

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::registry('current_customer');

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setEntity($customer)
                ->setFormCode('adminhtml_customer')
                ->ignoreInvisible(FALSE);

            $formData = $customerForm->extractData($this->getRequest(), 'account');

            // Handle 'disable auto_group_change' attribute
            if (isset($formData['disable_auto_group_change'])) {
                $formData['disable_auto_group_change'] = empty($formData['disable_auto_group_change']) ? '0' : '1';
            }

            $parent = Mage::getModel('magentothem_customuser/custusermap')->getCollection()->addFieldToFilter('cust_id',$customer->getId());

            if ($parent->count() > 0 && array_key_exists('sap_cust_id',$data) && !isset($data['isdefault']) && !isset($data['partnerinfo']['partner_type'])) {
                $this->_getSession()->addError(Mage::helper('adminhtml')->__('Please assign any one sold-to as default association.'));
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
                return;
            }


            if ((isset($data['sap_cust_id']) && !empty($data['sap_cust_id'])) && (isset($data['sold_to_id']) && !empty($data['sold_to_id'])) ) {
                $selectSapCustId = explode(',', $data['sap_cust_id']);
                $existSapCustId = explode('/', $data['sold_to_id']);
                $diffSapCustId = array_diff($selectSapCustId, $existSapCustId);

                /*
                 * if All the entered(comma seperated) sap_cust_id do not match with already exist ('/' sepearated hidden) sold_to_id for the     selected salesarea.
                */
                $reDiff = array_diff($selectSapCustId,$diffSapCustId);
                if(empty($reDiff)) { 
                    $this->_getSession()->addError(Mage::helper('adminhtml')->__('Please select appropriate Sold-to for selected SalesArea.'));
                    $this->_getSession()->setCustomerData($data);
                    $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
                    return;
                }
            }



            $errors = $customerForm->validateData($formData);
            if ($errors !== TRUE) {
                foreach ($errors as $error) {
                    $this->_getSession()->addError($error);
                }

                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
                return;
            }

            $customerForm->compactData($formData);

            // Unset template data
            if (isset($data['address']['_template_'])) {
                unset($data['address']['_template_']);
            }

            $modifiedAddresses = array();
            if (!empty($data['address'])) {
                /** @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(FALSE);

                foreach (array_keys($data['address']) as $index) {
                    $address = $customer->getAddressItemById($index);
                    if (!$address) {
                        $address = Mage::getModel('customer/address');
                    }

                    $requestScope = sprintf('address/%s', $index);
                    $formData = $addressForm->setEntity($address)
                        ->extractData($this->getRequest(), $requestScope);

                    // Set default billing and shipping flags to address
                    if (isset($data['account']['default_billing']) && $data['account']['default_billing'] == $index) {
                        $isDefaultBilling = TRUE;
                    }
                    else {
                        $isDefaultBilling = FALSE;
                    }

                    $address->setIsDefaultBilling($isDefaultBilling);

                    if (isset($data['account']['default_shipping']) && $data['account']['default_shipping'] == $index) {
                        $isDefaultShipping = TRUE;
                    }
                    else {
                        $isDefaultShipping = FALSE;
                    }

                    $address->setIsDefaultShipping($isDefaultShipping);
                    $errors = $addressForm->validateData($formData);
                    if ($errors !== TRUE) {
                        foreach ($errors as $error) {
                            $this->_getSession()->addError($error);
                        }

                        $this->_getSession()->setCustomerData($data);
                        $this->getResponse()->setRedirect(
                            $this->getUrl(
                                '*/customer/edit',
                                array(
                                  'id' => $customer->getId()
                                )
                            )
                        );
                        return;
                    }

                    $addressForm->compactData($formData);

                    // Set post_index for detect default billing and shipping addresses
                    $address->setPostIndex($index);

                    if ($address->getId()) {
                        $modifiedAddresses[] = $address->getId();
                    } else {
                        $customer->addAddress($address);
                    }
                }
            }

            // Default billing and shipping
            if (isset($data['account']['default_billing'])) {
                $customer->setData('default_billing', $data['account']['default_billing']);
            }

            if (isset($data['account']['default_shipping'])) {
                $customer->setData('default_shipping', $data['account']['default_shipping']);
            }

            if (isset($data['account']['confirmation'])) {
                $customer->setData('confirmation', $data['account']['confirmation']);
            }

            // Mark not modified customer addresses for delete
            foreach ($customer->getAddressesCollection() as $customerAddress) {
                if ($customerAddress->getId() && !in_array($customerAddress->getId(), $modifiedAddresses)) {
                    $customerAddress->setData('_deleted', TRUE);
                }
            }

            if (Mage::getSingleton('admin/session')->isAllowed('customer/newsletter')) {
                $customer->setIsSubscribed(isset($data['subscription']));
            }

            if (isset($data['account']['sendemail_store_id'])) {
                $customer->setSendemailStoreId($data['account']['sendemail_store_id']);
            }

            $isNewCustomer = $customer->isObjectNew();
            try {
                $sendPassToEmail = FALSE;
                // Force new customer confirmation
                if ($isNewCustomer) {
                    $customer->setPassword($data['account']['password']);
                    $customer->setForceConfirmed(TRUE);
                    if ($customer->getPassword() == 'auto') {
                        $sendPassToEmail = TRUE;
                        $customer->setPassword($customer->generatePassword());
                    }
                }

                Mage::dispatchEvent(
                    'adminhtml_customer_prepare_save',
                    array(
                        'customer'  => $customer,
                        'request'   => $this->getRequest()
                    )
                );

                $currentDate = date('Y-m-d');
                if(isset($data['account']['expiration_date'])) {
                    $postDate = date('Y-m-d', strtotime($data['account']['expiration_date']));

                    /* Change User Status if expiry date less than or equals to current date */
                    if ($postDate <= $currentDate ) {
                        $optionId = Mage::helper('magentothem_customuser')->getUserStatusId('Inactive');
                        $customer->setUserStatus($optionId);
                    }
                }
            
                $addEmail = $data['account']['addemail'];
                $currentAddEmail = $customer->getAddemail();
                if($currentAddEmail != $addEmail) {
                    if($addEmail && $addEmail != '') {
                        $addEmails = explode(',', $addEmail);
                        $addiEmails = array_unique($addEmails);
                        $emailError = FALSE;
                        foreach($addiEmails as $addemail) {
                            if (!Zend_Validate::is($addemail, 'EmailAddress')) {
                                $emailError = TRUE;
                            }
                        }

                        if($emailError) {
                            $this->_getSession()->addError(Mage::Helper('magentothem_usermanagement')->__('Enter valid email in Additional Email Address'));
                            $this->_getSession()->setCustomerData($data);
                            $this->_redirect('*/customer/edit', array('id' => $customer->getId(), '_current' => TRUE));
                            return;
                        }

                        $addEmail = implode(',', $addiEmails);
                    }

                    $customer->setAddemail($addEmail);
                }

                $customer->save();
                $code = Mage::getModel('core/website')->load($data['account']['website_id_value'])->getData('code');
                $partnerInfoModel = Mage::getModel('magentothem_customuser/partnerinfo');
                $customerId = $customer->getId();
                $partnerInfo = Mage::getModel('magentothem_customuser/partnerinfo')->getCollection()->addFieldToFilter('customer_id',$customerId);
                $partnerInfoData = $partnerInfo->getData();
                $partnerInfoModel = Mage::getModel('magentothem_customuser/partnerinfo');
                if($this->getRequest()->getParam('partner')) {
                    if (!empty($partnerInfoData)) {
                            $partnerInfoModel
                                 ->setUserType('partner')
                                 ->setPartnerType($data['partnerinfo']['partner_type'])
                                 ->setIsJmsPartner($data['partnerinfo']['is_jms_partner'])
                                 ->setJmsSystemUrl($data['partnerinfo']['jms_system_url'])
                                 ->setJmsSystemUsername($data['partnerinfo']['jms_system_username'])
                                 ->setJmsSystemPassword($data['partnerinfo']['jms_system_password'])
                                 ->setOwnerName($data['partnerinfo']['owner_name'])
                                 ->setOwnerEmail($data['partnerinfo']['owner_email'])
                                 ->setStopPrintInvoiceDate($data['partnerinfo']['stop_print_invoice_date'])
                                 ->setId($partnerInfoData[0]['partnerinfo_id'])
                                 ->save();
                    }
                    else {
                        $partnerInfoModel->setCustomerId($customerId)
                                 ->setUserType('partner')
                                 ->setPartnerType($data['partnerinfo']['partner_type'])
                                 ->setIsJmsPartner($data['partnerinfo']['is_jms_partner'])
                                 ->setJmsSystemUrl($data['partnerinfo']['jms_system_url'])
                                 ->setJmsSystemUsername($data['partnerinfo']['jms_system_username'])
                                 ->setJmsSystemPassword($data['partnerinfo']['jms_system_password'])
                                 ->setOwnerName($data['partnerinfo']['owner_name'])
                                 ->setOwnerEmail($data['partnerinfo']['owner_email'])
                                 ->setStopPrintInvoiceDate($data['partnerinfo']['stop_print_invoice_date'])
                                 ->save();
                    }
                }

                if ($data['in_customers']) {
                    foreach ($data['in_customers'] as $key => $subUserId) {
                        $subUserObj = Mage::getModel('customer/customer')->load($subUserId);
                        $subUserObj->setParentId($customer->getId());
                        $subUserObj->save();
                    }
                }

                // Send welcome email
                if ($customer->getWebsiteId() && (isset($data['account']['sendemail']) || $sendPassToEmail)) {
                    $storeId = $customer->getSendemailStoreId();
                    if ($isNewCustomer) {
                        $customer->sendNewAccountEmail('registered', '', $storeId);
                    } elseif ((!$customer->getConfirmation())) {
                        // Confirm not confirmed customer
                        $customer->sendNewAccountEmail('confirmed', '', $storeId);
                    }
                }

                if (!empty($data['account']['new_password'])) {
                    $newPassword = $data['account']['new_password'];
                    if ($newPassword == 'auto') {
                        $newPassword = $customer->generatePassword();
                    }

                    $customer->changePassword($newPassword);
                    $customer->sendPasswordReminderEmail();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The customer has been saved.')
                );

                Mage::dispatchEvent(
                    'adminhtml_customer_save_after',
                    array(
                        'customer'  => $customer,
                        'request'   => $this->getRequest()
                    )
                );

                if ($redirectBack) {
                    $this->_redirect(
                        '*/*/edit', 
                        array(
                            'id' => $customer->getId(),
                            '_current' => TRUE
                        )
                    );
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('adminhtml')->__('An error occurred while saving the customer.')
                );
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
                return;
            }
        }

        if(isset($data['account']['website_id']) && $data['account']['website_id'] != '') {
          $custWebsiteId = $data['account']['website_id'];
        }
        else if(isset($data['account']['website_id_value'])) {
          $custWebsiteId = $data['account']['website_id_value'];
        }

        $custWebsiteCode = Mage::getModel('core/website')->load($custWebsiteId)->getData('code');


        $returnUrl = $this->getUrl('*/customer');
        if($custWebsiteCode == Magentothem_Usermanagement_Helper_Data::NA_WEBSITE) {
          if(isset($data['partnerinfo']['partner_type'])) {
            $returnUrl = $this->getUrl('*/customer_napartner');
          }
          else {
              $returnUrl = $this->getUrl('*/customer_nauser');
          }
        }

        $this->getResponse()->setRedirect($returnUrl);
    }

    /**
    * Prepare associated subuser grid 
    **/
    public function subuserAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
    * Prepare assign subuser grid 
    **/
    public function assignuserAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
    * Partner Information form for NA Region
    */
    public function userinfoAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
    * Remove selected subuser association from Database 
    **/
    public function removesubuserAction()
    {
        $data = $this->getRequest()->getPost();
        try {
            $model = Mage::getModel('magentothem_customuser/custusermap')->removeSubuser($data);
            echo 'success';
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            echo 'success';
        }
    }


    /**
    * Prepare subuser grid 
    ** /
    public function userAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function griduserAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }*/

    public function ssouserAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ssousernaAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ssogriduserAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ssogridusernaAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /*public function napartnerAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }*/

    public function validateAction()
    {
        $response       = new Varien_Object();
        $response->setError(0);
        $websiteId      = Mage::app()->getStore()->getWebsiteId();
        $accountData    = $this->getRequest()->getPost('account');

        $customer = Mage::getModel('customer/customer');
        $customerId = $this->getRequest()->getParam('id');
        if ($customerId) {
            $customer->load($customerId);
            $websiteId = $customer->getWebsiteId();
        } else if (isset($accountData['website_id'])) {
            $websiteId = $accountData['website_id'];
        }

        /* @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setEntity($customer)
            ->setFormCode('adminhtml_customer')
            ->setIsAjaxRequest(TRUE)
            ->ignoreInvisible(FALSE);

        $data   = $customerForm->extractData($this->getRequest(), 'account');
        $errors = $customerForm->validateData($data);
        if ($errors !== TRUE) {
            foreach ($errors as $error) {
                $this->_getSession()->addError($error);
            }

            $response->setError(1);
        }

        # additional validate email
        /*if (!$response->getError()) {
            # Trying to load customer with the same email and return error message
            # if customer with the same email address exisits
            $checkCustomer = Mage::getModel('customer/customer')
                ->setWebsiteId($websiteId);
            $checkCustomer->loadByEmail($accountData['email']);
            if ($checkCustomer->getId() && ($checkCustomer->getId() != $customer->getId())) {
                $response->setError(1);
                $this->_getSession()->addError(
                    Mage::helper('adminhtml')->__('Customer with the same email already exists.')
                );
            }
        }*/

        $addressesData = $this->getRequest()->getParam('address');
        if (is_array($addressesData)) {
            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('customer/form');
            $addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(FALSE);
            foreach (array_keys($addressesData) as $index) {
                if ($index == '_template_') {
                    continue;
                }

                $address = $customer->getAddressItemById($index);
                if (!$address) {
                    $address   = Mage::getModel('customer/address');
                }

                $requestScope = sprintf('address/%s', $index);
                $formData = $addressForm->setEntity($address)
                    ->extractData($this->getRequest(), $requestScope);

                $errors = $addressForm->validateData($formData);
                if ($errors !== TRUE) {
                    foreach ($errors as $error) {
                        $this->_getSession()->addError($error);
                    }

                    $response->setError(1);
                }
            }
        }

        if ($response->getError()) {
            $this->_initLayoutMessages('adminhtml/session');
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }



    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer');
    }
    
    public function sendidocAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function sendidocajaxAction()
    {
        $fromDate = $this->getRequest()->getPost('from_date');
        $toDate = $this->getRequest()->getPost('to_date');
        $soldTo = $this->getRequest()->getPost('sold_to');
        $customerId = $this->getRequest()->getPost('customerId');
        $partnerInfo = Mage::getModel('magentothem_customuser/partnerinfo')->getCollection()->addFieldToFilter('customer_id',$customerId)->getData();
        if(isset($partnerInfo) && !empty($partnerInfo)) {
            if($partnerInfo[0]['is_jms_partner'] == '1') {
                $jmsUrl = $partnerInfo[0]['jms_system_url'];
                $collection = Mage::getModel('magentothem_customuser/custusermap')->getCollection()->addFieldToFilter('cust_user_map_id',$soldTo);
                $data = $collection->getData();
                if(isset($data) && !empty($data)) {
                    $soldTo = $data[0]['sap_cust_id'];
                    $idocCollection = Mage::getModel('idocs/idoc')->getCollection()->addFieldToFilter('sold_to_ref_num',$soldTo);
                    $idocCollection->addFieldToFilter(
                        'create_date',
                        array(
                            'from' => $fromDate,
                            'to' => $toDate,
                            'date' => TRUE,
                        )
                    );
                    
                    $idocData = $idocCollection->getData();
                    if(isset($idocData) && !empty($idocData)) {
                        foreach($idocData as $idoc) {
                            $docKey = $idoc['document_key'];
                            if($docKey == 'ASN') {
                                $returnData = $this->prepareASN($idoc, $jmsUrl);
                            } else {
                                $returnData = $this->prepareACK($idoc, $jmsUrl);
                            }
                        }
                    } else {
                        echo 'IDOCs are not availabel for this user during selected date period';
                    }
                } else {
                    echo 'Sold To is not available';
                }
            } else {
                echo 'Select Customer is not JMS Partner';
            }
        } else {
            echo 'Select Customer is not JMS Partner';
        }
    }
    
    public function prepareASN($idoc, $jmsUrl)
    {
        $payLoadId = Mage::getModel('idocs/idoc')->generatePayLoadId();
        $timestamp = date('Y-m-d\TH:i:sP');
        $url = '';
        if ($jmsUrl == self::NA_COMCEPT) {
            $vendor = 'Comcept';
            $url = Mage::getStoreConfig('jms/jmsconf/comcept');
        } elseif ($jmsUrl == self::NA_PERFECTION) {
            $vendor = 'Perfection';
            $url = Mage::getStoreConfig('jms/jmsconf/perfection');
        }
        
        $erpOrdNum = $idoc['erp_order_number'];
        $collection = Mage::getModel('magentothem_customcheckout/customorder')->getCollection();
        $collection->getSelect()->join(array('sfo' => 'sales_flat_order'), 'main_table.order_id = sfo.entity_id');
        $collection->getSelect()->join(array('ce' => 'customer_entity'), 'ce.entity_id = sfo.customer_id');
        $collection->getSelect()->join(array('np' => 'na_partnerinfo'), 'np.customer_id = ce.parent_id');
        $collection->getSelect()->where('main_table.sap_order_id = ' . $erpOrdNum);
        $data = $collection->getData();
        
        $refPayloadId = $data[0]['payload_id'];
        $idocNum = str_pad($idoc['idoc_number'],16,'0',STR_PAD_LEFT);
        $senderDetails = Mage::helper('jms')->getUserInfo($refPayloadId);
        
        $mode = 'production';
        if (Mage::getStoreConfig('jms/jmsconf/mode') == 1) {
            $mode = 'test';
        }
        
        $shipmentId = $idoc['sap_delivery_note'];
        $noticeDate = date('Y-M-d');
        $shipmentDate = $idoc['sap_ship_date'];
        
        $soldToDetail = array();
        $shipToDetail = array();
        $idocItemCollection = Mage::getModel('idocs/idoccustomer')->getCollection()->addFieldToFilter('idoc_key',$idoc['idoc_key'])->addFieldToFilter('sap_address_type',array('in' => array('SP','WE')));
        $custRefData = $idocItemCollection->getData();
        if(isset($custRefData) && !empty($custRefData)) {
            foreach ($custRefData as $cust) {
                if ($cust['sap_address_type'] == 'SP') {
                    $soldToDetail['id'] = $cust['orig_system_ref'];
                    $soldToDetail['name'] = $cust['partner_name'];
                }

                if ($cust['sap_address_type'] == 'WE') {
                    $shipToDetail['id'] = $cust['orig_system_ref'];
                    $shipToDetail['name'] = $cust['partner_name'];
                }
            }
        }
        
        if ($idoc['order_status'] == '9') {
            $status = 'Partially Delivered';
        } elseif ($idoc['order_status'] == '9') {
            $status = 'Completed';
        }
        
        $scac = $idoc['scac_code'];
        $compName = $idoc['carrier_name'];
        $proNumber = $idoc['pro_number'];
        $poNumber = $idoc['purchase_order_number'];
        
        $xml .= '<?xml version="1.0" encoding="UTF-8"?><cXML xml:lang="en-US" payloadID="' . $payLoadId . '" timestamp="' . $timestamp . '">
                <Header>
                    <From>
                        <Credential domain="Axalta">
                            <Identity> </Identity>
                        </Credential>
                    </From>
                    <To>
                        <Credential domain="ReceiverURL">
                            <Identity>' . $url . '</Identity>
                        </Credential>
                    </To>
                    <Sender>
                        <Credential domain="JMSUserId">
                            <Identity>' . $senderDetails['identity'] . '</Identity>
                            <SharedSecret>' . $senderDetails['sharedSecret'] . '</SharedSecret>
                        </Credential>
                        <UserAgent>Axalta OMS V1.0.0</UserAgent>
                    </Sender>
                </Header>
                <Request deploymentMode="' . $mode . '">
                    <ShipNoticeRequest>
                        <ShipNoticeHeader shipmentID="' . $shipmentId . '" noticeDate="' . $noticeDate . '" shipmentDate="' . $shipmentDate . '" >
                            <Contact role="ShipTo" addressID="' . $shipToDetail['id'] . '">
                                <Name xml:lang="en-US">' . htmlspecialchars($shipToDetail['name'], (ENT_QUOTES | ENT_XHTML), 'UTF-8') . '</Name>
                            </Contact>
                            <Comments xml:lang="en-US">' . $data[0]['delivery_instruction'] . '</Comments>
                            <Extrinsic name="orderStatus">' . $status . '</Extrinsic>
                            <Extrinsic name="confirmID">' . $shipmentId . '</Extrinsic>
                        </ShipNoticeHeader>
                        <ShipControl>
                            <CarrierIdentifier domain="SCAC">' . $scac . '</CarrierIdentifier>
                            <CarrierIdentifier domain="companyName">' . $compName . '</CarrierIdentifier>
                            <ShipmentIdentifier>' . $proNumber . '</ShipmentIdentifier>
                        </ShipControl>
                        <ShipNoticePortion>
                            <OrderReference orderID="' . $poNumber . '">
                                <DocumentReference payloadID="' . $refPayloadId . '" />
                            </OrderReference>
                            <Contact role="ShipFrom" addressID="' . $soldToDetail['id'] . '">
                                <Name xml:lang="en-US">' . htmlspecialchars($soldToDetail['name'], (ENT_QUOTES | ENT_XHTML), 'UTF-8') . '</Name>
                            </Contact>
                        ';
            $idocItemCollection = Mage::getModel('idocs/idocdetails')->getCollection()->addFieldToFilter('idoc_key',$idoc['idoc_key']);
            $itemData = $idocItemCollection->getData();
            $backOrderArray = array();
            $bIndex = 0;
            if(isset($itemData) && !empty($itemData)) {
                foreach($itemData as $items) {
                    if($items['backordered_quantity'] > 0) {
                        $backOrderArray[$bIndex]['sequence_id'] = intval($items['sequence_id']);
                        $backOrderArray[$bIndex]['sap_material_number'] = $items['sap_material_number'];
                        $backOrderArray[$bIndex]['sku'] = $items['sku'];
                        $backOrderArray[$bIndex]['description'] = $items['description'];
                        $backOrderArray[$bIndex]['sap_order_quantity'] = intval($items['sap_order_quantity']);
                        $backOrderArray[$bIndex]['unit_of_measure'] = $items['unit_of_measure'];
                        $backOrderArray[$bIndex]['unit_list_price'] = $items['unit_list_price'];
                        $backOrderArray[$bIndex]['po_line_item_no'] = $items['po_line_item_no'];
                        $backOrderArray[$bIndex]['rejection_code'] = $items['rejection_code'];
                        $backOrderArray[$bIndex]['rejection_desc'] = $items['rejection_desc'];
                        $backOrderArray[$bIndex]['action_code'] = $items['action_code'];
                        $backOrderArray[$bIndex]['backordered_quantity'] = intval($items['backordered_quantity']);
                        $bIndex++;
                    }
                    
                    $qty = $items['sap_order_quantity'];
                    if($items['po_line_item_no'] != '' && $items['po_line_item_no'] != 0) {
                        $lineNumber = $items['po_line_item_no'];
                    } else {
                        $lineNumber = intval($items['sequence_id']);
                    }

                    $xml .= '<ShipNoticeItem quantity="' . intval($qty) . '" lineNumber="' . $lineNumber . '">
                                    <UnitOfMeasure>' . $items['unit_of_measure'] . '</UnitOfMeasure>
                                </ShipNoticeItem>';
                }
            }
            
            $xml .= '</ShipNoticePortion>
            </ShipNoticeRequest>
                    </Request>
                </cXML>';
        
        
        
        $str = base64_encode($xml);
        $returnData['xml'] = $str;
        $returnData['test'] = $xml;
        $returnData['vendor'] = $vendor;
        $returnData['name'] = 'ASN_' . $erpOrdNum . '_' . ltrim($idocNum, 0);
        $this->sendFile($returnData);
        
        if (isset($backOrderArray) && !empty($backOrderArray)) {
            $arrXml['document_key'] = 'Order Change';
            $arrXml['idoc_number'] = $idoc['idoc_number'];
            $arrXml['erp_order_number'] = $idoc['erp_order_number'];
            $arrXml['purchase_order_number'] = $idoc['purchase_order_number'];
            $arrXml['order_doc_type'] = $idoc['order_doc_type'];
            $arrXml['dist_channel'] = $idoc['dist_channel'];
            $arrXml['sap_request_date'] = $idoc['sap_request_date'];
            $arrXml['sap_order_create_date'] = $idoc['sap_order_create_date'];
            $arrXml['payment_terms'] = $idoc['payment_terms'];
            $arrXml['idoc_key'] = $idoc['idoc_key'];
            $arrXml['item_data'] = $backOrderArray;
            $arrXml['order_status'] = '9';
            $returnData = $this->prepareACK($arrXml, $jmsUrl);
            $this->sendFile($returnData);
        }

        return $returnData;
    }
    
    public function prepareACK($idoc, $jmsUrl)
    {

        if($idoc['document_key'] == 'ACK') {
            $messageType = 'Order ACK';
            $operation = 'new';
        } else {
            $messageType = 'Order Change';
            $operation = 'update';
        }
        
        $erpOrdNum = $idoc['erp_order_number'];
        $collection = Mage::getModel('magentothem_customcheckout/customorder')->getCollection();
        $collection->getSelect()->join(array('sfo' => 'sales_flat_order'), 'main_table.order_id = sfo.entity_id');
        $collection->getSelect()->join(array('ce' => 'customer_entity'), 'ce.entity_id = sfo.customer_id');
        $collection->getSelect()->join(array('np' => 'na_partnerinfo'), 'np.customer_id = ce.parent_id');
        $collection->getSelect()->where('main_table.sap_order_id = ' . $erpOrdNum);
        $data = $collection->getData();
        $status = '';
        if ($idoc['order_status'] == '6' || $idoc['order_status'] == '3') {
            $status = 'Order Placed';
        } elseif ($idoc['order_status'] == '19') {
            $status = 'Completed';
        } else {
            $status = 'Partially Delivered';
        }
        
        if (isset($data) && !empty($data)) {
            $idocNum = str_pad($idoc['idoc_number'],16,'0',STR_PAD_LEFT);
            $poNum = $idoc['purchase_order_number'];
            $sapDocType = $idoc['order_doc_type'];
            $distChannel = $idoc['dist_channel'];
            $sapReqDate = $idoc['sap_request_date'];
            $sapOrdCreateDate = $idoc['sap_order_create_date'];
            $paymentTerms = $idoc['payment_terms'];
            
            $soldToDetail = array();
            $shipToDetail = array();
            $idocItemCollection = Mage::getModel('idocs/idoccustomer')->getCollection()->addFieldToFilter('idoc_key',$idoc['idoc_key'])->addFieldToFilter('sap_address_type',array('in' => array('AG','WE')));
            $custRefData = $idocItemCollection->getData();
            if(isset($custRefData) && !empty($custRefData)) {
                foreach ($custRefData as $cust) {
                    if ($cust['sap_address_type'] == 'AG') {
                        $soldToDetail['id'] = $cust['orig_system_ref'];
                        $soldToDetail['name'] = $cust['partner_name'];
                    }

                    if ($cust['sap_address_type'] == 'WE') {
                        $shipToDetail['id'] = $cust['orig_system_ref'];
                        $shipToDetail['name'] = $cust['partner_name'];
                    }
                }
            }
            
            if(isset($idoc['item_data']) && $idoc['item_data'] != '') {
                $itemData = $idoc['item_data'];
            } else {
                $idocItemCollection = Mage::getModel('idocs/idocdetails')->getCollection()->addFieldToFilter('idoc_key',$idoc['idoc_key']);
                $itemData = $idocItemCollection->getData();
            }
            
            $grossTotal = count($itemData);
            $xml = '';
            $payLoadId = Mage::getModel('idocs/idoc')->generatePayLoadId();
            $timestamp = date('Y-m-d\TH:i:sP');
            $userId = $data[0]['uid'];
            $deliveryType = $data[0]['delivery_type'];
            $orderNo = $data[0]['order_id'];
            
            $url = '';
            if ($jmsUrl == self::NA_COMCEPT) {
                $vendor = 'Comcept';
                $url = Mage::getStoreConfig('jms/jmsconf/comcept');
            } elseif ($jmsUrl == self::NA_PERFECTION) {
                $vendor = 'Perfection';
                $url = Mage::getStoreConfig('jms/jmsconf/perfection');
            }
            
            $mode = 'production';
            if (Mage::getStoreConfig('jms/jmsconf/mode') == 1) {
                $mode = 'test';
            }
            
            $noticeDate = date('Y-M-d');
            $refPayloadId = $data[0]['payload_id'];
            $senderDetails = Mage::helper('jms')->getUserInfo($refPayloadId);
            
            $xml .= '<?xml version="1.0" encoding="UTF-8"?><cXML payloadID="' . $payLoadId . '" timestamp="' . $timestamp . '" xml:lang="en-US">
                        <Header>
                                <From>
                                    <Credential domain="Axalta" />
                                </From>
                                <To>
                                    <Credential domain="ReceiverURL">
                                        <Identity>' . $url . '</Identity>
                                    </Credential>
                                </To>
                                <Sender>
                                    <Credential domain="JMSUserId">
                                        <Identity>' . $senderDetails['identity'] . '</Identity>
                                        <sharedSecret>'.$senderDetails['sharedSecret'].'</sharedSecret>
                                    </Credential>
                                </Sender>
                            </Header>
                            <Request deploymentMode="' . $mode . '">
                            <ConfirmationRequest>
                                <ConfirmationHeader confirmID="' . $erpOrdNum . '" noticeDate="' . $noticeDate . '" operation="' . $operation . '" type="allDetail">
                                    <DocumentReference payloadID="' . $refPayloadId . '" />
                                    <Contact addressID="' . $soldToDetail['id'] . '" role="soldTo">
                                        <Name xml:lang="en-US">' . htmlspecialchars($soldToDetail['name'], (ENT_QUOTES | ENT_XHTML), 'UTF-8') . '</Name>
                                    </Contact>
                                    <Contact addressID="' . $shipToDetail['id'] . '" role="shipTo">
                                        <Name xml:lang="en-US">' . htmlspecialchars($shipToDetail['name'], (ENT_QUOTES | ENT_XHTML), 'UTF-8') . '</Name>
                                    </Contact>
                                    <Shipping>
                                        <Money>USD</Money>
                                        <Description>' . $deliveryType . '</Description>
                                    </Shipping>
                                    <Extrinsic name="distributionChannel">' . $distChannel . '</Extrinsic>
                                    <Extrinsic name="paymentTerms">' . $paymentTerms . '</Extrinsic>
                                    <Extrinsic name="orderStatus">' . $status . '</Extrinsic>
                                    <Extrinsic name="cartNo">' . $orderNo . '</Extrinsic>
                                </ConfirmationHeader>
                                <OrderReference orderDate="' . $sapOrdCreateDate . '" orderID="' . $poNum . '">
                                    <DocumentReference payloadID="' . $refPayloadId . '" />
                                </OrderReference>';
            if(isset($itemData) && !empty($itemData)) {
                foreach ($itemData as $items) {
                    $show = TRUE;
                    if (($operation == 'new') || ($operation == 'update' && $items['action_code'] != '4')) {
                        if($items['po_line_item_no'] != '' && $items['po_line_item_no'] != 0) {
                            $lineNumber = $items['po_line_item_no'];
                        } else {
                            $lineNumber = intval($items['sequence_id']);
                        }

                        $action = $items['action_code'];
                        $comment = '';
                        $str = '';
                        $type = 'detail';
                        if ($action == '3') {
                            $comment = $items['rejection_desc'];
                            $type = 'reject';
                            $str = '<Extrinsic name="itemStatus">Cancelled</Extrinsic>';
                        }

                        $orderQty = intval($items['sap_order_quantity']);
                        $backOrderQty = intval($items['sap_order_quantity']);
                        if ($items['backordered_quantity'] > 0) {
                            $comment = 'Backordered';
                            $backOrderQty = intval($items['backordered_quantity']);
                            $type = 'backordered';
                            $str = '<Extrinsic name="itemStatus">backordered</Extrinsic>';
                        }

                        if ($action == '1') {
                            $type = 'accept';
                            if ($operation != 'new') {
                                $str = '<Extrinsic name="itemStatus">NewLineItem</Extrinsic>';
                            }
                        }
                        
                        if ($action == '2') {
                            $itemColl = Mage::getModel('sales/order')->getCollection();
                            $itemColl->addFieldToSelect('entity_id');
                            $itemColl->addFieldToFilter('entity_id', $orderNo);
                            $itemColl->getSelect()->join(array('sfoi' => 'sales_flat_order_item'), 'main_table.entity_id = sfoi.order_id', array('qty_ordered'));
                            $itemColl->addFieldToFilter('sfoi.sku', $items['sku']);
                            $itemData = $itemColl->getData();
                            $qtyOrdered = $itemData[0]['qty_ordered'];
                            if ($qtyOrdered != $items['sap_order_quantity']) {
                                $type = 'accept';
                                $str = '<Extrinsic name="itemStatus">Changed</Extrinsic>';
                            } else {
                                $show = FALSE;
                            }
                        }

                        if ($show) {
                            $xml .= '
                                <ConfirmationItem lineNumber="' . ltrim($lineNumber, 0) . '" quantity="' . $orderQty . '">
                                    <UnitOfMeasure>' . $items['unit_of_measure'] . '</UnitOfMeasure>
                                    <ConfirmationStatus quantity="' . $backOrderQty . '" type="' . $type . '">
                                        <UnitOfMeasure>' . $items['unit_of_measure'] . '</UnitOfMeasure>
                                        <Comments>' . htmlspecialchars($comment, (ENT_QUOTES | ENT_XHTML), 'UTF-8') . '</Comments>
                                        <Extrinsic name="supplierPartID">' . $items['sku'] . '</Extrinsic>' . $str . '
                                    </ConfirmationStatus>
                                </ConfirmationItem>';
                        }
                    }
                }

                if ($operation == 'new') {
                    $orderCollection = Mage::getModel('magentothem_customcheckout/customorder')->getCollection();
                    $orderCollection->getSelect()->join(array('sfo' => 'sales_flat_order'), 'main_table.order_id = sfo.entity_id');
                    $orderCollection->getSelect()->join(array('sfoi' => 'sales_flat_order_item'), 'sfoi.order_id = sfo.entity_id');
                    $orderCollection->getSelect()->where('main_table.sap_order_id = ' . $erpOrdNum . ' AND sfoi.status_code = 1');
                    $orderData = $orderCollection->getData();
                    if (isset($orderData) && !empty($orderData)) {
                        $type = 'reject';
                        $str = '<Extrinsic name="itemStatus">Cancelled</Extrinsic>';
                        foreach ($orderData as $value) {
                            $xml .= '
                                <ConfirmationItem lineNumber="' . ltrim($value['po_line_item_no'], 0) . '" quantity="' . $value['qty_ordered'] . '">
                                    <UnitOfMeasure>' . $value['unitofmeasure'] . '</UnitOfMeasure>
                                    <ConfirmationStatus quantity="' . $value['qty_ordered'] . '" type="' . $type . '">
                                        <UnitOfMeasure>' . $value['unitofmeasure'] . '</UnitOfMeasure>
                                        <Comments>' . htmlspecialchars($value['status_message'], (ENT_QUOTES | ENT_XHTML), 'UTF-8') . '</Comments>
                                        <Extrinsic name="supplierPartID">' . $value['sku'] . '</Extrinsic>' . $str . '
                                    </ConfirmationStatus>
                                </ConfirmationItem>';
                        }
                    }
                }
            }
            
            $xml .= '
                </ConfirmationRequest>
                </Request>
                </cXML>';
        }
        
        $str = base64_encode($xml);
        $returnData['xml'] = $str;
        $returnData['test'] = $xml;
        $returnData['vendor'] = $vendor;
        $returnData['name'] = 'ACK_' . $erpOrdNum . '_' . ltrim($idocNum, 0);
        $this->sendFile($returnData);
        return $returnData;
    }
    
    public function sendFile($returnData)
    {
        if ($returnData['xml'] != '') {
            $postUrl = Mage::getStoreConfig('jms/jmsconf/posturl');
            $objCh = curl_init();
            curl_setopt($objCh, CURLOPT_URL, $postUrl);
            curl_setopt($objCh, CURLOPT_VERBOSE, 1);
            curl_setopt($objCh, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($objCh, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($objCh, CURLOPT_POST, 1);
            curl_setopt($objCh, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($objCh, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
            curl_setopt($objCh, CURLOPT_POSTFIELDS, 'Vendor=' . $returnData['vendor'] . '&UniqueFilename=' . $returnData['name'] . '&cXML=' . $returnData['xml']);
            $response = curl_exec($objCh);
        }
    }

}
