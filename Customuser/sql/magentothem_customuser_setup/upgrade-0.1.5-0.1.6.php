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

$setup->addAttribute('customer', 'lang_store', array(
    'label'        => 'Language',
    'type'         => 'varchar',
    'visible'      => true,
    'required'     => true,
    'input'        => 'select',
    'source'       => 'eav/entity_attribute_source_table',
));


$tableOptions        = $installer->getTable('eav_attribute_option');
$tableOptionValues   = $installer->getTable('eav_attribute_option_value');


// add attribute for Store view code
$attributeId = (int)$setup->getAttribute('customer', 'lang_store', 'attribute_id');
$coreWebCol = Mage::getModel('core/website')->getCollection();
$coreWebCol->getSelect()->join( array('cs'=>'core_store'), 'main_table.website_id = cs.website_id', array('cs.code','cs.website_id','cs.name'));
$coreWebCol->addFieldToFilter('main_table.code',array('eq' => Axaltacore_Usermanagement_Helper_Data::LA_WEBSITE));
$coreWebCol = $coreWebCol->getData();

foreach($coreWebCol as $val)
{
	$coreWebColAt[] = $val ['code'];
}

foreach ($coreWebColAt as $sortOrder => $label) {

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

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'lang_store');
$attribute->setData('used_in_forms', $usedInforms)
->setData('is_used_for_customer_segment', TRUE)
->setData('is_system', 0)
->setData('is_user_defined', 1)
->setData('is_visible', 1)
->setData('sort_order', 0);
$attribute->save();

$installer->endSetup();