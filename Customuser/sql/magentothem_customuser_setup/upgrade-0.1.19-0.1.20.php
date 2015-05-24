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
$installer->run("ALTER TABLE na_partnerinfo ADD COLUMN jms_support_email  VARCHAR(100), ADD COLUMN loaded_invoice_dt  DATETIME, ADD COLUMN allow_bip_orderds_ind VARCHAR(100), ADD COLUMN partner_key INT(10);");
$installer->endSetup();