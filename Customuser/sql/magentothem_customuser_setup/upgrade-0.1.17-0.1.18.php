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
    ALTER TABLE `sap_customer_master` ADD `dspaymentterms` text COMMENT 'DSPAYMENTTERMS' AFTER `website_code`;
    "
);

$installer->endSetup();