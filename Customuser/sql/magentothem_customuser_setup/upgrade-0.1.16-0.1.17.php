<?php
/**
* Extension Axalta
*
* @category   Axaltacore
* @package    Axaltacore
* @author     Digitales
* @license
*/

$installer = $this;

$installer->startSetup();

$installer->run(
    "
    ALTER TABLE `sap_customer_master` ADD `paymentterms` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `website_code` ,
    ADD `paymenttermstext` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `paymentterms`;
    "
);

$installer->endSetup();