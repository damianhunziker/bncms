<?php

$q = "SELECT gruppe FROM bncms_user WHERE username = '".e($_SESSION['user'])."'";
$a = dbQuery($q);
$q = "SELECT * FROM bncms_user_groups WHERE id = '$a[gruppe]'";
$aUserGroup = q($q);
$sOverlay = "<div style=\"background-color:red; display: table; position:absolute; height:30px; width:30px; margin:0px; padding:0px;\" OnMouseOut=\"this.style.backgroundImage=''\" onMouseOver=\"this.style.backgroundImage='url(../image/animated/slide.gif)'\"></div>";

//Daten
if ($_SERVER['PHP_SELF'] == RELATIVEPATH."/admin/index.php") { 
	$sClassActive = "mantleNavElementActive";
} else { 
	$sClassActive = "mantleNavElementInactive";}
?><a href="index.php" onClick="javascript:jQuery('#prepage').fadeIn(); ajax_send_scrollpos('<?php echo $_SERVER['PHP_SELF'] ?>');" class="<?php echo $sClassActive?>" title="Daten" onMouseOut="this.style.backgroundImage=''" onMouseOver="this.style.backgroundImage='url(../image/animated/slide.gif)'"> <img src="<?php echo RELATIVEPATH; ?>/image/icons/search-<?php echo $_SESSION['icon_color']; ?>.gif"></a><?php

//Struktur
if ($aUserGroup['permit_configuration'] == "on") {
	if ($_SERVER['PHP_SELF'] == RELATIVEPATH."/admin/edit_fields.php") 
		$sClassActive = "mantleNavElementActive";
	else 
		$sClassActive = "mantleNavElementInactive";
	?><a href="edit_fields.php" onClick="javascript:jQuery('#prepage').fadeIn(); ajax_send_scrollpos('<?php echo $_SERVER['PHP_SELF'] ?>');" class="<?php echo $sClassActive?>" title="Konfiguration" onMouseOut="this.style.backgroundImage=''" onMouseOver="this.style.backgroundImage='url(../image/animated/slide.gif)'"><img src="<?php echo RELATIVEPATH; ?>/image/icons/sitemap-<?php echo $_SESSION['icon_color']; ?>.gif"></a><?php

    //Backup
	if ($_SERVER['PHP_SELF'] == RELATIVEPATH."/admin/backup.php") 
		$sClassActive = "mantleNavElementActive";
	else 
		$sClassActive = "mantleNavElementInactive";
	?><a href="backup.php?action=load" onClick="javascript:jQuery('#prepage').fadeIn(); ajax_send_scrollpos('<?php echo $_SERVER['PHP_SELF'] ?>');" class="<?php echo $sClassActive?>" title="Datenbank Backups verwalten"onMouseOut="this.style.backgroundImage=''" onMouseOver="this.style.backgroundImage='url(../image/animated/slide.gif)'"><img src="<?php echo RELATIVEPATH; ?>/image/icons/download-page-<?php echo $_SESSION['icon_color']; ?>.gif"></a><?php
}

//Logout
if ($_SERVER['PHP_SELF'] == RELATIVEPATH."/admin/logout.php") 
	$sClassActive = "mantleNavElementActive";
else 
	$sClassActive = "mantleNavElementInactive";
?><a href="?logout=1" onClick="javascript:jQuery('#prepage').fadeIn(); ajax_send_scrollpos('<?php echo $_SERVER['PHP_SELF'] ?>');" class="<?php echo $sClassActive?>" title="Ausloggen"onMouseOut="this.style.backgroundImage=''" onMouseOver="this.style.backgroundImage='url(../image/animated/slide.gif)'"><img src="<?php echo RELATIVEPATH; ?>/image/icons/checkout-<?php echo $_SESSION['icon_color']; ?>.gif"></a><div id="mantleNav1" class="mantleNavOverall">