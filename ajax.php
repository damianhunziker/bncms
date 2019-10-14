<?php
include("inc/start.inc.php");

if ($_GET['ntomAjaxSearch']) {
	$q = "SELECT * FROM conf_relations WHERE id = '".e($_GET['ntomAjaxSearch'])."'";
	$a = dbQuery($q);
	$tt = getTableProperties($a[0]['table2']);
	$q = "SELECT ".$a[0]['ntomAjaxDisplayTitleField']." title FROM ".$tt['name']." WHERE ".$a[0]['ntomAjaxDisplayTitleField']." LIKE '%".e($_GET['value'])."%'  ORDER BY ".$a[0]['ntomAjaxDisplayTitleField']."";
	$a = dbQuery($q);
	$r = array();
	foreach ($a as $k => $v) 
		array_push($r,$v['title']);
	echo json_encode($r);
} elseif ($_GET['nto1AjaxSearch']) {
	$a = getRelationPropertiesById($_GET['nto1AjaxSearch'], $_SESSION['aManualFieldProperties']['displayTable'][$_GET['formid']]);
	$tt = getTableProperties($a['nto1TargetTable'], $_SESSION['aManualFieldProperties'][displayTable][$_GET['formid']]);
	$sf = getFieldProperties($a['nto1SourceTable'], $a['nto1SourceField'], $_SESSION['aManualFieldProperties']['displayTable'][$_GET['formid']]);
	$q = "SELECT ".$sf['nto1DropdownTitleField']." title FROM ".$tt['name']." WHERE ".$sf['nto1DropdownTitleField']." LIKE '%".e($_GET[value])."%'  ORDER BY ".$sf['nto1DropdownTitleField']."";
	$a = dbQuery($q);
	$r = array();
	foreach ($a as $k => $v) 
		@array_push($r,$v['title']);
	echo json_encode($r);
}elseif ($_GET['validate']) {
	$va = unserialize(urldecode($_POST['userValues']));
	foreach ($va as $k => $v) {
		
		$a = getTableProperties($_GET['validate'], $_SESSION['manualFieldProperties'][$_GET['formid']]);
		$tid = $a['id'];
		$a = getFieldProperties($a['id'], $k, $_SESSION['manualFieldProperties'][$_GET['formid']]);
		$tp = getTableProperties($a[id_table], $_SESSION['manualFieldProperties'][$_GET['formid']]);
		
		$tn = $tp['name'];
			$fp=getFieldProperties($a['id_table'], $k, $_SESSION['manualFieldProperties'][$_GET['formid']]);
			if ($fp['type'] == "password" or $fp['type'] == "file"  or $fp['type'] == "image") {
				$q = "SELECT * FROM $tn WHERE $tp[columnNameOfId] ='".$_SESSION['tempRights'][e($_GET['formid'])]['id']."'";
				$ti = dbQuery($q);
				if ($ti[0][$k] != "")
					$notRequired=1;
			}
		if ($fp['type'] == 'image') {
			$ff = strtolower(getFileFormat($v));
			if ($ff == "gif" or $ff == "jpg" or $ff == "png" or $ff == "jpeg" or $v == "") {
				
			} else {
				$er[$k] .= "Bitte jpg, gif oder png verwenden.";
			}
		}
		if (strpos("a".$k, "ntom_") == 1) {
			$ak = explode("_", $k);
			$q = "SELECT * FROM conf_relations WHERE id = '$ak[1]'";
			$aRP = dbQuery($q);
			$aT = getTableProperties($aRP[0]['table2'],$_SESSION['manualFieldProperties'][$_GET['formid']]);
			$v = str_replace(", ",",",$v);
			$av = explode(",", $v);
			$av = array_unique($av);
			foreach ($av as $k2 => $v2) {
				if ($v2) {
					$q = "SELECT * FROM ".$aT['name']." WHERE ".$aRP[0]['ntomAjaxDisplayTitleField']." LIKE '$v2'";
					$aNT = dbQuery($q);
					if  (count($aNT)) {
						$c++;
					}
				}
			}
			if ($c < $aRP[0]['ntomAjaxDisplayMinSelections'] and $aRP[0]['ntomAjaxDisplayMinSelections'] > 0) {
				if ($aRP[0]['ntomAjaxDisplayMinSelections'] == 1)
					$sE = "Eintrag";
				else
					$sE = "Eintr&auml;ge";
				$er[$k] .= "Bitte mindestens ".$aRP[0]['ntomAjaxDisplayMinSelections']." $sE ausw&auml;hlen. ";
			}
		}
		if ($a['validation_required'] == "on" and $v == "" and $notRequired != 1) {
			if ($notRequired != 1)
				$er[$k] .= "Feld wird ben&ouml;tigt. "; 
		}
		
		$val_min_length = $a['validation_min_length'];
		if ($val_min_length > strlen($v) and $notRequired != 1) {
			$er[$k] .= "Bitte mindestens ".$val_min_length." Zeichen verwenden. "; 
		}
		if ($a['validation_unique'] == "on") {
			$q = "SELECT * FROM $_GET['validate'] WHERE $k = '$v'";
			$r = mysqli_query($DB, $q);
			if (mysqli_num_rows($r) > 0)
				$er[$k] .= "Wert ist bereits vorhanden. "; 
		}
		if (is_numeric($a['length_values']) and strlen($v) > $a['length_values']) {
			$q = "SELECT * FROM ".e($_GET[validate])." WHERE $k = '$v'";
			$r = mysqli_query($DB, $q);
			if (mysqli_num_rows($r) > 0)
			$er[$k] .= "Max. ".$a['length_values']." Zeichen ";
		}
		if (strlen($v) > 100000) { 
			$er[$k] .= "Max. 100 000 Zeichen ";
		}
	}
	echo urlencode(serialize($er));
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
	echo "rv";
	echo $rv;
}
?>