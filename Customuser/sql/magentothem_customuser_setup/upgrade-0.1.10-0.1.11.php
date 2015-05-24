<?php
/**
 * NA Partner installation script
 *
 * @author Digitales
 */

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS " . $installer->getTable('axaltacore_customuser/partnertypes'));
    
$partnertype = $installer->getConnection()
    ->newTable($installer->getTable('axaltacore_customuser/partnertypes'))
    ->addColumn
    (
        'partnertype_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        NULL,
        array(
            'unsigned' => TRUE,
            'identity' => TRUE,
            'nullable' => FALSE,
            'primary'  => TRUE,
        ), 'Partner Type Id'
    )
    ->addColumn(
        'title',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255, 
        array(
            'nullable' => FALSE,
            'default'  => NULL,
        ), 'Partner Title'
    );

$installer->getConnection()->createTable($partnertype);
$installer->run("
    INSERT INTO " . $installer->getTable('axaltacore_customuser/partnertypes') . " VALUES(1, 'Jobber');
    INSERT INTO " . $installer->getTable('axaltacore_customuser/partnertypes') . " VALUES(2, 'Direct [A Account]');
    INSERT INTO " . $installer->getTable('axaltacore_customuser/partnertypes') . " VALUES(3, 'System User');
");


$installer->run("DROP TABLE IF EXISTS " . $installer->getTable('axaltacore_customuser/partnerinfo'));


$partnerinfo = $installer->getConnection()
    ->newTable($installer->getTable('axaltacore_customuser/partnerinfo'))
    ->addColumn
    (
        'partnerinfo_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        NULL,
        array(
            'unsigned' => TRUE,
            'identity' => TRUE,
            'nullable' => FALSE,
            'primary'  => TRUE,
        ), 'Partner Info Id'
    )
    ->addColumn
    (
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        NULL,
        array(
            'nullable' => FALSE,
        ), 'Customer Id'
    )
    ->addColumn(
        'user_type',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        100, 
        array(
            'nullable' => FALSE,
            'default'  => NULL,
        ), 'User Type'
    )
    ->addColumn
    (
        'partner_type',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        NULL,
        array(
            'nullable' => FALSE,
        ), 'Partner Type'
    )
    ->addColumn
    (
        'is_jms_partner',
         Varien_Db_Ddl_Table::TYPE_TINYINT, 
         null, 
         array(
            'nullable' => true,
          ), 'Is JMS Partner'
     )
     ->addColumn(
        'jms_system_url',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        100, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'JMS System URL'
    )
    ->addColumn(
        'jms_system_username',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        100, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'JMS System Username'
    )
    ->addColumn(
        'jms_system_password',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        100, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'JMS System Password'
    )
    ->addColumn(
        'owner_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        100, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Owner Name'
    )
    ->addColumn(
        'owner_email',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        100, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Owner Email'
    )
    ->addColumn(
        'stop_print_invoice_date',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        NULL, 
        array(
            'nullable' => TRUE,
        ), 'Stop Print Invoice Date'
    );

$installer->getConnection()->createTable($partnerinfo);

$installer->getConnection()->addIndex(
    $installer->getTable('axaltacore_customuser/partnerinfo'),
    $installer->getIdxName($installer->getTable('axaltacore_customuser/partnerinfo'), array('customer_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
    array('customer_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->endSetup();