<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/
$installer = $this;
$installer->startSetup();
$installer->run("Alter table sales_flat_quote add column `budget_code` varchar(80)");
$installer->run("Alter table sales_flat_order add column `budget_code` varchar(80)");
$installer->endSetup();