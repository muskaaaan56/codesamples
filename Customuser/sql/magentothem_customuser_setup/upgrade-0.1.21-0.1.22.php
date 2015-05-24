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
$installer->run("ALTER TABLE `sap_customer_master` ADD COLUMN `status` smallint(5);");
$installer->endSetup();
