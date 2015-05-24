<?php
/**
* @category Axaltacore
* @package Customuser
* @author Digitales
*/

class Axaltacore_Customuser_Block_Adminhtml_Customer_Edit_Tab_Sendidocdate extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
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

        // Table for mapping data
        $customerId = $customermodel->getEntityId();
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

        $form = new Varien_Data_Form();
        if ($websiteCode == Axaltacore_Usermanagement_Helper_Data::NA_WEBSITE && !empty($partnerInfo) && $partnerInfo[0]['user_type'] == 'partner') {
            $sendIdocFieldset = $form->addFieldset(
                'sendidoc_fieldset',
                array('legend' => Mage::helper('customer')->__('Send Idoc'))
            );
            $sendIdocFieldset->addType('add_button', 'Axaltacore_Customuser_Block_Adminhtml_Customer_Edit_Tab_Field_Custom');
            
            $sendIdocFieldset->addField(
                'from_date',
                'date',
                array(
                    'name'      => 'from_date',
                    'title'     => Mage::helper('axaltacore_customuser')->__('From Date'),
                    'label'     => Mage::helper('axaltacore_customuser')->__('From Date'),
                    'image'     => $this->getSkinUrl('images/grid-cal.gif'), 
                    'format'    => Varien_Date::DATE_INTERNAL_FORMAT,
                    'required'  => FALSE,
                )
            );
            $sendIdocFieldset->addField(
                'to_date',
                'date',
                array(
                    'name'      => 'to_date',
                    'title'     => Mage::helper('axaltacore_customuser')->__('To Date'),
                    'label'     => Mage::helper('axaltacore_customuser')->__('To Date'),
                    'image'     => $this->getSkinUrl('images/grid-cal.gif'), 
                    'format'    => Varien_Date::DATE_INTERNAL_FORMAT,
                    'required'  => FALSE,
                )
            );
            $sendIdocFieldset->addField(
                'buttonadder_add_button',
                'add_button',
                array(
                    'title' => Mage::helper('axaltacore_customuser')->__('Send Idoc'),
                    'id' => 'buttonadder_id',
                )
            );
        }
        
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('axaltacore_customuser')->__('Send Idoc');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('axaltacore_customuser')->__('Send Idoc');
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