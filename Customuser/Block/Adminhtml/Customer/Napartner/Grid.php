<?php
/**
* @category Customuser
* @package Magentothem
* @author Ecomwhizz
*/

class Magentothem_Customuser_Block_Adminhtml_Customer_Napartner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_napartner_grid');
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
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at');

        $collection->getSelect()->joinleft(array('np' => 'na_partnerinfo'), 'e.entity_id = np.customer_id', array('np.user_type'));
        
        $usersDetails = Mage::getSingleton('admin/session')->getUser();
        $userInfo = $usersDetails->getData();
        $rolesDetail = $usersDetails->getRole()->getData();
        if($rolesDetail['role_name'] == 'Key User' && $rolesDetail['role_type'] == 'G') { 
            
            $customerIds = array();
            $salesAreaIds = Mage::getResourceModel('magentothem_customuser/salesarea')->getSalesAreaIds($userInfo['email']);
            if ($salesAreaIds) {
                $custUserMap = Mage::getModel('magentothem_customuser/custusermap')->getCollection();
                $custUserMap->getSelect()->where('salesarea_id IN ('.implode(',',$salesAreaIds).')')->group('cust_id');
                $customerData = $custUserMap->getData();
                foreach ($customerData as $data) {
                   $customerIds[] = $data['cust_id'];
                }

                $collection->getSelect()->where('e.entity_id IN ('.implode(',',$customerIds).')');
            }
        }
        
        /*$optionId = Mage::helper('magentothem_customuser')->getUserStatusId('Created');
        $custNameAttrId = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'user_status')->getAttributeId();
        $collection->getSelect()
                                ->join(array('cpev' => 'customer_entity_varchar'), 'e.entity_id = cpev.entity_id AND cpev.attribute_id='.$custNameAttrId.' AND cpev.value!='.$optionId.'',array());*/
        $collection->getSelect()->where('(e.website_id ='.$naWebsiteId.' AND np.user_type = "partner")');

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
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
               'index'     => 'entity_id',
               'type'  => 'number',
           )
       );

        $this->addColumn(
            'firstname', 
            array(
                'header'    => Mage::helper('customer')->__('Name'),
                'index'     => 'firstname',
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
            'action',
            array(
                'header'    => Mage::helper('customer')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                                array(
                                    'caption' => Mage::helper('customer')->__('Edit'),
                                    'field'   => 'id',
                                    'url'     => array(
                                                    'base'   => '*/customer/edit',
                                                    'params' => array('partner' => '1')
                                                 ),
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
        return $this->getUrl('*/customer/edit', array('id' => $row->getEntityId(),'partner' => 1));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/griduser', array('_current' => TRUE));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customer');

        $this->getMassactionBlock()->addItem(
            'newsletter_subscribe',
            array(
             'label'   => Mage::helper('customer')->__('Delete Partner'),
             'url'     => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('customer')->__('Are you sure?'),
            )
        );

        return $this;
    }
}
