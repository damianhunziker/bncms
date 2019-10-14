<?php
//copyright Damian Hunziker info@wide-design.ch
include("start.inc.php");

//fix scrollpos und layervisibility
if (!strpos($_SERVER[REQUEST_URI], "index.php")) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

include("../inc/save.inc.php");

if (isset($_GET['display'])) {
	if ($_GET['display'] != "") {
		$_SESSION['display'] = $_GET['display'];
	}
}
if (empty($_SESSION['display'])) {
	$_SESSION['display'] = "shops";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>DB-Editor</title>
<script>RELATIVEPATH = '<?php echo RELATIVEPATHAJAX?>';</script>
<script type="text/javascript" src="../jquery.js"></script>
<script type="text/javascript" src="admin.inc.js"></script>
<script type="text/javascript" src="scroller/mootools.svn.js"></script>
<script type="text/javascript" src="../lib/jquery-visible-master/jquery.visible.js"></script>
<script type="text/javascript">
		window.addEvent('domready', function(){
			var scroll2 = new Scroller('container', {area: 30, velocity: 2});
			
			// container
			jQuery('container').addEvent('mouseover', scroll2.start.bind(scroll2));
			jQuery('container').addEvent('mouseout', scroll2.stop.bind(scroll2));
			
		}); 
	</script>
<script type="text/javascript">

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
<link rel="stylesheet" href="../lib/jquery-ui/jquery-ui.min.css">
<script src="../lib/jquery-ui/jquery-ui.min.js"></script>
<style>
.ui-autocomplete-loading {
	padding-right: 10px;
	background: url('../image/loading.gif') right center no-repeat;
	background-size: 20px 20px;
	background-origin: content-box;
}
</style>
<link href="style-admin.css" rel="stylesheet" type="text/css">
<link href="style-admin-<?php echo $_SESSION[style_color]?>.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="waitPreloadPage();">
<?php include("loading.inc.php");?>
<div>
<h1>Administrationsbereich <?php echo $_SERVER['HTTP_HOST'] ?> <!--<a href=\"index.php?logout=true\">Logout</a>--></h1>
<?php
include("admin_nav.inc.php");
$_SESSION['sWorkType']="view";
if (is_array($aTable))
foreach ($aTable as $key => $value) {
    if (checkPermission($value['users'], $_SESSION['user'], $_SESSION['userGroup'])) {
		echo displayVisibilityButtons($value['lang'], $value['id'], $value['name']." ".$value['id']);
		echo "<div class=\"table_overall\" id='".$value['id']."' style='display:none;'>";
			displayTable(
			$value['id'], 
			$value['columnNameOfId'],
			$value['mysql_condition']);
		echo "</div>";
		$z = 1;
	} 
	
}
if($z != 1)
	echo "Dem Benutzer sind keine Tabellen zugewiesen."; 
?>
</div>
</div>
<?php include ("../inc/layer_visibility.inc.php"); ?>
</body>
</html>