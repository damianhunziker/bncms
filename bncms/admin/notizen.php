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
if ($_POST[notizen]) {
	 $q = "UPDATE bncms_user SET notizen = '$_POST[notizen]' WHERE username='$_SESSION[user]'";
	mysqli_query($DB, $q);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style-admin.css" rel="stylesheet" type="text/css" />
<link href="style-admin-<?php echo $_SESSION[style_color]?>.css" rel="stylesheet" type="text/css" />
<script>RELATIVEPATH = '<?php echo RELATIVEPATHAJAX?>';</script>
<script type="text/javascript" src="../jquery.js"></script>
<script type="text/javascript" src="../lib/jquery-visible-master/jquery.visible.js"></script>
<script type="text/javascript" src="admin.inc.js"></script>
<script type="text/javascript">
function an(div)
{
	document.getElementById(div).style.display = '';
	document.getElementById('minus'+div).style.display = '';
	document.getElementById('plus'+div).style.display = 'none';
}

function aus(div)
{
	document.getElementById(div).style.display = 'none';
	document.getElementById('minus'+div).style.display = 'none';
	document.getElementById('plus'+div).style.display = '';
}
</script>
<script language="javascript">
var wstat
var ns4up = (document.layers) ? 1 : 0
var ie4up = (document.all) ? 1 : 0
var xsize = screen.width
var ysize = screen.height
var breite=xsize
var hoehe=ysize
var xpos=(xsize-breite)
var ypos=(ysize-hoehe)
function opwin(url, name) {
	wstat=window.open(url,name,"scrollbars=yes,status=no,toolbar=no,location=no,directories=no,resizable=yes,menubar=no,width="+breite+",height="+hoehe+",screenX="+xpos+",screenY="+ypos+",top="+ypos+",left="+xpos)
	wstat.focus();
}
</script>
<title>Verschiedenes</title>
</head>
<body onload="waitPreloadPage();">
<?php include("loading.inc.php");?>
<div>
<h1>Verschiedenes</h1>
<?php
include("admin_nav.inc.php");
?>
<a href='../lib/phpwebstat/index.php?parameter=finished' target="_blank">Besucher-Statistik</a><br /><br />
<form action='' method="post">
<?php
 $q = "SELECT * FROM bncms_user WHERE username = '$_SESSION[user]'";
$a = mysqli_fetch_array(mysqli_query($DB, $q));?>
<textarea style="width:80%; height:400px" name="notizen"><?php echo $a[notizen]?></textarea><br />
<br /><input type="submit" />
</form>
</div>
<?php include ("../inc/layer_visibility.inc.php"); ?>
</body>
</html>