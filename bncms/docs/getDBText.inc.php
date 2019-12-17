<?php
function getDBText($art) {
	global $oQuerys;
	$query="SELECT * FROM kalkex_lang WHERE seite = '$art'";
	$RS=$oQuerys->sDBquery($query);
	$arr=array();
	while ($arrTempText=mysql_fetch_array($RS)) {
		//$arrTempText[lang]=nl2br($arrTempText[lang]); entfernt fuer fpdf
		$arrTempText[lang]=str_replace("[","<",$arrTempText[lang]);
		$arrTempText[lang]=str_replace("]",">",$arrTempText[lang]);
		$arrTempText[lang]=str_replace("&#039;","'",$arrTempText[lang]);
		array_push($arr,$arrTempText);
	}
	return $arr;
}
?>