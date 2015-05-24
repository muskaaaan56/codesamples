<?php
/**
* @category Axaltacore
* @package Customuser
* @author Digitales
*/

class Axaltacore_Customuser_Block_Adminhtml_Customer_Edit_Tab_Userinfo extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Initialize form object
     */
    public function initForm()
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customermodel = Mage::registry('current_customer');
        $customerData = $customermodel->getData();
        $disabled = FALSE;

        // Table for mapping data
        $customerId = $customermodel->getEntityId();
        $websiteId = $customermodel->getWebsiteId();
        $_websites = Mage::app()->getWebsites();

        foreach($_websites as $website){
            if($websiteId == $website->getId())
            {
                $websiteCode = $website->getCode();
            }
        }
        
        $require = TRUE;

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $form->setFieldNameSuffix('partnerinfo');
        if (Mage::registry('partner_info') ) {
          $partnerData = Mage::registry('partner_info')->getData();
        }

        $partnerFieldset = $form->addFieldset(
            'partner_fieldset',
            array('legend' => Mage::helper('customer')->__('Partner Information'))
        );
        
        $partnerType = array();
        array_unshift($partnerType, array('label' => 'Please Select', 'value' => ''));


        $partnertypeColl = Mage::getModel('axaltacore_customuser/partnertypes')->getCollection();
        $partnerTypeData = $partnertypeColl->getData();
        foreach($partnerTypeData as $key => $value) {
            $partnertypeId = $value['partnertype_id'];
            $partnertypeTitle = $value['title'];
            $partnerType[$partnertypeId] = $partnertypeTitle;
        }

        $url = Mage::getBaseUrl();
        $userType = array(
                        'partner' => 'partner',
                        'user' => 'user'
                    );

        $partnerFieldset->addField(
            'user_type', 'select', 
            array(
                'name'     => 'user_type',
                'label'    => Mage::helper('axaltacore_customuser')->__('User Type'),
                'title'    => Mage::helper('axaltacore_customuser')->__('User Type'),
                'values'   => $userType,
                'value'    => $partnerData['user_type'],
                'disabled' => TRUE,
            )
        );

        if (is_array($partnerData) && $partnerData['user_type'] == 'partner') {
            $partnerFieldset->addField(
                'partner_type', 'select', 
                array(
                    'name'     => 'partner_type',
                    'label'    => Mage::helper('axaltacore_customuser')->__('Partner Type'),
                    'title'    => Mage::helper('axaltacore_customuser')->__('Partner Type'),
                    'values'   => $partnerType,
                    'value'    => $partnerData['partner_type'],
                    'required' => $require,
                )
            );

            $partnerFieldset->addField(
                'is_jms_partner', 'checkbox', 
                array(
                    'name'     => 'is_jms_partner',
                    'label'    => Mage::helper('axaltacore_customuser')->__('Is JMS Partner'),
                    'title'    => Mage::helper('axaltacore_customuser')->__('Is JMS Partner'),
                    'onclick'  => 'this.value = this.checked ? 1 : 0;',
                    'checked'  => Mage::registry('partner_info') ? Mage::registry('partner_info')->getIsJmsPartner() : ''
                )
            );

            $partnerFieldset->addField(
                'jms_system_url', 'text', 
                array(
                    'name'     => 'jms_system_url',
                    'label'    => Mage::helper('axaltacore_customuser')->__('JMS System URL'),
                    'title'    => Mage::helper('axaltacore_customuser')->__('JMS System URL'),
                )
            );

            $partnerFieldset->addField(
                'jms_system_username', 'text', 
                array(
                    'name'     => 'jms_system_username',
                    'label'    => Mage::helper('axaltacore_customuser')->__('JMS System Username'),
                    'title'    => Mage::helper('axaltacore_customuser')->__('JMS System Username'),
                )
            );

           $partnerFieldset->addField(
               'jms_system_password', 'text', 
               array(
                   'name'     => 'jms_system_password',
                   'class'    => 'validate-password',
                   'label'    => Mage::helper('axaltacore_customuser')->__('JMS System Password'),
                   'title'    => Mage::helper('axaltacore_customuser')->__('JMS System Password'),
               )
           );

           $partnerFieldset->addField(
               'owner_name', 'text', 
               array(
                   'name'     => 'owner_name',
                   'label'    => Mage::helper('axaltacore_customuser')->__('Owner Name'),
                   'title'    => Mage::helper('axaltacore_customuser')->__('Owner Name'),
               )
           );

           $partnerFieldset->addField(
               'owner_email', 'text', 
               array(
                   'name'     => 'owner_email',
                   'class'    => 'validate-email',
                   'label'    => Mage::helper('axaltacore_customuser')->__('Owner Email ID'),
                   'title'    => Mage::helper('axaltacore_customuser')->__('Owner Email ID'),
               )
           );

            $imgSource = $this->helper('core/js')->getJsUrl('jquery/jquerydatepicker/images/calendar.gif');
            $partnerFieldset->addField(
                'stop_print_invoice_date',
                'text',
                array(
                    'name'      => 'stop_print_invoice_date',
                    'label'     => Mage::helper('axaltacore_customuser')->__('Stop Print Invoice Date'),
                    'title'     => Mage::helper('axaltacore_customuser')->__('Stop Print Invoice Date'),
                    'after_element_html' => '<img id ="stopinvoiceimg" class="ui-datepicker-trigger" src="'.$imgSource.'" alt="..." title="...">',
                )
            );

            $partnerFieldset->addField('stopinvoice', 'hidden', array('name' => 'stopinvoice'));
            $partnerData['stopinvoice'] = $this->helper('core/js')->getJsUrl('jquery/jquerydatepicker/images/calendar.gif');
        }


        $form->setValues($partnerData);
        $this->setForm($form);

       // return parent::_prepareForm();
       return $this;
    }

    public function getTabLabel()
    {
        return Mage::helper('axaltacore_customuser')->__('Partner Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('axaltacore_customuser')->__('Partner Information');
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