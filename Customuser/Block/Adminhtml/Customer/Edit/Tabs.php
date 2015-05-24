<?php
class Axaltacore_Customuser_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('edit_form');
        if ($this->getRequest()->getParam('partner')) {
            $this->setTitle(Mage::helper('customer')->__('Partner Information'));
        }
        else {
            $this->setTitle(Mage::helper('customer')->__('User Information'));
        }
    }

    protected function _prepareLayout()
    {
        //get all existing tabs
        $this->parent = parent::_prepareLayout();
        $customermodel = Mage::registry('current_customer');
        $partnerInfo = Mage::registry('partner_info');
        $userType = '';

        if ($partnerInfo) {
            $userType = $partnerInfo->getUserType();
        }

        $websiteId = $customermodel->getWebsiteId();
        $parentId = $customermodel->getParentId();
        $_websites = Mage::app()->getWebsites();
        foreach($_websites as $website){
            if($websiteId == $website->getId()) {
                $websiteCode = $website->getCode();
            }
        }

        $this->addTab(
            'account', 
            array(
                'label'     => Mage::helper('customer')->__('Account Information'),
                'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_account')->initForm()->toHtml(),
                'active'    => Mage::registry('current_customer')->getId() ? FALSE : TRUE
            )
        );

        //add new tab
        if ($websiteId) {
            $this->addTab(
                'userconfiguration',
                array(
                    'label'     => Mage::helper('customer')->__('Sold-To Association'),
                    'class'     => 'ajax test123',
                    'url'       => $this->getUrl('*/*/userconfiguration', array('_current' => TRUE)),
                )
            );
        }

        if($websiteCode == Axaltacore_Usermanagement_Helper_Data::NA_WEBSITE && $userType == 'partner') {
            $this->addTab(
                'users',
                array(
                    'label'     => Mage::helper('customer')->__('Users'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/subuser', array('_current' => TRUE)),
                )
            );

            $this->addTab(
                'assignusers',
                array(
                    'label'     => Mage::helper('customer')->__('Assign Users'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/assignuser', array('_current' => TRUE)),
                )
            );
            
        }

        if ($websiteCode == Axaltacore_Usermanagement_Helper_Data::NA_WEBSITE && $this->getRequest()->getParam('partner')) {
            $isJmsPartner = $partnerInfo->getIsJmsPartner();

            $this->addTab(
                'naattributes', 
                array(
                    'label'     => Mage::helper('customer')->__('Partner Information'),
                    'content'   => $this->getLayout()->createBlock('axaltacore_customuser/adminhtml_customer_edit_tab_userinfo')->initForm()->toHtml(),
                )
            );
            if($isJmsPartner) {
                $this->addTab(
                    'sendidocs',
                    array(
                        'label'     => Mage::helper('customer')->__('Send Idocs'),
                        'class'     => 'ajax',
                        'url'       => $this->getUrl('*/*/sendidoc', array('_current' => TRUE)),
                    )
                );
            }
        }

        return $this->parent;
    }

    /**
    Removed Unused tabs from Customer Edit Page
    */
    protected function _beforeToHtml()
    {
        $customerModel = Mage::registry('current_customer');
        $userStatus = $customerModel->getUserStatus();
        $optionId = Mage::helper('axaltacore_customuser')->getUserStatusId('Created');
        $websiteId = Mage::getModel('core/website')->load(Axaltacore_Usermanagement_Helper_Data::LA_WEBSITE)->getId();
        $customerWebsiteId = $customerModel->getWebsiteId();
        if($userStatus == $optionId && $websiteId == $customerWebsiteId) {
            $this->removeTab('userconfiguration');
        }

        $this->removeTab('customerbalance');
        $this->removeTab('customer_edit_tab_reward');
        $this->removeTab('taxexemption');
        $this->removeTab('purchaseorder');
        $this->removeTab('addresses');
        $this->removeTab('orders');
        $this->removeTab('customer_edit_tab_agreements');
        $this->removeTab('customer_edit_tab_recurring_profile');
        $this->removeTab('cart');
        $this->removeTab('wishlist');
        $this->removeTab('reviews');
        $this->removeTab('newsletter');
        $this->removeTab('tags');
        $this->removeTab('newsletter');
        $this->removeTab('giftregistry');
        $this->removeTab('customer_edit_tab_rma');
        $this->removeTab('customer_edit_tab_view');
        return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
    }
}
