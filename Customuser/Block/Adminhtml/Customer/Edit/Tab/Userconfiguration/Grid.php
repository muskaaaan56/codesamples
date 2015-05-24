<?php
/**
* @category Customuser
* @package Magentothem
* @author Ecomwhizz
*/

class Magentothem_Customuser_Block_Adminhtml_Customer_Edit_Tab_Userconfiguration_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_userconfiguration_grid');
        $this->setUseAjax(TRUE);
        $this->setDefaultSort('cust_user_map_id');
        $this->setDefaultDir('ASC');

        $this->setSaveParametersInSession(TRUE);
        $this->setEmptyText(Mage::helper('customer')->__('No Association Found'));
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
        $websiteId = $model->getWebsiteId();
        $_websites = Mage::app()->getWebsites();
        foreach($_websites as $website){
            if($websiteId == $website->getId())
            {
                $websiteCode = $website->getCode();
            }
        }

        if ($customerId) {
            $usersDetails = Mage::getSingleton('admin/session')->getUser();
            $userInfo = $usersDetails->getData();
            $rolesDetail = $usersDetails->getRole()->getData();
            $cityStateZipObj = new Zend_Db_Expr("CONCAT(`sca`.`street_no`, ', ',`sca`.`city`, ', ', `sca`.`state`) AS fulladdress");
            if($rolesDetail['role_name'] == 'Key User' && $rolesDetail['role_type'] == 'G') { 
                $entityId = Mage::getResourceModel('magentothem_customuser/partnerinfo')->getEntityIdByEmail($userInfo['email']);

                $salesAreaIds = Mage::getResourceModel('magentothem_customuser/salesarea')->getSalesAreaIds($userInfo['email']);
                if ($salesAreaIds) {
                    $collection = Mage::getModel('magentothem_customuser/custusermap')->getCollection();
                    $collection->getSelect()->join(array('sa' => 'magentothem_salesarea'), 'main_table.salesarea_id = sa.salesarea_id', array('sa.company_name'));
                    $collection->getSelect()->join(array('sca' => 'sap_customer_address'), 'main_table.sap_cust_id = sca.partner_id', array('sca.name','sca.street_no','sca.city','sca.state','sca.zip_code'));
                    $collection->getSelect()->where('main_table.cust_id = '.$customerId.' AND sa.website_id = "'.$websiteId.'"')
                    ->columns($cityStateZipObj);
                    $collection->getSelect()->where('main_table.salesarea_id IN ('.implode(',',$salesAreaIds).') OR main_table.is_default = 1' );
                }
            }
            else {
                $collection = Mage::getModel('magentothem_customuser/custusermap')->getCollection();

                $collection->getSelect()->join(array('sa' => 'magentothem_salesarea'), 'main_table.salesarea_id = sa.salesarea_id', array('sa.company_name'));
                $collection->getSelect()->join(array('sca' => 'sap_customer_address'), 'main_table.sap_cust_id = sca.partner_id', array('sca.name','sca.street_no','sca.city','sca.state','sca.zip_code'));
                $collection->getSelect()->where('main_table.cust_id = '.$customerId.' AND sa.website_id = "'.$websiteId.'"')
                ->columns($cityStateZipObj);
            }
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
        
        if ($parentId) {
            $this->addColumn(
                'can_create_order',
                array(
                    'header'    => Mage::helper('customer')->__('Create Order'),
                    'sortable'  => FALSE,
                    'filter' => FALSE,
                    'align' => 'center',
                    'index'     => 'can_create_order',
                    'renderer'  => 'magentothem_customuser/adminhtml_customer_grid_renderer_createorder'
                )
            );
            
            $this->addColumn(
                'can_view_order',
                array(
                    'header'    => Mage::helper('customer')->__('View Order'),
                    'sortable'  => FALSE,
                    'filter' => FALSE,
                    'align' => 'center',
                    'index'     => 'can_view_order',
                    'renderer'  => 'magentothem_customuser/adminhtml_customer_grid_renderer_vieworder'
                )
            );
        }

        $websiteId = $customermodel->getWebsiteId();
        $_websites = Mage::app()->getWebsites();
        foreach($_websites as $website){
            if($websiteId == $website->getId()) {
                $websiteCode = $website->getCode();
            }
        }

        if($websiteCode == Magentothem_Usermanagement_Helper_Data::LA_WEBSITE || $parentId) {
            $this->addColumn(
                'is_default',
                array(
                    'header'    => Mage::helper('catalog')->__('Default'),
                    'index'     => 'is_default',
                    'sortable'  => FALSE,
                    'filter' => FALSE,
                    'align' => 'center',
                    'renderer'  => 'magentothem_customuser/adminhtml_customer_grid_renderer_default'
                )
            );
        }

        $this->addColumn(
            'company_name',
            array(
                'header'    => Mage::helper('customer')->__('Sales Area'),
                'sortable'  => TRUE,
                'index'     => 'company_name'
            )
        );

        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('customer')->__('Sold-to Name'),
                'index'     => 'name',
                'filter_index'=>'sca.name',
            )
        );
        
        $this->addColumn(
            'fulladdress',
            array(
                'header'    => Mage::helper('customer')->__('Sold-to Address'),
                'index'     => 'fulladdress',
                'filter'    => false,
            )
        );

        $this->addColumn(
            'sap_cust_id',
            array(
                'header'    => Mage::helper('customer')->__('Sold-to Number'),
                'index'     => 'sap_cust_id'
            )
        );
        
        if (!$parentId) {
            $this->addColumn(
                'cust_user_map_id',
                array(
                    'header'    => Mage::helper('catalog')->__('Action'),
                    'type'      => 'text',
                    'index'     => 'cust_user_map_id',
                    'renderer'  => 'magentothem_customuser/adminhtml_customer_grid_renderer_removelink'
                )
            );
        }

        return parent::_prepareColumns();
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/fetchassociation', array('_current' => TRUE));
    }
}