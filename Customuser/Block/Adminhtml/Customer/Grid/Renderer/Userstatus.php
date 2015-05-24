<?php
/**
 * Extension Customuser
 *
 * @category   Customuser
 * @package    Axaltacore
 * @author     Digitales
 */
class Axaltacore_Customuser_Block_Adminhtml_Customer_Grid_Renderer_Userstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $userActive = Mage::getModel('axaltacore_customuser/custusermap')->getUserOptionValue($row->getUserStatus(), 'user_status');
        return $userActive;
    }
}
