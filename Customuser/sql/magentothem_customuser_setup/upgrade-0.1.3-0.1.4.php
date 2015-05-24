<?php
/**
* Extension Userrole
*
* @category   Axaltacore
* @package    Axaltacore
* @author     Digitales
* @license
*/
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('axaltacore_customuser/custusermap'),
    'can_create_order',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => FALSE,
        'unsigned'  => TRUE,
        'default' => '0',
        'comment' => 'Can Create Order'
    )
);
$installer->getConnection()
    ->addColumn($installer->getTable('axaltacore_customuser/custusermap'),
    'can_view_order',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => FALSE,
        'unsigned'  => TRUE,
        'default' => '0',
        'comment' => 'Can View Order'
    )
);
$installer->endSetup();