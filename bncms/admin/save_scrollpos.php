<?php
//session_start();
//error_reporting(E_WARNING);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

if ($iPosParameters = strpos($_GET['phpSelf'], "?")) {
	$_GET['phpSelf'] = substr($_GET['phpSelf'], 0, $iPosParameters);
}

$_SESSION['scrollPos'][$_GET['phpSelf']]['top'] = $_GET['scrollTop'];
$_SESSION['scrollPos'][$_GET['phpSelf']]['left'] = $_GET['scrollLeft'];
$aSessVisibleLayers = $_SESSION['aVisibleLayers'];
if ($_GET['an'] != "") {
	if (!is_array($aSessVisibleLayers[$_GET['phpSelf']])) 
		$aSessVisibleLayers[$_GET['phpSelf']] = array();
	array_push($aSessVisibleLayers[$_GET['phpSelf']], $_GET['an']);
	$aSessVisibleLayers[$_GET['phpSelf']]=array_unique($aSessVisibleLayers[$_GET['phpSelf']]);
}

if ($_GET['aus'] != "") {
	foreach ($aSessVisibleLayers[$_GET['phpSelf']] as $key => $value) {
		if ($value != $_GET['aus']) { 
			if (!is_array($aTempSessVisibleLayers)) 
				$aTempSessVisibleLayers = array();
			array_push($aTempSessVisibleLayers, $value);
		}
	}
	$aSessVisibleLayers[$_GET['phpSelf']] = $aTempSessVisibleLayers;
}
$_SESSION['aVisibleLayers'] = $aSessVisibleLayers;
?>