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

$setup->addAttribute(
        'customer',
        'expiration_date',
        array
        (
            'label'        => 'Expiration Date',
            'type'           => 'datetime',
            'input'          => 'date',
            'visible'        => TRUE,
            'required'       => TRUE
        ));

$usedInforms = array();

$usedInforms[] = 'customer_account_edit';
$usedInforms[] = 'adminhtml_customer';

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'expiration_date');
$attribute->setData('used_in_forms', $usedInforms)
        ->setData('is_used_for_customer_segment', TRUE)
        ->setData('is_system', 0)
        ->setData('is_user_defined', 1)
        ->setData('is_visible', 1)
        ->setData('sort_order', 0);
$attribute->save();

$setup->addAttribute('customer', 'orders_lobby', array(
    'label'        => 'Number of Orders to be fetched (Lobby Page)',
    'visible'      => TRUE,
    'required'     => TRUE,
    'type'         => 'varchar',
    'input'        => 'select',
    'source'        => 'eav/entity_attribute_source_table',
));


$tableOptions        = $installer->getTable('eav_attribute_option');
$tableOptionValues   = $installer->getTable('eav_attribute_option_value');

// add options for level of politeness
$attributeId = (int)$setup->getAttribute('customer', 'orders_lobby', 'attribute_id');
foreach (array('25', '50', '75', '100', '125') as $sortOrder => $label) {

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
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'orders_lobby');
$attribute->setData('used_in_forms', $usedInforms)
        ->setData('is_used_for_customer_segment', TRUE)
        ->setData('is_system', 0)
        ->setData('is_user_defined', 1)
        ->setData('is_visible', 1)
        ->setData('sort_order', 0);
$attribute->save();

$setup->addAttribute('customer', 'invoices_lobby', array(
    'label'        => 'Number of Invoices to be fetched (Lobby Page)',
    'visible'      => TRUE,
    'required'     => TRUE,
    'type'         => 'varchar',
    'input'        => 'select',
    'source'        => 'eav/entity_attribute_source_table',
));


$tableOptions        = $installer->getTable('eav_attribute_option');
$tableOptionValues   = $installer->getTable('eav_attribute_option_value');

// add options for level of politeness
$attributeId = (int)$setup->getAttribute('customer', 'invoices_lobby', 'attribute_id');
foreach (array('25', '50', '75', '100', '125') as $sortOrder => $label) {

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
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'invoices_lobby');
$attribute->setData('used_in_forms', $usedInforms)
        ->setData('is_used_for_customer_segment', TRUE)
        ->setData('is_system', 0)
        ->setData('is_user_defined', 1)
        ->setData('is_visible', 1)
        ->setData('sort_order', 0);
$attribute->save();



$setup->addAttribute('customer', 'orders_history_days', array(
    'label'        => 'Number of Days of Orders to be fetched (Order History)',
    'visible'      => TRUE,
    'required'     => TRUE,
    'type'         => 'varchar',
    'input'        => 'select',
    'source'        => 'eav/entity_attribute_source_table',
));


$tableOptions        = $installer->getTable('eav_attribute_option');
$tableOptionValues   = $installer->getTable('eav_attribute_option_value');

// add options for level of politeness
$attributeId = (int)$setup->getAttribute('customer', 'orders_history_days', 'attribute_id');
foreach (array('30', '60', '90', '120', '150') as $sortOrder => $label) {

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
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'orders_history_days');
$attribute->setData('used_in_forms', $usedInforms)
        ->setData('is_used_for_customer_segment', TRUE)
        ->setData('is_system', 0)
        ->setData('is_user_defined', 1)
        ->setData('is_visible', 1)
        ->setData('sort_order', 0);
$attribute->save();

$setup->addAttribute('customer', 'invoices_days', array(
    'label'        => 'Number of Days of Invoices to be fetched (Invoices Page)',
    'visible'      => TRUE,
    'required'     => TRUE,
    'type'         => 'varchar',
    'input'        => 'select',
    'source'        => 'eav/entity_attribute_source_table',
));


$tableOptions        = $installer->getTable('eav_attribute_option');
$tableOptionValues   = $installer->getTable('eav_attribute_option_value');

// add options for level of politeness
$attributeId = (int)$setup->getAttribute('customer', 'invoices_days', 'attribute_id');
foreach (array('30', '60', '90', '120', '150') as $sortOrder => $label) {

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
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'invoices_days');
$attribute->setData('used_in_forms', $usedInforms)
        ->setData('is_used_for_customer_segment', TRUE)
        ->setData('is_system', 0)
        ->setData('is_user_defined', 1)
        ->setData('is_visible', 1)
        ->setData('sort_order', 0);
$attribute->save();


$installer->endSetup();