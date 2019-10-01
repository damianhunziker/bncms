<?php
	
//include("npc/npc.php");
include("findoffice.inc.php");
include("browseremulator.class.php");
include("getDBText.inc.php");
include('FPDF/mc_table.php');
include('pdf.functions.inc.php');

function getImgTag($sReportType, $zip, $arrUser) {
	if ($arrUser[type] == 'Agent' or $arrUser[type] == 'Mobil' or $arrUser[username] == 'jaeggim' or $arrUser[username] == 'hunzikerd') {
		$imgName=findOffice($zip);
	} else {
		$imgName=findOffice('8704');
	}
	if ($imgName != ""){
		if ($sReportType == "Offerte" or $sReportType == "Auftragsbestaetigung" ){
				$imgTag=$imgName;
			}	else {
				$imgTag="";						
			}	
	}
	return $imgTag;
}

//$oQuerys=new DBquery();
//echo $id;

$oRep = new cDBrec ( 'report', "id='$id'" );

//print_r($oRep);
$comment=$oRep->comment;
$ts_i=$oRep->ts_i;
$ts_u=$oRep->ts_u;
$user=$oRep->user;
$useWir=$oRep->useWir; 
$offerValidTo=$oRep->offerValidTo; 
$pagesMod = 0;

//Speichern des F&auml;lligkeitsdatums
if($oRep->type == "Rechnung"){
	if ($oRep->faelligkeitsdatum == "") {
		if ($ts_u != "" and $ts_u != "0" and $ts_u != "-1") {
			$strFaellig=$ts_u; 
		} else {
			$strFaellig=$ts_i; 
		}
		$strFaellig=mktime(date("H", $strFaellig),date("i", $strFaellig),date("s", $strFaellig),date("m", $strFaellig),date("d", $strFaellig)+$oRep->zahlungsFrist,date("y", $strFaellig));

		$query="UPDATE report SET faelligkeitsdatum = '$strFaellig' WHERE id = '$oRep->id'";
		$RS=$oQuerys->sDBQuery($query);
	} else {
		$strFaellig=$oRep->faelligkeitsdatum;
	}
}

$query="SELECT * FROM user WHERE username = '$user'";
$RS=$oQuerys->sDBQuery($query);
$arrUser=mysql_fetch_array($RS);

$oCli = new cDBrec ( 'address', "id='$oRep->client_id'" );

//print_r($oRep);


$plz=$oCli->zip;



if ($imgName != ""){
	if ($oRep->type == "Offerte" or $oRep->type == "Auftragsbestaetigung" ){
		$imgTag=$imgName;
	}	else {
	//echo $oRep->type;
		$imgTag="";						
	}
};
/*echo "<pre>";
print_r($oRep);
echo "</pre>";
*/
$query="SELECT * FROM report WHERE id='$id'";
$RS=$oQuerys->sDBquery($query);
$arrID=mysql_fetch_array($RS);
$offerteId=$arrID['offerteId'];

$query="SELECT id_termin, id_object FROM report WHERE id = '$id'";
$RS=$oQuerys->sDBquery($query);
$arrTermineId=mysql_fetch_array($RS);

$arrTermine=array();
$query="SELECT id_schedule FROM assignment_report_schedule WHERE id_report = '$id'";
$RS=$oQuerys->sDBquery($query);
while ($arrTermineTemp=mysql_fetch_array($RS)) {
	array_push($arrTermine, $arrTermineTemp[id_schedule]);
}

$arrObjects=array();
$query="SELECT id_object FROM assignment_report_object WHERE id_report = '$id'";
$RS=$oQuerys->sDBquery($query);
while ($arrObjectsTemp=mysql_fetch_array($RS)) {
	array_push($arrObjects, $arrObjectsTemp[id_object]);
}
if ($oRep->type != "Offerte" and $oRep->type != "Rechnung" and $oRep->type != "Mahnung1" and $oRep->type != "Mahnung2"and $oRep->type != "Mahnung3" ) {
	if (empty($arrTermine)){
		?>
		<script type="text/javascript">
			alert('Damit gedruckt werden kann, muss erst ein Termin ausgew&auml;hlt werden.');
				window.close();
		</script>
		<?php
	}
	if (empty($arrObjects)){
		?>
		<script type="text/javascript">
			alert('Damit gedruckt werden kann, muss erst ein Objekt ausgew&auml;hlt werden.');
				window.close();
		</script>
		<?php
	}							
}


$query="SELECT realtime FROM schedule WHERE id = '$arrTermine[id_schedule]'";
$RS=$oQuerys->sDBquery($query);
$arrTime=mysql_fetch_array($RS);

/*
$query="SELECT * FROM report WHERE id='$offerteId'";
$RS=$oQuerys->sDBquery($query);
$arrLink=mysql_fetch_array($RS);
$link=$arrLink['link'];

$query="SELECT * FROM report WHERE link = '$arrLink[link]'";
$RS=$oQuerys->sDBquery($query);
while ($arrProdukte=mysql_fetch_array($RS)) {
}
*/
$typ=$oRep->type;
/*<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!---->
<title><?php echo $oRep->type ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="STYLESHEET" type="text/css" media="print" href="kalk-ex.css">
<link rel="STYLESHEET" type="text/css" media="print" href="style.css">  
<link rel="STYLESHEET" type="text/css" media="screen" href="kalk-ex.css">
<link rel="STYLESHEET" type="text/css" media="screen" href="style.css"> 
</head>

<body leftmargin="15" topmargin="30" marginwidth="15" marginheight="30">*/
	if ($typ == "Auftragsbestaetigung") {
 	
	  $arrAlleObjekte=array();
	  if (is_array($arrTermine)) {
	  	//print_r($arrTermine);
		foreach($arrTermine as $key => $value) {
			$nichtSenden=0;
			$query="SELECT realtime FROM schedule WHERE id = '$value'";
			$RS=$oQuerys->sDBquery($query);
			$arrRealtime=mysql_fetch_array($RS);
		}
		$query="SELECT a.id_object, o.* 
		FROM assignment_report_object as a, object as o 
		WHERE a.id_report = '$oRep->id'
		AND a.id_object = o.id";
		$RS=$oQuerys->sDBquery($query, "", "0");
		while ($arrObjekte=mysql_fetch_array($RS)) {
			array_push($arrAlleObjekte, $arrObjekte);
		}
		//echo "<pre>";
		//print_r($arrAlleObjekte);
		//echo "</pre>";
		$senden=0;
		//print_r($arrAlleObjekte);
		foreach ($arrAlleObjekte as $key => $value) {
			//echo $oCli->zip." != ".$value[zip]." or ".$oCli->address." != ".$value[address]." or ".$oCli->city." != ".$value[city];
			if ($oCli->zip != $value[zip] or $oCli->address != $value[address] or $oCli->city != $value[city] or $oCli->lastname != $value[lastname]) {
				$plz=$value[zip];
				$imgName=findOffice($plz);
				
				if ($arrUser[type] == 'Agent' or $arrUser[type] == 'Mobil' or $arrUser[username] == 'jaeggim') {
					$imgName=findOffice($plz);
				} else {
					$imgName=findOffice('8704');
				}
				if ($imgName != ""){
					if ($typ == "Offerte" or $typ == "Auftragsbestaetigung" ){
							$imgTag=$imgName;
						}	else {
							$imgTag="";						
						}	
				};
				//echo "hallo";
				$oObj = new cDBrec('object'," id = '$value[id]' ");
				//echo "hallo";
				$printMieterDoc=1; 
			}
		}
		}//if is_array
	} 
	if ($oRep->switchAddress == "on") {
		$query="SELECT o.* 
		FROM assignment_report_object as a, object as o 
		WHERE a.id_report = '$oRep->id'
		AND a.id_object = o.id";
		$RS=$oQuerys->sDBquery($query);
		while ($arrObjekteA=mysql_fetch_array($RS)) {
			//print_r($arrObjekte);
			$oObj = new cDBrec('object', "id = '$arrObjekteA[id]'");
		}
		$oCli->addresses = $oObj->addresses;
		$oCli->lastname = $oObj->lastname;
		$oCli->firstname = $oObj->firstname;
		$oCli->zustaendig = $oObj->zustaendig;
		$oCli->address = $oObj->address;
		$oCli->city = $oObj->city;
		$oCli->zip = $oObj->zip;
	}
	$imgTag = getImgTag($typ, $oCli->zip, $arrUser);
	$plz=$oCli->zip;
	#echo $_GET[type];
	
	//if ($_GET['type'] == "swisskalk") {
		include("inc/texte/tab.swisskalk.kunde.inc.pdf.php");
	/*} else {
		include("inc/texte/tab.standart.kunde.inc.pdf.php");
		if ($oRep->broker_id != "0") {*/
			//$oBroker = new cDBrec('user', " id = '$oRep->broker_id' ");
			//$imgTag = getImgTag($typ, $oBroker->zip, $arrUser);
			//Anzahl Seiten, die von der Gesamtzahlseiten abgezogen werden muss.
			//$pagesMod=$pagesMod+1;
			//include("inc/texte/tab.broker_letter.inc.pdf.php");
		
	
		}
		/*if ($printMieterDoc == 1 and $oRep->switchAddress != "on") {
			$imgTag = getImgTag($typ, $oCli->zip, $arrUser);
			//Anzahl Seiten, die von der Gesamtzahlseiten abgezogen werden muss.
			$pagesMod=$pagesMod+1;
			include("inc/texte/tab.standart.objekt.inc.pdf.php"); 
		}*/
	}
//Output von fpdf 
	
$outputString='pdf/'.date('Y',time()).'.'.date('m',time()).'.'.date('d',time()).'_'.$rep_id.'.pdf';



$pdf->output($outputString);

$pdf->output();

$oHist            = new cDBrec ( 'history' );
$oHist->parent_id = $_SESSION[iCli];
$oHist->comment   = "$type ausgedruckt durch " . $_SESSION[sUserName] . ". <br><a href='$outputString' class='menuinline' target='_blank'>Zum Dokument wechseln</a>";
$oHist->insert();
 ?>