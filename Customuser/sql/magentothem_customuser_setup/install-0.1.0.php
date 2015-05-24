<?php
/**
 * customuser installation script
 *
 * @author cignex datamatics
 */

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$custmaster = $installer->getConnection()
    ->newTable($installer->getTable('axaltacore_customuser/custmaster'))
    ->addColumn
    (
        'cust_master_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        NULL,
        array(
            'unsigned' => TRUE,
            'identity' => TRUE,
            'nullable' => FALSE,
            'primary'  => TRUE,
        ), 'Customer Master Id'
    )
    ->addColumn(
        'sap_cust_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        NULL,
        array(
            'nullable' => FALSE,
            'default'  => 0,
        ), 'SAP Customer Id'
    )
    ->addColumn(
        'sales_organization_id',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255, 
        array(
            'nullable' => FALSE,
            'default'  => NULL,
        ), 'Sales Area Id'
    )
    ->addColumn(
        'division',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255, 
        array(
            'nullable' => FALSE,
            'default'  => NULL,
        ), 'Division'
    )
    ->addColumn(
        'distribution_channel',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255, 
        array(
            'nullable' => FALSE,
            'default'  => NULL,
        ), 'Distribution Channel'
    )
   ->addColumn(
        'name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
            'nullable' => FALSE,
            'default'  => NULL,
        ), 'Sales Area Name'
    )
    ->addColumn(
        'website_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Website Code'
    )
    ->setComment('SAP Customer and Sales Area Mapping');
$installer->getConnection()->createTable($custmaster);

$custaddress = $installer->getConnection()
    ->newTable($installer->getTable('axaltacore_customuser/custaddress'))
    ->addColumn(
        'custaddressid', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
            'unsigned' => TRUE,
            'nullable' => FALSE,
            'primary'  => TRUE,
        ), 'Customer Address Id'
    )
    ->addColumn(
        'partner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
           'nullable' => TRUE,
           'default' => 0
        ), 'Partner id'
    )
    ->addColumn(
        'street_no', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Street Number'
    )
    ->addColumn(
        'zip_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'ZIP Postcode '
    )
    ->addColumn(
        'city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'City'
    )
    ->addColumn(
        'state', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'State'
    )
    ->addColumn(
        'country', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Country Code'
    )
    ->setComment('SAP Partner Address');

$installer->getConnection()->createTable($custaddress);

$custpartner = $installer->getConnection()
    ->newTable($installer->getTable('axaltacore_customuser/custpartner'))
    ->addColumn(
        'cust_partner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
            'unsigned' => TRUE,
            'identity' => TRUE,
            'nullable' => FALSE,
            'primary'  => TRUE,
        ), 'Customer Address Id'
    )
    ->addColumn(
        'sap_cust_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
            'nullable' => FALSE,
        ), 'SAP Customer Id'
    )
    ->addColumn(
        'address_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Address Type'
    )
    ->addColumn(
        'partner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
           'nullable' => FALSE,
        ), 'Partner Id'
    )
    ->setComment('SAP Partner Function Detail');
    $installer->getConnection()->createTable($custpartner);
    /*$installer->getConnection()->addConstraint(
        'FK_PARTNER_ID',
        $installer->getTable('axaltacore_customuser/custpartner'), 
        'partner_id',
        $installer->getTable('axaltacore_customuser/custaddress'), 
        'partner_id',
        'cascade', 
        'cascade'
    );

    $installer->getConnection()->addConstraint(
        'FK_SAP_CUST_ID_CUSTPARTNER',
        $installer->getTable('axaltacore_customuser/custpartner'), 
        'sap_cust_id',
        $installer->getTable('axaltacore_customuser/custmaster'), 
        'sap_cust_id',
        'cascade',
        'cascade'
    );*/

$table_salesarea = $installer->getConnection()
    ->newTable($installer->getTable('axaltacore_customuser/salesarea'))
    ->addColumn(
        'salesarea_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
            'unsigned' => TRUE,
            'identity' => TRUE,
            'nullable' => FALSE,
            'primary'  => TRUE,
        ), 'Axalta salesarea id'
    )
    ->addColumn(
        'company_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Company Name'
    )
    ->addColumn(
        'sales_organization_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Sales Organization Id'
    )
    ->addColumn(
        'division', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Division'
    )
    ->addColumn(
        'distribution_channel', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Distribution Channel'
    )
    ->addColumn(
        'name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Name'
    )
    ->addColumn(
        'iln', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'ILN'
    )
    ->addColumn(
        'street_no', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Street No'
    )
    ->addColumn(
        'zip_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'ZIP Postcode '
    )
    ->addColumn(
        'city', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'City'
    )
    ->addColumn(
        'country', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Country'
    )
    ->addColumn(
        'email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Email'
    )
    ->addColumn(
        'phone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Phone'
    )
    ->addColumn(
        'fax', Varien_Db_Ddl_Table::TYPE_TEXT, 255, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Fax'
    )
    ->addColumn(
        'website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, NULL, 
        array(
            'nullable' => FALSE,
            'unsigned'  => TRUE,
            'default' => '0',
        ), 'Website Id'
    )
    ->setComment('Axalta Sales Area');
$installer->getConnection()->createTable($table_salesarea);

$user = $installer->getConnection()
    ->newTable($installer->getTable('axaltacore_customuser/custusermap'))
    ->addColumn(
        'cust_user_map_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
            'unsigned' => TRUE,
            'identity' => TRUE,
            'nullable' => FALSE,
            'primary'  => TRUE,
        ), 'Customer User Id'
    )
    ->addColumn(
        'cust_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
            'nullable' => TRUE,
            'default'  => 0,
        ), 'Customer Id'
    )
    ->addColumn(
        'sap_cust_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'SAP Customer Id'
    )
    ->addColumn(
        'salesarea_id', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
            'nullable' => TRUE,
            'default'  => NULL,
        ), 'Sales Area Id'
    )
    ->addColumn(
        'is_default', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
            'nullable' => TRUE,
            'default'  => 0,
        ), 'Is Default'
    )
    ->addColumn(
        'is_deleted', Varien_Db_Ddl_Table::TYPE_INTEGER, NULL, 
        array(
            'nullable' => TRUE,
            'default'  => 0,
        ), 'Is Deleted'
    )
    ->setComment('SAP Customer and Magento User Mapping');

$installer->getConnection()->createTable($user);

$installer->getConnection()->addConstraint(
    'FK_SALESAREA_ID_SALESAREA',
    $installer->getTable('axaltacore_customuser/custusermap'), 
    'salesarea_id',
    $installer->getTable('axaltacore_customuser/salesarea'), 
    'salesarea_id',
    'cascade', 
    'cascade'
);

$installer->getConnection()->addConstraint(
    'FK_CUSTOMER_ID_CUSTUSER',
    $installer->getTable('axaltacore_customuser/custusermap'), 
    'cust_id',
    $installer->getTable('customer/entity'), 
    'entity_id',
    'cascade',
    'cascade'
);
