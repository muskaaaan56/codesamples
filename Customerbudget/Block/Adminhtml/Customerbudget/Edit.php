<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/
class Magentothem_Customerbudget_Block_Adminhtml_Customerbudget_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'customerbudget';
        $this->_controller = 'adminhtml_customerbudget';
        
        $this->_updateButton('save', 'label', Mage::helper('customerbudget')->__('Save Budget Code'));
        $this->_updateButton('delete', 'label', Mage::helper('customerbudget')->__('Delete Budget Code'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('customerbudget_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'customerbudget_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'customerbudget_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('customerbudget_data') && Mage::registry('customerbudget_data')->getId() ) {
            return Mage::helper('customerbudget')->__("Edit Budget Code '%s'", $this->htmlEscape(Mage::registry('customerbudget_data')->getTitle()));
        } else {
            return Mage::helper('customerbudget')->__('Add Budget Code');
        }
    }
}