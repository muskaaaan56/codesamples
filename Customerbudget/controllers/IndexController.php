<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/
class Magentothem_Customerbudget_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
      Give the posted code routing.
      */
    public function codePostAction()
    {
        try {
            $budgetData = $this->getRequest()->getPost();
            $budgetCode = $budgetData['budget_code'];
            $checkoutSession = Mage::getSingleton('checkout/session');
            $quote = $checkoutSession->getQuote();
            $quoteId = $quote->getId();
            $quote->setBudgetCode($budgetCode);
            $quote->save();
            $quoteModel = Mage::getModel('sales/quote');
            $quoteModel->setData($quote)
                       ->setId($quoteId)
                       ->save();
            $message = $this->__('Your Budget Code has been successfully added.');
            $checkoutSession->addSuccess($message);
        }
        catch(exception $e) {
            $message = $this->__('There is some problem in adding the Budget code please try again later.');
            $checkoutSession->addError($message);
        }
        
        $this->_redirect('checkout/cart');
    }

    /**
      Store the Po Number
      */
    public function ponumberPostAction()
    {
        try {
            $poData = $this->getRequest()->getPost();
            $poCode = $poData['po_number'];
            $checkoutSession = Mage::getSingleton('checkout/session');
            $quote = $checkoutSession->getQuote();
            $quoteId = $quote->getId();
            $quote->setPoNumber($poCode);
            $quote->save();
            $quoteModel = Mage::getModel('sales/quote');
            $quoteModel->setData($quote)
                       ->setId($quoteId)
                       ->save();
            $message = $this->__('PO Number is successfully saved');
            $checkoutSession->addSuccess($message);
        }
        catch(exception $e) {
            $message = $this->__('Po Number is not saved.');
            $checkoutSession->addError($message);
        }
        
        $this->_redirect('checkout/cart');
    }
    
    /**
      Save the order type
      */
    public function ordertypePostAction()
    {
        try {
            $ordertypeData = $this->getRequest()->getPost();
            $orderTypeCode = $ordertypeData['order_type'];
            $checkoutSession = Mage::getSingleton('checkout/session');
            $quote = $checkoutSession->getQuote();
            $quoteId = $quote->getId();
            $quote->setOrderType($orderTypeCode);
            $quote->save();
            $quoteModel = Mage::getModel('sales/quote');
            $quoteModel->setData($quote)
                       ->setId($quoteId)
                       ->save();
            $message = $this->__('Order Type is successfully saved.');
            $checkoutSession->addSuccess($message);
        }
        catch(exception $e) {
            $message = $this->__('Order Type is not saved.');
            $checkoutSession->addError($message);
        }
        
        $this->_redirect('checkout/cart');
    }
}
