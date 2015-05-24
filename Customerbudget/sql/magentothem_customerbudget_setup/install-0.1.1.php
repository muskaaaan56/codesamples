<?php
/**
* @category magentothem
* @package magentothem
* @author Ecomwhizz
*/
$installer = $this;
$installer->startSetup();

$order = $installer->getConnection()
    ->newTable($installer->getTable('magentothem_customerbudget/customerbudget'))
    ->addColumn
    ('customerbudget_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        NULL,
        array(
            'unsigned' => TRUE,
            'identity' => TRUE,
            'nullable' => FALSE,
            'primary'  => TRUE,
        ), 'Primary key of Customerbudget Table'
    )
    ->addColumn
    ('budget_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        80,
        array(
            'nullable' => FALSE,
            'default'  => NULL,
        ), 'Budget Name'
    )
    ->addColumn
    ('budget_code',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        80,
        array(
            'nullable' => FALSE,
            'default'  => NULL,
        ), 'Budget Code'
    )
    ->addColumn
    ('expired_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        NULL,
        array(
            'nullable' => FALSE,
        ), 'Expired At'
    )
    ->addColumn
    ('status',
        Varien_Db_Ddl_Table::TYPE_TINYINT,
        NULL,
        array(
            'nullable' => FALSE,
            'default'  => 1,
        ), 'Status'
    );
    
    
$installer->getConnection()->createTable($order);
 



$this->endSetup();