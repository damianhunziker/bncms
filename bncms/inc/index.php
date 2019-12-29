<?php

/** @license bncms
 *
 * Copyright (c) Damian Hunziker and other bncms contributors
 * https://github.com/damianhunziker/bncms
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

//DB-Editor
//Startseite

include("start.inc.php");

if (@$_POST['savePost'] == "on") {
	foreach ($_POST as $key => $value) {
		if ($key != 'columnNameOfId' and $key != 'table' and $key != 'id' and $key != 'savePost') {
			if ($aProp[$_POST[table]][$key] == "password")
				$value = md5($value);
			//Test ob feld existiert
            //muss prepared statement machen
			$query="SELECT $key FROM ".e($_POST['table'])." WHERE ".e($_POST['columnNameOfId'])."='".e($_POST['id'])."'";
			$aTest=dbQuery($query);
			if (is_array($aTest)) {
				if (@!in_array($key,$aRightsUnchangeable[$_POST['table']])) {
					$query="UPDATE ".e($_POST['table'])."
					SET ".e($key)." = '".e($value)."'
					WHERE ".e($_POST['columnNameOfId'])." = '".e($_POST['id'])."'";
					dbQuery($query);
				}
			} else  {
				echo "<br>Feldname $key nicht gefunden in Tabelle ".t($_POST['table']);
			}
			
		}
	}
}
if (isset($_GET['display']) ) {
	if ($_GET['display'] != "") {
		$_SESSION['display'] = $_GET['display'];
	}
}

if (empty($_SESSION['display'])) {
	$_SESSION['display'] = "shops";
}

displayTable('article', 'article_id');
displayTable('language', 'language_id');
displayTable('customer', 'customer_id');
displayTable('shops', 'shop_id');
displayTable('shop_order', 'order_id');
displayTable('shop_order_items', 'order_item_id');


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>DB-Editor</title>
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>

</body>
</html>
