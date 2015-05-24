<?php
/**
 * @category   Magentothem
 * @package    Magentothem
 * @author     Ecomwhizz
 */

class Magentothem_Customuser_Helper_Data extends Mage_Core_Helper_Abstract
{
    CONST STR_USER_ROLE_CSR = 'axaltacsr';
    /**
     * Create Address HTML format from array
     * @param array $address
     * @return string
     */
    public function arrayToAddress($addressColl)
    {
      if(is_array($addressColl)) {
       //  $addressData = $addressColl->getData();
       
        $address = $addressColl[0];
        if(!empty($address['street_no'])) {
            $html = $address['street_no'].',<br>';
        }

        if(!empty($address['city']) || !empty($address['state'])) {
            $html .= $address['city'].', '.$address['state'].'<br>';
        }

        if(!empty($address['country']) && ($address['country'] == Magentothem_Usermanagement_Helper_Data::COUNTRY_BRAZIL)) {
          if(!empty($address['taxcode'])) {
              $html .= $address['taxcode'].'<br>';
          }
        }

        if(!empty($address['country']) || !empty($address['zip_code'])) {
            $html .= $address['country'].', '.$address['zip_code'];
        }

        return $html;
      }
    }

    public function getShipToaddress()
    {
        $shipTo = Mage::getSingleton('customer/session')->getShipTo();
        if($shipTo) {
            $address = Mage::getModel('magentothem_customuser/custaddress')->getCollection()->addFieldToFilter('partner_id', $shipTo);
            $addressHtml = $this->arrayToAddress($address->getData());
            return $addressHtml;
        }
    }

    public function getSoldToaddress()
    {
        $soldTo = Mage::getSingleton('customer/session')->getSap_customer_id();
        if($soldTo) {
            $address = Mage::getModel('magentothem_customuser/custaddress')->getCollection()->addFieldToFilter('partner_id', $soldTo);
            $addressHtml = $this->arrayToAddress($address->getData());
            return $addressHtml;
        }
    }
    
    public function getSoldToaddressById($soldTo)
    {
        $address = Mage::getModel('magentothem_customuser/custaddress')->getCollection()->addFieldToFilter('partner_id', $soldTo);
        $addressHtml = $this->arrayToAddress($address->getData());
        return $addressHtml;
    }

    /**
     * get Salesarea Id from orgId, Division, Dist Id.
     */
    public function getSalesareaId()
    {
        $salesOrgId = Mage::getSingleton('customer/session')->getSalesOrgId();
        $divisionId = Mage::getSingleton('customer/session')->getDivisionId();
        $distrChannel = Mage::getSingleton('customer/session')->getDistrChannel();
        $salesareadata = Mage::getModel('magentothem_customuser/salesarea')->getCollection()
                                                                ->addFieldToFilter('sales_organization_id',$salesOrgId)
                                                                ->addFieldToFilter('division',$divisionId)
                                                                ->addFieldToFilter('distribution_channel',$distrChannel)
                                                                ->getData();
        return $salesareadata[0]['salesarea_id'];
    }
    
    /*
     * Return Customer Full name
     * @params $customerId
     * @return $customerName
     */
    public function getCustomerName($customerId)
    {
        $customerData = Mage::getModel('customer/customer')->load($customerId);
        $customerName = $customerData->getName();
        return $customerName;
    }
    
    /*
     * Return Customer Title name
     * @return $title
     */
    public function getCustomerTitle()
    {
        $optionId = Mage::getSingleton('customer/session')->getCustomer()->getUserTitle();
        $title = Mage::getModel('magentothem_customuser/custusermap')->getUserOptionValue($optionId, 'user_title');
        return $title;
    }
    
    /*
     * Return Customer Title name
     * @params $optionId
     * @return $title
     */
    public function getTitleByOptionId($optionId)
    {
        $title = Mage::getModel('magentothem_customuser/custusermap')->getUserOptionValue($optionId, 'user_title');
        return $title;
    }

    /*
     *
     * Return User's Status Id Value
     * @params $status
     * @return $optionId
     *
     */
    public function getUserStatusId($status)
    {
        $custNameAttrId = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'user_status')->getAttributeId();
        $eavoptionCollection = Mage::getModel('eav/entity_attribute_option')->getCollection()->addFieldToFilter('attribute_id', $custNameAttrId); 
        $eavoptionCollection->getSelect()->join(array('eapv' => 'eav_attribute_option_value'), 'eapv.option_id = main_table.option_id', array())
                                   ->where('eapv.value = "' . $status . '"');
        foreach($eavoptionCollection as $eavoption) {
            $optiondata = $eavoption->getData();
            $optionId = $optiondata['option_id'];
        }

        return $optionId;
    }

    /*
     *
     * Return User's Language Code Id Value
     * @params $language
     * @return $optionId
     *
     */
    public function getUserLanguageId($language)
    {
        $custLangAttrId = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lang_store')->getAttributeId();
        $eavoptionCollection = Mage::getModel('eav/entity_attribute_option')->getCollection()->addFieldToFilter('attribute_id', $custLangAttrId); 
        $eavoptionCollection->getSelect()->join(array('eapv' => 'eav_attribute_option_value'), 'eapv.option_id = main_table.option_id', array())
                                   ->where('eapv.value = "' . $language . '"');
        foreach($eavoptionCollection as $eavoption) {
            $optiondata = $eavoption->getData();
            $optionId = $optiondata['option_id'];
        }

        return $optionId;
    }

    /*
     * Get User Type
     * return String
     */
    public function getUserType($customerData, $partnerData)
    {
      if(!empty($customerData)) {
        $code = Mage::getModel('core/website')->load($customerData['website_id'])->getData('code');
        $custWebsiteCode = Mage::getModel('core/website')->load($code)->getData('code');
        if($custWebsiteCode == Magentothem_Usermanagement_Helper_Data::NA_WEBSITE) {
          if (Mage::registry('partner_info') ) {
            if(isset($partnerData['user_type']) && $partnerData['user_type'] == 'user') {
              return Magentothem_Usermanagement_Helper_Data::NA_USER;
            }
            else if(isset($partnerData['user_type']) && $partnerData['user_type'] == 'partner') {
              return Magentothem_Usermanagement_Helper_Data::NA_PARTNER;
            }
          }
          else {
            return Magentothem_Usermanagement_Helper_Data::LA_USER;
          }
        }
      }
    }

    public function formatDate($date, $deieveryDateFlag=NULL)
    {
      $websiteCode = $_SERVER['MAGE_RUN_CODE'];
      $isLa = FALSE;
      $isNa = FALSE;

      if($websiteCode == Magentothem_Usermanagement_Helper_Data::LA_WEBSITE) {
          $isLa = TRUE;
      }
      else if($websiteCode == Magentothem_Usermanagement_Helper_Data::NA_WEBSITE) {
          $isNa = TRUE;
      }

      if($isLa) {
        $date1 = date('m/d/Y', strtotime($date));
        if($date1 == '01/01/1970') {
            $date = date('d/m/Y');
        }
        else {
            $date = date('d/m/Y', strtotime($date1));
        }
      }
      else if($isNa) {
        if($deieveryDateFlag != NULL && $deieveryDateFlag != '') {
            $date = date('m/d/Y',strtotime($date));
        } else {
            $date = date('m/d/Y', strtotime($date));
            if($date == '01/01/1970') {
                $date = date('m/d/Y');
            }
        }
      }

      return $date;
    }

    public function formatDateTime($dateTime)
    {
      $websiteCode = $_SERVER['MAGE_RUN_CODE'];
      $isLa = FALSE;
      $isNa = FALSE;

      if($websiteCode == Magentothem_Usermanagement_Helper_Data::LA_WEBSITE) {
          $isLa = TRUE;
      }
      else if($websiteCode == Magentothem_Usermanagement_Helper_Data::NA_WEBSITE) {
          $isNa = TRUE;
      }

      if($isLa) {
        $date1 = date('m/d/Y', strtotime($dateTime));
        if($date1 == '01/01/1970') {
            $dateTime = date('d/m/Y');
        } else {
            $dateTime = date('d/m/Y', strtotime($date1));
        }
      }
      else if($isNa) {
        $date = date('m/d/Y', strtotime($dateTime));
        if($date == '01/01/1970') {
            $dateTime = date('m/d/Y');
        } else {
            $dateTime = date('m/d/Y', strtotime($dateTime));
        }
      }

      return $dateTime;
    }

    /* Function for Date and Time for NA / Yada / 
    *
    */

    public function formatDateTimeYadaCrm($dateTime, $showTime=FALSE)
    {   
        date_default_timezone_set(Magentothem_Customcheckout_Helper_Data::EST_TIMEZONE);
        if($dateTime != '' && !empty($dateTime)) {
            $dateTime = date('m/d/Y', strtotime($dateTime));
        } else {
            $dateTime = date('m/d/Y');
        }

        if($showTime && $dateTime != '01/01/1970') {
            $dateTime = date('m/d/Y',strtotime($dateTime));
        } else if($showTime && $dateTime == '01/01/1970') {
            $dateTime = date('m/d/Y');
        } else {
            $dateTime = date('m/d/Y',strtotime($dateTime));
        }
        
        return $dateTime;
    }
    
    /* 
     * Add space after comma in customer address
     */
    public function getProperAddress($str)
    {
        $substrings = explode(',', $str);
        foreach($substrings as $substring) {
            $strings[] = trim($substring);
        }
        
        return implode(', ', $strings);
    }

    /* Get SoldTo Country
     * return string if TRUE or boolean if FALSE
     */
    public function getSoldToCountry()
    {
        $soldTo = Mage::getSingleton('customer/session')->getSap_customer_id();
        if($soldTo) {
            $addressColl = Mage::getModel('magentothem_customuser/custaddress')->getCollection()->addFieldToFilter('partner_id', $soldTo);
            $addressCount = count($addressColl);

            if($addressCount > 0) {
              $addressData = $addressColl->getData();
              $address = $addressData[0];
              if(!empty($address['country'])) {
                return $address['country'];
              }
            }
        }

        return FALSE;
    }
}