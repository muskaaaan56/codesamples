<?php
/**
 * Extension Customuser
 *
 * @category   Customuser
 * @package    Axaltacore
 * @author     Digitales
 */
class Axaltacore_Customuser_Block_Adminhtml_Customer_Grid_Renderer_Vieworder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $check = '';
        if ($row->getCanViewOrder()) {
            $check = 'checked="checked"';
        }
        
        $str = '<input '.$check.' type="checkbox" name="can_view_order[]" onclick="unassignViewSoldTo(this)" value="'.$row->getSapCustId().'" />';
        return $str;  
    }
}
