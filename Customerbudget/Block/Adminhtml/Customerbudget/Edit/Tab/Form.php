<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/
class Magentothem_Customerbudget_Block_Adminhtml_Customerbudget_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
       $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('customerbudget_form', array('legend'=>Mage::helper('customerbudget')->__('Item information')));
     
      $fieldset->addField('budget_name', 'text', array(
          'label'     => Mage::helper('customerbudget')->__('Budget Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'budget_name',
      ));

      $fieldset->addField('budget_code', 'text', array(
          'label'     => Mage::helper('customerbudget')->__('Budget Code'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'budget_code',
	  ));

      $fieldset->addField('expired_at', 'date', array(
          'label'     => Mage::helper('customerbudget')->__('Expiry Date'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'expired_at',
          'image'     => $this->getSkinUrl('images/grid-cal.gif'),
          'format'    => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) ,
          'value'     => date( Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT), strtotime('next weekday') )
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('customerbudget')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('customerbudget')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('customerbudget')->__('Disabled'),
              ),
          ),
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCustomerbudgetData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCustomerbudgetData());
          Mage::getSingleton('adminhtml/session')->setCustomerbudgetData(null);
      } elseif ( Mage::registry('customerbudget_data') ) {
          $form->setValues(Mage::registry('customerbudget_data')->getData());
      }
      return parent::_prepareForm();
  }
}