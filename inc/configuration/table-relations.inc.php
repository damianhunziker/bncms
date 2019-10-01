<?php
//alle n zu 1 Relationen in Relationen Tabelle schreiben, ist damit nicht alle Felder neu speichern muss
/*$q = "SELECT * FROM conf_fields";
$a = q($q);
foreach ($a as $k => $v) {
	$q = "SELECT * FROM conf_tables WHERE id = '$v[id_table]'";
	$b = q($q);
	//print_r($b);
	if (!count($b)) {
		$q = "DELETE FROM conf_fields WHERE id = '$v[id]'";
		q($q);
	}
}

$q = "SELECT * FROM conf_fields WHERE type = 'nto1'";
$a = q($q);
foreach ($a as $k => $v) {
	$q = "SELECT * FROM conf_relations WHERE type = 'nto1' AND nto1SourceField = '$v[name]' AND nto1SourceTable = '$v[id_table]'";
	$aR = q($q);
	$q = "SELECT * FROM conf_fields WHERE id = '$v[nto1TargetField]'";
	$aF = q($q);
	if (count($aR)) {
		$q = "UPDATE conf_relations SET type = 'nto1', nto1SourceField = '$v[name]', nto1SourceTable = '$v[id_table]', nto1TargetField = '$aF[name]', nto1TargetTable = '$v[nto1TargetTable]' WHERE id = '$aR[id]'";
		q($q);
	} else {
		$q = "INSERT INTO conf_relations SET type = 'nto1', nto1SourceField = '$v[name]', nto1SourceTable = '$v[id_table]', nto1TargetField = '$aF[name]', nto1TargetTable = '$v[nto1TargetTable]'";
		q($q);
	}
}*/
$query="SELECT * FROM conf_fields WHERE type = 'nto1'";
$aRelTemp=dbQuery($query,"",1);
if (is_array($aRelTemp))
foreach ($aRelTemp as $key => $value) {
	$query="SELECT * FROM conf_tables WHERE id ='$value[id_table]'";
	$aTableTemp=dbQuery($query,"",1);
	if (@$value['nto1TargetTable']) {
		$query="SELECT * FROM conf_tables WHERE id ='$value[nto1TargetTable]'";
		$aTargetTableTemp=dbQuery($query,"",1);
		if ($aTargetTableTemp != 0) {
			$aRel['NTo1'][$aTableTemp[0]['id']][$value['name']]=$aTargetTableTemp[0]['id'];
		}
	}
}
//n:m 
$query="SELECT * FROM conf_relations WHERE type = 'ntom'";
$aRelTemp=dbQuery($query);
if (is_array($aRelTemp)) {
	foreach ($aRelTemp as $key => $value) {
		if (is_array($value)) 
		if ($value['name']) {
			$query="SELECT * FROM conf_tables WHERE id ='$value[table1]'";
			$aTableTemp=dbQuery($query);
			$query="SELECT * FROM conf_tables WHERE id ='$value[table2]'";
			$aTargetTableTemp=dbQuery($query);
			$aRel['NToM'][$aTableTemp[0]['id']][]=array(
			'destTable' => $aTargetTableTemp[0]['id'], 
			'relationId' => $value['id'],
			'assignTable' => $value['name'],
			//'sourceFieldname' => 'id_'.$aTableTemp[0]['name'],
			//'destFieldname' => 'id_'.$aTargetTableTemp[0]['name'],
			'sourceFieldname' => $value['ntomAssignFieldTable1'],
			'destFieldname' => $value['ntomAssignFieldTable2'],
			'seperateColumns' => $value['seperateColumns'],
			'users' => $value['users'],
			'editors' => $value['editors'],
			'deletors' => $value['deletors'],
			'addors' => $value['addors'],
			'ntomDisplayType' => $value['ntomDisplayType'],
			'ntomAjaxDisplayTitleField' => $value['ntomAjaxDisplayTitleField'],
			'ntomAjaxDisplayMinSelections' => $value['ntomAjaxDisplayMinSelections']
			);
			//beide richtungen
			$aRel['NToM'][$aTargetTableTemp[0]['id']][]=array(
			'destTable' => $aTableTemp[0]['id'], 
			'relationId' => $value['id'],
			'assignTable' => $value['name'],
			//'sourceFieldname' => 'id_'.$aTableTemp[0]['name'],
			//'destFieldname' => 'id_'.$aTargetTableTemp[0]['name'],
			'sourceFieldname' => $value['ntomAssignFieldTable2'],
			'destFieldname' => $value['ntomAssignFieldTable1'],
			'seperateColumns' => $value['seperateColumns'],
			'users' => $value['users'],
			'editors' => $value['editors'],
			'deletors' => $value['deletors'],
			'addors' => $value['addors'],
			'ntomDisplayType' => $value['ntomDisplayType'],
			'ntomAjaxDisplayTitleField' => $value['ntomAjaxDisplayTitleField'],
			'ntomAjaxDisplayMinSelections' => $value['ntomAjaxDisplayMinSelections']
			);
		}
	}
}
?>