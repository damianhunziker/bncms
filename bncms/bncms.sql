-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 20. Okt 2019 um 12:45
-- Server-Version: 5.7.17
-- PHP-Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `bncms`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `assign_orders_products`
--

CREATE TABLE `assign_orders_products` (
  `id` int(20) NOT NULL,
  `id_orders` int(20) NOT NULL,
  `id_products` int(20) NOT NULL,
  `options` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `assign_orders_products`
--

INSERT INTO `assign_orders_products` (`id`, `id_orders`, `id_products`, `options`) VALUES
(1, 1, 2, 'Mit gelben Bendeln23'),
(2, 1, 2, 'Version GrÃ¼n');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bncms_banned_ips`
--

CREATE TABLE `bncms_banned_ips` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bncms_user`
--

CREATE TABLE `bncms_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `notizen` text NOT NULL,
  `gruppe` int(5) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `bncms_user`
--

INSERT INTO `bncms_user` (`id`, `username`, `password`, `notizen`, `gruppe`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', 1),
(2, 'webuser', '21232f297a57a5a743894a0e4a801fc3', '', 0),
(3, 'redakteur', 'f1f5e247297b0133033cd5d34e057da6', '', 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bncms_user_groups`
--

CREATE TABLE `bncms_user_groups` (
  `id` int(20) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `permit_configuration` set('','on','off') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `bncms_user_groups`
--

INSERT INTO `bncms_user_groups` (`id`, `name`, `permit_configuration`) VALUES
(1, 'Administratoren', 'on'),
(2, 'Redakteure', 'off');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `conf_fields`
--

CREATE TABLE `conf_fields` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(250) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'textfield',
  `mysql_order` decimal(3,2) NOT NULL DEFAULT '0.00',
  `unchangeable` varchar(250) DEFAULT NULL,
  `hidden` varchar(250) DEFAULT NULL,
  `id_table` int(5) NOT NULL DEFAULT '0',
  `mysqlType` varchar(250) NOT NULL DEFAULT '0',
  `mysql_type_bez` varchar(250) NOT NULL,
  `length_values` varchar(250) NOT NULL,
  `nto1TargetField` varchar(15) NOT NULL,
  `nto1TargetTable` varchar(35) NOT NULL,
  `validation_required` set('','on','off') NOT NULL,
  `validation_unique` set('','on','off') NOT NULL,
  `validation_min_length` int(5) NOT NULL,
  `nto1DisplayType` varchar(20) NOT NULL,
  `nto1DropdownTitleField` varchar(50) NOT NULL,
  `processing` varchar(30) NOT NULL,
  `min_height` int(6) NOT NULL,
  `min_width` int(6) NOT NULL,
  `max_height` int(6) NOT NULL,
  `max_width` int(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `conf_fields`
--

INSERT INTO `conf_fields` (`id`, `name`, `title`, `type`, `mysql_order`, `unchangeable`, `hidden`, `id_table`, `mysqlType`, `mysql_type_bez`, `length_values`, `nto1TargetField`, `nto1TargetTable`, `validation_required`, `validation_unique`, `validation_min_length`, `nto1DisplayType`, `nto1DropdownTitleField`, `processing`, `min_height`, `min_width`, `max_height`, `max_width`) VALUES
(1, 'id', '', 'textfield', '0.00', NULL, NULL, 1, 'int(10) unsigned', 'INT', '10', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(2, 'username', '', 'textfield', '0.00', NULL, NULL, 1, 'varchar(50)', 'VARCHAR', '50', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(3, 'password', '', 'password', '0.00', '0', '0', 1, 'varchar(50)', 'VARCHAR', '50', '', 'b', 'off', 'off', 0, 'radio', '', '', 0, 0, 0, 0),
(4, 'notizen', '', 'textfield', '0.00', NULL, NULL, 1, 'text', 'TEXT', '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(5, 'gruppe', '', 'nto1', '0.00', '', '', 1, 'int(5)', 'INT', '5', '6', '2', 'off', 'off', 0, 'dropdown', 'name', '', 0, 0, 0, 0),
(6, 'id', '', 'number', '0.00', '0', '0', 2, 'int(20)', 'INT', '20', '150', '1', 'off', 'off', 0, 'radio', 'orders_id', '', 0, 0, 0, 0),
(7, 'name', '', 'textfield', '0.00', '0', '0', 2, 'varchar(30)', 'VARCHAR', '30', '1', '1', 'off', 'on', 0, 'radio', 'orders_id', '', 0, 0, 0, 0),
(8, 'permit_configuration', 'Erlaube Konfiguration', 'checkbox', '0.00', '0', '0', 2, 'set(\'\',\'on\',\'off\')', 'SET', '\'\',\'on\',\'off\'', '1', '1', 'off', 'off', 0, 'radio', 'orders_id', '', 0, 0, 0, 0),
(9, 'id', '', 'textfield', '0.00', NULL, NULL, 3, 'varchar(250)', '', '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(10, 'title', '', 'textfield', '0.00', NULL, NULL, 3, 'varchar(250)', '', '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(11, 'text', '', 'textfield', '0.00', NULL, NULL, 3, 'text', '', '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(12, 'image', '', 'textfield', '0.00', NULL, NULL, 3, 'varchar(250)', '', '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(13, 'price', '', 'textfield', '0.00', NULL, NULL, 3, 'decimal(2,0)', '', '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(14, 'category_id', '', 'textfield', '0.00', NULL, NULL, 3, 'int(20)', '', '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `conf_relations`
--

CREATE TABLE `conf_relations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '0',
  `table1` int(5) NOT NULL DEFAULT '0',
  `table2` int(5) NOT NULL DEFAULT '0',
  `ntomAssignFieldTable1` varchar(255) NOT NULL DEFAULT '',
  `ntomAssignFieldTable2` varchar(255) NOT NULL DEFAULT '',
  `seperateColumns` set('','on','off') NOT NULL DEFAULT '',
  `users` text NOT NULL,
  `editors` text NOT NULL,
  `deletors` text NOT NULL,
  `addors` text NOT NULL,
  `ntomDisplayType` varchar(20) NOT NULL,
  `ntomAjaxDisplayTitleField` varchar(50) NOT NULL,
  `ntomAjaxDisplayMinSelections` int(5) NOT NULL,
  `nto1TargetField` varchar(100) NOT NULL,
  `nto1TargetTable` int(5) NOT NULL,
  `nto1SourceField` varchar(100) NOT NULL,
  `nto1SourceTable` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `conf_relations`
--

INSERT INTO `conf_relations` (`id`, `name`, `type`, `table1`, `table2`, `ntomAssignFieldTable1`, `ntomAssignFieldTable2`, `seperateColumns`, `users`, `editors`, `deletors`, `addors`, `ntomDisplayType`, `ntomAjaxDisplayTitleField`, `ntomAjaxDisplayMinSelections`, `nto1TargetField`, `nto1TargetTable`, `nto1SourceField`, `nto1SourceTable`) VALUES
(1, '', 'nto1', 0, 0, '', '', '', '', '', '', '', '', '', 0, 'id', 2, 'gruppe', 1),
(2, '', 'ntom', 0, 3, '', '', '', '', '', '', '', '', '', 0, '', 0, '', 0),
(3, '', 'ntom', 0, 3, '', '', '', '', '', '', '', '', '', 0, '', 0, '', 0),
(4, '', 'ntom', 0, 3, '', '', '', '', '', '', '', '', '', 0, '', 0, '', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `conf_relation_visibility`
--

CREATE TABLE `conf_relation_visibility` (
  `id` int(10) NOT NULL,
  `path` varchar(255) NOT NULL,
  `users` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `showWithEditIcons` set('Separat','Normal','Beides') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `conf_relation_visibility`
--

INSERT INTO `conf_relation_visibility` (`id`, `path`, `users`, `icon`, `title`, `showWithEditIcons`) VALUES
(1, '1-1', 'a:2:{i:0;s:15:\"Administratoren\";i:1;s:10:\"Redakteure\";};', '', '', ''),
(2, '2-1', 'a:2:{i:0;s:15:\"Administratoren\";i:1;s:5:\"admin\";};', '', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `conf_tables`
--

CREATE TABLE `conf_tables` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `columnNameOfId` varchar(64) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '0',
  `insert` varchar(10) NOT NULL DEFAULT '0',
  `mysql_condition` varchar(255) NOT NULL DEFAULT '0',
  `orderkey` decimal(3,2) NOT NULL DEFAULT '0.00',
  `color` varchar(10) NOT NULL DEFAULT '0',
  `users` text NOT NULL,
  `editors` text NOT NULL,
  `deletors` text NOT NULL,
  `addors` text NOT NULL,
  `is_assign_table` varchar(5) NOT NULL DEFAULT '',
  `id_relation` int(10) UNSIGNED NOT NULL,
  `editable` varchar(250) NOT NULL DEFAULT '',
  `sort_order` varchar(50) NOT NULL,
  `sort_order_ascdesc` set('','asc','desc') NOT NULL,
  `entries_per_page` int(5) NOT NULL DEFAULT '10',
  `export_xls` set('','on','off') NOT NULL,
  `export_csv` set('','on','off') NOT NULL,
  `actualize` set('','on','off') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `conf_tables`
--

INSERT INTO `conf_tables` (`id`, `name`, `columnNameOfId`, `lang`, `insert`, `mysql_condition`, `orderkey`, `color`, `users`, `editors`, `deletors`, `addors`, `is_assign_table`, `id_relation`, `editable`, `sort_order`, `sort_order_ascdesc`, `entries_per_page`, `export_xls`, `export_csv`, `actualize`) VALUES
(1, 'bncms_user', 'id', 'Benutzer', '0', '', '9.99', '', 'a:2:{i:0;s:15:\"Administratoren\";i:1;s:10:\"Redakteure\";};', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', '', 0, '', '', '', 50, '', '', ''),
(2, 'bncms_user_groups', 'id', 'Benutzergruppen', '0', '', '9.99', '', 'a:2:{i:0;s:15:\"Administratoren\";i:1;s:5:\"admin\";};', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', '', 0, '', '', '', 0, '', '', ''),
(3, 'products', 'id', 'Produke', '0', '', '0.00', '', 'a:2:{i:0;s:15:\"Administratoren\";i:1;s:10:\"Redakteure\";}', 'a:2:{i:0;s:15:\"Administratoren\";i:1;s:10:\"Redakteure\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', '', 0, '', '', '', 0, '', '', 'on');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE `orders` (
  `id` varchar(250) NOT NULL DEFAULT '',
  `title` varchar(250) NOT NULL,
  `address_id` int(20) NOT NULL,
  `state` varchar(250) NOT NULL,
  `price` decimal(2,0) DEFAULT NULL,
  `user_id` int(20) NOT NULL,
  `date` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`id`, `title`, `address_id`, `state`, `price`, `user_id`, `date`) VALUES
('1', 'Testbestellung', 3, 'Bestellt', '45', 5, -434937499);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products`
--

CREATE TABLE `products` (
  `id` varchar(250) NOT NULL DEFAULT '',
  `title` varchar(250) NOT NULL,
  `text` text NOT NULL,
  `image` varchar(250) NOT NULL,
  `price` decimal(2,0) DEFAULT NULL,
  `category_id` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `products`
--

INSERT INTO `products` (`id`, `title`, `text`, `image`, `price`, `category_id`) VALUES
('1', 'GÃ¼nstige Turnschuhe', '<p>Diese Turnschuhe sind sehr g&uuml;nstig</p>', 'file/Koala.jpg', '20', 3),
('2', 'Nike Fussballschuhe', '<p>Super gute Fussball Schuhe!</p>', 'file/Jellyfish.jpg', '10', 4),
('3', 'Sommersandalen', '<p>Lauschige Sommersandalen</p>', 'file/Hydrangeas.jpg', '0', 2);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `assign_orders_products`
--
ALTER TABLE `assign_orders_products`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `bncms_banned_ips`
--
ALTER TABLE `bncms_banned_ips`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `bncms_user`
--
ALTER TABLE `bncms_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indizes für die Tabelle `bncms_user_groups`
--
ALTER TABLE `bncms_user_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `conf_fields`
--
ALTER TABLE `conf_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `conf_relations`
--
ALTER TABLE `conf_relations`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `conf_relation_visibility`
--
ALTER TABLE `conf_relation_visibility`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `path` (`path`);

--
-- Indizes für die Tabelle `conf_tables`
--
ALTER TABLE `conf_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `assign_orders_products`
--
ALTER TABLE `assign_orders_products`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT für Tabelle `bncms_banned_ips`
--
ALTER TABLE `bncms_banned_ips`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `bncms_user`
--
ALTER TABLE `bncms_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `conf_fields`
--
ALTER TABLE `conf_fields`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT für Tabelle `conf_relations`
--
ALTER TABLE `conf_relations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT für Tabelle `conf_relation_visibility`
--
ALTER TABLE `conf_relation_visibility`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT für Tabelle `conf_tables`
--
ALTER TABLE `conf_tables`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
