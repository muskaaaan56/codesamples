<?php
/**
 * Extension Customuser
 *
 * @category   Customuser
 * @package    Axaltacore
 * @author     Digitales
 */
class Axaltacore_Customuser_Block_Adminhtml_Customer_Grid_Renderer_Default extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $check = '';
        $customermodel = Mage::registry('current_customer');
        $parentId = $customermodel->getParentId();
        $value = $row->getCustUserMapId();

        if ($row->getIsDefault()) {
            $check = 'checked="checked"';
        }
        
        $str = '<input '.$check.' type="radio" name="isdefault[]" value="'.$value.'" />';
        return $str;  
    }
}
