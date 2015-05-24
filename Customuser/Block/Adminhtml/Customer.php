<?php
/**
 *
 * @category   Axaltacore
 * @package    Axaltacore_Customuser
 * @author     Ameri & Partner
 */

class Axaltacore_Customuser_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Customer
{
    public function __construct()
    {
        $this->_controller = 'customer';
        $this->_headerText = Mage::helper('customer')->__('Latin America User');
        //$this->_addButtonLabel = Mage::helper('customer')->__('Add New User');
        Mage_Adminhtml_Block_Widget_Grid_Container::__construct();
        $this->_removeButton('add');
    }
}