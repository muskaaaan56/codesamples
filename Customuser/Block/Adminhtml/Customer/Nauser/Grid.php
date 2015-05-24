<?php
/**
* @category Customuser
* @package Magentothem
* @author Ecomwhizz
*/

class Magentothem_Customuser_Block_Adminhtml_Customer_Nauser_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_nauser_grid');
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
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('user_status')
            ->addAttributeToFilter('website_id', $naWebsiteId);
        $optionId = Mage::helper('magentothem_customuser')->getUserStatusId('Created');
        $custNameAttrId = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'user_status')->getAttributeId();
        $collection->getSelect()
           ->join(array('cpev' => 'customer_entity_varchar'), 'e.entity_id = cpev.entity_id AND cpev.attribute_id='.$custNameAttrId.' AND cpev.value!='.$optionId.'',array('value'))
           ->joinLeft(array('np' => 'na_partnerinfo'), 'e.entity_id = np.customer_id', array('np.user_type'))
           ->joinLeft(array('aur' => 'magentothem_user_role'), 'e.entity_id = aur.user_id', array('aur.role_id'))
           ->joinLeft(array('ar' => 'magentothem_role'), 'aur.role_id = ar.magentothem_role_id', array('role_name'  => new Zend_Db_Expr('group_concat(distinct  ar.role_name SEPARATOR ",")')))
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
            'uid', 
            array(
                'header' => Mage::helper('customer')->__('User Name'),
                'index'  => 'uid'
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
                'index'     => 'email'
            )
        );

        $this->addColumn(
            'role_name', 
            /*array(
                'header'    => Mage::helper('customer')->__('Roles'),
                'index'     => 'role_name'
            )*/
            array(
                'header'                    => Mage::helper('customer')->__('Roles'),
                'index'                     => 'role_name',
                'filter_condition_callback' => array(
                                                $this, 
                                                '_filterRoleName',
                                               ),
                'order_callback'            => array(
                                                $this, 
                                                '_SortCustomField',
                                               ),
            )
        );

        $this->addColumn(
            'parent_id',
            array(
                'header'    => Mage::helper('catalog')->__('Parent Id'),
                'index'     => 'parent_id',
            )
        );

        $this->addColumn(
            'user_status', 
            array(
                'header'   => Mage::helper('customer')->__('Status'),
                'renderer' => 'magentothem_customuser/adminhtml_customer_grid_renderer_userstatus',
                'index'    => 'user_status',
                'filter'   => FALSE,
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
                                ),
                               ),
                'filter'    => FALSE,
                'sortable'  => FALSE,
                'index'     => 'stores',
                'is_system' => TRUE,
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


    /**
     * @param Mage_Customer_Model_Resource_Customer_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column
    */
    protected function _filterRoleName($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }

        $condition = $collection->getConnection()
            ->prepareSqlCondition('ar.role_name', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    protected function _SortCustomField($collection, $column)
    {
        $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }
}