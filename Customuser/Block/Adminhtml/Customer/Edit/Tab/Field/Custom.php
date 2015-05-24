<?php
class Axaltacore_Customuser_Block_Adminhtml_Customer_Edit_Tab_Field_Custom extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
       parent::__construct($attributes);
    }

    public function getElementHtml()
    {     
        $value = $this->getTitle();
        $id = $this->getId();
        $html = '<tr>';
        $html .= '<td class="label">&nbsp; </td>';
        $html .= '<td class="value">';
        $html .= '<button type="button" style="margin-left:5px" onClick="sendIdoc()"><span><span><span>'.$value.'</span></span></span></button>';
        $html .= '</td>';
        $html .= '</tr>';
        return $html;
    }
}