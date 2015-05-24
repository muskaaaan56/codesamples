<?php
/**
 *
 * @category   CA
 * @package    Ca_Organization
 * @author     Cignex
 */
class Axaltacore_Customuser_Block_Adminhtml_Customer_Grid_Renderer_Removelink extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $model = Mage::registry('current_customer');
        $url = Mage::getBaseUrl();
        $isDefault = $row->getIsDefault();
        if($isDefault == 0) {
            $str = '<a href="#" onclick="removeassociation(\''.$row->getCustUserMapId().'\',\''.$model->getId().'\',\''.$row->getSapCustId().'\',\''.$url.'\')">Remove</a>';
        }

        return $str;
    }
}
