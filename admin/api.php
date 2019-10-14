<?php
//copyright Damian Hunziker info@wide-design.ch

include("start.inc.php");
function rankTableGroup(){
}
function searchTableGroup($table){
	$aDestTables = getDestFromSourceTableNTo1($table);
	$aSourceTables = getSourceFromDestTableNTo1($table);
	if (is_array($aDestTables ))
		foreach ($aDestTables as $k => $v)
			$aGroup[]=$v;
	if (is_array($aSourceTables ))
		foreach ($aSourceTables as $k => $v)
			$aGroup[]=$v;
	return $aGroup;
}

if ($_GET[conf] == "tables") {
	echo json_encode($aTable);
}
if ($_GET[conf] == "relations") {
	echo json_encode($aRel);
} 
if ($_GET[conf] == "properties") {
	echo json_encode($aProp);
}
if ($_GET[conf] == "rightsHidden") {
	echo json_encode($aRightsHidden);
}
if ($_GET[conf] == "rightsUnchangeable") {
	echo json_encode($aRightsUnchangeable);
}
if ($_GET[conf] == "all") {
	foreach ($aTable as $k => $v) {
		$q = "SELECT * FROM $v[name]";
		$r = mysqli_query($DB, $q);
		$aTable[$k]['count'] = mysqli_num_rows($r);
		$countRels=0;
		while ($a = mysqli_fetch_array($r)) {
			$id = $a[$v[columnNameOfId]];
			$aTable[$k][content][$id][text] = "text";	
			foreach ($a as $k2 => $v2) 
				if ($aRel[NTo1][$v[name]][$k2] and $v2 != 0 and $v2 != "" ) {
					$aTable[$k][content][$id][rel][table] = $aRel[NTo1][$v[name]][$k2];
					$aTable[$k][content][$id][rel][value]  = $v2;
					$ti = getTableArrayNoFromName($aRel[NTo1][$v[name]][$k2]);
				 	$aTable[$ti]['rels_in']++;  
					$aTable[$k]['rels_out']++;
				}
		}
	}
	
	foreach ($aTable as $k => $v) {
		if ($v['count'] > $maxCount) {
			$maxCountTable = $k;
			$maxCount = $v['count'];
		}
		if ($v['rels_out'] > $maxRelsOut) {
			$maxRelsOutTable = $k;
			$maxRelsOut = $v['rels_out'];
		}
		if ($v['rels_in'] > $maxRelsIn) {
			$maxRelsInTable = $k;
			$maxRelsIn = $v['rels_in'];
		} 
	}
	if ($maxCountTable == $maxRelsInTable or $maxCountTable == $maxRelsOutTable)
		$centerTable = $maxCountTable;
	elseif ($maxRelsInTable == $maxRelsOutTable)
		$centerTable = $maxRelsInTable;
	else 
		$centerTable = $maxCountTable;
	$aTable[$centerTable][rank] = 1;
	$tablesRanked = 1;
	$rankedTables[$centerTable]=1;
	
	$aGroup=searchTableGroup($centerTable);
	if (is_array($aGroup))
	foreach ($aGroup as $k => $v) {
		$tablesRanked++;
		$ti = getTableArrayNoFromName($v);
		$aTable[$ti][rank] = $tablesRanked;
		$rankedTables[$v]=1;
	}
	
	$a = array();
	$a[tables]=$aTable;
	$a[properties]=$aProp;
	$a[relations]=$aRel;
	$a[rightsHidden]=$aRightsHidden;
	$a[rightsUnchangeable]=$aRightsUnchangeable;
	
	if ($_GET[pre])
		echo "<pre>".print_r($a,true)."</pre>";   
	else
		echo json_encode($a);
}
?>