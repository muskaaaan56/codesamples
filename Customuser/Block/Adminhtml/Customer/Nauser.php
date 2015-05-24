<?php
/**
* @category Customuser
* @package Axaltacore
* @author Ecomwhizz
*/

class Axaltacore_Customuser_Block_Adminhtml_Customer_Nauser extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  /*
  public function __construct()
  {
    $this->_controller = 'adminhtml_customer_nauser';
    $this->_headerText = Mage::helper('axaltacore_customuser')->__('North America User');
    $this->_blockGroup = 'axaltacore_customuser';

    $this->_addButtonLabel = Mage::helper('axaltacore_customuser')->__('Add User');
    parent::__construct();
  }

    public function getCreateUrl()
    {
        return $this->getUrl('* /* /new/');
    }
*/


    public function __construct()
    {
      $this->_controller = 'adminhtml_customer_nauser';
      $this->_headerText = Mage::helper('axaltacore_customuser')->__('North America User');
      $this->_blockGroup = 'axaltacore_customuser';
      //$this->_addButtonLabel = Mage::helper('axaltacore_customuser')->__('Add User');
      parent::__construct();
      $this->_removeButton('add');

      //$this->setTemplate('customer/nauser.phtml');
    }

    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/customer_nauser_grid', 'customer.grid'));
        return parent::_beforeToHtml();
    }

    protected function _prepareLayout()
    {
        $this->setChild('filterForm', $this->getLayout()->createBlock('adminhtml/customer_nauser_filter'));
        return parent::_prepareLayout();
    }

    public function getFilterFormHtml()
    {
        return $this->getChild('filterForm')->toHtml();
    }

}