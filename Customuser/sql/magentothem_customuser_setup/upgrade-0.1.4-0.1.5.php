<?php
/**
* Extension Userrole
*
* @category   Axaltacore
* @package    Axaltacore
* @author     Digitales
* @license
*/
$installer = $this;
$installer->startSetup();
$setup = new Mage_Customer_Model_Entity_Setup();

$tableOptionValues   = $installer->getTable('eav_attribute_option_value');
$resource = Mage::getSingleton('core/resource');
     
/**
 * Retrieve the write connection
*/
$writeConnection = $resource->getConnection('core_write');


$invoices_lobby_attributes = Mage::getResourceSingleton('customer/customer')->getAttribute('invoices_lobby')->getSource()->getAllOptions(false);
$optionOrder = array(0 => '5', 1 =>'10', 2 => '15', 3 => '20', 4 =>'25');
$count = 0;
foreach($invoices_lobby_attributes as $val)
{
	$optionId      = $val['value'];
	$optionLabel   = $optionOrder[$count];
	$data = array(
        'option_id' => $optionId,
        'store_id'  => 0,
        'value'     => $optionLabel,
    );
    $count++;
	$query = "UPDATE {$tableOptionValues} SET value = '{$optionLabel}' WHERE value_id = " . (int)$optionId;             
    $writeConnection->query($query);
    if($optionLabel == 25 )
    	$default_invoice = $optionId;
}

$orders_lobby_attributes = Mage::getResourceSingleton('customer/customer')->getAttribute('orders_lobby')->getSource()->getAllOptions(false);
$count = 0;
foreach($orders_lobby_attributes as $val)
{
	$optionId      = $val['value'];
	$optionLabel   = $optionOrder[$count];
	$data = array(
        'option_id' => $optionId,
        'store_id'  => 0,
        'value'     => $optionLabel,
    );
    $count++;
	$query = "UPDATE {$tableOptionValues} SET value = '{$optionLabel}' WHERE value_id = " . (int)$optionId;             
    $writeConnection->query($query);
    if($optionLabel == 25 )
    	$default_order = $optionId;
}

$query = "UPDATE `eav_attribute` SET `default_value` = '$default_invoice' WHERE `eav_attribute`.`attribute_code`='invoices_lobby'";
$writeConnection->query($query);

$query = "UPDATE `eav_attribute` SET `default_value` = '$default_order' WHERE `eav_attribute`.`attribute_code`='orders_lobby'";
$writeConnection->query($query);
