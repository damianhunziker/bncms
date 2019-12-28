<?php

/** @license bncms
 *
 * Copyright (c) Damian Hunziker and other bncms contributors
 * https://github.com/damianhunziker/bncms
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

include("start.inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style-admin.css" rel="stylesheet" type="text/css" />
<link href="style-admin-<?php echo $_SESSION['style_color']?>.css" rel="stylesheet" type="text/css" />
<script>RELATIVEPATH = '<?php echo RELATIVEPATHAJAX?>';</script>
<script type="text/javascript" src="../jquery.js"></script>
<script type="text/javascript" src="../lib/jquery-visible-master/jquery.visible.js"></script>
<script type="text/javascript" src="admin.inc.js"></script>
<script type="text/javascript">
</script>
<link rel="stylesheet" href="../lib/jquery-ui/jquery-ui.min.css" />
<script src="../lib/jquery-ui/jquery-ui.min.js"></script>
<style>
.ui-autocomplete-loading {
	padding-right: 10px;
	background: url('../image/loading.gif') right center no-repeat;
	background-size: 20px 20px;
	background-origin: content-box;
}
</style>
<title>Edit Fields</title>
</head>
<body onload="waitPreloadPage();">

<?php include("loading.inc.php");?>
<div>
<h1>Konfiguration</h1>
<?php
include("admin_nav.inc.php");
if (strpos($_SERVER['QUERY_STRING'], "etStructure=true") != "") 
	$sClassActive = "activeLink";
else 
	$sClassActive = "";
?>
<a href="edit_fields.php?createTable=true" class="<?php echo $sClassActive ?>" title="Neue Tabelle"><img src="<?php echo RELATIVEPATH; ?>/image/icons/add-page-<?php echo $_SESSION['icon_color'] ?>.gif" /></a>&nbsp;
<a href="edit_fields.php?getStructure=true" class="<?php echo $sClassActive ?>" title="Struktur runterladen. Verwenden um die Test-Struktur zur&uuml;ckzusetzen zur Live-Struktur. Nur mySQL Eigenschaften betroffen."><img src="<?php echo RELATIVEPATH; ?>/image/icons/structure-save-<?php echo $_SESSION['icon_color'] ?>.gif" /></a>&nbsp;
<?php 
if (strpos($_SERVER['QUERY_STRING'], "riteStructure=true") != "") 
	$sClassActive = "activeLink";
else 
	$sClassActive = "";
?>
<a href="edit_fields.php?writeStructure=true" class="<?php echo $sClassActive ?>" title="Struktur hochladen. Verwenden um die Test-Struktur ins Live System einzutragen."><img src="<?php echo RELATIVEPATH; ?>/image/icons/structure-upload-<?php echo $_SESSION['icon_color'] ?>.gif" /></a>&nbsp;
<a href="edit_fields.php?relationVisibility=true" class="<?php echo $sClassActive ?>" title="Relationen-Sichtbarkeit. Konfiguration der Sichtbarkeit der Relationen"><img src="<?php echo RELATIVEPATH; ?>/image/icons/structure-visibility.gif" /></a>&nbsp;
<br /><br />

<?php
if(@$_GET['removeTable']) {
	removeTable();
	echo displayFields(); 
} elseif (@$_GET['duplicateTable']) {
	duplicateTable();
	echo displayFields();
} elseif (@$_POST['table_name']) {
	saveTable();
	echo displayFields();
} elseif (@$_GET['createTable'] or @$_GET['editTable']) {
	editTable();
} elseif(@$_GET['delete']) {
	deleteField();
} elseif (@$_GET['getStructure']) {
	getDatabaseStructure();
}elseif (@$_GET['writeStructure']) {
	writeDatabaseStructure();
} elseif (@$_GET['sendQuerystack']) {
	sendDatabaseStructureStack();
} elseif (@$_GET['confirmed']) {
	deleteField();
} elseif (@$_POST['field_id']) {
	saveField();
	if (!$_POST[closeafter])
		editField();
	else
		echo "<script>window.close()</script>";
} elseif (@$_GET['saveRelation']) {
	saveRelation();
	editRelation();
} elseif (@$_GET['deleteRelation']) {
	deleteRelation();
} elseif (@$_GET['relation']) {
	editRelation();
} elseif (@$_GET['id']) {
	editField();
} elseif (@$_GET['relationVisibility']) {
	editRelationVisibility();
} else {
	echo displayFields(); 
}
?>
</div>
<?php include ("../inc/layer_visibility.inc.php"); ?>
</body>
</html>