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

$installer->run("ALTER TABLE `customer_entity` ADD `sfdc_user_id` varchar(20) ;");

$installer->endSetup();