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
$installer->run("ALTER TABLE `customer_entity` MODIFY  `uid` VARCHAR(255);");
$installer->endSetup();
