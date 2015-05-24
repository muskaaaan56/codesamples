<?php
/**
* Extension Axalta
*
* @category   Axaltacore
* @package    Axaltacore
* @author     CIGNEX DATAMATICS
* @license
*/

$installer = $this;
$setup = new Mage_Customer_Model_Entity_Setup();
$installer->startSetup();

$setup->addAttribute('customer', 'country_id', array(
    'label'        => 'Country',
    'type'         => 'varchar',
    'visible'      => true,
    'required'     => false,
    'input'        => 'select',
    'source'       => 'customer/entity_address_attribute_source_country',
    'readonly'     => true,
));


$usedInforms = array();

$usedInforms[] = 'customer_account_edit';
$usedInforms[] = 'adminhtml_customer';

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'country_id');
$attribute->setData('used_in_forms', $usedInforms)
        ->setData('is_used_for_customer_segment', TRUE)
        ->setData('is_system', 0)
        ->setData('is_user_defined', 1)
        ->setData('is_visible', 1)
        ->setData('sort_order', 0);
$attribute->save();

$installer->endSetup();