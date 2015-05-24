<?php
/**
 * Customer account form block
 *
 * @category   Axaltacore
 * @package    Axaltacore_Customuser
 * @author     Ameri & Partner
 */
class Axaltacore_Customuser_Block_Adminhtml_Customer_Edit_Tab_Account extends Mage_Adminhtml_Block_Customer_Edit_Tab_Account
{
    /**
     * Initialize form
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Account
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_account');
        $form->setFieldNameSuffix('account');
        $customer = Mage::registry('current_customer');

        /** @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setEntity($customer)
            ->setFormCode('adminhtml_customer')
            ->initDefaultValues();

        $fieldset = $form->addFieldset(
            'base_fieldset', 
            array(
                'legend' => Mage::helper('customer')->__('Account Information')
            )
        );

        $attributes = $customerForm->getAttributes();
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();
        }

        $dagcAttrName = 'disable_auto_group_change';
        $this->_setFieldset($attributes, $fieldset, array($dagcAttrName));

        $form->getElement('group_id')->setRenderer(
            $this->getLayout()
            ->createBlock('adminhtml/customer_edit_renderer_attribute_group')
            ->setDisableAutoGroupChangeAttribute
            (
                $customerForm->getAttribute($dagcAttrName)
            )
            ->setDisableAutoGroupChangeAttributeValue
            (
                $customer->getData($dagcAttrName)
            )
        );

        if ($customer->getId()) {
            $form->getElement('website_id')->setDisabled('disabled');
            $form->addField('website_id_value', 'hidden', array('name' => 'website_id_value'));
            $customer->setData('website_id_value', $customer->getWebsiteId());
            $form->getElement('created_in')->setDisabled('disabled');
        } else {
            $fieldset->removeField('created_in');
            $form->getElement('website_id')->addClass('validate-website-has-store');

            $websites = array();
            foreach (Mage::app()->getWebsites(TRUE) as $website) {
              if(is_NULL($website->getDefaultStore())) {
                $websites[$website->getId()] = TRUE;
              }
              else {
                $websites[$website->getId()] = FALSE;
              }
            }

            $prefix = $form->getHtmlIdPrefix();

            $form->getElement('website_id')->setAfterElementHtml(
                '<script type="text/javascript">
                var '.$prefix.'_websites = ' . Mage::helper('core')->jsonEncode($websites) .';
                Validation.add(
                    "validate-website-has-store",
                    "' . Mage::helper('customer')->__('Please select a website which contains store view') . '",
                    function(v, elem){
                        return '.$prefix.'_websites[elem.value] == true;
                    }
                );
                Element.observe("{'.$prefix.'}website_id", "change", function(){
                    Validation.validate($("'.$prefix.'website_id"))
                }.bind($("'.$prefix.'website_id")));
                </script>'
            );
        }

        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $form->getElement('website_id')->setRenderer($renderer);

        $customerStoreId = NULL;
        if ($customer->getId()) {
            $customerStoreId = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
        }

        $prefixElement = $form->getElement('prefix');
        if ($prefixElement) {
            $prefixOptions = $this->helper('customer')->getNamePrefixOptions($customerStoreId);
            if (!empty($prefixOptions)) {
                $fieldset->removeField($prefixElement->getId());
                $prefixField = $fieldset->addField(
                    $prefixElement->getId(),
                    'select',
                    $prefixElement->getData(),
                    $form->getElement('group_id')->getId()
                );
                $prefixField->setValues($prefixOptions);
                if ($customer->getId()) {
                    $prefixField->addElementValues($customer->getPrefix());
                }
            }
        }

        $suffixElement = $form->getElement('suffix');
        if ($suffixElement) {
            $suffixOptions = $this->helper('customer')->getNameSuffixOptions($customerStoreId);
            if (!empty($suffixOptions)) {
                $fieldset->removeField($suffixElement->getId());
                $suffixField = $fieldset->addField(
                    $suffixElement->getId(),
                    'select',
                    $suffixElement->getData(),
                    $form->getElement('lastname')->getId()
                );
                $suffixField->setValues($suffixOptions);
                if ($customer->getId()) {
                    $suffixField->addElementValues($customer->getSuffix());
                }
            }
        }

        // Make sendemail and sendmail_store_id disabled if website_id has empty value
        $isSingleMode = Mage::app()->isSingleStoreMode();
        $sendEmailId = $isSingleMode ? 'sendemail' : 'sendemail_store_id';
        $sendEmail = $form->getElement($sendEmailId);

        $prefix = $form->getHtmlIdPrefix();
        if ($sendEmail) {
            $_disableStoreField = '';
            if (!$isSingleMode) {
                $_disableStoreField = '$("'.$prefix.'sendemail_store_id").disabled=(""==this.value || "0"==this.value);';
            }

            $sendEmail->setAfterElementHtml(
                '<script type="text/javascript">
                $("'.$prefix.'website_id").disableSendemail = function() {
                    $("'.$prefix.'sendemail").disabled = ("" == this.value || "0" == this.value);'.
                $_disableStoreField
                .'}.bind($("'.$prefix.'website_id"));
                Event.observe("'.$prefix.'website_id", "change", $("'.$prefix.'website_id").disableSendemail);
                $("'.$prefix.'website_id").disableSendemail();
                </script>'
            );
        }

        if ($customer->isReadonly()) {
            foreach ($customer->getAttributes() as $attribute) {
                $element = $form->getElement($attribute->getAttributeCode());
                if ($element) {
                    $element->setReadonly(TRUE, TRUE);
                }
            }
        }

        /*** Remove prefernce attributes for NA Partner and make it require for LA User and NA User  *****/
        $attributes = $customerForm->getAttributes();
        foreach ($attributes as $attribute) {
            $lawebsiteId = Mage::getModel('core/website')->load(Axaltacore_Usermanagement_Helper_Data::LA_WEBSITE)->getId();
            $customerWebsiteId = $customer->getWebsiteId();
            $attrId = $attribute->getAttributeCode();
            if($lawebsiteId == $customerWebsiteId && $attrId == 'country_id') {
                $form->getElement($attrId)->setRequired(TRUE);
                $form->getElement($attrId)->setDisabled('disabled');
            }

            else if($attrId == 'country_id') {
                $fieldset->removeField($attrId);
            }

            if($attrId == 'prefix' || $attrId == 'middlename' || $attrId == 'suffix' || $attrId == 'dob' || $attrId == 'taxvat' || $attrId == 'gender') {
                $fieldset->removeField($attrId);
            }

            if(!($this->getRequest()->getParam('partner')) && ($attrId == 'group_id' || $attrId == 'website_id' || $attrId == 'created_in')) {
                $fieldset->removeField($attrId);
            }

            if (!($this->getRequest()->getParam('partner')) && ($attrId == 'orders_lobby' || $attrId == 'invoices_lobby' || $attrId == 'orders_history_days' || $attrId == 'invoices_days')) {
                $form->getElement($attrId)->setRequired(TRUE);
            }

            if (!($this->getRequest()->getParam('partner')) && ($attrId == 'firstname' || $attrId == 'lastname' || $attrId == 'email')) {
                $form->getElement($attrId)->setDisabled('disabled');
            }

            if (($this->getRequest()->getParam('partner')) && ($attrId == 'orders_lobby' || $attrId == 'invoices_lobby' || $attrId == 'orders_history_days' || $attrId == 'invoices_days' || $attrId == 'user_title' || $attrId == 'user_status' || $attrId == 'created_in')) {
                $fieldset->removeField($attrId);
            }
            else if($attrId == 'user_status') {
                $form->getElement($attrId)->setRequired(TRUE);
            }

            if($attrId == 'expiration_date' && $lawebsiteId == $customerWebsiteId) {
                $form->getElement($attrId)->setRequired(TRUE);
            }
            else if($attrId == 'expiration_date') {
                $fieldset->removeField($attrId);
            }

            if($lawebsiteId == $customerWebsiteId && $attrId == 'user_title') {
                $fieldset->removeField($attrId);
            }

            else if(!$this->getRequest()->getParam('partner') && $attrId == 'user_title') {
                $form->getElement($attrId)->setRequired(TRUE);
            }

            if($attrId == 'lang_store' && $lawebsiteId == $customerWebsiteId) {
                $form->getElement($attrId)->setDisabled('disabled');
            }

            if($attrId == 'lang_store' && $lawebsiteId != $customerWebsiteId) {
                $fieldset->removeField($attrId);
            }
            

            if($lawebsiteId == $customerWebsiteId && $attrId == 'role_name') {
                $form->getElement($attrId)->setDisabled('disabled');
            }
        }

        if($lawebsiteId == $customerWebsiteId) {
             $fieldset->addField(
                 'addemail',
                 'textarea', 
                 array(
                      'label' => 'Additional Email',
                      'name'  => 'addemail',
                 )
             );
        }
        
        $form->setValues($customer->getData());
        $this->setForm($form);
        return $this;
    }

    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'      => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_file'),
            'image'     => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_image'),
            'boolean'   => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_boolean'),
               );
    }
}
