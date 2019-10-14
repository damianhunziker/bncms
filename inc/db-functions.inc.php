<?php
//DB-Editor
//Datenbank-Funktionen
//copyright Damian Hunziker info@wide-design.ch

//install
//$q = "DROP TABLE text";
//mysqli_query($DB,$q);

$q = "CREATE TABLE IF NOT EXISTS `bncms_banned_ips` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `ip` varchar(50) NOT NULL default '',
    PRIMARY KEY  (`id`)
)";
mysqli_query($DB,$q);

$q = "CREATE TABLE IF NOT EXISTS `conf_tables` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `name` varchar(50) NOT NULL default '',
    `columnNameOfId` varchar(64) NOT NULL default '',
	`lang` varchar(50) NOT NULL default '0',
	`insert` varchar(10) NOT NULL default '0', 
	`mysql_condition` varchar(255) NOT NULL default '0',
	`orderkey` decimal(3,2) NOT NULL default '0',
	`color` varchar(10) NOT NULL default '0',
	`users` text NOT NULL default '',
	`editors` text NOT NULL default '',
	`deletors` text NOT NULL default '',
	`addors` text NOT NULL default '',
	`is_assign_table` varchar(5) NOT NULL default '',
	`id_relation` int(10) unsigned NOT NULL,
	`editable` varchar(250) NOT NULL default '',
	`sort_order` varchar(50) NOT NULL,
	`sort_order_ascdesc`  set('','asc','desc') NOT NULL,
    `entries_per_page` int(5) NOT NULL DEFAULT '10',
	`export_xls` SET('','on','off') NOT NULL,
	`export_csv` SET('','on','off') NOT NULL,
	`actualize` SET('','on','off') NOT NULL,
    PRIMARY KEY  (`id`)
);
";
mysqli_query($DB,$q) or exit(mysqli_error($DB));
$q = "CREATE TABLE IF NOT EXISTS `conf_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `max_width` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ";
mysqli_query($DB,$q) or exit(mysqli_error($DB));
$q = "CREATE TABLE IF NOT EXISTS `conf_relations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '0',
  `table1` int(5) NOT NULL DEFAULT '0',
  `table2` int(5) NOT NULL DEFAULT '0',
  `ntomAssignFieldTable1` varchar(255) NOT NULL,
  `ntomAssignFieldTable2` varchar(255) NOT NULL,
  `seperateColumns` set('','on','off') NOT NULL,
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
  `nto1SourceTable` int(5) NOT NULL,
  PRIMARY KEY (`id`)

)";
mysqli_query($DB,$q) or exit(mysqli_error($DB));
$q = "CREATE TABLE IF NOT EXISTS  `conf_relation_visibility` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `users` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `showWithEditIcons` set('Separat','Normal','Beides') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
mysqli_query($DB,$q) or exit(mysqli_error($DB));
$q = "CREATE TABLE IF NOT EXISTS `bncms_user` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `username` varchar(50) NOT NULL default '',
	`password` varchar(50) NOT NULL default '',
	`notizen` text NOT NULL default '',
	`gruppe` int(5) NOT NULL DEFAULT '0', 
    PRIMARY KEY  (`id`),
    UNIQUE KEY (`username`) 
)";
mysqli_query($DB,$q) or exit(mysqli_error($DB));

$q = "CREATE TABLE IF NOT EXISTS `bncms_user_groups` (
`id` int(20) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `permit_configuration` set('','on','off') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
mysqli_query($DB,$q) or exit(mysqli_error($DB));

$q = "SELECT * FROM bncms_user WHERE username = 'admin' ";
$res =mysqli_query($DB,$q);

if (!mysqli_num_rows($res)) {

    $q = "

INSERT INTO `conf_tables` (`id`, `name`, `columnNameOfId`, `lang`, `insert`, `mysql_condition`, `orderkey`, `color`, `users`, `editors`, `deletors`, `addors`, `is_assign_table`, `id_relation`, `editable`, `sort_order`, `sort_order_ascdesc`, `entries_per_page`, `export_xls`, `export_csv`, `actualize`) VALUES
(1, 'bncms_user', 'id', 'Benutzer', '0', '', '9.99', '', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', '', 0, '', '', '', 50, '', '', ''),
(2, 'bncms_user_groups', 'id', 'Benutzergruppen', '0', '', '9.99', '', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', 'a:1:{i:0;s:15:\"Administratoren\";}', '', 0, '', '', '', 0, '', '', '');";
    mysqli_query($DB,$q);
    echo mysqli_error($DB);

    $q = "INSERT INTO `bncms_user` (`id`, `username`, `password`, `notizen`, `gruppe`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', 1),
(2, 'webuser', '21232f297a57a5a743894a0e4a801fc3', '', ''),
(3, 'redakteur', 'f1f5e247297b0133033cd5d34e057da6', '', 2);";
mysqli_query($DB,$q);
    echo mysqli_error($DB);

$q = "INSERT INTO `bncms_user_groups` (`id`, `name`, `permit_configuration`) VALUES
(1, 'Administratoren', 'on'),
(2, 'Redakteure', 'off');";
mysqli_query($DB,$q);
    echo mysqli_error($DB);

$q = "
INSERT INTO `conf_fields` (`id`, `name`, `title`, `type`, `mysql_order`, `unchangeable`, `hidden`, `id_table`, `mysqlType`, `mysql_type_bez`, `length_values`, `nto1TargetField`, `nto1TargetTable`, `validation_required`, `validation_unique`, `validation_min_length`, `nto1DisplayType`, `nto1DropdownTitleField`, `processing`, `min_height`, `min_width`, `max_height`, `max_width`) VALUES
(1, 'id', '', 'textfield', 0.00, NULL, NULL, 1, 'int(10) unsigned', 'INT', '10', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(2, 'username', '', 'textfield', 0.00, NULL, NULL, 1, 'varchar(50)', 'VARCHAR', '50', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(3, 'password', '', 'password', 0.00, '0', '0', 1, 'varchar(50)', 'VARCHAR', '50', '', 'b', 'off', 'off', 0, 'radio', '', '', 0, 0, 0, 0),
(4, 'notizen', '', 'textfield', 0.00, NULL, NULL, 1, 'text', 'TEXT', '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0),
(5, 'gruppe', '', 'nto1', '0.00', '0', '0', 1, 'int(20)', 'INT', '5', '6', '2', 'off', 'off', 0, 'dropdown', 'name', '', 0, 0, 0, 0),
(6, 'id', '', 'number', '0.00', '0', '0', 2, 'int(20)', '', '', '150', '1', 'off', 'off', 0, 'radio', 'orders_id', '', 0, 0, 0, 0),
(7, 'name', '', 'textfield', '0.00', '0', '0', 2, 'varchar(250)', 'VARCHAR', '30', '1', '1', 'off', 'on', 0, 'radio', 'orders_id', '', 0, 0, 0, 0),
(8, 'permit_configuration', 'Erlaube Konfiguration', 'checkbox', '0.00', '0', '0', 2, 'set(\'\', \'on\', \'off\')', '', '', '1', '1', 'off', 'off', 0, 'radio', 'orders_id', '', 0, 0, 0, 0);
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
