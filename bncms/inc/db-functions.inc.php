<?php
//DB-Editor
//Datenbank-Funktionen
//copyright Damian Hunziker info@wide-design.ch

$q = "
CREATE TABLE IF NOT EXISTS `bncms_banned_ips` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `ip` CHAR(45) NOT NULL,
  UNIQUE KEY(`ip`)
)
ENGINE=INNODB CHARACTER SET UTF8 COLLATE UTF8_UNICODE_CI AUTO_INCREMENT = 1;
";

mysqli_query($DB,$q);

$q = "
CREATE TABLE IF NOT EXISTS `conf_tables` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL DEFAULT '',
  `columnNameOfId` VARCHAR(64) NOT NULL DEFAULT '',
  `lang` VARCHAR(50) NOT NULL DEFAULT '0',
  `insert` VARCHAR(10) NOT NULL DEFAULT '0', 
  `mysql_condition` VARCHAR(255) NOT NULL DEFAULT '0',
  `orderkey` DECIMAL(3,2) NOT NULL DEFAULT '0',
  `color` VARCHAR(10) NOT NULL DEFAULT '0',
  `users` TEXT NOT NULL,
  `editors` TEXT NOT NULL,
  `deletors` TEXT NOT NULL,
  `addors` TEXT NOT NULL,
  `is_assign_table` VARCHAR(5) NOT NULL DEFAULT '',
  `id_relation` INT(11) NOT NULL,
  `editable` VARCHAR(250) NOT NULL default '',
  `sort_order` VARCHAR(50) NOT NULL,
  `sort_order_ascdesc`  SET('','asc','desc') NOT NULL,
  `entries_per_page` INT(11) NOT NULL DEFAULT '10',
  `export_xls` SET('','on','off') NOT NULL,
  `export_csv` SET('','on','off') NOT NULL,
  `actualize` SET('','on','off') NOT NULL
)
ENGINE=INNODB CHARACTER SET UTF8 COLLATE UTF8_UNICODE_CI AUTO_INCREMENT = 1;
";

mysqli_query($DB,$q) or exit(mysqli_error($DB));
$q = "
CREATE TABLE IF NOT EXISTS `conf_fields` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `title` VARCHAR(250) NOT NULL,
  `type` VARCHAR(255) NOT NULL DEFAULT 'textfield',
  `mysql_order` DECIMAL(3,2) NOT NULL DEFAULT '0.00',
  `unchangeable` VARCHAR(250) DEFAULT NULL,
  `hidden` VARCHAR(250) DEFAULT NULL,
  `id_table` INT(11) NOT NULL,
  `mysqlType` VARCHAR(250) NOT NULL DEFAULT '0',
  `mysql_type_bez` VARCHAR(250) NOT NULL,
  `length_values` VARCHAR(250) NOT NULL,
  `nto1TargetField` VARCHAR(15) NOT NULL,
  `nto1TargetTable` VARCHAR(35) NOT NULL,
  `validation_required` SET('','on','off') NOT NULL,
  `validation_unique` SET('','on','off') NOT NULL,
  `validation_min_length` INT(11) NOT NULL,
  `nto1DisplayType` VARCHAR(20) NOT NULL,
  `nto1DropdownTitleField` VARCHAR(50) NOT NULL,
  `processing` VARCHAR(30) NOT NULL,
  `min_height` INT(11) NOT NULL,
  `min_width` INT(11) NOT NULL,
  `max_height` INT(11) NOT NULL,
  `max_width` INT(11) NOT NULL,
  FOREIGN KEY (`id_table`) REFERENCES conf_tables(`id`)
    ON DELETE CASCADE 
)
ENGINE=INNODB CHARACTER SET UTF8 COLLATE UTF8_UNICODE_CI AUTO_INCREMENT = 1;
";

mysqli_query($DB,$q) or exit(mysqli_error($DB));

$q = "
CREATE TABLE IF NOT EXISTS `conf_relations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `type` VARCHAR(10) NOT NULL DEFAULT '0',
  `table1` INT(11) NOT NULL,
  `table2` INT(11) NOT NULL,
  `ntomAssignFieldTable1` VARCHAR(255) NOT NULL DEFAULT '',
  `ntomAssignFieldTable2` VARCHAR(255) NOT NULL DEFAULT '',
  `seperateColumns` SET('','on','off') NOT NULL DEFAULT '',
  `users` TEXT NOT NULL,
  `editors` TEXT NOT NULL,
  `deletors` TEXT NOT NULL,
  `addors` TEXT NOT NULL,
  `ntomDisplayType` VARCHAR(20) NOT NULL,
  `ntomAjaxDisplayTitleField` VARCHAR(50) NOT NULL,
  `ntomAjaxDisplayMinSelections` INT(11) NOT NULL,
  `nto1TargetField` VARCHAR(100) NOT NULL,
  `nto1TargetTable` INT(11) NOT NULL,
  `nto1SourceField` VARCHAR(100) NOT NULL,
  `nto1SourceTable` INT(11) NOT NULL,
  FOREIGN KEY (`table1`) REFERENCES conf_tables(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`table2`) REFERENCES conf_tables(`id`)
    ON DELETE CASCADE
)
ENGINE=INNODB CHARACTER SET UTF8 COLLATE UTF8_UNICODE_CI AUTO_INCREMENT = 1;
";

mysqli_query($DB,$q) or exit(mysqli_error($DB));

$q = "
CREATE TABLE IF NOT EXISTS `conf_relation_visibility` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `path` VARCHAR(255) NOT NULL,
  `users` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(100) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `showWithEditIcons` SET('Separat','Normal','Beides') NOT NULL,
  UNIQUE KEY (`path`)
) ENGINE=INNODB CHARACTER SET UTF8 COLLATE UTF8_UNICODE_CI AUTO_INCREMENT = 1;
";

mysqli_query($DB,$q) or exit(mysqli_error($DB));

$q = "
CREATE TABLE IF NOT EXISTS `bncms_user_groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(30) NOT NULL,
  `permit_configuration` SET('', 'on', 'off') NOT NULL
) ENGINE=INNODB CHARACTER SET UTF8 COLLATE UTF8_UNICODE_CI AUTO_INCREMENT = 1;
";

mysqli_query($DB,$q) or exit(mysqli_error($DB));


$q = "
CREATE TABLE IF NOT EXISTS `bncms_user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(32) NOT NULL DEFAULT '',
  `password` VARCHAR(32) NOT NULL DEFAULT '',
  `notizen` TEXT NOT NULL,
  `gruppe` INT(11) NOT NULL,
  UNIQUE KEY (`username`),
  FOREIGN KEY (`gruppe`) REFERENCES bncms_user_groups(`id`) ON DELETE RESTRICT
) ENGINE=INNODB CHARACTER SET UTF8 COLLATE UTF8_UNICODE_CI AUTO_INCREMENT = 1;
";

mysqli_query($DB,$q) or exit(mysqli_error($DB));



$q = "SELECT * FROM bncms_user WHERE username = 'admin';";
$res =mysqli_query($DB,$q);

if (!mysqli_num_rows($res)) {
$q = "
INSERT INTO `conf_tables` (`id`, `name`, `columnNameOfId`, `lang`, `insert`, `mysql_condition`, `orderkey`, `color`, `users`, `editors`, `deletors`, `addors`, `is_assign_table`, `id_relation`, `editable`, `sort_order`, `sort_order_ascdesc`, `entries_per_page`, `export_xls`, `export_csv`, `actualize`) VALUES
(1, 'bncms_user', 'id', 'Benutzer', '0', '', '9.99', '', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', '', 0, '', '', '', 50, '', '', ''),
(2, 'bncms_user_groups', 'id', 'Benutzergruppen', '0', '', '9.99', '', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', '', 0, '', '', '', 0, '', '', '');
";

mysqli_query($DB,$q);
echo mysqli_error($DB);



$q = "INSERT INTO `bncms_user_groups` (`id`, `name`, `permit_configuration`) VALUES
(1, 'Administratoren', 'on'),
(2, 'Redakteure', 'off'),
(3, 'Frontend', 'off');
";

mysqli_query($DB,$q);
echo mysqli_error($DB);

$q = "
INSERT INTO `bncms_user` (`id`, `username`, `password`, `notizen`, `gruppe`) VALUES
  (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', 1),
  (2, 'redakteur', 'f1f5e247297b0133033cd5d34e057da6', '', 2),
  (3, 'frontend', 'aca33b9c046b2a50b8c3c54cc0380de8', '', 3);
";

mysqli_query($DB,$q);
echo mysqli_error($DB);

$q = "
INSERT INTO `conf_fields` (`id`, `name`, `title`, `type`, `mysql_order`, `unchangeable`, `hidden`, `id_table`, `mysqlType`, `mysql_type_bez`, `length_values`, `nto1TargetField`, `nto1TargetTable`, `validation_required`, `validation_unique`, `validation_min_length`, `nto1DisplayType`, `nto1DropdownTitleField`, `processing`, `min_height`, `min_width`, `max_height`, `max_width`) VALUES
	(1, 'id', '', 'textfield', 0.00, NULL, NULL, 1, 'int(11)', 'INT', '11', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
	(2, 'username', '', 'textfield', 0.00, NULL, NULL, 1, 'varchar(32)', 'VARCHAR', '32', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
	(3, 'password', '', 'password', 0.00, '0', '0', 1, 'varchar(32)', 'VARCHAR', '32', '', 'b', 'off', 'off', 0, 'radio', '', '', 0, 0, 0, 0),
	(4, 'notizen', '', 'textfield', 0.00, NULL, NULL, 1, 'text', 'TEXT', '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
	(5, 'gruppe', '', 'nto1', 0.00, '0', '0', 1, 'int(11)', 'INT', '11', '6', '2', 'off', 'off', 0, 'dropdown', 'name', '', 0, 0, 0, 0),
	(6, 'id', '', 'number', 0.00, '0', '0', 2, 'int(11)', 'INT', '11', '150', '1', 'off', 'off', 0, 'radio', 'orders_id', '', 0, 0, 0, 0),
	(7, 'name', '', 'textfield', 0.00, '0', '0', 2, 'varchar(30)', 'VARCHAR', '30', '1', '1', 'off', 'on', 0, 'radio', 'orders_id', '', 0, 0, 0, 0),
	(8, 'permit_configuration', 'Erlaube Konfiguration', 'checkbox', 0.00, '0', '0', 2, 'set(\'\',\'on\',\'off\')', 'SET', '\'\',\'on\',\'off\'', '1', '1', 'off', 'off', 0, 'radio', 'orders_id', '', 0, 0, 0, 0);
";

mysqli_query($DB,$q);
echo mysqli_error($DB);

}

function removeAssociativeKeys($a) {
	foreach ($a as $k => $v) {
		if (is_numeric($k)) {
			$r[$k] = $v;
		}
	}
	return $r;
}
function q($q,$l="",$r="") {
	return dbQuery($q,$l,$r);
}
function dbQuery ($query, $limit="", $removeAssociativeKeys="") {
	global $DB;
	$arrOut=array();
	if ($limit != "") {
		$query .= "LIMIT ".$limit;
		
	}
	$RS=mysqli_query($DB,$query);
	if (mysqli_error($DB)) {
		echo "<red>Fehler in Anfrage: ".$query."<br>".mysqli_error($DB)."<br></red>"; 
		exit();
	}
	if (@ mysqli_num_rows($RS) == 0) 
		return;
	if (@ mysqli_num_rows($RS) == 1) {
		$arrOut[0]=mysqli_fetch_array($RS, MYSQLI_ASSOC);
	}
	elseif (@ mysqli_num_rows($RS) > 1) {
	    while ($arrTempOut=mysqli_fetch_array($RS, MYSQLI_ASSOC)) {
			$arrOut[]=$arrTempOut;
		}
	} else {
	    $arrOut= @mysqli_fetch_array($RS, MYSQLI_ASSOC);
	}
	if (count($arrOut) == 1) {
		//inhalte wenn nur ein Eintrag nicht nur unter index 0 erreichbar machen sondern auch direkt �ber die assoziativen Bezeichnungen
		$oldArrOut = $arrOut[0];
		$arrOut = $arrOut[0];
		$arrOut[0] = $oldArrOut;
	} 
	if ($removeAssociativeKeys)
		$arrOut = removeAssociativeKeys($arrOut);
	return $arrOut;
}

function selectRec ($table, $condition, $limit = "", $order = "") {
	global $DB;
	$arrOut=array();
	if ($condition != "") {
		$condition = " WHERE $condition ";
	}
	if ($order != "") {
		$condition .= " ORDER BY ".$order;
	}
	if ($limit != "") {
		$condition .= " LIMIT ".$limit;
	}
	$query="SELECT * FROM $table $condition"; 
	$RS=mysqli_query($DB, $query);
	if (mysqli_error($DB)) {
		echo "<red>Fehler in Anfrage: ".$query."<br>".mysqli_error($DB)."<br></red>";
		exit();
	}
	if (mysqli_num_rows($RS) == 1) {
		$arrOut[0]=mysqli_fetch_array($RS, MYSQL_ASSOC);
	}
	if (mysqli_num_rows($RS) > 1) {
		while ($arrTempOut=mysqli_fetch_array($RS, MYSQL_ASSOC)) {
			array_push($arrOut, $arrTempOut);
		}
	}
	return $arrOut;
}
?>
