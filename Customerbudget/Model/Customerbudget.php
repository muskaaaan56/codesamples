<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/

class Magentothem_Customerbudget_Model_Customerbudget extends Mage_Core_Model_Abstract
{
  protected $_customerSession;
  public function _construct()
  {
     parent::_construct();
     $this->_init('magentothem_customerbudget/customerbudget');
     if(Mage::getSingleton('customer/session')->isLoggedIn()) {
       $this->customerSession = Mage::getSingleton('customer/session')->getCustomer();
     }
  }
}