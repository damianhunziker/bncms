<?php
$query="SELECT * FROM conf_tables ORDER BY orderkey";
$aTable=dbQuery($query,'',1);
if (is_array($aTable))
foreach ($aTable as $key => $value) {
	$query="SELECT * FROM conf_fields WHERE id_table = '$value[id]' ";
	$aFields = dbQuery($query);
	if (is_array($aFields)) {
		foreach ($aFields as $keyField => $valueField) {
			if (is_numeric($keyField))
			$aProp[$value['id']][$valueField['name']]=$valueField['type'];
		}
	}
}
//Überschreiben mit manuellen Tabelleneingeschaften
if (is_array($aManualFieldProperties))
foreach ($aManualFieldProperties as $k => $v) {
	foreach ($v as $k2 => $v2) {
		foreach ($v2 as $k3 => $v3) {
			$aTN = explode("-",$k);
			if ($v3 == "type")
				$aProp[$aTN[1]][$k2]= $v3;
		}
	}
}
?>