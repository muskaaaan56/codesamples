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
    ALTER TABLE `customer_entity` ADD `addemail` Varchar(500) NULL COMMENT 'Additional Emails';
    "
);

$installer->endSetup();