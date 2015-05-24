<?php
/**
 * Customer edit block
 *
 * @category   Axaltacore
 * @package    Axaltacore_Customuser
 * @author     Digitales
 */
class Axaltacore_Customuser_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Customer_Edit
{
    public function __construct()
    {
        parent::__construct();
        $this->_removeButton('delete');
        $this->_removeButton('order');
        if(Mage::registry('current_customer') && !Mage::registry('current_customer')->getId()) {
            $this->_removeButton('save');
        }
        
        $url = Mage::helper('adminhtml')->getUrl('*/*/sendIdocAjax');
        $customerId = $this->getRequest()->getParam('id');
        $this->_formScripts[] = "
            function sendIdoc(){
                var soldTo = document.getElementsByName('isdefault[]');
                var selected = '';
                for(var i = 0; i < soldTo.length; i++){
                    if(soldTo[i].checked){
                        selected = soldTo[i].value;
                    }
                }
                if(selected == '') {
                    alert('Please select Sold To');
                    return false;
                }
                
                // Date validations
                var fromDate = document.getElementById('from_date').value ;
                var toDate = document.getElementById('to_date').value ;
                if(fromDate == '') {
                    alert('Please Select From Date');
                    return false;
                }
                if(toDate == '') {
                    alert('Please Select To Date');
                    return false;
                }
                
                fromArr = fromDate.split('-');
                fromDate = fromArr[1]+'/'+fromArr[2]+'/'+fromArr[0];
                
                toArr = toDate.split('-');
                toDate = toArr[1]+'/'+toArr[2]+'/'+toArr[0];
                
                d = new Date();
                
                fromT = new Date(fromDate).getTime() / 1000; 
                toT = new Date(toDate).getTime() / 1000;
                current_to_dateT = new Date(d).getTime() / 1000;
                
                if(toT < fromT) {
                    alert('Please select proper dates');
                    return false;
                } else if (toT > current_to_dateT) {
                    alert('Please select proper dates');
                    return false;
                }
                
                // Send AJAX request
                var url = '".$url."';
                var customerId = '".$customerId."';
                var request = new Ajax.Request(url,{
                    method: 'post', 
                    parameters: {from_date: fromDate, to_date: toDate, sold_to: selected, customerId:customerId},
                    onComplete: function(responce){ 
                       alert(responce.responseText);
                       return false;
                    }
                }
                );
            }
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_customer') && Mage::registry('current_customer')->getId()) {
            return $this->escapeHtml(Mage::registry('current_customer')->getName());
        }
        else {
            if ($this->getRequest()->getParam('partner')) {
                return Mage::helper('customer')->__('New Partner');
            }
            else {
                return Mage::helper('customer')->__('New User');
            }
        }
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
      $backUrl = $this->getUrl('*/customer/');
      if(Mage::registry('current_customer') && Mage::registry('partner_info') && Mage::registry('current_customer')->getId() && Mage::registry('partner_info')->getId()) {
        $customer = Mage::registry('current_customer');
        $customerData = $customer->getData();
        $partnerData = Mage::registry('partner_info')->getData();

        $userType = Mage::helper('axaltacore_customuser')->getUserType($customerData, $partnerData);
        if($userType == Axaltacore_Usermanagement_Helper_Data::NA_USER) {
          $backUrl = $this->getUrl('*/customer_nauser/');
        }
        else if($userType == Axaltacore_Usermanagement_Helper_Data::NA_PARTNER) {
          $backUrl = $this->getUrl('*/customer_napartner/');
        }
      }

      if(Mage::registry('current_customer') && Mage::registry('current_customer')->getId()) {
        $customer = Mage::registry('current_customer');
        $customerWebsiteId = $customer->getWebsiteId();
        $userStatus = $customer->getUserStatus();
        $laWebsiteId = Mage::getModel('core/website')->load(Axaltacore_Usermanagement_Helper_Data::LA_WEBSITE)->getId();
        $naWebsiteId = Mage::getModel('core/website')->load(Axaltacore_Usermanagement_Helper_Data::NA_WEBSITE)->getId();
        $optionId = Mage::helper('axaltacore_customuser')->getUserStatusId('Created');
        if($userStatus == $optionId) {
            if($customerWebsiteId == $laWebsiteId) {
                $backUrl = $this->getUrl('*/customer/ssouser/');
            }

            else if($customerWebsiteId == $naWebsiteId) {
                $backUrl = $this->getUrl('*/customer/ssouserna/');
            }
        }
      }

      return $backUrl;

    }
}
