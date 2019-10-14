<?php
//copyright by Damian Hunziker, Brand New Design";
define(PATH,$_SERVER['DOCUMENT_ROOT']."/bncms");
define(RELATIVEPATH,"/bncms");
define(RELATIVEPATHAJAX,"/bncms");
define(RELATIVEPATHAPP,"/");
define(DOMAIN,"http://localhost"); 
$webuser = "webuser";

if ($_GET[projectpath] != "") {
	include($_SERVER['DOCUMENT_ROOT'].$_GET[projectpath]."/project_config.php");
} else {
	include($_SERVER['DOCUMENT_ROOT']."/project_config.php");
}

include (PATH."/inc/configuration/database-settings.inc.php");
$DB = @mysqli_connect($aDatabase['host'],$aDatabase['user'],$aDatabase['password']);
@mysqli_select_db($DB, $aDatabase['dbname']);

if (!$DB)
{
	exit("Datenbank Login Fehler");
}

mysqli_query($DB, "SET NAMES 'utf8'");
include (PATH."/inc/db-functions.inc.php");
include (PATH."/inc/functions.inc.php");

include (PATH."/inc/editor-functions.inc.php");
include (PATH."/inc/display-functions.inc.php");

session_set_cookie_params(24*60*60, RELATIVEPATHAPP);
session_start();
include (PATH."/inc/configuration/table-rights.inc.php");
include (PATH."/inc/configuration/table-relations.inc.php");
include (PATH."/inc/configuration/table-properties.inc.php");


if ($_SESSION[errorMsg]) {
	$outErrormsg = "Fehlermeldung: $_SESSION[errorMsg]";
	$_SESSION[errorMsg] = "";
}

$query="
SELECT * FROM text as a, site as b 
WHERE b.url = '".str_replace("/","",$_SERVER['PHP_SELF'])."'
AND b.id = a.id_site
";
$aText = dbQuery($query);
if (is_array($aText)) {
	foreach ($aText as $key => $value) {
		if ($value[place] == "header") {
			$sHeader = $value[html];
		}
		if ($value[place] == "footer") {
			$sFooter = $value[html];
		}
	}
}

if (!$_SESSION[style_color])
	$_SESSION[style_color] = "green";
	
include(PATH."/inc/save.inc.php");

if ($_GET['action'] == "edit" or $_GET['action'] == "new") {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>DB-Editor</title>
<link href="s.css" rel="stylesheet" type="text/css">

 <script type="text/javascript" src="bncms/jquery.js"></script>
 <script type="text/javascript" src="bncms/lib/jquery-visible-master/jquery.visible.js"></script>
  <script type="text/javascript" src="bncms/frontend.inc.js"></script>
  <script>RELATIVEPATH = '<?php echo RELATIVEPATHAJAX?>';</script>
<script>
$(function() {
	function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }
    var cache = {};
    jQuery( ".ntom_autocomplete" ).bind( "keydown", function( event ) {
        if ( event.keyCode === jQuery.ui.keyCode.TAB &&
            jQuery( this ).autocomplete( "instance" ).menu.active ) {
          event.preventDefault();
        }
      }).autocomplete({
      minLength: 1,
	    select: function( event, ui ) {
          var terms = split( this.value );
          // remove the current input
          terms.pop();
          // add the selected item
          terms.push( ui.item.value );
          // add placeholder to get the comma-and-space at the end
          terms.push( "" );
          this.value = terms.join( ", " );
          return false;
        },
		search: function() {
          // custom minLength
          var term = extractLast( this.value );
          if ( term.length < 1 ) {
            return false;
          }
        },
		focus: function() {
          // prevent value inserted on focus
          return false;
        },
      source: function( request, response ) {
        var term = extractLast(request.term);
        if ( term in cache ) {
          response( cache[ term ] );
          return;
        }
		var ntomid = this.element[0].id.replace('ntom_','');
		//alert(RELATIVEPATH+"/ajax.php?ntomAjaxSearch="+ntomid+"&value="+term);
        jQuery.getJSON( RELATIVEPATH+"/ajax.php?ntomAjaxSearch="+ntomid+"&value="+term, {
            term: extractLast( request.term )
          }, function( data, status, xhr ) {
          cache[ term ] = data;
          response( data );
        });
      }
    });
  });</script>
  <link href="bncms/frontend.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="bncms/lib/jquery-ui/jquery-ui.min.css">
<script src="bncms/lib/jquery-ui/jquery-ui.min.js"></script>
<style>.ui-autocomplete-loading {padding-right:10px; background: url('<? echo RELATIVEPATH; ?>/image/loading.gif') right center no-repeat;background-size:20px 20px; background-origin: content-box;}</style>
<!--<script type="text/javascript" src="vlaCalendar/jslib/mootools-1.2-core.js"></script>
<script type="text/javascript" src="vlaCalendar/jslib/vlaCal-v2.1.js"></script>
<link type="text/css" media="screen" href="vlaCalendar/styles/vlaCal-v2.1.css" rel="stylesheet" />
-->
<title>Edit Entry</title>
</head>

<BODY onLoad="waitPreloadPage();">
</DIV><h1>Datensatz bearbeiten </h1>
<?php
if ($_GET['duplicate'] == true) {
	$query="SELECT * FROM $_GET[table] WHERE id = '$_GET[id]'";
	$aDestId=dbQuery($query);
	$query="SELECT id FROM conf_tables WHERE name = '$_GET[table]'";
	$aTableId=dbQuery($query);
	$query="SELECT * FROM conf_fields WHERE id_table = '".$aTableId[0][id]."' and type='image'";
	$aImageFields = dbQuery($query);
	$query="INSERT INTO $_GET[table] SET ";
	foreach ($aDestId[0] as $key => $value) {
		//Abfrage f&uuml;r Bild Duplizierung
		foreach ($aImageFields as $keyField => $valueField) {
				if ($valueField[name] == $key) {
					//todo formate, bildpfad
					$sNewFileName=str_replace(".jpg","",$value).rand(0,100).".jpg";
					while (file_exists("../".$sNewFileName)) {
						$sNewFileName=str_replace(".jpg","",$value).rand(0,100).".jpg";
					}
					@copy("../".$value, "../".$sNewFileName);
					$value = $sNewFileName;
			}
		}
		if ($key != "id" and $value != "") {
			$query.=" $key = '$value', ";
		}
		
	}
	$query=preg_replace('/(, )$/im','',$query);
	dbQuery($query);
	echo "<div>Der Eintrag wurde dupliziert unter ".mysqli_insert_id($DB)."</div>";
	$_GET['id'] = mysqli_insert_id($DB);
}
$_SESSION[sWorkType]="edit";
$a = $_SESSION[aManualFieldProperties][displayTable][$_GET[ajaxExec]];
//Überschreiben Form spezifische Variablen
foreach ($a[$_GET['table']]["bncms_edit_form"]["fields"] as $k => $v) {
	foreach ($v as $k2 => $v2)
		$a[$_GET['table']]["fields"][$k][$k2] = $v2;
}
foreach ($a[$_GET['table']]["bncms_edit_form"]["table"] as $k => $v) {
	foreach ($v as $k2 => $v2)
		$a[$_GET['table']]["table"][$k][$k2] = $v2;
}
displayRow(
	$_GET['id'], 
	$_GET['columnNameOfId'],
	$_GET['table'],
	"",
	"",
	$a
); 
?>
</body>
</html>
<?
exit();
}	
?>
