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

$installer->run(
    "
    ALTER TABLE axaltacore_salesarea CHANGE `email` `email` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Email';
    ALTER TABLE sap_customer_user ADD UNIQUE `cust_id_sap_cust_id_salesarea_id` (`cust_id`, `sap_cust_id`, `salesarea_id`);
    ALTER TABLE axaltacore_salesarea ADD dupont_salesarea_id INT NOT NULL COMMENT 'taken for ref while data migration';
    ALTER TABLE axaltacore_salesarea ADD UNIQUE sales_organization_id_division_distribution_channel_website_id (`sales_organization_id`, `division`, `distribution_channel`, `website_id`);
    "
);

$installer->endSetup();