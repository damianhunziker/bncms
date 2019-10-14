<?php
include("start.inc.php");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style-admin.css" rel="stylesheet" type="text/css">
<link href="style-admin-<?php echo $_SESSION[style_color]?>.css" rel="stylesheet" type="text/css"> <script>RELATIVEPATH = '<?php echo RELATIVEPATHAJAX?>';</script>
<script type="text/javascript" src="../jquery.js"></script>
<script type="text/javascript" src="../lib/jquery-visible-master/jquery.visible.js"></script>
<script type="text/javascript" src="admin.inc.js"></script> 
<title>DB-Editor</title>
</head>
<body onLoad="waitPreloadPage();">
<?php include("loading.inc.php");?>
<div class="table_overall">
<?php
displayAssignRow($_GET['idName'], $_GET['idValue'], $_SESSION['assignTableName'], $_SESSION['sourceTableName'], $_GET['action']); 
?>
</div>
</body>
</html>