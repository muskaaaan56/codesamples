<?php
/**
* @category Customuser
* @package Magentothem
* @author Ecomwhizz
*/

class Magentothem_Customuser_Block_Adminhtml_Customer_Edit_Tab_Assignusergrid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_assignuser_grid');
        $this->setUseAjax(TRUE);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');

        $this->setSaveParametersInSession(TRUE);
        $this->setEmptyText(Mage::helper('customer')->__('No User Found'));
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $model = Mage::registry('current_customer');
        $customerId = $model->getEntityId();
        $naWebsite = Magentothem_Usermanagement_Helper_Data::NA_WEBSITE;

        if ($customerId) {
           $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at');
           $optionId = Mage::helper('magentothem_customuser')->getUserStatusId('Created');
           $custNameAttrId = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'user_status')->getAttributeId();
           $collection->getSelect()
               ->join(array('cpev' => 'customer_entity_varchar'), 'e.entity_id = cpev.entity_id AND cpev.attribute_id='.$custNameAttrId.' AND cpev.value!='.$optionId.'',array('value'))
               ->join(array('cw' => 'core_website'), 'e.website_id = cw.website_id', array('cw.code'))
               ->join(array('npa' => 'na_partnerinfo'),'e.entity_id = npa.customer_id', array('npa.user_type'))
               ->joinLeft(array('aur' => 'magentothem_user_role'), 'e.entity_id = aur.user_id', array('aur.role_id'))
               ->joinLeft(array('ar' => 'magentothem_role'), 'aur.role_id = ar.magentothem_role_id', array('role_name'  => new Zend_Db_Expr('group_concat(distinct ar.role_name SEPARATOR ",")')))
               ->where('e.parent_id = 0')
               ->where('cw.code ="'.$naWebsite.'"')
               ->where('npa.user_type="user"')
               ->group('aur.user_id');
        }
        else {
            $collection = new Varien_Data_Collection();
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $customermodel = Mage::registry('current_customer');
        $parentId = $customermodel->getParentId();

        $this->addColumn(
            'in_customers', 
            array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'field_name'        => 'in_customers[]',
                'values'            => $this->_getSelectedCustomers(),
                'align'             => 'center',
                'index'             => 'entity_id'
            )
        );

        $this->addColumn(
            'entity_id', 
            array(
                'header'    => Mage::helper('customer')->__('ID'),
                'width'     => '50px',
                'index'     => 'entity_id',
                'type'  => 'number',
            )
        );

        $this->addColumn(
            'uid', 
            array(
                'header'    => Mage::helper('customer')->__('Username'),
                'index'     => 'uid',
                'type'  => 'text',
            )
        );

        $this->addColumn(
            'name', 
            array(
                'header'    => Mage::helper('customer')->__('Name'),
                'index'     => 'name'
            )
        );

        $this->addColumn(
            'email', 
            array(
                'header'    => Mage::helper('customer')->__('Email ID'),
                'index'     => 'email'
            )
        );

        $this->addColumn(
            'role_name', 
            array(
                'header'    => Mage::helper('customer')->__('Roles'),
                'index'     => 'role_name'
            )
        );

        $this->addColumn(
            'action',
            array(
                'header'    => Mage::helper('customer')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                                array(
                                    'caption' => Mage::helper('customer')->__('Edit'),
                                    'url'     => array('base' => '*/customer/edit'),
                                    'field'   => 'id',
                                    'target'  => '_blank'
                                ),
                               ),
                'filter'    => FALSE,
                'sortable'  => FALSE,
                'index'     => 'stores',
                'is_system' => TRUE,
            )
        );

        $this->setFilterVisibility(TRUE);
        return parent::_prepareColumns();
    }

    /**
    * Retrieve related products
    *
    * @return array
    */
    protected function _getSelectedCustomers()
    {
        $products = $this->getRequest()->getPost('assigned_products', NULL);
        if (!is_array($products)) {
            $products = array();
        }

        return $products;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id' => $row->getEntityId()));
    }
}