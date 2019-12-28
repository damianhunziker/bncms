<?php

/** @license bncms
 *
 * Copyright (c) Damian Hunziker and other bncms contributors
 * https://github.com/damianhunziker/bncms
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

function dbrec ($table, $condition) {
	global $DB;
	$arrOut=array();
	$query="SELECT * FROM $table WHERE $condition";
	$RS=mysqli_query($DB, $query);
	if (mysqli_num_rows($RS) == 1) {
		$arrOut=mysqli_fetch_array($RS);
	}
	if (mysqli_num_rows($RS) > 1) {
		while ($arrTempOut=mysqli_fetch_array($RS)) {
			array_push($arrOut, $arrTempOut);
		}
	}
	return $arrOut;
}
function recursiveGetCategoryTree($iThisCategoryId = 0) {
	global $arrCategoryTree;
	$arrThisLevel = selectRec ("category", "id_parent_category = '$iThisCategoryId' ORDER BY name DESC");
	//echo "<pre>ThisLevel id_parent_category = '$iThisCategoryId'";
	/*if ($arrThisLevel[id] != "") {
		$arrTemp[0] = $arrThisLevel;
		$arrThisLevel = $arrTemp;
	}//herausfinden ob mehrere Items da sind*/
	$arrCategoryTree[$iThisCategoryId] = $arrThisLevel;
	//	echo "</pre>";
	
	foreach ($arrThisLevel as $key => $value) {
		if ($value[id] != 0 and $value[id] != $iThisCategoryId ) {
			$aCountTest = selectRec ("category", "id_parent_category = '$value[id]' ORDER BY name DESC");
			//echo "<b>".count($aCountTest)."</b>";
			if (count($aCountTest) > 0) {
				recursiveGetCategoryTree($value['id'], $arrCategoryTree);
			}
		}	
	}
}
function recursiveDisplayCategoryTree($id_category="") {
	global $arrCategoryTree, $sOutput;
	//echo " id = '$id_category'";
	$aCategory = selectRec("category", "id = '$id_category'");
	$sOutput = "<div class=\"hr\"></div>".$sOutput;
	 //print_r($aCategory);
	//echo $aCategory[0][id_parent_category];
	foreach ($arrCategoryTree[$aCategory[0][id_parent_category]] as $key => $value) {
		if ($value[name] != "")
		$sOutput = "<a href=\"".$_SERVER['PHP_SELF']."?cat=$value[id]\" class=\"tab\">$value[name]</a> | ".$sOutput;
	}
	
	//$sOutput = "<div class=\"hr1\">recursive</div>".$sOutput;
	if ($aCategory[0][id_parent_category] != "0")
		recursiveDisplayCategoryTree($aCategory[0][id_parent_category]);
}
function displayActualCategory($id_category=0) {
	global $arrCategoryTree;
	$aCategory = selectRec("category", "id = '$id_category'");
	//$sOutput .= "<br />";
	if (is_array($arrCategoryTree[$id_category])) { 
		$sOutput .= "<div class=\"hr\"></div>";
		foreach ($arrCategoryTree[$id_category] as $key => $value) {
			if ($value[name] != "")
			$sOutput = "<a href=\"".$_SERVER['PHP_SELF']."?cat=$value[id]\" class=\"tab\">$value[name]</a> | ".$sOutput;
		}
		
	}
	return $sOutput;
} 
function displayFirstLevelCategory() {
	global $arrCategoryTree;
	//$sOutput .= "<br />";
	foreach ($arrCategoryTree[0] as $key => $value) {
		if ($value[name] != "")
		$sOutput = "<a href=\"".$_SERVER['PHP_SELF']."?cat=$value[id]\" class=\"tab\">$value[name]</a> | ".$sOutput;
	}
	$sOutput .= "<div class=\"hr\"></div>";
	return $sOutput;
}


?>