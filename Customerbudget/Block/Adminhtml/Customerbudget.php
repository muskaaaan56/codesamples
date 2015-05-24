<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/
class Magentothem_Customerbudget_Block_Adminhtml_Customerbudget extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_customerbudget';
    $this->_blockGroup = 'customerbudget';
    $this->_headerText = Mage::helper('customerbudget')->__('Customer Budget Code Manager');
    $this->_addButtonLabel = Mage::helper('customerbudget')->__('Add Budget Code');
    parent::__construct();
  }
}