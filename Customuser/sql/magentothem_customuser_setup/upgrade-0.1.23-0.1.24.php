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

$installer->run("

CREATE TABLE IF NOT EXISTS `sap_customer_ordertype` (
  `ordertype_id` int(10) NOT NULL AUTO_INCREMENT,
  `sales_org` char(4) NOT NULL,
  `code` char(1) DEFAULT NULL,
  `order_type` char(4) NOT NULL,
  `language` char(2) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`ordertype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `sap_customer_ordertype` (`ordertype_id`, `sales_org`, `code`, `order_type`, `language`, `description`) VALUES
(1, '2501', 'S', 'OR', 'en', 'Standard Order'),
(2, '2501', 'S', 'PS', 'es', 'PEDIDO ESTÁNDAR'),
(3, '2501', 'S', 'OT', 'pt', 'Ordem standard'),
(4, '1002', 'S', 'ZREV', 'en', 'Standard Order'),
(5, '1002', 'S', 'ZREV', 'es', 'Reservado p/Brazil'),
(6, '1002', 'S', 'ZREV', 'pt', 'VENDAS PARA REVENDA'),
(7, '1002', 'I', 'ZIND', 'en', 'Standard Order'),
(8, '1002', 'I', 'ZIND', 'es', 'Reservado p/Brazil'),
(9, '1002', 'I', 'ZIND', 'pt', 'VENDAS P/ INDUSTRIA'),
(10, '1002', 'P', 'ZSIP', 'en', 'Standard Order'),
(11, '1002', 'P', 'ZSIP', 'es', 'Reservado p/Brazil'),
(12, '1002', 'P', 'ZSIP', 'pt', 'VENDAS C/ Susp. IPI'),
(13, '1002', 'H', 'ZDIF', 'en', 'Standard Order'),
(14, '1002', 'H', 'ZDIF', 'es', 'Reservado p/Brazil'),
(15, '1002', 'H', 'ZDIF', 'pt', 'VENDAS C/ DIF.ICMS'),
(16, '1002', 'G', 'ZCON', 'en', 'Standard Order'),
(17, '1002', 'G', 'ZCON', 'es', 'Reservado p/Brazil'),
(18, '1002', 'G', 'ZCON', 'pt', 'VENDA P/ CONSUMO'),
(19, '1002', NULL, 'ZDWB', 'en', 'Standard Order'),
(20, '1002', NULL, 'ZDWB', 'es', 'Reservado p/Brazil'),
(21, '1002', NULL, 'ZDWB', 'pt', 'VENDAS DRAWBACK'),
(22, '1002', 'K', 'KBB', 'en', 'Consignmnt fillup BR'),
(23, '1002', 'K', 'KBB', 'es', 'Reservado p/Brazil'),
(24, '1002', 'K', 'KBB', 'pt', 'ENVIO P/ CONSIGNAÇÃO'),
(25, '1002', 'R', 'RCM', 'en', 'Rem.p/cta.e ord.merc'),
(26, '1002', 'R', 'RCM', 'es', 'Reservado p/Brazil'),
(27, '1002', 'R', 'RCM', 'pt', 'REMESSA CONTA/ORDEM'),
(28, '1002', NULL, 'KEB', 'en', 'Consignment Issue BR'),
(29, '1002', NULL, 'KEB', 'es', 'Reservado p/Brazil'),
(30, '1002', NULL, 'KEB', 'pt', 'VENDA MAT.CONSIGNADO'),
(31, '1002', NULL, 'RCS', 'en', 'Rem.p/ cta. simpl.f.'),
(32, '1002', NULL, 'RCS', 'es', 'Reservado p/Brazil'),
(33, '1002', NULL, 'RCS', 'pt', 'FATURAM.CONTA/ORDEM'),
(34, '2001', 'D', 'ZDIS', 'en', 'Standard Order'),
(35, '2001', 'D', 'ZDIS', 'es', 'Reservado p/Brazil'),
(36, '2001', 'D', 'ZDIS', 'pt', 'VENDAS PARA DISTRIB'),
(37, '2001', 'B', 'ZCID', 'en', 'Standard Order'),
(38, '2001', 'B', 'ZCID', 'es', 'Reservado p/Brazil'),
(39, '2001', 'B', 'ZCID', 'pt', 'VENDAS CLIENTE INDUS'),
(40, '2001', 'Z', 'ZDZF', 'en', 'Standard Order'),
(41, '2001', 'Z', 'ZDZF', 'es', 'Reservado p/Brazil'),
(42, '2001', 'Z', 'ZDZF', 'pt', 'VENDAS DIST Z FRANCA'),
(43, '2001', 'N', 'ZDNZ', 'en', 'Standard Order'),
(44, '2001', 'N', 'ZDNZ', 'es', 'Reservado p/Brazil'),
(45, '2001', 'N', 'ZDNZ', 'pt', 'VENDAS DIST NÃO ZFRA'),
(46, '1503', 'S', 'OR', 'en', 'Standard Order'),
(47, '1503', 'S', 'PS', 'es', 'PEDIDO ESTÁNDAR'),
(48, '1503', 'S', 'OT', 'pt', 'Ordem standard'),
(49, '1502', 'S', 'OR', 'en', 'Standard Order'),
(50, '1502', 'S', 'PS', 'es', 'PEDIDO ESTÁNDAR'),
(51, '1502', 'S', 'OT', 'pt', 'Ordem standard');

");

$installer->endSetup();