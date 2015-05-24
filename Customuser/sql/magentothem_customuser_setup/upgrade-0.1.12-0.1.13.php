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
$installer
    ->getConnection()
    ->addConstraint(
        'FK_CUSTOMER_RELATION_PARTNER_INFO',
        $installer->getTable('na_partnerinfo'), 
        'customer_id',
        $installer->getTable('customer_entity'), 
        'entity_id',
        'cascade', 
        'cascade'
);

$installer->endSetup();