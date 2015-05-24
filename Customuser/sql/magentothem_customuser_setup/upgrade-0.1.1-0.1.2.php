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

$setup->addAttribute('customer', 'user_status', array(
    'label'        => 'Status',
    'type'         => 'varchar',
    'visible'      => true,
    'required'     => true,
    'input'        => 'select',
    'source'        => 'eav/entity_attribute_source_table',
));


$tableOptions        = $installer->getTable('eav_attribute_option');
$tableOptionValues   = $installer->getTable('eav_attribute_option_value');

// add options for level of politeness
$attributeId = (int)$setup->getAttribute('customer', 'user_status', 'attribute_id');
foreach (array('Active','Inactive') as $sortOrder => $label) {

    // add option
    $data = array(
        'attribute_id' => $attributeId,
        'sort_order'   => $sortOrder,
    );
    $setup->getConnection()->insert($tableOptions, $data);

    // add option label
    $optionId = (int)$setup->getConnection()->lastInsertId($tableOptions, 'option_id');
    $data = array(
        'option_id' => $optionId,
        'store_id'  => 0,
        'value'     => $label,
    );
    $setup->getConnection()->insert($tableOptionValues, $data);
}

$usedInforms = array();

$usedInforms[] = 'customer_account_edit';
$usedInforms[] = 'adminhtml_customer';

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'user_status');
$attribute->setData('used_in_forms', $usedInforms)
        ->setData('is_used_for_customer_segment', TRUE)
        ->setData('is_system', 0)
        ->setData('is_user_defined', 1)
        ->setData('is_visible', 1)
        ->setData('sort_order', 0);
$attribute->save();

$installer->endSetup();