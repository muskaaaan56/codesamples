<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
class Magentothem_Customuser_Adminhtml_Customer_NapartnerController extends Mage_Adminhtml_Controller_Action
{


    protected function _initCustomer($idFieldName='id')
    {
        $this->_title($this->__('Customers'))->_title($this->__('Manage Na Partners'));

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

    public function indexAction()
    {
        $this->_title($this->__('Customers'))->_title($this->__('NA Partners'));

        if($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_initCustomer();
        $this->loadLayout();

        $this->_setActiveMenu('customer/managenapartner');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/customer_napartner', 'customers'));

        $this->_addBreadcrumb(Mage::helper('customer')->__('Customers'), Mage::helper('customer')->__('Customers'));
        $this->_addBreadcrumb(Mage::helper('customer')->__('NA Partners'), Mage::helper('customer')->__('NA Partners'));

        $this->renderLayout();
    }



    public function griduserAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if(!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {
                $customer = Mage::getModel('customer/customer');
                $partnerInfoModel = Mage::getResourceModel('magentothem_customuser/partnerinfo');
                foreach ($customersIds as $customerId) {
                    $customer->reset()
                        ->load($customerId)
                        ->delete();
                    $partnerInfoModel->unassginedUser($customerId);
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

}
