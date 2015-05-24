<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/
class Magentothem_Customerbudget_Block_Adminhtml_Customerbudget_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('customerbudget_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('customerbudget')->__('Budget Code Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('customerbudget')->__('Budget Code Information'),
          'title'     => Mage::helper('customerbudget')->__('Budget Code Information'),
          'content'   => $this->getLayout()->createBlock('customerbudget/adminhtml_customerbudget_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}