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
    ALTER TABLE `customer_entity` ADD `user_key` INT( 20 ) NULL DEFAULT '0' COMMENT 'User Key of the User.';
    "
);

$installer->endSetup();