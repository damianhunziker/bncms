<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

if ($webuser)
{
	$s = $webuser;
}
else
{
	$s = $_SESSION['user'];
}

if ($_SESSION['userGroup'])
{
    $sQueryAdd =  " or hidden LIKE '%". $_SESSION['userGroup'] . "%'";
}

$query="SELECT * FROM conf_fields WHERE hidden LIKE '%$s%' $sQueryAdd";
$aRightsHiddenTemp = dbQuery($query,"",1);

if (is_array($aRightsHiddenTemp)) {
	foreach ($aRightsHiddenTemp as $key => $value ) {
		$query="SELECT * FROM conf_tables WHERE id = '" . $value['id_table'] . "'";
		$aTable=dbQuery($query);
		$aRightsHidden[$aTable[0]['id']][$value['name']] = 1;
	}
}

if ($_SESSION['userGroup'])
{
    $sQueryAdd =  " or unchangeable LIKE '%$_SESSION[userGroup]%'";
}

$query="SELECT * FROM conf_fields WHERE unchangeable LIKE '%$s%' $sQueryAdd";
$aRightsUnchangeableTemp = dbQuery($query,"",1);
if (is_array($aRightsUnchangeableTemp)) {
	foreach ($aRightsUnchangeableTemp as $key => $value ) {
		$query="SELECT * FROM conf_tables WHERE id = '$value[id_table]'";
		$aTable=dbQuery($query);
		$aRightsUnchangeable[$aTable[0]['id']][$value['name']] = 1;
	}
}

error_reporting(E_ALL & ~E_NOTICE && ~E_WARNING);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>