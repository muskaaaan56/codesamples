<?php
class Magentothem_Customuser_Block_Adminhtml_Page_Menu extends Mage_Adminhtml_Block_Page_Menu
{
    CONST KEYUSER_ROLE = 'Key User';

    protected function _buildMenuArray(Varien_Simplexml_Element $parent=NULL, $path='', $level=0)
    {
        if (is_null($parent)) {
            $parent = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');
        }

        $parentArr = array();
        $sortOrder = 0;
        foreach ($parent->children() as $childName => $child) {

            if (1 == $child->disabled) {
                continue;
            }

            $aclResource = 'admin/' . ($child->resource ? (string)$child->resource : $path . $childName);
            if (!$this->_checkAcl($aclResource)) {
                continue;
            }

            if ($child->depends && !$this->_checkDepends($child->depends)) {
                continue;
            }

            $menuArr = array();

            $menuArr['label'] = $this->_getHelperValue($child);


            $menuArr['sort_order'] = $child->sort_order ? (int)$child->sort_order : $sortOrder;

            if ($child->action) {
                $menuArr['url'] = $this->_url->getUrl((string)$child->action, array('_cache_secret_key' => TRUE));
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }

            $childActive = 0;
            if(($this->getActive() == $path.$childName) || (strpos($this->getActive(), $path.$childName.'/') === 0)) {
              $childActive = 1;
            }

            $menuArr['active'] = $childActive;

            $menuArr['level'] = $level;

            if ($child->children) {
                $menuArr['children'] = $this->_buildMenuArray($child->children, $path.$childName.'/', ($level + 1));
            }

            $parentArr[$childName] = $menuArr;

            $sortOrder++;
        }

        uasort($parentArr, array($this, '_sortMenu'));

        while (list($key, $value) = each($parentArr)) {
            $last = $key;
        }

        if (isset($last)) {
            $parentArr[$last]['last'] = TRUE;
        }

        $data = $this->_isAdmin($parentArr);

        return $data;
    }

    protected function _isAdmin($data)
    {
        $userRole = Mage::getSingleton('admin/session')->getUser()->getRole();
        $naCode = Magentothem_Usermanagement_Helper_Data::NA_WEBSITE;
        $naWebsite = Mage::getModel('core/website')->getCollection()->addFieldToFilter('code',$naCode);
        $naWebsiteData = $naWebsite->getData();
        $naWebsiteCode = $naWebsiteData[0]['code'];
        $naAdminRoleName = $naWebsiteCode.'admin';


        $laCode = Magentothem_Usermanagement_Helper_Data::LA_WEBSITE;
        $laWebsite = Mage::getModel('core/website')->getCollection()->addFieldToFilter('code',$laCode);
        $laWebsiteData = $laWebsite->getData();
        $laWebsiteCode = $laWebsiteData[0]['code'];
        $laAdminRoleName = $laWebsiteCode.'admin';

        $roleName = $userRole->getRoleName();
        $roleId = $userRole->getRoleId();


        return $data;


    }
}

?>