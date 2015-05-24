<?php
/**
* @category Customuser
* @package Axaltacore
* @author Ecomwhizz
*/

class Axaltacore_Customuser_Block_Adminhtml_Customer_Napartner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  /*
  public function __construct()
  {
    $this->_controller = 'adminhtml_customer_napartner';
    $this->_headerText = Mage::helper('axaltacore_customuser')->__('Manage Partner');
    $this->_blockGroup = 'axaltacore_customuser';

    $this->_addButtonLabel = Mage::helper('axaltacore_customuser')->__('Add Partner');
    parent::__construct();
  }

    public function getCreateUrl()
    {
        return $this->getUrl('* /* /new/partner/1');
    }
    */


    public function __construct()
    {
      $this->_controller = 'adminhtml_customer_napartner';
      $this->_headerText = Mage::helper('axaltacore_customuser')->__('North America Partner');
      $this->_blockGroup = 'axaltacore_customuser';
      $this->_addButtonLabel = Mage::helper('axaltacore_customuser')->__('Add Partner');
      parent::__construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/customer/new/partner/1');
    }


    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/customer_napartner_grid', 'customer.grid'));
        return parent::_beforeToHtml();
    }

    protected function _prepareLayout()
    {
        $this->setChild('filterForm', $this->getLayout()->createBlock('adminhtml/customer_napartner_filter'));
        return parent::_prepareLayout();
    }

    public function getFilterFormHtml()
    {
        return $this->getChild('filterForm')->toHtml();
    }

}