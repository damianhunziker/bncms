<?php
$aSessVisibleLayers = @$_SESSION['aVisibleLayers'];
if (!is_array($aSessVisibleLayers)) 
	$aSessVisibleLayers = array();
$_SESSION['aVisibleLayers'] = $aSessVisibleLayers;

if (@is_array($aSessVisibleLayers[($_SERVER['PHP_SELF'])])) {
	foreach ($aSessVisibleLayers[($_SERVER['PHP_SELF'])] as $key => $value) {
		if (!strpos($value, "_relations_"))
			echo "
<script type='text/javascript'>
anschalten('$value');
</script>";
	}
}
if (@is_array($_SESSION['scrollPos'][$_SERVER['PHP_SELF']])) {
?>
<script type="text/javascript">
	window.scrollTo(<?php echo $_SESSION['scrollPos'][$_SERVER['PHP_SELF']]['left'].",".$_SESSION['scrollPos'][$_SERVER['PHP_SELF']]['top'] ?>);
</script>
<?php
}
echo '<div style="position:absolute; top:0; left:0; width:100%; background-color:white;">';
?>
<div id="admin_header_right"><img src="../image/logo-small-<?php echo $_SESSION['style_color']; ?>.jpg" style="margin-left:15px;width:80px;"></div><?php   
if ($_SERVER['QUERY_STRING'] != "") 
	$sQuerySeparator = "&";
else 
	$sQuerySeparator = "";
echo "<div id=\"admin_header_left\"> ";
$sQueryStringOut = preg_replace('/([&]{0,1}style_color=[purplegreenblue]+)/',"",$_SERVER['QUERY_STRING']);
echo date("d.m.Y").", hallo " . $_SESSION['user'] . "&nbsp;&nbsp;&nbsp;";
echo "</div></div>";
?>