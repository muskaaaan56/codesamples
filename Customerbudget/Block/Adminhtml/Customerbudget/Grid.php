<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/
class Magentothem_Customerbudget_Block_Adminhtml_Customerbudget_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('customerbudgetGrid');
      $this->setDefaultSort('customerbudget_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('magentothem_customerbudget/customerbudget')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    $this->addColumn(
        'customerbudget_id',
        array(
          'header'    => Mage::helper('customerbudget')->__('Budget Id'),
          'align'     => 'left',
          'index'     => 'customerbudget_id',
        )
    );
    $this->addColumn(
        'budget_name',
        array(
            'header'    => Mage::helper('customerbudget')->__('Budget Name'),
            'index'     => 'budget_name',
            'align'     => 'left',
        )
    );
    $this->addColumn(
        'budget_code',
        array(
            'header'    => Mage::helper('customerbudget')->__('Budget Code'),
            'index'     => 'budget_code',
            'align'     => 'left',
        )
    );
    $this->addColumn(
        'expired_at',
        array(
            'header'    => Mage::helper('customerbudget')->__('Expiry Date'),
            'index'     => 'expired_at',
            'align'     => 'left',
            'type'      => 'date',
            'format'    => 'dd-MM-Y',
        )
    );

    $this->addColumn(
        'status', 
        array(
          'header'    => Mage::helper('customerbudget')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
        )
    );

    $this->addColumn(
        'action',
        array(
            'header'    => Mage::helper('customerbudget')->__('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                            array(
                                        'caption'   => Mage::helper('customerbudget')->__('Edit'),
                                        'url'       => array('base' => '*/*/edit'),
                                        'field'     => 'id',
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

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customerbudget_id');
        $this->getMassactionBlock()->setFormFieldName('customerbudget');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('customerbudget')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('customerbudget')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('magentothem_customerbudget/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('customerbudget')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('customerbudget')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}