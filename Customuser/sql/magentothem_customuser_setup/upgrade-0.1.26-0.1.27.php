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
    ALTER TABLE `sap_customer_address` ADD `pricegroup` TEXT NOT NULL AFTER `transp_code`;
    "
);

$installer->endSetup();