<?php
error_reporting(E_WARNING);

include ("../inc/configuration/backend-config.inc.php");

date_default_timezone_set("UTC");

include (PATH."/inc/configuration/database-settings.inc.php");
$DB = mysqli_connect($aDatabase['host'],$aDatabase['user'],$aDatabase['password'],$aDatabase['dbname']);

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

include (PATH."/inc/db-functions.inc.php");
include (PATH."/inc/functions.inc.php");
include (PATH."/inc/editor-functions.inc.php");
include (PATH."/inc/display-functions.inc.php");
session_set_cookie_params(30*24*60*60, RELATIVEPATHAJAX);
session_start();

dbQuery("SET NAMES utf8");

include (PATH."/inc/configuration/table-rights.inc.php");
include (PATH."/inc/configuration/table-relations.inc.php");
include (PATH."/inc/configuration/table-properties.inc.php");
//checkBannedIPs();

if (file_exists(PATH."/admin/project_config.php")) {
	include (PATH."/admin/project_config.php");
}

if ($_POST['username'] and $_POST['password'] and !@$_POST['savePost'] and $_POST['username'] != "webuser") {
    $query="SELECT bncms_user.*,  bncms_user_groups.name FROM bncms_user, bncms_user_groups WHERE bncms_user.gruppe = bncms_user_groups.id AND BINARY bncms_user.username = '".e($_POST['username'])."' and BINARY bncms_user.password = '".md5($_POST['password'])."' and (bncms_user_groups.name = 'Administratoren' or bncms_user_groups.name  = 'Redakteure')";

    $arr = dbQuery($query);
    $_SESSION['user'] = $_POST['username'];
    $_SESSION['userGroup'] = $arr["name"];
    if (is_array($arr)) {
        $_SESSION['user_allowed'] = 1;
    }
    echo "<script>window.location.href='index.php';</script>";
    exit();
}
if (@$_GET['logout'] == true) {
	$_SESSION = "";
	$_SESSION['user_allowed'] = 0;	
}
if ($_SESSION['user_allowed'] != 1) {
?>
<script>jQuery('body').html("");</script>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>DB-Editor</title><script>RELATIVEPATH = '<?php echo RELATIVEPATHAJAX?>';</script>
<script type="text/javascript" src="../jquery.js"></script>
<script type="text/javascript" src="../lib/jquery-visible-master/jquery.visible.js"></script>
<script type="text/javascript" src="admin.inc.js"></script>
<script type="text/javascript" src="scroller/mootools.svn.js"></script>
<script type="text/javascript">
		window.addEvent('domready', function(){
			var scroll2 = new Scroller('container', {area: 30, velocity: 2});
			$('container').addEvent('mouseover', scroll2.start.bind(scroll2));
			$('container').addEvent('mouseout', scroll2.stop.bind(scroll2));
			
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
alert(name);
	wstat=window.open(url,name,"scrollbars=yes,status=no,toolbar=no,location=no,directories=no,resizable=yes,menubar=no,width="+breite+",height="+hoehe+",screenX="+xpos+",screenY="+ypos+",top="+ypos+",left="+xpos)
	wstat.focus();
}
</script>
<link href="style-admin.css" rel="stylesheet" type="text/css">
<link href="style-admin-<?php echo $_SESSION[style_color]?>.css" rel="stylesheet" type="text/css">
</head>

<body onLoad="waitPreloadPage();">
<style>
.container {
  display: table;
  height: 100%;
  position: absolute;
  overflow: hidden;
  width: 100%;background-color:white;top:0; left:0}
.helper {
  display: table-cell;
  vertical-align: middle;}
.content {
  margin:0 auto;
  width:492px;
-moz-transform:rotate(<?php echo rand(1,10) ?>deg) translate3d(0px,0px,1px); /* Firefox 3.6 Firefox 4 */
-webkit-transform:rotate(<?php echo rand(1,10) ?>deg) translate3d(0px,0px,1px); /* Safari */
-o-transform:rotate(<?php echo rand(1,10) ?>deg) translate3d(0px,0px,1px); /* Opera */
-ms-transform:rotate(<?php echo rand(1,10) ?>deg) translate3d(0px,0px,1px); /* IE9 */
transform:rotate(<?php echo rand(1,10) ?>deg) translate3d(0px,0px,1px); /* W3C */   
  }
.logo {
border:1px solid #566642;
background:#FFFFFF;
padding:10px;
border-radius:25px;
width:100px;
margin-left:80px;
margin-top:-30px;
margin-bottom:20px;
-moz-transform:rotate(-<?php echo rand(10,20) ?>deg translate3d(0px,0px,1px); /* Firefox 3.6 Firefox 4 */
-webkit-transform:rotate(-<?php echo rand(10,20) ?>deg translate3d(0px,0px,1px); /* Safari */
-o-transform:rotate(-<?php echo rand(10,20) ?>deg translate3d(0px,0px,1px); /* Opera */
-ms-transform:rotate(-<?php echo rand(10,20) ?>deg translate3d(0px,0px,1px); /* IE9 */
transform:rotate(-<?php echo rand(10,20) ?>deg translate3d(0px,0px,1px); /* W3C */ 
}
</style>
<div class="container">
  <div class="helper">
    <div class="content"><div style=''><!--stuff-->
<?php	echo "<img src='../image/login.png' style='opacity:0.8'>"; 
	echo "<div style='position:absolute; margin-top:-304px;margin-left:115px; text-align:center'><br /><br />";
	echo "<form action=\"index.php\" method=\"post\">";
	echo "<table style='margin:1px; padding:1px; background-color:none'><tr><td colspan='2'><div class=logo><img src=\"../image/logo-small-green.jpg\"></div></td></tr><tr><td><input type=\"text\" name=\"username\" value=\"\"></td><td> Benutzername</td></tr>";
	echo "<tr><td><input type=\"password\" name=\"password\" value=\"\"></td><td> Passwort</td></tr>";
	echo "<tr><td colspan='2'> <input type=\"submit\" value='Login' class=\"crmforms_submit submit\"></td></tr>
	</table>";
	echo "</form></div></div></div>";
	?>
	</div>
  </div>
</div>
</body></html>
	<?php
	die();
} else {
	
    if (isset($_GET['style_color']))
    {
        $_SESSION['style_color'] = $_GET['style_color'];
    }
    else
    {

         $_SESSION['style_color'] = "green";
    }

    if (isset($_SESSION['errorMsg'])) {
        $outErrormsg = "Fehlermeldung: $_SESSION[errorMsg]";
        $_SESSION['errorMsg'] = "";
    }
}
?>