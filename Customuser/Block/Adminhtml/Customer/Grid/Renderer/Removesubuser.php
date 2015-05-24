<?php
/**
 *
 * @category   CA
 * @package    Ca_Organization
 * @author     Cignex
 */
class Axaltacore_Customuser_Block_Adminhtml_Customer_Grid_Renderer_Removesubuser extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $model = Mage::registry('current_customer');
        $entityId = $row->getEntityId();
        $url = Mage::getBaseUrl().'admin/customer/removesubuser';
        $editUrl = Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit/id/'.$entityId);
        if($isDefault == 0) {
            $str = '<a href="'.$editUrl.'" target="_blank">Edit</a> | <a href="#" onclick="removesubuser(\''.$model->getId().'\',\''.$entityId.'\',\''.$url.'\')">Remove</a>';
        }

        return $str;
    }
}
