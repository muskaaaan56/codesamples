<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/
class Magentothem_Customerbudget_Block_Customerbudget extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    

    /** 
      * Get all the budget code from the database
      */
     public function getBudgetCode()     
     { 
        $todayDate = new Zend_Date();
        $codeData = Mage::getModel('magentothem_customerbudget/customerbudget')->getCollection()
                    ->addFieldToFilter('expired_at', array('from' => $todayDate, 'datetime' => TRUE))
                    ->addFieldToFilter('status', 1)
                    ->addFieldToSelect('budget_code')
                    ->getData();
        return $codeData;
    }

    
    /** 
      * Get the current Budget Code
      */
    public function getCurrentBudgetCode()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getBudgetCode();
    }

    /** 
      * Get the current Budget Code
      */
    public function getCurrentPoNUmber()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getPoNumber();
    }

    /** 
      * Get the current Budget Code
      */
    public function getCurrentOrderType()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getOrderType();
    }
}