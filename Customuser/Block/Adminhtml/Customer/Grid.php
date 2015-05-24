<?php
/**
 * Customer edit block
 *
 * @category   Magentothem
 * @package    Magentothem_Customuser
 * @author     Digitales
 */
class Magentothem_Customuser_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    
    protected function _prepareCollection()
    {
        $laCode = Magentothem_Usermanagement_Helper_Data::LA_WEBSITE;
        $laWebsite = Mage::getModel('core/website')->getCollection()->addFieldToFilter('code',$laCode);
        $laWebsiteData = $laWebsite->getData();
        $laWebsiteId = $laWebsiteData[0]['website_id'];

        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('user_status');

        $usersDetails = Mage::getSingleton('admin/session')->getUser();
        $userInfo = $usersDetails->getData();
        $rolesDetail = $usersDetails->getRole()->getData();

        $optionId = Mage::helper('magentothem_customuser')->getUserStatusId('Created');
        $custNameAttrId = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'user_status')->getAttributeId();
        $collection->getSelect()
                                ->join(array('cpev' => 'customer_entity_varchar'), 'e.entity_id = cpev.entity_id AND cpev.attribute_id='.$custNameAttrId.' AND cpev.value!='.$optionId.'',array('value'));

        // Add Sap Cust Id, Sales Area , Last Login columns in Grid
        $collection->getSelect()
                   ->joinLeft(array('scu' => 'sap_customer_user'), 'scu.cust_id = e.entity_id AND scu.is_default=1', array('scu.sap_cust_id','scu.salesarea_id','scu.is_default'))
                   ->joinLeft(array('sa' => 'magentothem_salesarea'), 'scu.salesarea_id = sa.salesarea_id', array('company_name'));

        $collection->getSelect()->joinLeft(array('lc' => 'log_customer'),'e.entity_id = lc.customer_id',array('MAX(login_at) as max_loginat'))
                   ->group('e.entity_id');

        if($rolesDetail['role_name'] == 'Key User' && $rolesDetail['role_type'] == 'G') { 
            
            $entityId = Mage::getResourceModel('magentothem_customuser/partnerinfo')->getEntityIdByEmail($userInfo['email']);
            
            $customerIds = array();
            $custUserId = array();
            $salesAreaIds = Mage::getResourceModel('magentothem_customuser/salesarea')->getSalesAreaIds($userInfo['email']);
            if ($salesAreaIds) {
                $custUserMap = Mage::getModel('magentothem_customuser/custusermap')->getCollection();
                $custUserMap->getSelect()->where('salesarea_id IN ('.implode(',',$salesAreaIds).')')->group('cust_id');
                $customerData = $custUserMap->getData();
                foreach ($customerData as $data) {
                   $customerIds[] = $data['cust_id'];
                }
            }

            $custUsers = Mage::getResourceModel('magentothem_customuser/custusermap')->getCustUserMap();
            foreach($custUsers as $custUser) {
                $custUserId[] = $custUser['cust_id'];
            }

        
            /* to remove the customer wholse salesarea are same but they already are key users and same logic for user who don't have any salesarea and country name are save. */
            $customerCollection = Mage::getModel('customer/customer')->getCollection();
            $customerCollection->getSelect()->join(array('ka' => 'admin_user'), 'e.email = ka.email', array());
            $keyUserData = $customerCollection->getData();
            foreach($keyUserData as $keyUser) {
                if(($key = array_search($keyUser['entity_id'], $customerIds)) !== FALSE) {
                    unset($customerIds[$key]);
                }

                $keyUserId[] = $keyUser['entity_id'];
            }

            $custUserId = array_diff($custUserId, $customerIds);
            $custUserId = array_merge($custUserId, $keyUserId);
            array_push($custUserId, $entityId);
            $custUserId = array_unique($custUserId);
            if(($key = array_search($entityId, $customerIds)) !== FALSE) {
                unset($customerIds[$key]);
            }

            /* START : To Allow Key User to Edit Own Profile */
              $loggedinAdminKey = array_search($entityId, $custUserId);

              if($loggedinAdminKey) {
                unset($custUserId[$loggedinAdminKey]);
              }

              if(!in_array($entityId, $customerIds)) {
                array_push($customerIds, $entityId);
              }

            /* END : To Allow Key User to Edit Own Profile */

            $countryCollection = Mage::getResourceModel('customer/customer_collection')->addAttributeToSelect('*')->addFieldToFilter('email', $userInfo['email'])->load();
            foreach($countryCollection as $country) {
                $countryId = $country->getCountryId();
            }

            $custNameAttrId = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'country_id')->getAttributeId();
            $collection->getSelect()->join(array('cev' => 'customer_entity_varchar'), 'e.entity_id = cev.entity_id AND cev.attribute_id='.$custNameAttrId, array('value'));

            if(is_array($customerIds) && !empty($customerIds) && !empty($customerIds[0])) {
                $collection->getSelect()->where('e.entity_id IN ('.implode(',',$customerIds).') OR (e.entity_id not IN ('.implode(',',$custUserId).') AND cev.value = "'. $countryId . '")');
            }
            else {
                $collection->getSelect()->where('e.entity_id not IN ('.implode(',',$custUserId).') AND cev.value = "'. $countryId .'"');
            }
        }

        $collection->getSelect()->where('(e.website_id ='.$laWebsiteId.')');
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customer');

        $this->getMassactionBlock()->addItem(
            'newsletter_subscribe',
            array(
             'label' => Mage::helper('customer')->__('Subscribe to Newsletter'),
             'url'   => $this->getUrl('*/*/massSubscribe'),
            )
        );

        $this->getMassactionBlock()->addItem(
            'newsletter_unsubscribe', 
            array(
             'label' => Mage::helper('customer')->__('Unsubscribe from Newsletter'),
             'url'   => $this->getUrl('*/*/massUnsubscribe'),
            )
        );

        $groups = $this->helper('customer')->getGroups()->toOptionArray();

        array_unshift(
            $groups, 
            array(
                'label' => '',
                'value' => ''
            )
        );
        $this->getMassactionBlock()->addItem(
            'assign_group', 
            array(
             'label'      => Mage::helper('customer')->__('Assign a Customer Group'),
             'url'        => $this->getUrl('*/*/massAssignGroup'),
             'additional' => array(
                'visibility'  => array(
                     'name'   => 'group',
                     'type'   => 'select',
                     'class'  => 'required-entry',
                     'label'  => Mage::helper('customer')->__('Group'),
                     'values' => $groups
                                 )
                             )
            )
        );

        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id', 
            array(
                'header'    => Mage::helper('customer')->__('User ID'),
                'width'     => '20px',
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
            'firstname', 
            array(
                'header' => Mage::helper('customer')->__('First Name'),
                'index'  => 'firstname'
            )
        );

        $this->addColumn(
            'lastname', 
            array(
                'header' => Mage::helper('customer')->__('Last Name'),
                'index'  => 'lastname'
            )
        );

        $this->addColumn(
            'email', 
            array(
                'header' => Mage::helper('customer')->__('Email'),
                'index'  => 'email'
            )
        );

       $this->addColumn(
           'sap_cust_id', 
           array(
            'header'                    => Mage::helper('customer')->__('Sold To Code'),
            'index'                     => 'sap_cust_id',
            'filter_index'              => 'scu.sap_cust_id',
            //Stock Magento Callback - Notice the callback key has been assigned.
            'filter_condition_callback' => array(
                                            $this, 
                                            '_filterSapCustId',
                                           ),
            //Custom Callback Index
            'order_callback'            => array(
                                            $this, 
                                            '_SortCustomField',
                                           ),
           )
       );

       $this->addColumn(
           'company_name', 
           array(
                'header'                    => Mage::helper('customer')->__('Sales Area'),
                'index'                     => 'company_name',
                'filter_condition_callback' => array(
                                                $this, 
                                                '_filterCompanyName',
                                               ),
                'order_callback'            => array(
                                                $this, 
                                                '_SortCustomField',
                                               ),
           )
       );

        $this->addColumn(
            'max_loginat', 
            array(
                'header'                    => Mage::helper('customer')->__('Last Login'),
                'type'                      => 'datetime',
                'align'                     => 'center',
                'index'                     => 'max_loginat',
                'filter_condition_callback' => array(
                                                $this, 
                                                '_filterLoginAt',
                                               ),
                'order_callback'            => array(
                                                $this, 
                                                '_SortCustomField',
                                               ),
                'gmtoffset'                 => TRUE
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
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                                array(
                                    'caption' => Mage::helper('customer')->__('Edit'),
                                    'url'     => array('base' => '*/*/edit'),
                                    'field'   => 'id',
                                ),
                               ),
                'filter'    => FALSE,
                'sortable'  => FALSE,
                'index'     => 'stores',
                'is_system' => TRUE,
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => TRUE));
    }

    /**
     * @param Mage_Customer_Model_Resource_Customer_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column
    */
    protected function _filterSapCustId($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }

        $condition = $collection->getConnection()
            ->prepareSqlCondition('scu.sap_cust_id', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    /**
     * @param Mage_Customer_Model_Resource_Customer_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column
    */
    protected function _filterCompanyName($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }

        $condition = $collection->getConnection()
            ->prepareSqlCondition('sa.company_name', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    /**
     * @param Mage_Customer_Model_Resource_Customer_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column          $column
    */
    protected function _filterLoginAt($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }

        $condition = $collection->getConnection()
            ->prepareSqlCondition('lc.login_at', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    protected function _SortCustomField($collection, $column)
    {
        $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }

    protected function _setCollectionOrder($column)
    {
        if ($column->getOrderCallback()) {
            call_user_func($column->getOrderCallback(), $this->getCollection(), $column);

            return $this;
        }

        return parent::_setCollectionOrder($column);
    }
}
