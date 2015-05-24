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
$installer->run("ALTER TABLE `customer_entity` ADD UNIQUE INDEX `User Id` (`uid`)");
$installer->endSetup();