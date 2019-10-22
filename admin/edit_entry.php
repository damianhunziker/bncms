<?php
//copyright Damian Hunziker info@wide-design.ch
include("start.inc.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>DB-Editor</title>
<link href="style-admin.css" rel="stylesheet" type="text/css">
<link href="style-admin-<?php echo $_SESSION[style_color]?>.css" rel="stylesheet" type="text/css">
<script>RELATIVEPATH = '<?php echo RELATIVEPATHAJAX?>';</script>
 <script type="text/javascript" src="../jquery.js"></script>
 <script type="text/javascript" src="../lib/jquery-visible-master/jquery.visible.js"></script>
  <script type="text/javascript" src="admin.inc.js"></script>
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
          terms.pop();
          terms.push( ui.item.value );
          terms.push( "" );
          this.value = terms.join( ", " );
          return false;
        },
		search: function() {
          var term = extractLast( this.value );
          if ( term.length < 1 ) {
            return false;
          }
        },
		focus: function() {
          return false;
        },
      source: function( request, response ) {
        var term = extractLast(request.term);
        if ( term in cache ) {
          response( cache[ term ] );
          return;
        }
		var ntomid = this.element[0].id.replace('ntom_','');
        jQuery.getJSON( RELATIVEPATH+"/ajax.php?ntomAjaxSearch="+ntomid+"&value="+term, {
            term: extractLast( request.term )
          }, function( data, status, xhr ) {
          cache[ term ] = data;
          response( data );
        });
      }
    });
  });</script>
<link rel="stylesheet" href="../lib/jquery-ui/jquery-ui.min.css">
<script src="../lib/jquery-ui/jquery-ui.min.js"></script>
<style>.ui-autocomplete-loading {padding-right:10px; background: url('<? echo RELATIVEPATH; ?>/image/loading.gif') right center no-repeat;background-size:20px 20px; background-origin: content-box;}</style>

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
<script type="text/javascript" src="vlaCalendar/jslib/mootools-1.2-core.js"></script>
<script type="text/javascript" src="vlaCalendar/jslib/vlaCal-v2.1.js"></script>
<link type="text/css" media="screen" href="vlaCalendar/styles/vlaCal-v2.1.css" rel="stylesheet" />
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
<title>Edit Entry</title>
</head>

<BODY onLoad="waitPreloadPage();">

<?php include("loading.inc.php");?>
</div><h1>Datensatz bearbeiten </h1>
<?php
/*if ($_GET['duplicate'] == true) {
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
}*/

$_SESSION[sWorkType]="edit";
displayRow($_GET['id'], $_GET['columnNameOfId'], $_GET['table']); 
?>
<?php include ("../inc/layer_visibility.inc.php"); ?>
</body>
</html>