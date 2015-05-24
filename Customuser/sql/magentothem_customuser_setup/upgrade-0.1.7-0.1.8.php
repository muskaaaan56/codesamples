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
    ALTER TABLE `sap_customer_master` DROP `partner_key`;
    ALTER TABLE `customer_entity` ADD `partner_key` INT( 11 ) NULL DEFAULT '0' COMMENT 'Partner key of the user.';
    "
);

$installer->endSetup();