<?php
include("inc/start.inc.php");

$q = $_SESSION[$_GET[f]][export][query];
$t = $_SESSION[$_GET[f]][export][table];
$f = $_SESSION[$_GET[f]][export][userFunction];
$m = $_SESSION[$_GET[f]][export][manualFieldProperties];
	
if ($_GET[t] == "xls")
	$s = "vnd-ms-excel";
if ($_GET[t] == "csv")
	$s = "CSV";
header("Content-type: application/$s");
header("Content-Disposition: attachment; filename=export.$_GET[t]"); 
$a = q($q);
$atp = getTableProperties($t);
if ($_GET[t] == "xls") {
	echo "<table>";
	echo "<tr>";
}
foreach ($a[0] as $k2 => $v2) {
	if ((!$aRightsHidden[$t][$k2] and 
	@!$m[$atp[name]."-".$t][$k2][hidden]) or 
	@$m[$atp[name]."-".$t][$k2][visible]) {
		$fp = getFieldProperties($t, $k2);
		
		if ($_GET[t] == "xls")
			echo "<td>$fp[title]</td>";
		if ($_GET[t] == "csv")
			echo "$fp[title];";
	}
}
if ($_GET[t] == "xls")
	echo "</tr>";
if ($_GET[t] == "csv")
	echo "
";
foreach ($a as $k => $v) {
if ($_GET[t] == "xls")
		echo "<tr>";
	foreach ($v as $k2 => $v2) {
		if ((!$aRightsHidden[$t][$k2] and 
		@!$m[$atp[name]."-".$t][$k2][hidden]) or 
		@$m[$atp[name]."-".$t][$k2][visible]) {
			if ($_GET[t] == "xls")
				echo "<td>";
			
				$s = generateField(
			$t, //1
			$k2, //2 
			$v2, //3 
			$viewtype="view", //4 
			"", //5 
			"", //6 
			"", //7 
			"", //8 
			$v, //9 
			$f, //10
			"" //11
			);
			echo $s;
			if ($_GET[t] == "xls")
				echo "</td>";
			if ($_GET[t] == "csv")
				echo ";";
		}
	}
	if ($_GET[t] == "xls")
		echo "</tr>";
	if ($_GET[t] == "csv")
		echo "
";
}
if ($_GET[t] == "xls")
	echo "</table>";
?>