<?php

include("start.inc.php");

if ($_GET['saveTitleIcon']) {
	$q = "SELECT * FROM conf_relation_visibility WHERE path = '".e($_GET['path'])."'";
	$aRV = q($q);
	if ($_FILES) {
		foreach ($_FILES as $k => $v) {
			if ($v['name'] != "") {
				$filename = $v['name']; 
				$filename = preg_replace('/[^a-z0-9A-Z.\-_]/', "", $filename);
				$addFilename = "";
				$fn = "../file/relation-icons/".$addFilename.$filename;
				while (file_exists($fn)) {
					$addFilename = rand(0,1000000);
					$fn = "../file/relation-icons/".$addFilename.$filename;
				}
				if (!move_uploaded_file($v['tmp_name'], $fn)) {
				} 
			}
		}
	} else 
		$fn = $aRV['icon'];
	if ($_GET['deleteIcon'] == "on") {
		@unlink($aRV['icon']);
		$fn = "";
	}
	echo $fn;
	$q = "UPDATE conf_relation_visibility SET title = '".e($_GET['title'])."', icon = '$fn', showWithEditIcons = '".e($_GET['showWithEditIcons'])."' WHERE path = '".e($_GET['path'])."'";
	q($q);
}  elseif ($_GET['showPossibleRelations']) {
	//$aUsers = explode(",",urldecode($_GET[users]));
	if ($_GET['users'] == "null")
		$aUsers = "";
	if (strpos($_GET['path'],"-")) {
		if (!is_array($aUsers)) {
			echo $q = "DELETE FROM conf_relation_visibility WHERE path = '".e($_GET[path])."'";
			q($q);
			exit();
		} else {
			$q = "SELECT id FROM conf_relation_visibility WHERE path = '".e($_GET[path])."'";
			$a = q($q);
			if (count($a)) {
				echo $q = "UPDATE conf_relation_visibility SET users = '".serialize($aUsers)."' WHERE path = '".e($_GET['path'])."'";
			} else {
				echo $q = "INSERT INTO conf_relation_visibility SET users = '".serialize($aUsers)."', path = '".e($_GET['path']."'";
			}
		}
		q($q);
	} else {
		echo $q = "UPDATE conf_tables SET users = '".serialize($aUsers)."' WHERE id='".e($_GET['showPossibleRelations'])."'";
		q($q);
        echo $q = "UPDATE conf_relation_visibility SET users = '".serialize($aUsers)."' WHERE path = '".e($_GET['showPossibleRelations'])."-'";
        q($q);
		if (!is_array($aUsers)) {
			exit();
		}
	}
	if (!$_GET['isStartEntry'])
		echo showNextLayer($_GET['path'], $_GET['md5'], urlencode(serialize($aUsers)), $_GET['showPossibleRelations']);
	else
		echo showPossibleRelations($_GET['showPossibleRelations'], urlencode(serialize($aUsers)), $_GET['path']);
} elseif ($_GET['ntomAjaxSearch']) {
	$q = "SELECT * FROM conf_relations WHERE id = '".e($_GET['ntomAjaxSearch'])."'";
	$a = dbQuery($q);
	$tt = getTableProperties($a[0]['table2']);
	$q = "SELECT ".$a[0]['ntomAjaxDisplayTitleField']." title FROM ".$tt['name']." WHERE ".$a[0]['ntomAjaxDisplayTitleField']." LIKE '".e($_GET['value'])."%'";
	$a = dbQuery($q);
	$r = array();
	foreach ($a as $k => $v) 
		@array_push($r,$v['title']);
	echo json_encode($r);
} elseif ($_GET['nto1AjaxSearch']) {
	$q = "SELECT * FROM conf_relations WHERE id = '".e($_GET['nto1AjaxSearch'])."'";
	$a = dbQuery($q);
	$tt = getTableProperties($a['nto1TargetTable']);
	$sf = getFieldProperties($a['nto1SourceTable'],$a['nto1SourceField']);
	$q = "SELECT ".$sf['nto1DropdownTitleField']." title FROM ".$tt['name']." WHERE ".$sf['nto1DropdownTitleField']." LIKE '%".e($_GET[value])."%'  ORDER BY ".$sf['nto1DropdownTitleField']."";
	$a = dbQuery($q);
	$r = array();
	foreach ($a as $k => $v) 
		array_push($r,$v['title']);
	echo json_encode($r);
} else {
	if ($_POST['func'] == "dt")
		$f = "displayTable";
	$p = array();
	$t = unserialize(urldecode($_POST['param']));
	$p[0] = "e";
	$p[1] = "e";
	$p[2] = "";
	$p[3] = "";
	$p[4] = "";
	$p[5] = "";
	$p[6] = "";
	$p[7] = "";
	$p[8] = "";
	$p[9] = "";
	$p[10] = "";
	$p[11] = $t['id'];
	
	if ($t['sp'])
	{
		$p[12] = $t['sp'];
	}
	
	$rv = call_user_func_array($f , $p); 
	echo $rv;
}
?>