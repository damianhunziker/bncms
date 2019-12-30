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
$q = $_SESSION[$_GET[f]][export][query];
$t = $_SESSION[$_GET[f]][export][table];
$f = $_SESSION[$_GET[f]][export][userFunction];
$m = $_SESSION[$_GET[f]][export][manualFieldProperties];
	
if ($_GET[t] == "xls")
	$s = "vnd-ms-excel";
if ($_GET[t] == "csv")
	$s = "CSV";
header("Content-type: application/$s");
header("Content-Disposition: attachment; filename=export." . t($_GET['t'])); 
$a = q($q);
$atp = getTableProperties($t,$m);
if ($_GET[t] == "xls") {
	echo "<table>";
	echo "<tr>";
}

foreach ($a[0] as $k2 => $v2) {
	if ((!$aRightsHidden[$t][$k2] and 
	@!$m[$atp[name]."-".$t][$k2][hidden]) or 
	@$m[$atp[name]."-".$t][$k2][visible]) {
		$fp = getFieldProperties($t, $k2, $m);
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
		if ((!$aRightsHidden[$t][$k2] and $k2 != "date" and
		@!$m[$atp[name]."-".$t][$k2][hidden]) or 
		@$m[$atp[name]."-".$t][$k2][visible]) {
			if ($_GET[t] == "xls")
				echo "<td>";

				$s = generateField(
			$t,
			$k2,
			$v2,
			$viewtype="view",
			"",
			"",
			"",
			"",
			$v,
			$f,
			""
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