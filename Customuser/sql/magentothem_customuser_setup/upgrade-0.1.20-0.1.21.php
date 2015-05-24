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

$installer->startSetup();
$installer->run("ALTER TABLE `sap_customer_address` ADD COLUMN `address3` VARCHAR(255);");
$installer->run("ALTER TABLE `sap_customer_master` MODIFY COLUMN `name` VARCHAR(255);");
$installer->endSetup();
