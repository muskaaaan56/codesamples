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
    ALTER TABLE `customer_entity` ADD `parent_id` INT( 11 ) NULL DEFAULT '0' COMMENT 'Parent Id of the User.';
    "
);

$installer->endSetup();