<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/
$installer = $this;
$installer->startSetup();
$installer->run("Alter table sales_flat_quote add column `po_number` varchar(255)");
$installer->run("Alter table sales_flat_order add column `po_number` varchar(255)");
$installer->run("Alter table sales_flat_quote add column `order_type` varchar(15)");
$installer->run("Alter table sales_flat_order add column `order_type` varchar(15)");
$installer->endSetup();