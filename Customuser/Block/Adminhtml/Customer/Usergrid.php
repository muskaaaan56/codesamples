<?php
/**
* @category Customuser
* @package Magentothem
* @author Ecomwhizz
*/

class Magentothem_Customuser_Block_Adminhtml_Customer_Usergrid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_user_grid');
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
        $naCode = Magentothem_Usermanagement_Helper_Data::NA_WEBSITE;
        $naWebsite = Mage::getModel('core/website')->getCollection()->addFieldToFilter('code',$naCode);
        $naWebsiteData = $naWebsite->getData();
        $naWebsiteId = $naWebsiteData[0]['website_id'];
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at');
        $optionId = Mage::helper('magentothem_customuser')->getUserStatusId('Created');
        $custNameAttrId = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'user_status')->getAttributeId();
        $collection->getSelect()
           ->join(array('cpev' => 'customer_entity_varchar'), 'e.entity_id = cpev.entity_id AND cpev.attribute_id='.$custNameAttrId.' AND cpev.value!='.$optionId.'',array('value'))
           ->joinLeft(array('np' => 'na_partnerinfo'), 'e.entity_id = np.customer_id', array('np.user_type'))
           ->joinLeft(array('aur' => 'magentothem_user_role'), 'e.entity_id = aur.user_id', array('aur.role_id'))
           ->joinLeft(array('ar' => 'magentothem_role'), 'aur.role_id = ar.magentothem_role_id', array('role_name'  => new Zend_Db_Expr('group_concat(ar.role_name SEPARATOR ",")')))
           ->where('np.user_type = "user"')
           ->group('aur.user_id');

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
            'name', 
            array(
                'header'    => Mage::helper('customer')->__('Name'),
                'index'     => 'name',
            )
        );

        $this->addColumn(
            'email', 
            array(
                'header'    => Mage::helper('customer')->__('Email ID'),
                'width'     => '150',
                'index'     => 'email'
            )
        );

        $this->addColumn(
            'role_name', 
            array(
                'header'    => Mage::helper('customer')->__('Roles'),
                'width'     => '150',
                'index'     => 'role_name'
            )
        );

        $this->addColumn(
            'parent_id',
            array(
                'header'    => Mage::helper('catalog')->__('Parent Id'),
                'index'     => 'parent_id',
            )
        );

        return parent::_prepareColumns();
    }


    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id' => $row->getEntityId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/griduser', array('_current' => TRUE));
    }
}