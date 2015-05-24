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

$installer->run(
    "
    ALTER TABLE `sap_customer_master` ADD `dsordertype` text COMMENT 'LA DSORDERTYPE';
    "
);

$installer->endSetup();