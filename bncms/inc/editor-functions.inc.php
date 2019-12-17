<?php
//DB-Editor
//Editor-Funktionen
//copyright Damian Hunziker info@wide-design.ch

function checkPermission($aPermission, $sUser, $sUserGroup="") {
    if (inSerializedArray($sUser, $aPermission) or inSerializedArray($sUserGroup, $aPermission))
        return true;
    else
        return false;
}
function checkBannedIPs() {
	$q = "SELECT * FROM bncms_banned_ips WHERE ip = '" . $_SERVER['REMOTE_ADDR'] . "'";
	$a = q($q,"",1);
	if (count($a))
		exit();
}
function removeAssociativeKeyFromArray($a,$key) {
	foreach ($a as $k => $v) {
		if ($k != $key)
			$na[$k] = $v;
	}
	return $na;
}

function c($v) {
	//comingfrom darf keine _ enthalten
	return str_replace("_","",$v);
}
//frontend functions
//Sicherheit
function h($v) {
	return createGetToken($v);
}
function createGetToken($v) {
	//eindeutige ID für diese Ausführung
	$b = openssl_random_pseudo_bytes(5);
	$link_id = bin2hex($b);

	$ta = array();
	//herauslöschen der ältestens tokens wenn mehr als 200
	if ($_SESSION[jobs_user_id])
	if (count($_SESSION[allowed_get_tokens] > 200)) {
		foreach($_SESSION[allowed_get_tokens] as $k => $va) {
			if ($k > count($_SESSION[allowed_get_tokens]) - 199)
				$ta[] = $va;
		}
	}
	$_SESSION[allowed_get_tokens] = $ta;
	if (!in_array($link_id, $_SESSION[allowed_get_tokens])) {
		$_SESSION[allowed_get_tokens][] = $link_id;
	}
	if (strpos("a".$v,"?") != "")
		$s= "&";
	else
		$s= "?";
	//vor anker einfügen
	if (strpos($v,"#")) {
		$hinterAnker = explode("#",$v);
		$v = $hinterAnker[0];
		$hi = "#".$hinterAnker[1];
	}
	$v = preg_replace('/([\?|&]gtkn=[0-9abcdef]+)/',"",$v);
	return $v.$s."gtkn=".$link_id.$hi;
}
function checkGetToken() {
	//Wenn gtkn nicht gesetzt ist aber abgefragtt wird heisst das, dass es eine Anfrage ausführt bei welcher das token schon entfert wurde, dh. schon ausgeführt wurde. Muss daher nicht noch einmal ausgeführt werden und auch kein hacklog eintrag machen weil, nachdem der Code ausgeführt wurde die Seite neu aufgerufen wird mit entferntem token. Muss aber deswegen bei allen gtkn vorgängen am Ende die Seite neu aufrufen mit gelöschtem token. Browser Historie einträge werden nur gemacht ohne token.
	if (!$_GET[gtkn])
		return false;
	if (in_array($_GET[gtkn], $_SESSION[allowed_get_tokens])) {
		foreach ($_SESSION[allowed_get_tokens] as $k => $v) {
			if ($v == $_GET[gtkn]) {
				//echo "Vorgang erlaubt. token gelöscht $_GET[token]. $_SESSION[allowed_get_tokens][$k]";
				array_slice($_SESSION[allowed_get_tokens], $k);
				return 1;
			}
		}
	} else {
		echo "<script>alert('Vorgang aus Sicherheitsgründen abgelaufen, bitte wiederholen Sie die Aktion.'); window.history.back();</script>";
		hacklog('CSRF Get Token nicht vorhanden oder falsch');
		exit();
	}
}
function hacklog($process) {
	global $DB;
$data = "<br><br>_SERVER <br>".print_r($_SERVER,1)."
	<br><br>_POST <br>".print_r($_POST,1)."
	<br><br>_GET <br>".print_r($_GET,1)."
	<br><br>_SESSION <br>".print_r($_SESSION,1)."
	<br><br>_COOKIE <br>".print_r($_COOKIE,1);
	mail(ADMINMAIL,"Möglicher Angriff $process","Vorgang: $process<pre>
	$data");
	$q = "INSERT INTO bncms_hacklog SET
		file = '".$_SERVER['PHP_SELF']."', 
		process = '".$process."', 
		user = '".$_SESSION['jobs_user_id']."',
		data = '".mysqli_real_escape_string($DB,$data)."',
		date = '".time()."',
		ip = '".$_SERVER['REMOTE_ADDR']."'
	";
	dbQuery($q);
}
function pre($v,$r="") {
	$o .= "<pre>";
	$o .= print_r($v,1);
	$o .= "</pre>";
	if ($r)
		return $o;
	echo $o;
}
function editRelationVisibility() {
	echo "<h2>Sichtbarkeit der Relationen</h2>
<b>=></b> Ausgehende n zu 1 Zuordnung, 
<b><=</b> Eingehende n zu 1 Zuordnung, 
<b><=o=></b> n zu m Zuordnung<br>
	<br><br>
	";
	global $aRel;
	$q = "SELECT * FROM conf_tables WHERE is_assign_table != 'yes'";
	$aTables = q($q);
	foreach ($aTables as $k => $v) {
		$md5 = md5($v['id'].rand(0,111111111111111111));
        /*$q = "SELECT * FROM bncms_user";
        $a = dbQuery($q,"",1);
        $of = "<select multiple='multiple' size='".count($a)."'  onchange=' showPossibleRelations(\"$v['id']\",jQuery(this).val(),\"$md5\",\"$path\",1)'>";

        foreach ($a as $k2 => $v2)	{
            if (is_array($aUsers)) {
                if (in_array($v2[username], $aUsers))
                    $s = "selected='selected'";
                else
                    $s = "";
            } else
                $s = "";
            $of .= "<option $s>$v2[username]</option>";
        }*/

		//$path = $v['id'];

        $of = generateUserDropdown("", $v[users], ' onchange="showPossibleRelations(\''.$v['id'].'\',jQuery(this).val(),\''.$md5.'\',\''.$path.'\',1)" ');
        //$of = pre($v,1);

        $aUsers = unserialize($v[users]);
		if (is_array($aUsers))
			$s = showPossibleRelations($v['id'], $aUsers, $path);
		else
			$s = "";
		$of .= "</select><br><br><div id='$md5' class='$path'>".$s."</div>";
		$o .= displayVisibilityButtons(formTableName($v['id']), $v['id'], $v[name]." ".$v['id']);
		$o .= "<div class=\"table_overall\" id='".$v['id']."' style='display:none;width:95%'>$of</div>";
	}

	echo $o;
}
function showVisibilitySelect($tableId, $users, $path, $md5) {
	if (!is_array($users))
		$aUsers = unserialize(urldecode($users));
	else
		$aUsers = $users;
	//echo "users";

	//wenn gespeicherte benutzer nicht verfügbar sind durch übergeordnete selektionen lösche aus path
	$q = "SELECT * FROM conf_relation_visibility WHERE path = '$path'";
	$a = q($q);
	if (is_array(unserialize($a[users])))
	foreach (unserialize($a[users]) as $k => $v) {
		if (is_array($users))
		if (in_array($v,$users))
			$aRU[] = $v;
	}
	$q = "UPDATE conf_relation_visibility SET users = '".serialize($aRU)."' WHERE path = '$path'";
	q($q);
	/*$o .= "<select multiple='multiple' style='float:left' size='".count($aUsers)."' onchange='showPossibleRelations(\"$tableId\",jQuery(this).val(),\"$md5\",\"$path\")'>";*/


    $q = "SELECT * FROM conf_relation_visibility WHERE path = '$path'";
    $aRV = q($q);
    //pre($aRV);
    $aTU = unserialize($aRV[users]);

    /*TODO */



    //pre($aTU);
	$o .= generateUserDropdown("", $aUsers, 'size="'.count($aUsers).'" onchange="showPossibleRelations(\''.$tableId.'\',jQuery(this).val(),\''.$md5.'\',\''.$path.'\')"',$aUsers);
    //$o .= pre($aTU,1);


	/*$q = "SELECT * FROM bncms_user, bncms_user_groups";
	$a = q($q,"",1);
	//pre($a);
	$q = "SELECT * FROM conf_relation_visibility WHERE path = '$path'";
	$aRV = q($q);
	//pre($aRV);
	$aTU = unserialize($aRV[users]);

	foreach ($a as $k2 => $v2)	{
		if (is_array($aTU))
		if (in_array($v2[username], $aTU))
			$s = "selected='selected'";
		else
			$s = "";
		if (is_array($aUsers))
			if (in_array($v2[username], $aUsers))
				$o .= "<option $s>$v2[username]</option>";
	}*/
	if (strpos($path,"-")) {
		$mv = md5("darstellung".$path);
		if ($aRV[icon])
			$sIR = "<img src='$aRV[icon]' style='display:inline;height:16px;width:16px'> Icon löschen <input type=checkbox id='deleteIcon_$path'><br>";
		if ($aRV[showWithEditIcons])
			$sSE[$aRV[showWithEditIcons]] = "selected";
		else
			$sSE[$aRV[showWithEditIcons]] = "";
		//pre($sSE);
		$sTI = "<div class='darstellung_$path' style='display:none'>".displayVisibilityButtons('Darstellung', $mv, "", 1,"","")."
		<nobr id=$mv  style='display:none'>
		<br>Titel <input type=text id='title_$path' value='$aRV[title]'>
		<br>Icon <input type=file id='icon_$path'><br>
		<div id=sir_$path>$sIR</div>
		Anzeigen <select id='showWithEditIcons_$path'>
		<option $sSE[Normal]>Normal</option>
		<option $sSE[Separat]>Separat</option>
		<option $sSE[Beides]>Beides</option>
		</select><br>
		<input id=bu_$path onclick='saveTitleIcon(\"$path\");' class=submit value=Speichern style='width:60px' ><img src='../image/loading.gif' class='loa' id=lo_$path style='display:none'><div id='responses_$path'></div></nobr><br></div>";
	}
	$o .= "</select></td><td valign=top>$sTI</td>";
	return $o;
}
function showNextLayer($path, $md5, $users, $tableId) {
	//$o .= $path;

	if (!is_array($users))
		$aUsers = unserialize(urldecode($users));
	else
		$aUsers = $users;

	if (!$path)
		$path = $tableId."-";
    //$o .= pre($aUsers,1);
	$q = "SELECT * FROM conf_relation_visibility WHERE path = '".$path."'";
	$aRV = q($q);
	//pre($aRV);
	$o .= "<div id='$md5' class='$path' style='position:relative; min-width:300px'>";
	$aTU = unserialize($aRV[users]);
	//$o .= pre($aVisibleUsers[users],1);
	//Für die nächsttiefere Ebene nur diese anzeigen die auf dieser Ebene aktiv sind und in nächstvorderen aktiviert waren.
    /*echo "<br><br>1";
	pre($aTU);
    echo "2";
    pre($aUsers);*/
	if (is_array($aTU))
	foreach ($aTU as $k => $v) {
		foreach ($aUsers as $k2 => $v2) {
			if ($v == $v2)
				$aNextLayerUsers[] = $v2;
		}
	}
    /*echo "3";
	*/
    $aNextLayerUsers = $aTU;
    //pre($aNextLayerUsers);
	if (is_array($aNextLayerUsers)) {
		if (preg_match("/\-a$/",$path)) {
			$o .= "<script>jQuery('.darstellung_$path').show();</script>".showPossibleRelations($tableId, $aNextLayerUsers, $path);
			$o .= showPossibleRelations($tableId, $aNextLayerUsers, preg_replace("/\-a$/","",$path));
		} else
			$o .= "<script>jQuery('.darstellung_$path').show();</script>".showPossibleRelations($tableId, $aNextLayerUsers, $path);
	}
	$o .= "</div>";
	return $o;
}
function formTableName($tableOrId, $aManualFieldProperties="") {
	$a = getTableProperties($tableOrId, $aManualFieldProperties);
	if ($a[lang])
		$r = $a[lang];
	else
		$r = $a[name];
	if (!$r and !is_numeric($tableOrId))
		return $tableOrId;
	else
		return $r;
}
function formFieldName($tableOrId, $field, $aManualFieldProperties="") {
	$a = getFieldProperties($tableOrId, $field, $aManualFieldProperties);
	if ($a[title])
		$r = $a[title];
	else
		$r = $a[name];
	return $r;
}
function showPossibleRelations($tableId, $users, $path) {
	//$o .= "$tableId, $users, $path";
	//$o .= pre($users,1);
	global $aRel;
	if ($users != "N;") {
		if (!$path)
			$path = $tableId;
		if (!is_array($users))
			$aUsers = unserialize(urldecode($users));
		else
			$aUsers = $users;

		//echo "path".$path;
		//echo strlen($path)-1." == ".strpos($path, "-");
		/*if ((strlen($path)-1) == strpos($path, "-"))
			$path = substr($path,0,strlen($path)-1);
        //echo "path".$path;

		pre($aUsers);*/
		//Alle NToM Relationen holen
		if (preg_match("/\-a$/",$path)) {
			$o .= "<table><tr><td valign=top>Relationen der Zuordnungstabelle</td></tr></table>";
			preg_match("/\-([0-9])+-a$/",$path,$t);
			$ntomRelation = $t[1];
			$q = "SELECT * FROM conf_relations WHERE id = '$ntomRelation'";
			$aNToMRelation = q($q);
			///pre($aNToMRelation);
			$q = "SELECT * FROM conf_tables WHERE id_relation = '$ntomRelation'";
			$aAssignTable = q($q);
			//pre($aAssignTable);
			$tableId = $aAssignTable['id'];
		} else {
			preg_match("/\-([0-9]+)$/",$path,$t);
			$iR = $t[1];
			$q = "SELECT * FROM conf_relations WHERE id = '$iR'";
			$a = q($q);
			if ($a[type] == "ntom") {
				//$o .= $q.$path;
				//$o .= pre($a,1);
				$o .= "<table><tr><td valign=top>Relationen der Zieltabelle</td></tr></table>";
			}
		}

		$aS = getTableProperties($tableId);
        //pre($aS);
		$sS = formTableName($aS['id']);
		if (is_array($aRel[NToM][$tableId]))
		foreach ($aRel[NToM][$tableId] as $k => $v) {
			if (!strpos($path."-", "-".$v[relationId]."-")) { //Relation die in dieser Abfolge einmal gewählt wurde nicht wieder zur Auswahl anzeigen

				$aT = getTableProperties($v[destTable]);
				$sT = formTableName($aT['id']);
				$md5 = md5($tableId.rand(0,111111111111111111));

				$om = "<tr><td valign=top><b>$sS <=o=> $sT (".formTableName($v[assignTable]).")</b>";
				$om .= "</td><td valign=top>";
				$om .= showVisibilitySelect($aT['id'], $users, $path."-".$v[relationId]."-a", $md5);
				$om .= "</tr>";
				$q = "SELECT * FROM conf_relation_visibility WHERE path = '$path-$v[relationId]'";
				$a = q($q);
				//$om .= pre($a[users],1).count(unserialize($a[users]));

				$om .= "<tr><td colspan=2>";
				$om .= showNextLayer($path."-".$v[relationId]."-a",$md5, $users, $aT['id']);
				$om .= "</td></tr>";
			}
		}
		if ($om)
			$o .= "<table>$om</table>";
		//Alle ausgehenden NTo1 Relationen holen
		if (is_array($aRel[NTo1][$tableId]))
		foreach ($aRel[NTo1][$tableId] as $assignmentField => $targetTable) {
			$aT = getTableProperties($targetTable);
			$sI = getNTo1RelationId($targetTable,$assignmentField,$tableId);
			//$o .= "strpos($path-,-$sI-)";
			//$o .= strpos($path."-", "-".$sI."-");
			if (!strpos($path."-", "-".$sI."-")) { //Relation die in dieser Abfolge einmal gewählt wurde nicht wieder zur Auswahl anzeigen
				$sT = formTableName($aT['id']);
				$md5 = md5($tableId.rand(0,111111111111111111));
				$oa .= "<tr><td valign=top><b>$sS: ".formFieldName($tableId, $assignmentField)." => $sT </b>";
				$oa .= "</td><td valign=top>";
				$oa .= showVisibilitySelect($aT['id'], $users, $path."-".$sI, $md5);
				$oa .= "</tr>";
				$oa .= "<tr><td colspan=2>";
				$oa .= showNextLayer($path."-".$sI,$md5, $users, $aT['id']);
				$oa .= "</td></tr>";
			}
		}
		if ($oa)
			$o .= "<table>$oa</table>";
		//Alle eingehenden NTo1 Relationen holen
		foreach ($aRel[NTo1] as $targetTable => $v) {
			foreach ($v as $assignmentField => $sourceTable) {
				if ($sourceTable == $tableId) {
					$aT = getTableProperties($targetTable);
					$sI = getNTo1RelationId($sourceTable,$assignmentField,$targetTable);
					if (!strpos($path."-", "-".$sI."-")) { //Relation die in dieser Abfolge einmal gewählt wurde nicht wieder zur Auswahl anzeigen
						$sT = formTableName($aT['id']);
						$md5 = md5($tableId.rand(0,111111111111111111));
						$oi .= "<tr><td valign=top><b>$sS <= $sT: ".formFieldName($targetTable, $assignmentField)."</b>";
						$oi .= "</td><td valign=top>";
						$oi .= showVisibilitySelect($aT['id'], $users, $path."-".$sI, $md5);
						$oi .= "</tr>";
						$oi .= "<tr><td colspan=2>";
						$oi .= showNextLayer($path."-".$sI,$md5, $users, $aT['id']);
						$oi .= "</td></tr>";
					}
				}
			}
		}
		if ($oi)
			$o .= "<table>$oi</table>";
		if($o)
			return "<div style=' padding-left:30px; border-left:1px solid darkgreen; border-bottom-left-radius:30px;'>".$o."<div style='clear:both'></div></div><br>";
		else
			return "<div style='margin-left:30px; margin-top:5px'>Keine Zuordnungen</div>";
	}
}
function prepareManualFieldProperties($aManualFieldProperties) {
	//muss wenn nto1 type hat die Relationen Konfig gleich ins Array lesen, geht ja aus der Feldkonfiguration hervor
	//pre($aManualFieldProperties);
	foreach ($aManualFieldProperties as $table => $value) {

	    $errorLevel = error_level_tostring(error_reporting(), ',');

	    error_reporting(E_ALL & ~E_NOTICE && ~E_WARNING);
	    ini_set('display_errors', 1);
	    ini_set('display_startup_errors', 1);

		foreach ($value['fields'] as $field => $value2) {

			if (@$value2['type'] == "nto1") {

				$c++;
				$aManualFieldProperties['bncms_relations'][$value2['relationId']]['type'] = 'nto1';
				$aManualFieldProperties['bncms_relations'][$value2['relationId']]['nto1TargetTable'] = $value2['nto1TargetTable'];
				$aManualFieldProperties['bncms_relations'][$value2['relationId']]['nto1TargetField'] = $value2['nto1TargetField'];
				$aManualFieldProperties['bncms_relations'][$value2['relationId']]['nto1SourceTable'] = $table;
				$aManualFieldProperties['bncms_relations'][$value2['relationId']]['nto1SourceField'] = $field;
			}
		}

		error_reporting($errorLevel);
	}
	return $aManualFieldProperties;
}
function getNTo1RelationId($targetTable,$assignmentField,$sourceTable,$aManualFieldProperties="") {

	//Ist manuell konfiguriert?
	//pre($aManualFieldProperties["relations"] );
    if (isset($aManualFieldProperties["bncms_relations"]))
        foreach ($aManualFieldProperties["bncms_relations"] as $k => $v) {
            //echo "$v[nto1TargetTable] == $targetTable and $v[nto1SourceTable] == $sourceTable and $v[nto1SourceField] == $assignmentField $k";
            if ($v['nto1TargetTable'] == $targetTable and
            $v['nto1SourceTable'] == $sourceTable and
            $v['nto1SourceField'] == $assignmentField) {
                return $k;
            }
        }

	if (!is_numeric($targetTable)) {
		$a = getTableProperties($targetTable);
		if ($a['id'])
			$targetTable = $a['id'];
	}
	if (!is_numeric($sourceTable)) {
		$a = getTableProperties($sourceTable);
		if ($a['id'])
			$sourceTable = $a['id'];
	}
	if (is_numeric($assignmentField)) {
		$a = getFieldProperties($targetTable,$assignmentField);
		$sourceTable = $a[name];
	}

	$q = "SELECT * FROM conf_relations WHERE nto1TargetTable = '$targetTable' AND nto1SourceTable='$sourceTable' AND nto1SourceField = '$assignmentField'";
	$a = q($q);
	$a['id'];
	return $a['id'];

}

function getRelationVisibility($sComingFromRelations, $aManualFieldProperties="") {
	global $webuser;
	$q = "SELECT * FROM conf_relation_visibility WHERE path = '$sComingFromRelations'";
	$a = q($q);
	//pre($aManualFieldProperties['bncms_relation_visibility']);
	foreach ($aManualFieldProperties['bncms_relation_visibility'] as $k => $v) {
		//echo $v['path']." == ".$sComingFromRelations;
		if ($v['path'] == $sComingFromRelations)
		$a = array(
			'id'=>'manual',
			'path'=>$v['path'],
			'users'=>$v['users'],
			'icon'=>$v['icon'],
			'title'=>$v['title'],
			'showWithEditIcons'=>$v['showWithEditIcons']
		);
	}
	//pre($a);
	return $a;
}

function checkRelationVisibility($sComingFromRelations, $place, $aManualFieldProperties="") {
	global $webuser;
	$a =  getRelationVisibility($sComingFromRelations, $aManualFieldProperties);

	if (is_array(unserialize($a[users])))
		$aU = unserialize($a[users]);
	else
		$aU = $a[users];

	if ($a[showWithEditIcons] == "Separat" and $place == "Normal")
		return false;
	if ($a[showWithEditIcons] == "Normal" and $place == "Separat")
		return false;
	if ($webuser)
		$u = "webuser";
	else
		$u = $_SESSION[user];

	return checkPermission($aU, $u, $_SESSION['userGroup']);
}
function displayThumbName($p) {
	return str_replace("file/","file/th_",$p);
}
function extractComingFrom($sComingFrom) {
	preg_match("/(_[0-9]+$)/",$sComingFrom,$t);
	$f = str_replace($t[1],"",$sComingFrom);
	$a[] = explode("-",$f);
	$a[] = str_replace("_","",$t[1]);
	return $a;
}
function displayLightbox($id, $content) {
	return "<div class='table_overall lightbox' id='$id' style='display:none'><div class='table_overall'>$content</div></div>";
}
function packGlobals () {
	global $sDisplayTableRecursivePath;
	global $aNTo1TablePath;
	global $aPagingRecursivePath, $lastRowIdNToM, $lastTableNameNToM;

	$aGlobal[aPagingRecursivePath] = $aPagingRecursivePath;
	$aGlobal[aNTo1TablePath] = $aNTo1TablePath;
	$aGlobal[sDisplayTableRecursivePath] = $sDisplayTableRecursivePath;
	$aGlobal[lastRowIdNToM] = $lastRowIdNToM;
	$aGlobal[lastTableNameNToM] = $lastTableNameNToM;
	return serialize($aGlobal);
}
function getIdFromTableString($id) {
	return $id;
}
function getNameFromTableString($nameOrId) {
	if (is_numeric($nameOrId)) {
		$q = "SELECT name FROM conf_tables WHERE id = '$nameOrId'";
		$a = dbQuery($q);
		//$a = explode("-",$ts);
		return $a[0][name];
	} else
		return $nameOrId;
}

function e($s) {
	 //Um Benutzer-Variablen vor den Anfrage zu escapen
	global $DB;
	return mysqli_real_escape_string($DB,$s);
}
function inSerializedArray($s, $a) {
	if (is_array(unserialize($a)))
		$a = unserialize($a);
	if (is_array($a))
		return in_array($s, $a);
}
function getPrice($id_product, $id_title) {
	global $DB;
	$query="SELECT price 
	FROM ass_product_title
	WHERE id_product = '$id_product'
	AND id_title = '$id_title'";
	$aPrice=dbQuery($query);
	return $aPrice;
}
/*function displayVisibilityButtons($text, $instance, $altText="", $simple=0, $iconOpen="", $iconClose="") {
	global $sBeforeAjaxQueryString;
	if (strpos($_SERVER[REQUEST_URI], "ajax.php"))
		$requesturi = urlencode($sBeforeAjaxQueryString);
	else
		$requesturi = urlencode($_SERVER[REQUEST_URI]);
	if ($simple)
		$s = "table_overall_simple";
	else {
		$s = "table_overall_title";
		$onm = "onMouseOut=\"this.style.backgroundImage=''\" onMouseOver=\"this.style.backgroundImage='url(../image/animated/slide.gif)'\"";
	}
	if ($iconOpen and !$iconClose)
		$iconClose = $iconOpen;
	if (!$iconOpen)
		$iconOpen = RELATIVEPATH."/image/icons/folder-open-$_SESSION[style_color].gif";
	if (!$iconClose)
		$iconClose = RELATIVEPATH."/image/icons/folder-close-$_SESSION[style_color].gif";

	$out = "
<div id=\"plus".$instance."\" style=\"display:block;\" class=\"$s plus\" $onm>
	<nobr>
	<a id=\"a$arrSched['id']\" href=\"javascript:void(0);\" onClick=\"javascript: ajax_send_scrollpos('".$requesturi."', '".$instance.urlencode($queryString)."');\" title=\"$altText\" >
	<img src=\"$iconClose\"></a>&nbsp;
	<a id = \"a$arrSched['id']\" href=\"javascript:void(0);\" onClick=\"javascript: ajax_send_scrollpos('".$requesturi."', '".$instance.urlencode($queryString)."', '');\" title=\"$altText\" >
	$text
	</a>
	</nobr>
</div>
<div id=\"minus".$instance."\" style=\"display:none; clear:both\" class=\"$s minus\" $onm>
	<nobr>
	<a id=\"b$arrSched['id']\" href=\"javascript:void(0);\" onClick=\"javascript: ajax_send_scrollpos('".$requesturi."', '', '".$instance.$queryString."');\" title=\"$altText\">
	<img src=\"$iconOpen\"></a>&nbsp;
	<a id=\"b$arrSched['id']\" href=\"javascript:void(0);\" onClick=\"javascript: ajax_send_scrollpos('".$requesturi."', '', '".$instance.$queryString."');\" title=\"$altText\" >
	$text
	</a>
	</nobr>
</div>
";

	if ($text != "") {
		return "$out";
	} else {
		return $out;
	}
}*/
function displayVisibilityButtons($text, $instance, $altText="", $simple=0, $iconOpen="", $iconClose="") {
	global $sBeforeAjaxQueryString;

	if (strpos($_SERVER['REQUEST_URI'], "ajax.php"))
		$requesturi = urlencode($sBeforeAjaxQueryString);
	else
		$requesturi = urlencode($_SERVER['REQUEST_URI']);

	if ($simple) {
		$s = "table_overall_simple";
		if ($text)
			$sp = "&nbsp;";
	} else {
		$s = "table_overall_title";
		$onm = "onMouseOut=\"this.style.backgroundImage=''\" onMouseOver=\"this.style.backgroundImage='url(../image/animated/slide.gif)'\"";
		$sp = "&nbsp;";
	}
	if ($iconOpen and !$iconClose)
		$iconClose = $iconOpen;
	if (!$iconOpen)
		$iconOpen = RELATIVEPATH."/image/icons/folder-open-$_SESSION[style_color].gif";
	if (!$iconClose)
		$iconClose = RELATIVEPATH."/image/icons/folder-close-$_SESSION[style_color].gif";

	$out = "<div id=\"plus".$instance."\" style=\"display:inline;\" class=\"$s plus\" $onm><nobr><a id=\"a" . @$arrSched['id'] . "\" href=\"javascript:void(0);\" onClick=\"javascript: ajax_send_scrollpos('".$requesturi."', '".$instance.urlencode(@$queryString)."');\" title=\"$altText\" ><img src=\"$iconClose\"></a>$sp<a id = \"a" . @$arrSched['id'] . "\" href=\"javascript:void(0);\" onClick=\"javascript: ajax_send_scrollpos('".$requesturi."', '".$instance.urlencode(@$queryString)."', '');\" title=\"$altText\" >$text</a></nobr></div><div id=\"minus".$instance."\" style=\"display:none; clear:both\" class=\"$s minus\" $onm><nobr><a id=\"b" . @$arrSched['id'] . "\" href=\"javascript:void(0);\" onClick=\"javascript: ajax_send_scrollpos('".$requesturi."', '', '".$instance.@$queryString."');\" title=\"$altText\"><img src=\"$iconOpen\"></a>$sp<a id=\"b" . @$arrSched['id'] . "\" href=\"javascript:void(0);\" onClick=\"javascript: ajax_send_scrollpos('".$requesturi."', '', '".$instance.@$queryString."');\" title=\"$altText\" >$text</a></nobr></div>";

	if ($text != "") {
		return "$out";
	} else {
		return $out;
	}
}
function getTableArrayNoFromName($tableName) {
	global $aTable;
	foreach ($aTable as $k => $v)
		if ($v[name] == $tableName)
			return $k;
}
function getIdentifierFromSourceTableNTo1($sourceTable, $targetTable="") {
	global $aRel;
	if ($targetTable)
		foreach ($aRel['NTo1'][$sourceTable] as $key => $value) {
			if ($value == $targetTable)
				$identifier[] = $key;
		}
	else
		foreach ($aRel['NTo1'][$sourceTable] as $key => $value) {
			$identifier[] = $key;
		}
	return $identifier;
}
function checkInt($v) {
	$v = strtolower($v);
	if ($v == "int" or $v == "smallint" or $v == "tinyint"  or $v == "mediumint" or $v == "bigint"  or $v == "float" or $v == "decimal"  or $v == "double" or $v == "numeric")
		return true;
	else
		return false;
}
function getFieldProperties($tableOrId,$field="",$aManualFieldProperties="") {
	global $aFields;
	if (is_numeric($field)) {
		$q = "SELECT * FROM conf_fields WHERE id = '$field'";
		$a = dbQuery($q,"",1);
		$r = $a[0];
	} else {
		if (is_numeric($tableOrId)) {
			$q = "SELECT * FROM conf_fields WHERE id_table = '$tableOrId' and name = '$field'";
			$a = dbQuery($q,"",1);
			$r = $a[0];
		} else {
			$q = "SELECT * FROM conf_fields WHERE id_table = '$tableOrId' and name = '$field'";
			$a = dbQuery($q,"",1);
			$r = $a[0];
		}
	}
	if (!$field) {
		if (is_numeric($tableOrId)) {
			$q = "SELECT * FROM conf_fields WHERE id_table = '$tableOrId'";
			$a = dbQuery($q,"",1);
			//Überschreiben der Feldeigenschaften
			foreach ($a as $k => $v) {
				foreach ($aManualFieldProperties[$tableOrId]["fields"][$k] as $kManual => $vManual) {
					$a[$k][$kManual] = $vManual;
				}
			}
			return $a;
		}
	}

	//Überschreiben der Feldeigenschaften
	if (is_numeric($field))
		$field = $r[name];
	else
		$r['name'] = $field;
	if (isset($aManualFieldProperties[$tableOrId]["fields"][$field]))
	foreach ($aManualFieldProperties[$tableOrId]["fields"][$field] as $k => $v) {
		$r[$k] = $v;
	}
	return $r;
}
function overwriteRights($aManualFieldProperties) {
	//Überschreiben mit manuellen Feldeigenschaften
	global $aRightsHidden, $aRightsUnchangeable;
	if (is_array($aManualFieldProperties))
	foreach ($aManualFieldProperties as $k => $v) {
		foreach ($v["fields"] as $k2 => $v2) {
			foreach ($v2 as $k3 => $v3) {
				if ($k3 == "hidden") {
					if ($v3 == "yes")
						$aRightsHidden[$k][$k2]= 1;
					if ($v3 == "no")
						$aRightsHidden[$k][$k2]= 0;
				}
				if ($k3 == "unchangeable") {
					if ($v3 == "yes")
						$aRightsUnchangeable[$k][$k2]= 1;
					if ($v3 == "no")
						$aRightsUnchangeable[$k][$k2]= 0;
				}
			}
		}
	}
}

function error_level_tostring($intval, $separator = ',')
{
    $errorlevels = array(
        E_ALL => 'E_ALL',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        E_DEPRECATED => 'E_DEPRECATED',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_STRICT => 'E_STRICT',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_NOTICE => 'E_NOTICE',
        E_PARSE => 'E_PARSE',
        E_WARNING => 'E_WARNING',
        E_ERROR => 'E_ERROR');
    $result = '';
    foreach($errorlevels as $number => $name)
    {
        if (($intval & $number) == $number) {
            $result .= ($result != '' ? $separator : '').$name; }
    }
    return $result;
}

function overwriteRelations($aManualFieldProperties) {
	//Überschreiben mit manuellen Feldeigenschaften
	global $aRel;
	//pre($aManualFieldProperties['bncms_relations']);

	$errorLevel = error_level_tostring(error_reporting(), ',');

	error_reporting(E_ALL & ~E_NOTICE && ~E_WARNING);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);

	foreach (@$aManualFieldProperties['bncms_relations'] as $k => $v) {
		if ($v['type'] == 'nto1')
			$aRel['NTo1'][$v['nto1SourceTable']][$v['nto1SourceField']] = $v['nto1TargetTable'];
		if ($v['type'] == 'ntom') {
			$aRel['NToM'][$v['table1']][] = array(
				'destTable' => $v['table2'],
				'relationId' => $k,
				'assignTable' => $v['name'],
				//'sourceFieldname' => 'id_'.$aTableTemp[0]['name'],
				//'destFieldname' => 'id_'.$aTargetTableTemp[0]['name'],
				'sourceFieldname' => $v['ntomAssignFieldTable1'],
				'destFieldname' => $v['ntomAssignFieldTable2'],
				'seperateColumns' => $v['seperateColumns'],
				'users' => $v['users'],
				'editors' => $v['editors'],
				'deletors' => $v['deletors'],
				'addors' => $v['addors'],
				'ntomDisplayType' => $v['ntomDisplayType'],
				'ntomAjaxDisplayTitleField' => $v['ntomAjaxDisplayTitleField'],
				'ntomAjaxDisplayMinSelections' => $v['ntomAjaxDisplayMinSelections']
			);

			$aRel['NToM'][$v['table2']][] = array(
				'destTable' => $v['table1'],
				'relationId' => $k,
				'assignTable' => $v['name'],
				//'sourceFieldname' => 'id_'.$aTableTemp[0]['name'],
				//'destFieldname' => 'id_'.$aTargetTableTemp[0]['name'],
				'sourceFieldname' => $v['ntomAssignFieldTable2'],
				'destFieldname' => $v['ntomAssignFieldTable1'],
				'seperateColumns' => $v['seperateColumns'],
				'users' => $v['users'],
				'editors' => $v['editors'],
				'deletors' => $v['deletors'],
				'addors' => $v['addors'],
				'ntomDisplayType' => $v['ntomDisplayType'],
				'ntomAjaxDisplayTitleField' => $v['ntomAjaxDisplayTitleField'],
				'ntomAjaxDisplayMinSelections' => $v['ntomAjaxDisplayMinSelections']
			);
		}
	}
	//pre($aRel);

	error_reporting($errorLevel);
}


function returnTableAndId($tableOrId,$aManualFieldProperties) {
	$aTableProperties = getTableProperties($tableOrId, $aManualFieldProperties);
	if (is_numeric($tableOrId)) {
		$table = $aTableProperties['name'];
		$tableId = $aTableProperties['id'];
	} else {
		$table = $tableOrId;
		$tableId = "";
	}
	return array($table,$tableId);
}
function getTableProperties($tableOrId, $aManualFieldProperties="") {
	//$table kann id oder name von tabelle sein
	//global $aTable;
	/*if (is_numeric($tableOrId))
		$nameOrId = "id";
	else
		$nameOrId = "name";
	*/
	if (is_numeric($tableOrId)) {
		$q = "SELECT * FROM conf_tables WHERE id = '$tableOrId'";
		$r = q($q,0,1);
		$r = $r[0];
	} else {
		$q = "SELECT * FROM conf_tables WHERE name = '$tableOrId'";
		$r = q($q,0,1);
		$r = $r[0];
		//$r[name] = $tableOrId;
	}
	/*foreach ($aTable as $key => $content) {
		if ($content[$nameOrId] == $tableOrId) {
			$r = $content;
		}
	}
	 else {
		foreach ($aTable as $key => $content) {
			if ($content[$nameOrId] == $table) {
				return $content[$field];
			}
		}
	}*/
	//Überschreiben der Feldeigenschaften
	//echo $tableOrId;
	//pre($aManualFieldProperties[$tableOrId]["table"]);
	if (isset($aManualFieldProperties[$tableOrId]["table"]))
	foreach ($aManualFieldProperties[$tableOrId]["table"] as $k => $v) {
		$r[$k] = $v;
	}
	return $r;
}
function getDestFromSourceTableNTo1($sourceTable, $sourceField="") {
	global $aRel;
	if ($sourceField)
		if (is_array($aRel['NTo1'][$sourceTable]))
			foreach ($aRel['NTo1'][$sourceTable] as $key => $value) {
				if ($key == $sourceField)
					$destTable[] = $value;
			}
	else
		foreach ($aRel['NTo1'][$sourceTable] as $key => $value)
			$destTable[] = $value;
	return $destTable;
}
function getSourceFromDestTableNTo1($targetTable) {
	global $aRel;
	foreach ($aRel['NTo1'] as $table => $value) {
		foreach ($value as $key2 => $value2) {
			if ($value2 == $targetTable)
				$sourceTable[] = $table;
		}
	}
	return $sourceTable;
}

function getIdName($table, $aManualFieldProperties="") {
	//pre(getTableProperties($table, $aManualFieldProperties));
	return getTableProperties($table, $aManualFieldProperties)['columnNameOfId'];
}
function getOtherTableNToM($sTable) {
	global $aRel;
	foreach ($aRel['NToM'] as $table => $content) {
		if ($table == $sTable) {
			return $content['destTable'];
			break;
		}
		if($content['destTable'] == $sTable) {
			return $table;
			break;
		}
	}
}
function getRelationProperties($assignTable, $aManualFieldProperties="") {
	if (is_numeric($assignTable)) {
		$q = "SELECT * FROM conf_tables WHERE id = '$assignTable'";
		$a = dbQuery($q);
		$assignTable = $a[0][id_relation];
		$bez = "id";
	} else
		$bez = "name";
	$q = "SELECT * FROM conf_relations WHERE $bez = '$assignTable'";
	$a = dbQuery($q);
	//Überschreiben der Feldeigenschaften
	if (isset($aManualFieldProperties["bncms_relations"][$relationId])) {
		foreach ($aManualFieldProperties["bncms_relations"][$relationId] as $k => $v) {
			if ($v['name'] == $assignTable)
				return $v;
		}
	}
	return $a[0];
}
function getRelationPropertiesById($relationId, $aManualFieldProperties="") {
	//Überschreiben der Feldeigenschaften
	if (isset($aManualFieldProperties["bncms_relations"][$relationId])) {
		foreach ($aManualFieldProperties["bncms_relations"][$relationId] as $k => $v) {
			$r[$k] = $v;
		}
		return $r;
	}
	$q = "SELECT * FROM conf_relations WHERE id = '$relationId'";
	$a = dbQuery($q);
	return $a[0];
}
function getAssignTableNToM($table1, $table2) {
	global $aRel;
	foreach ($aRel['NToM'] as $table => $v) {
		foreach ($v as $k => $content) {
			//echo "$table == $table1 and $content[destTable] == $table2<br>";
			if ($table == $table1 and $content['destTable'] == $table2) {
				if (!is_numeric($table1) and !is_numeric($table2))
					return $content[assignTable];
				$q = "SELECT id FROM conf_tables WHERE name = '$content[assignTable]'";
				$a = dbQuery($q);
				//echo $a[0]['id'];
				return $a[0]['id'];
				break;
			}
		}
	}
}

function getIdentifierNToM($table1, $table2){
	global $aRel;
	//pre($aRel);
	//echo "$table1, $table2";
	foreach ($aRel['NToM'] as $table => $v) {
		foreach ($v as $k => $content) {
			if ($table == $table1 and $content['destTable'] == $table2) {
				//pre($v);
				$r=array();
				array_push($r, $content['sourceFieldname']);
				array_push($r, $content['destFieldname']);

				//Source kommt erst, dann destination
				//if ($content['destTable'] == $sTable) {
					//$r=array_reverse($r);
				//}
				break;
			}
		}
	}
	return $r;
}

function ArrayDepth($Array,$DepthCount=-1) {
// Find maximum depth of an array
// Usage: int ArrayDepth( array $array )
// returns integer with max depth
// if Array is a string or an empty array it will return 0
  $DepthArray=array(0);
  $DepthCount++;
  $Depth = 0;
  if (is_array($Array))
    foreach ($Array as $Key => $Value) {
      $DepthArray[]=ArrayDepth($Value,$DepthCount);
    }
  else
    return $DepthCount;
  return max($DepthCount,max($DepthArray));
}
function getLengthFromField($field, $tableOrId, $aManualFieldProperties="") {
	return getLengthFromMysqlType(getMysqlTypeFromField ($field, $tableOrId, $aManualFieldProperties));
}
function getLengthFromMysqlType($type) {
	preg_match('/\(([0-9]+)\)/',$type,$r);
	if (is_numeric($r[1]))
		return $r[1];
	else
		return(30);
}
function getMysqlTypeFromField ($field, $tableOrId, $aManualFieldProperties="") {
	$q = "SELECT mysql_type_bez, length_values FROM conf_fields WHERE id_table = '".getTableId($tableOrId)."' and name = '$field'";
	$r = dbQuery($q);
	//pre($aManualFieldProperties);
	//echo "aManualFieldProperties[".$tableOrId."][fields][".$field."][length_values]";
	//echo $aManualFieldProperties[$tableOrId]["fields"][$field]['length_values'];
	//überschreiben
	if ($aManualFieldProperties[$tableOrId]["fields"][$field]['mysql_type_bez'])
		$r[0][mysql_type_bez] = $aManualFieldProperties[$tableOrId]["fields"][$field]['mysql_type_bez'];
	if ($aManualFieldProperties[$tableOrId]["fields"][$field]['length_values'])
		$r[0][length_values] = $aManualFieldProperties[$tableOrId]["fields"][$field]['length_values'];
	if ($r[0][length_values])
		$a = "(".$r[0][length_values].")";
	return $r[0][mysql_type_bez].$a;
}
function getTableId($table) {
	if (is_numeric($table)) {
		return $table;
	} else {
		$q = "SELECT id FROM conf_tables WHERE name = '$table'";
		$a = dbQuery($q);
		return $a[0]['id'];
	}
}
function getInputWidthFromLength($length, $is_numeric = 0) {
	if (is_numeric)
		$l = $length*7;
	else
		$l = $length*8;
	if ($l > 500)
		$l = 500;
	return $l;
}
function st($t) {
		//tabellennamen dürfen kein underscore enthalten sonst mach nto1tablepath und comingfrom kaputt
		return str_replace("_","",$t);
	}
	function getFileFormat($s) {
		preg_match('/\.(.{3,4})$/', $s, $t);
		return $t[1];
	}
function resize($imgfile, $weite, $index="") {
	global $source;
	list($width, $height) = getimagesize($imgfile);
	$imgratio=$width/$height;
	if ($weite != "") {
		$newwidth = $weite;
		$newheight = $weite/$imgratio;
	} else {
		$newheight = $height;
		$newwidth = $width;
	}
	$thumb = ImageCreateTrueColor($newwidth,$newheight);
	$fileFormat = strtolower(getFileFormat($imgfile));
	if ($fileFormat == "jpg" or $fileFormat == "jpeg" ) {
		$fileFormat = "jpeg";
	}
	//$source = imagecreatefromjpeg($imgfile);

	$source = call_user_func_array( "imagecreatefrom".$fileFormat, array($imgfile));
	imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	$imgfile=str_replace("file/","file/th_", $imgfile);
	//imagejpeg($thumb,$imgfile,100) or die("error 2 ".$imgfile) ;
	call_user_func_array( "image".$fileFormat, array($thumb,$imgfile));
	return $imgfile;
}

function resizeRatio($imgfile, $ratio, $index="") {
	global $source;
	list($width, $height) = getimagesize($imgfile);
	$imgRatio=$width/$height;
	$ar = explode(":",$ratio);
	$parameterRatio = $ar[0]/$ar[1];

	// 4 : 3 = x : 50
	//x = 50 * 4 / 3

	if ($imgRatio > $parameterRatio) {
		//Bild ist weiter als Verhältnis
		$newheight = $height;
		$newwidth = ($height * $ar[0]) / $ar[1];
	}
	if ($imgRatio < $parameterRatio) {
		//Bild ist höher als Verhältnis
		$newwidth = $width;
		$newheight = ($width * $ar[1]) / $ar[0];
	}
	$fileFormat = strtolower(getFileFormat($imgfile));
	if ($fileFormat == "jpg" or $fileFormat == "jpeg" ) {
		$fileformat = "jpeg";
	}
	$thumb = ImageCreateTrueColor($newwidth,$newheight);
	//$source = imagecreatefromjpeg($imgfile);
	$source = call_user_func_array( "imagecreatefrom".$fileFormat, array($imgfile));

	imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $newwidth, $newheight);
	//$imgfile=str_replace("file/","file/", $imgfile);
	//$imgfile=str_replace("file/","file/th_", $imgfile);
	//imagejpeg($thumb,$imgfile,70) or die("error 2 ".$imgfile) ;
	call_user_func_array( "image".$fileFormat, array($thumb,$imgfile));
	//echo $imgfile;
	//echo $thumb;
	//exit();
	return $imgfile;
}


function waterMark($fileInHD, $wmFile, $transparency = 50, $jpegQuality = 50, $margin = 5, $index) {
	//echo $index;
	$wmImg  = imageCreateFromPng($wmFile);
	$jpegImg = imageCreateFromJPEG($fileInHD);
	imagealphablending($wmImg,0);
	imagealphablending($jpegImg,0);
	$markbreite=imagesx($wmImg);
	$markhoehe=imagesy($wmImg);
	$srcbreite=imagesx($jpegImg);
	$srchoehe=imagesy($jpegImg);
	$endbreite=$srcbreite/1.5;
	$endhoehe=$markhoehe*$endbreite/$markbreite;
	$startx=$srcbreite/2-$endbreite/2;
	$starty=$srchoehe/2-$endhoehe/2;
	//imageCopyMerge($jpegImg, $newwmImg, $startx, $starty, 0, 0, $endbreite, $endhoehe, 50);
	$newwmImg  = imageCreate($srcbreite, $srchoehe);
	imagecopyresized($newwmImg, $wmImg, 0, 0, 0, 0, $srcbreite, $srchoehe, 500, 375);
	//imagecolortransparent($newwmImg,imagecolorexact($insert,255,255,255));
	// Water mark process
	imageCopyMerge($jpegImg, $newwmImg, 0, 0, 0, 0, $srcbreite, $srchoehe, 40);
	// Overwriting image
	ImageJPEG($jpegImg, $fileInHD, $jpegQuality);
}

function displayFields($sTable="", $sComingFrom='') {

	if ($sTable != "")
	{
		$sQueryAdd = " WHERE id = '$sTable' ";
	}

	error_reporting(0);
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);

	$query= "SELECT * FROM conf_tables ".$sQueryAdd." ORDER BY orderkey";

	error_reporting(E_ALL & ~E_NOTICE && ~E_WARNING);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);

	$aTables = dbQuery($query,"",1);
	//print_r($_SESSION);
	if (is_array($aTables)) {
		foreach ($aTables as $key => $value) {
			if (is_numeric($key)) {
				if ($value['lang'])
				{
					$ol = $value['lang'];
				}
				else
				{
					$ol = $value['name'];
				}

				error_reporting(0);
				ini_set('display_errors', 0);
				ini_set('display_startup_errors', 0);

				$sOutput .= displayVisibilityButtons($ol,"onoff_".$value['id']."_".$sComingFrom);

				error_reporting(E_ALL & ~E_NOTICE && ~E_WARNING);
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);

				$sOutput .= "<div class='table_overall conf_editor' id='onoff_".$value['id']."_".$sComingFrom."' style='display:none;'>";

				$query ="SELECT * FROM conf_relations WHERE table1 = '" . $value['id'] . "' or table2 = '" . $value['id'] . "'";
				$aRelations = dbQuery($query);


				/*n zu m Relationen Konfigurationtabelle anzeigen*/
				if (is_array($aRelations)) {
					$sOutput .= "<div style='padding:10px'>".displayVisibilityButtons("n zu m Relationen $value[lang]","onoff_relation".$value['id'],"",1);
					$sOutput .= "</div><div id='onoff_relation" . $value['id'] . "' class='ntom' style='display:none;'>";
					$sOutput .= "<table>";
					//echo "<pre>";
					//print_r($aRelations);
					/*Kopfzeile*/
					$sOutput .= "<tr class=\"table_head\">";
					$sOutput .= "<td class=\"table_sidebar\"><a href=\"edit_fields.php?relation=ntom&id_table=".$value['id']."\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_blank\" title=\"n zu m Relation erstellen\"><img src=\"".RELATIVEPATH."/image/icons/add-folder-$_SESSION[style_color].gif\"></a></td><td class=\"table_sidebar\"></td><td class=\"table_sidebar\"></td>";

					foreach ($aRelations[0] as $key3 => $value3) {
						if ($key3 != "id")
							$sOutput .= "<td>$key3</td>";
					}

					$sOutput .= "</tr>";
					foreach ($aRelations as $key2 => $value2) {
						if (is_numeric($key2))
						if ($value2[name]) {
							$query="SELECT * FROM conf_tables WHERE id = '$value2[table1]'";
							$aTable1=dbQuery($query);
							$query="SELECT * FROM conf_tables WHERE id = '$value2[table2]'";
							$aTable2=dbQuery($query);
							$query="SELECT * FROM conf_tables WHERE name = '$value2[name]'";
							$aAssignTable=dbQuery($query);
							//echo "<tr><td>asd";
							//print_r($aAssignTable[0]['id']);
							//echo "</td></tr>";
							//print_r($value2);

							$sOutPutAssignTable = displayFields($aAssignTable[0]['id'], $value[name]."-".$value['id']);
							$sOutput .= "<tr>
							<td class=\"table_sidebar\"><a href=\"edit_fields.php?relation=ntom&editRelation=true&id_relation=" . $value2['id'] . "&id_table=" . $value['id'] . "\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_blank\" title=\"n zu m Relation bearbeiten\"><img src=\"".RELATIVEPATH."/image/icons/edit-page-" . $_SESSION['style_color'] . ".gif\"></a></td>
							
							<td class=\"table_sidebar\"><a href=\"edit_fields.php?relation=ntom&id_relation=" . $value2['id'] . "&deleteRelation=true\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_blank\" title=\"Relation entfernen\"><img src=\"".RELATIVEPATH."/image/icons/delete-page-" . $_SESSION['style_color'] . ".gif\"></a></td>
							<td class=\"table_sidebar\"><a href=\"edit_fields.php?id=new&id_table=".$aAssignTable[0]['id']."\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_blank\" title=\"Feld erstellen\"><img src=\"".RELATIVEPATH."/image/icons/add-page-" . $_SESSION['style_color'] . ".gif\"></a></td>";
							foreach ($value2 as $k => $v) {
								if ($k == "table1" or $k == "table2") {
									$t = getTableProperties($v);
									$v = $t[name];
								}
								if ($k != "id")
									$sOutput .= "<td>$v</td>";
							}
							/*$sOutput .= "<td>$value2[name]</td>
							<td>$value2[type]</td>
							<td>".$aTable1[0][name]."</td>
							<td>".$aTable2[0][name]."</td>
							<td>$value2[ntomAssignFieldTable1]</td>
							<td>$value2[ntomAssignFieldTable2]</td></tr>";*/

							$sOutput .= "<tr><td colspan=\"5\">".$sOutPutAssignTable."</td></tr>";

						}
					}
					$sOutput .= "</table></div>";
				}
				$sOutput .= "<table>";
				//echo "SELECT * FROM conf_fields WHERE table = '$value['id']'";
				$aFields = dbQuery("SELECT * FROM conf_fields WHERE id_table = '" . $value['id'] . "' ORDER BY mysql_order ASC","",1);
				//todo reparieren alle felder l&ouml;schen die keine tabelle mehr haben nach l&ouml;schung der tabelle

				if ($value['is_assign_table'] != "yes") {
					$sOutput .= "<tr class=\"table_head\"><td colspan=\"5\" class=\"table_head\">";
					$sOutput .= "<a href=\"edit_fields.php?id=new&id_table=".$value['id']."\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_blank\" title=\"Feld erstellen\"><img src=\"".RELATIVEPATH."/image/icons/add-page-$_SESSION[style_color].gif\"></a>
					&nbsp;<a href=\"edit_fields.php?removeTable=true&id=".$value['id']."\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_self\" title=\"Tabelle l&ouml;schen\"><img src=\"".RELATIVEPATH."/image/icons/delete-page-red.gif\"></a>
					&nbsp;<a href=\"edit_fields.php?editTable=true&id=".$value['id']."\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_self\" title=\"Tabelle &auml;ndern\"><img src=\"".RELATIVEPATH."/image/icons/edit-page-$_SESSION[style_color].gif\"></a>
					&nbsp;<a href=\"edit_fields.php?duplicateTable=true&id=".$value['id']."\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_self\" title=\"Tabelle duplizieren\"><img src=\"".RELATIVEPATH."/image/icons/duplicate-$_SESSION[style_color].gif\"></a>
					&nbsp;<a href=\"edit_fields.php?relation=ntom&id_table=".$value['id']."\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_self\" title=\"n zu m Relation erstellen\"><img src=\"".RELATIVEPATH."/image/icons/add-folder-$_SESSION[style_color].gif\"></a></td></tr>";
				}
				$sOutput .= "<tr class=\"table_head\">
				<td></td>
				<td></td>";
				if (is_array($aFields[0])) {
					foreach ($aFields[0] as $key3 => $value3) {
						if ($key3 != "id")
							$sOutput .= "<td valign=\"top\">$key3</td>";
					}
				}

				if (is_array($aFields)) {

					$sOutput .= "</tr>";
					foreach ($aFields as $id => $fields) {
						$sOutputContent = "";
						$sOutputHeader = "";
						if (is_numeric($id))
						foreach ($fields as $fieldname => $fieldcontent) {
							if ($fieldname != "id") {

								if ($fieldname == "id_table") {

									$sOutputContent .= "<td valign=top>".$fieldcontent."</td>";
								}  else {
									if ($fieldname == "name")
										$sOutputContent .= "<td valign=top><b>".$fieldcontent."</b></td>";
									else
										$sOutputContent .= "<td valign=top>".$fieldcontent."</td>";

									$sOutputHeader .= "<td valign=top>".$fieldname."</td>";
								}
							} else {
								$field_id = $fieldcontent;
							}
						}
						$sOutput .= "<tr><td valign=top class=\"table_sidebar\"><a href=\"edit_fields.php?id=$field_id&id_table=" . $value['id'] . "\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_blank\" title=\"Feld &auml;ndern\"><img src=\"".RELATIVEPATH."/image/icons/edit-$_SESSION[style_color].gif\"></a></td>
						<td valign=\"top\" class=\"table_sidebar\"><a href=\"edit_fields.php?id=$field_id&delete=true&id_table=" . $value['id'] . "\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_blank\" title=\"Feld l&ouml;schen\"><img src=\"".RELATIVEPATH."/image/icons/delete-page-$_SESSION[style_color].gif\"></a></td>
						".$sOutputContent."<td valign=top></td></tr>";
					}
				}
				$sOutput .= "</table>";
				$sOutput .= "</div>";
			}
		}
	} else
			echo "Keine Tabellen gefunden.";
	return $sOutput;
}
function editField() {
	global $DB;
	//echo "SELECT * FROM conf_fields WHERE id = '$iId'";
	if (!$_POST[id_table])
		$_POST[id_table] = $_GET[id_table];
	if (!$_GET[id_table])
		$_GET[id_table] = $_GET[id_table];
	if ($_GET['id'] =="new") {
	//echo "INSERT INTO conf_fields SET id_table = '$_GET[id_table]'";
		dbQuery("INSERT INTO conf_fields SET id_table = '".e($_GET['id_table'])."'");
		$_GET['id'] = mysqli_insert_id($DB);
	}
	$q = "SELECT * FROM conf_fields WHERE id = '" . e($_GET['id']) . "'";
	$aFields= dbQuery($q);

	foreach ($aFields[0] as $fieldname => $fieldcontent) {
		$query="SHOW Columns FROM conf_fields LIKE '$fieldname' ";
		$aColumn = dbQuery($query);
		if ($fieldname != "id" and $fieldname != "processing" and $fieldname != "min_width" and $fieldname != "min_height" and $fieldname != "max_height" and $fieldname != "max_width") {

			//print_r($aColumn);
			$sItems = str_replace("set(","",$aColumn[0]['Type']);
			$sItems = str_replace(")","",$sItems);
			$sItems = str_replace("'","",$sItems);
			$aItems = explode(",",$sItems);
			if ($fieldname == "id_table") {
				/*$query="SELECT * FROM conf_tables WHERE id = '$fieldcontent'";
				$aTable = dbQuery($query);
				$sOutputContent .= "<td valign=top>".$aTable[name]."</td>";*/
				$sOutputContent .= "<input type=\"hidden\" name=\"id_table\" value=\"".$_POST[id_table]."\">";
			} elseif ($fieldname == "hidden" or $fieldname == "unchangeable") {
				$f = unserialize($fieldcontent);
				$q = "SELECT * FROM bncms_user";
				$a = dbQuery($q,"",1);
                $sOutputContent .= "<td valign=\"top\" class=\"b\">".$fieldname."</td></tr><tr><td valign=\"top\">";
                $sOutputContent .= generateUserDropdown("save_".$fieldname, $fieldcontent);
                $sOutputContent .= "</td></tr><tr>";
			}  elseif ($fieldname == "name" or $fieldname == "title") {
				/*if ($aFields[0][type] == "nto1") {
					$sOutputHeader .= "<td valign=\"top\">".$fieldname."</td>";
					$query="SELECT name FROM conf_tables WHERE id = '".$aFields[0]['nto1TargetTable']."'";
					$aTargetTableName = dbQuery($query);
					$sOutname = "id_".$aTargetTableName[0][name];
					$sOutputContent .="<td valign=\"top\">$sOutname<input type=\"hidden\" name=\"name\" value=\"$sOutname\"></td>";
				} else {*/
					$sOutputContent .= "<td valign=\"top\" class=\"b\">".$fieldname."</td></tr><tr>";
					$sOutputContent .="<td valign=\"top\"><input type=\"text\" name=\"save_$fieldname\" value=\"$fieldcontent\"></td></tr><tr>";
				//}
			} elseif ($fieldname == "mysqlType") {
				$sOutputContent .= "<td valign=\"top\" class=\"b\">".$fieldname."</td></tr><tr>";
				$aMysqlType = mysqlFieldPropsForType($aFields[0][type]);
				$sMysqlType = strtolower($aMysqlType[0]);
				if ($aMysqlType[1] != "") {
					$sMysqlType .=  "(".$aMysqlType[1].")";
				}
					$sOutputContent .= '<td valign=top>Vorgeschlagener MySQL-Typ: <b>'.$sMysqlType.'</b>
				<input type="hidden" name="mysqlType" value="'.$sMysqlType.'" id=\"mysqlType\"></td></tr> ';
				} elseif ($fieldname == "mysql_type_bez") {
				 function checkSelected($st,$mt) {
					if ($st == $mt)
						return "selected";
				}
			$sOutputContent .= "<td valign=\"top\" class=\"b\">".$fieldname."</td></tr><tr>";
			$sOutputContent .= '<td valign=top>'.$fieldcontent.'
				<select name="save_mysql_type_bez" id="mysql_type_bez" >
					<option></option>
					<option value="INT" '.checkSelected('INT',$fieldcontent).'>INT</option>
					<option value="VARCHAR" '.checkSelected('VARCHAR',$fieldcontent).'>VARCHAR</option>
					<option value="TEXT" '.checkSelected('TEXT',$fieldcontent).'>TEXT</option>
					<option value="DATE" '.checkSelected('DATE',$fieldcontent).'>DATE</option>
					<optgroup label="NUMERIC">
						<option value="TINYINT" '.checkSelected('TINYINT',$fieldcontent).'>TINYINT</option>
						<option value="SMALLINT" '.checkSelected('SMALLINT',$fieldcontent).'>SMALLINT</option>
						<option value="MEDIUMINT" '.checkSelected('MEDIUMINT',$fieldcontent).'>MEDIUMINT</option>
						<option value="INT" '.checkSelected('INT',$fieldcontent).'>INT</option>
						<option value="BIGINT" '.checkSelected('BIGINT',$fieldcontent).'>BIGINT</option>
						<option value="-">-</option>
						<option value="DECIMAL" '.checkSelected('DECIMAL',$fieldcontent).'>DECIMAL</option>
						<option value="FLOAT" '.checkSelected('FLOAT',$fieldcontent).'>FLOAT</option>
						<option value="DOUBLE" '.checkSelected('DOUBLE',$fieldcontent).'>DOUBLE</option>
						<option value="REAL" '.checkSelected('REAL',$fieldcontent).'>REAL</option>
						<option value="-">-</option>
						<option value="BIT" '.checkSelected('BIT',$fieldcontent).'>BIT</option>
						<option value="BOOL" '.checkSelected('BOOL',$fieldcontent).'>BOOL</option>
						<option value="SERIAL" '.checkSelected('SERIAL',$fieldcontent).'>SERIAL</option>
					</optgroup>
					<optgroup label="DATE and TIME">
						<option value="DATE" '.checkSelected('DATE',$fieldcontent).'>DATE</option>
						<option value="DATETIME" '.checkSelected('DATETIME',$fieldcontent).'>DATETIME</option>
						<option value="TIMESTAMP" '.checkSelected('TIMESTAMP',$fieldcontent).'>TIMESTAMP</option>
						<option value="TIME" '.checkSelected('TIME',$fieldcontent).'>TIME</option>
						<option value="YEAR" '.checkSelected('YEAR',$fieldcontent).'>YEAR</option>
					</optgroup>
					<optgroup label="STRING">
						<option value="CHAR" '.checkSelected('CHAR',$fieldcontent).'>CHAR</option>
						<option value="VARCHAR" '.checkSelected('VARCHAR',$fieldcontent).'>VARCHAR</option>
						<option value="-">-</option>
						<option value="TINYTEXT" '.checkSelected('TINYTEXT',$fieldcontent).'>TINYTEXT</option>
						<option value="TEXT" '.checkSelected('TEXT',$fieldcontent).'>TEXT</option>
						<option value="MEDIUMTEXT" '.checkSelected('MEDIUMTEXT',$fieldcontent).'>MEDIUMTEXT</option>
						<option value="LONGTEXT" '.checkSelected('LONGTEXT',$fieldcontent).'>LONGTEXT</option>
						<option value="-">-</option>
						<option value="BINARY" '.checkSelected('BINARY',$fieldcontent).'>BINARY</option>
						<option value="VARBINARY" '.checkSelected('VARBINARY',$fieldcontent).'>VARBINARY</option>
						<option value="-">-</option>
						<option value="TINYBLOB" '.checkSelected('TINYBLOB',$fieldcontent).'>TINYBLOB</option>
						<option value="MEDIUMBLOB" '.checkSelected('MEDIUMBLOB',$fieldcontent).'>MEDIUMBLOB</option>
						<option value="BLOB" '.checkSelected('BLOB',$fieldcontent).'>BLOB</option>
						<option value="LONGBLOB" '.checkSelected('LONGBLOB',$fieldcontent).'>LONGBLOB</option>
						<option value="-">-</option>
						<option value="ENUM" '.checkSelected('ENUM',$fieldcontent).'>ENUM</option>
						<option value="SET"  '.checkSelected('SET',$fieldcontent).'>SET</option>
					</optgroup>
					<optgroup label="SPATIAL" >
						<option value="GEOMETRY" '.checkSelected('GEOMETRY',$fieldcontent).'>GEOMETRY</option>
						<option value="POINT" '.checkSelected('POINT',$fieldcontent).'>POINT</option>
						<option value="LINESTRING" '.checkSelected('LINESTRING',$fieldcontent).'>LINESTRING</option>
						<option value="POLYGON" '.checkSelected('POLYGON',$fieldcontent).'>POLYGON</option>
						<option value="MULTIPOINT" '.checkSelected('MULTIPOINT',$fieldcontent).'>MULTIPOINT</option>
						<option value="MULTILINESTRING" '.checkSelected('MULTILINESTRING',$fieldcontent).'>MULTILINESTRING</option>
						<option value="MULTIPOLYGON" '.checkSelected('MULTIPOLYGON',$fieldcontent).'>MULTIPOLYGON</option>
						<option value="GEOMETRYCOLLECTION" '.checkSelected('GEOMETRYCOLLECTION',$fieldcontent).'>GEOMETRYCOLLECTION</option>
					</optgroup>   
				 </select>
				 </td></tr><tr>';
			} elseif ($fieldname == "length_values") {

				$sOutputContent .= "<td valign=top class=\"b\">".$fieldname."</td></tr><tr>";
				$sOutputContent .= "<td valign=top><input type=\"text\" name=\"save_".$fieldname."\" value=\"".$fieldcontent."\" id=\"mysql_type_val\"></td></tr><tr>";
			} elseif ($fieldname == "type") {
				$sOutputContent .= "<td valign=top class=\"b\">".$fieldname."</td></tr><tr>";
				$sOutputContent .= "<td valign=top><select name=\"save_".$fieldname."\"  onChange=\" form.submit();\" >";
				$aTypes = array("textfield", "textarea", "tinymce", "dropdown", "file", 'image','text','html','number','password','checkbox','date','price','nto1','url','ip' );
				foreach ($aTypes as $key => $value) {
					if ($value == $fieldcontent)
						$selected = "selected";
					else
						$selected = "";
					$sOutputContent .= "<option value=\"$value\" $selected>$value</option>";
				}
				$sOutputContent .= "</select>";
				$sActualType = $fieldcontent;
				//dropdown
				if ($fieldcontent == "dropdown") {
				//print_r($aColumn);
					$aItems = explode(",", $aFields[0][length_values]);
					//print_r($aItems);
					foreach ($aItems as $k => $v) {
						$v = str_replace('"',"",str_replace("'","",$v));
						$sOutputContent .= "<nobr>SQL-Name: <input type='text' name='dropdownitem_$k' value='$v'></nobr><br>";
					}
					$sOutputContent .= "<a href='javascript:void(0)'>Neuen Dropdown-Eintrag erstellen</a><br>";
				}

				//image
				if ($fieldcontent == "image") {
					$sOutputContent .= "<br>Bild Bearbeitung: <select name=save_processing>";
					if ($aFields[0][processing] == "Keine")
						$s = "selected";
					else
						$s = "";
					$sOutputContent .= "<option $s>Keine</option>";
					if ($aFields[0][processing] == "4:3 Beschneiden")
						$s = "selected";
					else
						$s = "";
					$sOutputContent .= "<option $s>4:3 Beschneiden</option>";
					if ($aFields[0][processing] == "16:9 Beschneiden")
						$s = "selected";
					else
						$s = "";
					$sOutputContent .= "<option $s>16:9 Beschneiden</option>";
					$sOutputContent .= "</select><br>";

					$sOutputContent .= "Minimale Breite: <input maxlength=6 type=number name=save_min_width value='".$aFields[0][min_width]."'><br>";
					$sOutputContent .= "Minimale Höhe: <input maxlength=6 type=number name=save_min_height value='".$aFields[0][min_height]."'><br>";
					$sOutputContent .= "Maximale Breite: <input maxlength=6 type=number name=save_max_width value='".$aFields[0][max_width]."'><br>";
					$sOutputContent .= "Maximale Höhe: <input maxlength=6 type=number name=save_max_height value='".$aFields[0][max_height]."'><br>";

				}

				//nto1
				if ($fieldcontent == "nto1")
					$display = "";
				else
					$display = "style=\"display:none\"";
				$sOutputContent .= "<div id=\"nto1Targets\" $display>";
				$query ="SELECT name, id FROM conf_tables  WHERE is_assign_table != 'yes' ";
				$aTables = dbQuery($query);
				//print_r($aTables);
				$sOutputContent .= "<br /><select name=\"save_nto1TargetTable\"  onChange=\"form.submit();\">";

				foreach ($aTables as $key => $value) {
					if ($value['id'] == $aFields[0]['nto1TargetTable']) {
						$selected = "selected";
						$sActualnto1TargetTable = $aFields[0]['nto1TargetTable'];
					} else {
						$selected = "";
					}
					$sOutputContent .= "<option value=\"" . $value['id'] . "\" $selected>-> Table: " . $value['name'] . "</option>";
				}
				$sOutputContent .= "</select>";
				$query ="SELECT name, id FROM conf_fields WHERE id_table = '$sActualnto1TargetTable'";
				$aTargetFields = dbQuery($query);
				$sOutputContent .= "<br /><select name=\"save_nto1TargetField\" >";
				//$sOutputContent .= "<option value=\"id\">-> Field: id</option>";
				if (is_array($aTargetFields)) {
					foreach ($aTargetFields as $key => $value) {
							if ($value['id'] == $aFields[0]['nto1TargetField'])
								$selected = "selected";
							else
								$selected = "";
							$sOutputContent .= "<option value=\"" . $value['id'] . "\" $selected>-> Field: " . $value[name] . "</option>";
					}
				}
				$sOutputContent .= "</select>";

				$sOutputContent .= "<br><br>nto1DisplayType<br><select name=\"save_nto1DisplayType\" onChange=\"form.submit();\">";
				if ($aFields[0]['nto1DisplayType'] == "radio") {
					$sOutputContent .= "<option value='radio' selected>Radio (Tabelle)</option>";
				} else {
					$sOutputContent .= "<option value='radio'>Radio (Tabelle)</option>";
				}
				if ($aFields[0]['nto1DisplayType'] == "dropdown") {
					$sOutputContent .= "<option value='dropdown' selected>Dropdown</option>";
				} else {
					$sOutputContent .= "<option value='dropdown'>Dropdown</option>";
				}
				$sOutputContent .= "</select>";

				//if ($aFields[0]['nto1DisplayType'] == "dropdown") {
				$query ="SELECT name, id FROM conf_fields WHERE id_table = '$sActualnto1TargetTable'";
				$aTargetFields = dbQuery($query);
				$sOutputContent .= "<br><br>nto1DropdownTitleField (Hauptidentifikationsfeld für Zieltabellen-Objekt, meist Titel, Name oder Label. Verwendet für die Dropdown Auswahl oder für die Suche in verweisenden Tabellen. Dieses Feld muss zwingend gewählt werden sonst funktioniert die Suche nicht.)<br><select name=\"save_nto1DropdownTitleField\" >";
				//$sOutputContent .= "<option value=\"id\">-> Field: id</option>";
				if (is_array($aTargetFields)) {
					foreach ($aTargetFields as $key => $value) {
							if ($value[name] == $aFields[0]['nto1DropdownTitleField'])
								$selected = "selected";
							else
								$selected = "";
							$sOutputContent .= "<option value=\"$value[name]\" $selected>-> Field: $value[name]</option>";
					}
				}
				$sOutputContent .= "</select>";
				//}

				$sOutputContent .= "</div></td></tr><tr>";

			}

			elseif (strstr($aColumn[0]['Type'], "set") != "" ) {
				if (count($aItems) == 3 and in_array('on',$aItems) and in_array('off',$aItems)) { // checkbox
					//Checkbox
					$sOutputContent .= "<td valign=top class=\"b\">".$fieldname."</td></tr><tr>";
					if ($fieldcontent == "on")
						$checked = "checked";
					else
						$checked = "";
					$sOutputContent .= "<td valign=top><input type=\"checkbox\" name=\"save_".$fieldname."\" $checked></td></tr><tr>";
					$sOutputContent .= "<input type=\"hidden\" name=\"checkbox_".$fieldname."\" value=\"1\">";
				} else {
					$sOutputContent .= "<td valign=top style=\"b\">".$fieldname."</td></tr><tr>";
					$sOutputContent .= "<td valign=top><select name=\"save_".$fieldname."\" >";

					foreach ($aItems as $key => $value) {
						if ($value == $fieldcontent)
							$selected = "selected";
						else
							$selected = "";
						$sOutputContent .= "<option value=\"$value\" $selected>$value</option>";
					}
					$sOutputContent .= "</select></td></tr><tr>";

				}
			} else {
				if ($fieldname != "nto1TargetTable" and $fieldname != "nto1TargetField" and $fieldname != "ntomTargetTable" and $fieldname != "ntomTargetField"  and $fieldname != "nto1DisplayType"  and $fieldname != "nto1DropdownTitleField") {
					$sOutputContent .= "<td valign=top class=\"b\">".$fieldname."</td></tr><tr>";
					$sOutputContent .= "<td valign=top><input type=\"text\" name=\"save_".$fieldname."\" value=\"".$fieldcontent."\"></td></tr><tr>";
				}
			}
		}/* else {
			if ($fieldname != "processing" and $fieldname != "min_width" and $fieldname != "min_height" and $fieldname != "max_height" and $fieldname != "max_width")
				$field_id = $fieldcontent;
		}*/
	}
	//print_r($_GET);
	//if ($field_id == 0)
	$field_id = $_GET['id'];
	echo $sOutput .= "
	<script>
	function su() {
		document.getElementById(\"mysqlType\").value = document.getElementById(\"mysql_type_bez\").value+\"(\"+document.getElementById(\"mysql_type_val\").value+\")\";
		
	}
	</script>
	<table><tr>
	<form action=\"edit_fields.php?id=$field_id&id_table=$_GET[id_table]\" method=\"post\">
	".$sOutputContent."
	<td valign=top colspan=2><br><input type='hidden' name='closeafter' id='closeafter' value='0'><input type=\"hidden\" name=\"field_id\" value=\"$field_id\">
	<nobr><input type=\"Submit\" class='submit' value=\"Speichern\" onsubmit='
	su(); 
	return true;'> <input type=\"Button\" class='submit' value=\"Speichern und Zur&uuml;ck\" onclick='su(); document.getElementById(\"closeafter\").value = \"1\";
	this.form.submit();'></nobr><br>
	<input onClick=\"window.close()\" type=\"button\" class='submit' value=\"Zur&uuml;ck\"></td>
	</form>
	</tr></table>";
}
function saveField() {
	global $DB;
	$_GET[id_table] = $_POST[id_table];
	$_GET['id'] = $_POST[field_id];
	$a = mysqlFieldPropsForType($_POST[save_type]);
	$mysqlType = strtolower($a[0]);
	if ($a[1])
		$mysqlType .= "(".$a[1].")";
	//print_r($mysqlType);
	$_POST[save_mysqlType] = $mysqlType;
	//echo "<pre>";
	if ($_POST[save_type] == 'nto1') {
		$id = getNTo1RelationId($_POST['save_nto1TargetTable'], $_POST['save_name'], $_POST['id_table']);
		$q = "SELECT * FROM conf_relations WHERE id='$id'";
		$a = q($q);
		$aF = getFieldProperties($_POST['save_nto1TargetTable'], $_POST['save_nto1TargetField']);
		if (count($a)) {
		    $q = "UPDATE conf_relations SET type = 'nto1', 
			nto1TargetTable = '".e($_POST['save_nto1TargetTable'])."', 
			nto1TargetField = '$aF[name]', 
			nto1SourceTable = '".e($_POST['id_table'])."', 
			nto1SourceField = '".e($_POST['save_name'])."' 
			WHERE id='$id' ";
			$a = q($q);
		} else {
			$q = "INSERT INTO conf_relations SET type = 'nto1', 
			nto1TargetTable = '".e($_POST['save_nto1TargetTable'])."', 
			nto1TargetField = '$aF[name]', 
			nto1SourceTable = '".e($_POST['id_table'])."', 
			nto1SourceField = '".e($_POST['save_name'])."' ";
			$a = q($q);
		}
	}
	foreach ($_POST as $key => $value) {
		//echo "<br>".strstr($key, "save_") . "-" . strstr($key,"checkbox_") . " " .$key;
		if (is_array($value))
			$value = serialize($value);
		if (strstr($key, "save_") and $key != "field_id") {
			$query = "UPDATE conf_fields SET ".str_replace("save_","",$key)." = '".e($value)."', id_table = '".e($_POST['id_table'])."' WHERE id ='".e($_POST['field_id'])."'";
			//echo "<br /> normal".$query;
			dbQuery($query);
		}
		if (strstr($key,"checkbox_") and $key != "field_id") {//off modus checkboxen
			if ($_POST['save_'.str_replace("checkbox_","",$key)] != "on") {
				$query = "UPDATE conf_fields SET ".str_replace("checkbox_","",$key)." = 'off', id_table = '".e($_POST['id_table'])."' WHERE id ='".e($_POST['field_id'])."'";
				//echo "<br />checkbox ".$query;
				dbQuery($query);
			}
		}
	}
	echo "<script type=\"text/javascript\">window.opener.location.reload()</script>";
}
function deleteField() {
	if ($_GET['confirmed'] != true) {
		echo "<script type='text/javascript'>
			Check = confirm('Wollen Sie den Eintrag '+unescape(\"%F6\")+' loeschen?');
			if (Check == false) {
				window.close();
			 } else {
				window.location.href=\"edit_fields.php?confirmed=true&id=\"" . $_GET['id'] . ";
			 }
			</script>";
	} else {
		$query="DELETE FROM conf_fields WHERE id = '" . e($_GET['id']). "'";
		dbQuery($query);
		echo "<script type=\"text/javascript\">window.opener.location.reload()</script>";
		echo "<script type=\"text/javascript\">window.close()</script>";
	}
}
function backupMenu() {
	echo "<a href=\"backup.php?action=save\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_blank\" title=\"Backup der Datenbank erstellen\"><img src=\"".RELATIVEPATH."/image/icons/download-page-".$_SESSION['style_color'].".gif\"></a><br /><br />";
	echo "<div><b>Datenbank Backup laden</b> alle Daten nach dem Datum der Sicherung gehen verloren.</div><br />";
}
function saveBackup($sButton="on") {
	global $aDatabase;
	$backupFile = "backup/".@$dbname . date("d.m.Y-H-i-s")  . '.sql';
	echo $command = "mysqldump --opt -h$aDatabase[host] -u$aDatabase[user] -p$aDatabase[password] $aDatabase[dbname] > $backupFile";
	system($command, $fp);
	if ($fp==0) echo "<div style=\"border:1px solid green; width:350px; padding:5px; color:green; font-weight:bold\">Datenbank gesichert unter $backupFile.</div>"; else echo "<div style=\"border:1px solid red; width:350px; padding:5px; color:red; font-weight:bold\">ACHTUNG: Datenbank konnte nicht gesichert werden unter $backupFile. Fehler: $fp</div>";
	if ($sButton == "on") {
		echo "<br /><input type=\"button\" onclick=\"window.close();\" value=\"Schliessen\">";
		echo "<script type=\"text/javascript\">window.opener.location.reload()</script>";
	}
}
function loadBackup() {
	global $aDatabase;

	if (@$_GET['loadfile'] != "") {
		if ($_GET['confirmed'] != true) {
			echo "<script type='text/javascript'>
				Check = confirm('Wollen Sie wirklich die Sicherung ".$_GET[filename]." laden? Ein Backup des alten Stands wird erstellt.');
				if (Check == false) {
					window.close();
				 } else {
					window.location.href='backup.php?action=load&loadfile=".$_GET[loadfile]."&confirmed=true';
				 }
				</script>";
		} else {
			$d = dir("backup");
			while (false !== ($entry = $d->read())) {
				if ($entry != "." and $entry != ".." ) {
					$countFiles++;
					if ($countFiles == $_GET[loadfile]) {
                        saveBackup();
						$command = "mysql  -E -h$aDatabase[host] -u$aDatabase[user] -p$aDatabase[password] $aDatabase[dbname] < backup/".$entry;
						system($command, $fp);
						if ($fp==0) echo "<div style=\"border:1px solid green; width:350px; padding:5px; color:green; font-weight:bold\">Daten importiert von $entry.</div><br /><br />"; else echo "<div style=\"border:1px solid red; width:350px; padding:5px; color:red; font-weight:bold\">Es ist ein Fehler aufgetreten Fehler: $fp</div><br /><br />";
						//echo "<br /><input type=\"button\" onclick=\"window.close();\" value=\"Schliessen\"></div>";
						//Quelle: http://board.protecus.de/t9581.htm#ixzz0gR3vve4z
						//Quelle: http://board.protecus.de/t9581.htm#ixzz0gR3HEHpE
					}
				}
			}
			$d->close();
		}

	}
	$countFiles = 0;
	$d = dir("backup"); //ToDo

	while (false !== ($entry = $d->read())) {
		if ($entry != "." and $entry != ".." ) {
			$countFiles++;
			echo "<a href=\"backup.php?loadfile=$countFiles&action=load&filename=$entry\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\">".$entry." ".(round(filesize("backup/".$entry)/1024))." kB</a><br />";
			;
		}
	}
	$d->close();
}
function getUserDropdown($name, $title) {
    global $aDatabase;

    $re .= $title;
    $re .= "<select multiple name=\"users[]\">";
    $aUsers = unserialize($aRel[0][$name]);
    $q = "SELECT * FROM bncms_user";
    $r = dbQuery($q,"",1);
    foreach ($r as $k => $a){
        if (in_array($a[username], $aUsers))
            $s = "selected";
        else
            $s = "";
        $re .= "<option $s name=" . $a['id'] . ">" . $a['username'] . "</option>";
    }
    $re .= "</select>";
    return $re;
}
function editRelation() {
	global $aDatabase;

	if ($_GET['relation'] == "ntom") {
		if ($_GET['id_relation']) {
			$q = "SELECT * FROM conf_relations WHERE id = '".e($_GET['id_relation'])."'";
			$aRel = dbQuery($q);
			$d = " disabled";
		}
		if ($_GET[id_relation]) {
			$sourceTable = getTableProperties($aRel[0][table1]);
			$targetTable = getTableProperties($aRel[0][table2]);
			$query ="SELECT name, id FROM conf_tables WHERE is_assign_table != 'yes'";
		} else {
			$sourceTable = getTableProperties($_GET[id_table]);
			$targetTable = getTableProperties($_GET[ntomTargetTable]);
			$query ="SELECT name, id FROM conf_tables WHERE id != '".e($_GET['id_table'])."' and is_assign_table != 'yes'";
		}
		$aTables = dbQuery($query,'',1);

		//ntom
		$sOutputContent .= "<table width='100%'><tr>
		
		<form action=\"edit_fields.php?id_table=$_GET[id_table]&relation=$_GET[relation]&saveRelation=true\" method=\"post\">
		
		<td><div><h2>n zu m Relation</h2> zwischen der Tabelle ".$sourceTable[name]."  und der Tabelle";


		$sOutputContent .= "<input type=\"hidden\" name=\"id_table\" value=\"$_GET[id_table]\">";
		$sOutputContent .= "<br /><select name=\"ntomTargetTable\" $d>";
		$sOutputContent .= "<option value=\"\">Tabelle w&auml;hlen</option>";

		foreach ($aTables as $key => $value) {
			if ($_GET['ntomTargetTable'] == $value['id'] or $targetTable['name'] == $value['name'])
				$s = "selected";
			else
				$s = "";
			$sOutputContent .= "<option value=\"" . $value['id'] . "\" $s>Tabelle: " . $value['name'] . "</option>";
		}
		$sOutputContent .= "</select>";

		$query ="SHOW TABLES";
		$aAllTables = dbQuery($query);

		$sOutputContent .= "<br><br> Name einer bereits existierenden Zuweisungstabelle (optional)<br><select name='assign_table' onchange='this.form.submit()' $d>";
		$sOutputContent .= "<option value=\"\"></option>";
		foreach ($aAllTables as $k => $v) {
			if ($_GET[assign_table] == $v["Tables_in_".$aDatabase['dbname']] or  $aRel[0][name] == $v["Tables_in_".$aDatabase['dbname']])
				$s = "selected";
			else
				$s = "";
			$sOutputContent .= "<option $s>".$v["Tables_in_".$aDatabase['dbname']]."</option>";
		}
		$sOutputContent .= "</select><br>";
		if ($_GET[aAssignColumns] or $_GET[id_relation]) {
			if ($_GET[id_relation]) {
				$aAssignColumns = getFieldProperties($aRel[0]['id']);
			}
			$sOutputContent .= "<br> Feld der Zuweisungstabelle $_GET[assign_table], das die Urspungstabelle <b>$sourceTable[name]</b> anlinkt. <br><select name='assign_nto1field_source' $d>";
			$sOutputContent .= "<option value=\"\">Feld w&auml;hlen</option>";
			if ($_GET[id_relation]) {
				foreach ($aAssignColumns as $k => $v) {
					if ($aRel[0][ntomAssignFieldTable1] == $v[name])
						$s = "selected";
					else
						$s = "";
					$sOutputContent .= "<option $s>$v[name]</option>";
				}
			} else {
				foreach ($_GET[aAssignColumns] as $k => $v) {
					$sOutputContent .= "<option>$v[Field]</option>";
				}
			}
			$sOutputContent .= "</select><br>";

			$sOutputContent .= "<br> Feld der Zuweisungstabelle, das die Zieltabelle <b>$targetTable[name]</b> anlinkt. <br><select name='assign_nto1field_target' $d>";
			$sOutputContent .= "<option value=\"\">Feld w&auml;hlen</option>";
			if ($_GET[id_relation]) {
				foreach ($aAssignColumns as $k => $v) {
					if ($aRel[0][ntomAssignFieldTable2] == $v[name])
						$s = "selected";
					else
						$s = "";
					$sOutputContent .= "<option $s>$v[name]</option>";
				}
			} else {
				foreach ($_GET[aAssignColumns] as $k => $v) {
					$sOutputContent .= "<option>$v[Field]</option>";
				}
			}
			$sOutputContent .= "</select><br>";
		}

		$sOutputContent .= "<br /><br>Sichtbar f&uuml;r die Benutzer:<br>";
        $sOutputContent .= generateUserDropdown("users", $aRel[0][users], "class='userDropdown'");

		$sOutputContent .= "<br /><br>Editieren k&ouml;nnen die Benutzer:<br>";
        $sOutputContent .= generateUserDropdown("editors", $aRel[0][editors], "class='userDropdown'");

		$sOutputContent .= "<br /><br>L&ouml;schen k&ouml;nnen die Benutzer:<br>";
        $sOutputContent .= generateUserDropdown("deletors", $aRel[0][deletors], "class='userDropdown'");

		$sOutputContent .= "<br /><br>Hinzuf&uuml;gen k&ouml;nnen die Benutzer:<br>";
        $sOutputContent .= generateUserDropdown("addors", $aRel[0][addors], "class='userDropdown'");

		if ($_GET[id_relation]) {
			$sOutputContent .= "<input type='hidden' name='id_relation' value='$_GET[id_relation]'>";
			if ($aRel[0][seperateColumns] == "on")
				$c = "checked";
		}
		if ($aRel[0][ntomDisplayType] == "table")
			$dts = "selected";
		if ($aRel[0][ntomDisplayType] == "ajax")
			$das = "selected";
		$sOutputContent .= "
		<br /><br><input type='checkbox' name='seperateColumns' $c> Leere Zuweisungsfelder ausblenden und Kolumnennamen pro Eintrag einzeln anzeigen<br>
		<br /><br>
		<select name='ntomDisplayType' onchange='this.form.submit()'>
			<option value='table' $dts>Tabelle</option>
			<option value='ajax' $das>Ajax-Autocomplete, mehrfach Auswahl</option>
		</select>
		  Ansichtsart der Relation (ntomDisplayType), Ajax-Suche muss in der Relationskonfiguration von Quell- und Zieltabelle getrennt konfiguriert werden<br>";
		 if ($aRel[0][ntomDisplayType] == "ajax") {
		 	$sOutputContent .= "<select name='ntomAjaxDisplayTitleField'>";
			$q = "SELECT * FROM conf_fields WHERE id_table = '" . $targetTable['id'] . "'";
			$a = dbQuery($q);
			foreach ($a as $k => $v) {
				if ($aRel[0][ntomAjaxDisplayTitleField] == $v[name])
					$s = "selected";
				else
					$s = "";
				$sOutputContent .= "<option $s>$v[name]</option>";
			}
			$sOutputContent .= "</select> Feld in der Zieltabelle das als Titel für die Ajax Suche verwendet wird<br>
			<input type='number' name='ntomAjaxDisplayMinSelections' value='".$aRel[0][ntomAjaxDisplayMinSelections]."'> Mindest Anzahl Einträge für die Validierung<br>";

		 }
		$sOutputContent .= "<br><input type='Submit'  class='submit' value='Speichern und Schliessen'><br>
		<input onClick=\"window.close()\" type='button' class='submit' value='Zur&uuml;ck'>
		</div></td></form></tr></table>";
		echo $sOutputContent;
	}
}
function saveRelation() {
	global $DB;
	if ($_GET[relation] == "ntom") {
		if ($_POST[id_relation]) {
			//update
			echo $query="UPDATE conf_relations SET
			seperateColumns = '".e($_POST['seperateColumns'])."',
			users = '".serialize($_POST['users'])."',
			editors = '".serialize(['editors'])."',
			deletors = '".serialize($_POST['deletors'])."',
			addors = '".serialize($_POST['addors'])."',
			ntomDisplayType = '".e($_POST['ntomDisplayType'])."',
			ntomAjaxDisplayTitleField = '".e($_POST['ntomAjaxDisplayTitleField'])."',
			ntomAjaxDisplayMinSelections = '".e($_POST['ntomAjaxDisplayMinSelections'])."'
			WHERE id = '".e($_POST['id_relation'])."'";
			dbQuery($query);

			echo "<script type=\"text/javascript\">window.opener.location.reload();</script>";
			echo "<script type=\"text/javascript\">window.close()</script>";
			exit();
		} else {
			//insert
			if (!$_POST[assign_table]) {
				$query="SELECT name FROM conf_tables WHERE id = '".e($_POST['id_table'])."'";
				$aTable1=dbQuery($query);
				$query="SELECT name FROM conf_tables WHERE id = '".e($_POST['ntomTargetTable'])."'";
				$aTable2=dbQuery($query);
				//Relation erstellen
				$query="INSERT INTO conf_relations SET type = 'ntom', table1='".e($_POST['id_table'])."', table2='".e($_POST['ntomTargetTable'])."', name = 'assign_".$aTable1[0][name]."_".$aTable2[0][name]."' , ntomAssignFieldTable1='id_".$aTable1[0][name]."', ntomAssignFieldTable2='id_".$aTable2[0][name]."',
				users = '".serialize($_POST[users])."',
				editors = '".serialize($_POST[editors])."',
				deletors = '".serialize($_POST[deletors])."',
				addors = '".serialize($_POST[addors])."',
				ntomDisplayType = '".e($_POST[ntomDisplayType])."',
				ntomAjaxDisplayTitleField = '".e($_POST[ntomAjaxDisplayTitleField])."',
				ntomAjaxDisplayMinSelections = '".e($_POST[ntomAjaxDisplayMinSelections])."'";
				dbQuery($query);
				$id_relation = mysqli_insert_id($DB);

				//Assignment Tabelle in Test-Struktur schreiben
				$query="INSERT INTO conf_tables SET name = 'assign_".$aTable1[0][name]."_".$aTable2[0][name]."', is_assign_table = 'yes', id_relation = '".$id_relation."', conf_tables.insert = 'yes', columnNameOfId = 'id', editable = 'on'";
				mysqli_query($DB, $query);
				echo mysqli_error($DB);
				$idOfTable = mysqli_insert_id($DB);

				//todo aufr&auml;umen der felder deren relation nicht mehr existiert
				//Assignment Felder in Test-Struktur schreiben
				$query="INSERT INTO conf_fields SET mysqlType = 'int(20)', unchangeable = 'yes', type = 'nto1', name = 'id_".$aTable1[0][name]."',  id_table = '$idOfTable', nto1TargetTable = '".$aTable1[0][name]."', nto1TargetField = '".getIdName($aTable1[0][name])."' ";
				dbQuery($query);
				$query="INSERT INTO conf_fields SET mysqlType = 'int(20)', unchangeable = 'yes', type = 'nto1', name = 'id_".$aTable2[0][name]."',  id_table = '$idOfTable', nto1TargetTable = '".$aTable2[0][name]."', nto1TargetField = '".getIdName($aTable2[0][name])."' ";
				dbQuery($query);

			} else {
				//muss erst nto1 felder festlegen in Zuweisungstabelle
				if (!$_POST[assign_nto1field_source] or !$_POST[assign_nto1field_target]) {
					$q = "SHOW COLUMNS FROM $_POST[assign_table];";
					$_GET[aAssignColumns] = dbQuery($q);
					$_GET[table_id] = $_POST[table_id];
					$_GET[assign_table] = $_POST[assign_table];
					$_GET[ntomTargetTable] = $_POST[ntomTargetTable];
				} else {
					//Relation erstellen
					$query="INSERT INTO conf_relations SET 
					type = 'ntom', 
					table1='".e($_POST['id_table'])."', 
					table2='".e($_POST['ntomTargetTable'])."', 
					name='".e($_POST['assign_table'])."', 
					ntomAssignFieldTable1='".e($_POST['assign_nto1field_source'])."', 
					ntomAssignFieldTable2='".e($_POST['assign_nto1field_target'])."',
					users = '".serialize($_POST[users])."',
					editors = '".serialize($_POST[editors])."',
					deletors = '".serialize($_POST[deletors])."',
					addors = '".serialize($_POST[addors])."',
					ntomDisplayType = '".e($_POST[ntomDisplayType])."',
					ntomAjaxDisplayTitleField = '".e($_POST[ntomAjaxDisplayTitleField])."',
					ntomAjaxDisplayMinSelections = '".e($_POST[ntomAjaxDisplayMinSelections])."'";
					dbQuery($query);
					$id_relation = mysql_insert_id();

					$query="SELECT name FROM conf_tables WHERE id = '".e($_POST['id_table'])."'";
					$aTable1=dbQuery($query);
					$query="SELECT name FROM conf_tables WHERE id = '".e($_POST['ntomTargetTable'])."'";
					$aTable2=dbQuery($query);

					//todo columnNameOfId von assignment Table
					//Assignment Tabelle in conf_tables eintragen



					$query="INSERT INTO conf_tables SET 
					name = '".e($_POST['assign_table'])."', 
					is_assign_table = 'yes', 
					id_relation = '".mysql_insert_id()."',
					conf_tables.insert = 'yes',
					
					columnNameOfId = 'id', 
					editable = 'on'";
					mysqli_query($DB, $query);
					echo mysqli_error($DB);
					$idOfTable = mysql_insert_id();

					//Assignment Felder in Test-Struktur eintragen
					$sourceTable = getTableProperties($_POST[id_table]);
					$t = getMysqlType($_POST[assign_table], $_POST[assign_nto1field_source]);
					//todo aufr&auml;umen der felder deren relation nicht mehr existiert
					$query="INSERT INTO conf_fields SET 
					mysqlType = '".e($t)."', 
					unchangeable = 'yes', 
					type = 'nto1', 
					name = '".e($_POST['assign_nto1field_source'])."',  
					id_table = '$idOfTable', 
					nto1TargetTable = '$sourceTable[name]', 
					nto1TargetField = '".getIdName($sourceTable[name])."' ";
					dbQuery($query);

					$targetTable = getTableProperties($_POST[ntomTargetTable]);
					$t = getMysqlType($_POST[assign_table], $_POST[assign_nto1field_target]);
					$query="INSERT INTO conf_fields SET 
					mysqlType = '".e($t)."', 
					unchangeable = 'yes', 
					type = 'nto1', 
					name = '".e($_POST['assign_nto1field_target'])."',  
					id_table = '$idOfTable', 
					nto1TargetTable = '$targetTable[name]', 
					nto1TargetField = '".getIdName($targetTable[name])."' ";
					dbQuery($query);
				}
			}
			echo "<script type=\"text/javascript\">window.opener.location.href='edit_fields.php?relation=ntom&editRelation=true&id_relation=$id_relation&id_table=$_POST[id_table]';)</script>";
			echo "<script type=\"text/javascript\">window.close()</script>";
		}
	}
}
function getMysqlType($table, $field) {
	$q = "SHOW COLUMNS FROM $table";
	$c = dbQuery($q);
	//print_r($c);
	//echo $field;
	foreach ($c as $k => $v)
		if ($v[Field] == $field)
			return $v[Type];
}
function deleteRelation() {
	if ($_GET['confirmed_relation'] != true) {
		echo "<script type='text/javascript'>
			Check = confirm('Wollen Sie den Eintrag wirklich l'+unescape(\"%F6\")+'schen?');
			if (Check == false) {
				window.close();
			 } else {
				window.location.href='edit_fields.php?confirmed_relation=true&relation=ntom&id_relation=$_GET[id_relation]&deleteRelation=true';
			 }
			</script>";
	} else {
		$query="SELECT * FROM conf_relations as r, conf_tables as t WHERE (r.table1 = t.id or r.table2 = t.id ) and r.id = '".e($_GET['id_relation'])."'";
		$aToDelete = dbQuery($query);
		$aToDelete2 = dbQuery("SELECT * FROM conf_tables WHERE name = 'assign_".$aToDelete[0][name]."_".$aToDelete[1][name]."'");

		foreach ($aToDelete2 as $key => $value) {
			$query="DELETE FROM conf_fields WHERE id_table = '".$value['id']."'";
			dbQuery($query);
			$query="DELETE FROM conf_tables WHERE id = '".$value['id']."'";
			dbQuery($query);
		}
		$query="DELETE FROM conf_relations WHERE id = '".e($_GET['id_relation'])."'";
		dbQuery($query);
		echo "<script type=\"text/javascript\">window.opener.location.reload()</script>";
		echo "<script type=\"text/javascript\">window.close()</script>";
	}
}
function cleanUpDB() {
	$query="DELETE FROM conf_fields WHERE name = ''";
	dbQuery($query);
}

function writeDatabaseStructure() {
	global $aDatabase,$DB;
	echo "<h2>Struktur schreiben</h2>";
	//aufr&auml;umen
	cleanUpDB();
	//Arrays initialisieren aus den Daten der conf Tabellen
	$query="SELECT * FROM conf_tables";
	$aTables=dbQuery($query,"",1);
	$query="SELECT * FROM conf_fields";
	$aFields=dbQuery($query,"",1);
	$query="SELECT * FROM conf_relations";
	$aRelations=dbQuery($query,"",1);
	//echo "<pre>";
	//print_r($aTables);
	//Liste der Tabellen die bereits im mysql existieren
	$aMysqlListTables = getMysqlTableNames();
	//pr&uuml;fen ob Tabellen erstellt werden m&uuml;ssem
	$iTestNotProven=0;
	foreach ($aTables as $key => $value) {
		if (in_array($value[name],$aMysqlListTables)) {
			//Tabelle muss erstellt werden
		} else {
			if ($value[name]) {
			echo $query = "
CREATE TABLE `".$aDatabase['dbname']."`.`$value[name]` (
`".getIdName($value[name])."` INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY
) ENGINE = MYISAM ;";
			//echo $query."<br />";
			//$aQueryStack[] = $query.";";
			q($query);
			$iTestNotProven=1;
			$iQueryAction++;
			//dbQuery($query);
			}
		}
	}
	if ($iTestNotProven==1)
		echo "-> AKTION: Musste Tabellen erstellen.";
	else
		echo "-> Alle Tabellen schon erstellt.";
	//Pr&uuml;fen ob die Felder existieren
	//Liste der Felder der Tabellen die bereits im mysql existieren
	$iTestNotProvenChange=0;
	$iTestNotProvenAdd=0;
	foreach ($aTables as $iTableCount => $aTableProperties) {
		//echo $aTableProperties[name]."SHOW COLUMNS FROM $aTableProperties[name]<br>";
		//if ($aTableProperties[name] != "") {
			$arrTableColumnNames = dbQuery("SHOW COLUMNS FROM $aTableProperties[name]","",1	);
			//pre($aFields);
			foreach ($aFields as $iFieldCount => $aFieldProperties) {

				if ($aFieldProperties[id_table] == $aTableProperties['id']) {
					//echo $aFieldProperties[name]."<br>";
					$iDontMustWriteField = 0;

					foreach ($arrTableColumnNames as $key => $value) {
						//echo "<br />$aFieldProperties[name] == $value[Field]";
						if ($aFieldProperties['name'] == $value['Field']) {
							$iDontMustWriteField = 1;
						}
					}
					if ($iDontMustWriteField == 0) {
						//Muss Feld neu erstellen
						//'image','text','html','number','checkbox','date','price','nto1')
						$aMysqlQueryParts = mysqlFieldPropsForType($aFieldProperties[type]);

						if ($aFieldProperties[mysql_type_bez]) {
						$sNewType = strtolower($aFieldProperties[mysql_type_bez]);
						if ($aFieldProperties[length_values])
							$sNewType .= "($aFieldProperties[length_values])";
							$aMysqlQueryParts[3] = $sNewType;
						}

						$query = "ALTER TABLE `$aTableProperties[name]` ADD `$aFieldProperties[name]` ".stripslashes($aMysqlQueryParts[3])." ";
						//echo $query."<br />";
						//dbQuery($query);
						$iTestNotProvenAdd = 1;
						$aQueryStack[] = $query.";";
						$iQueryAction++;

					} else {
						//Muss Feld &auml;ndern?
						$query="SHOW COLUMNS FROM $aTableProperties[name] LIKE '$aFieldProperties[name]' ";
						$aActualColumn = dbQuery($query);
						//pre($aActualColumn);
						$aWantHaveColumn = mysqlFieldPropsForType($aFieldProperties[type]);

						if ($aWantHaveColumn[2] == "NOT NULL")
							$aWantHaveColumn[2] = "NO";

						$aWantHaveColumn[1] = str_replace(" ","", $aWantHaveColumn[1]);

						$aWantHaveColumn[0] = strtolower($aWantHaveColumn[0]);
						if ($aWantHaveColumn[0] == "decimal")
							$aWantHaveColumn[1] = $aWantHaveColumn[1].",2";
							//echo "<br><br>".$aWantHaveColumn[0]."($aWantHaveColumn[1]) != ".$aActualColumn[0][Type]." or ".$aWantHaveColumn[2]." != ".$aActualColumn[0]['Null']."<br><br>";
						$sNewType = strtolower($aFieldProperties[mysql_type_bez]);
						if ($aFieldProperties[length_values])
							$sNewType .= "($aFieldProperties[length_values])";
						//echo "<br>$aTableProperties[name] $aFieldProperties[name] $sNewType != ".$aActualColumn[0][Type]." and $aFieldProperties[mysql_type_bez] != ''";

						if ($sNewType != str_replace(" unsigned","",$aActualColumn[0][Type]) and $aFieldProperties[mysql_type_bez] != ""){
							//&Auml;nderung n&ouml;tig
							//print_r($aFieldProperties);
							$iTestNotProven=0;
							$query = "ALTER TABLE `$aTableProperties[name]` CHANGE `$aFieldProperties[name]` `$aFieldProperties[name]` ".$sNewType." ";
							$iTestNotProvenChange=1;
							$aQueryStack[] = $query.";";
							$iQueryAction++;
						}
					}
				}
			}
			//Reihenfolge der Felder
			//echo "<pre>";
			$query="SELECT * FROM conf_fields WHERE id_table='" . $aTableProperties['id'] . "'";
			$aFieldsOfTable=dbQuery($query,"",1);
			foreach ($aFieldsOfTable as $iFieldCount => $aFieldProperties) {
				//print_r($aFieldProperties);
				/*hole Feld mit letzttieferem Wert in mysql_order*/
				$query="SELECT * FROM conf_fields WHERE 
				id_table = '" . $aTableProperties['id'] . "' 
				and mysql_order < '$aFieldProperties[mysql_order]'
				ORDER by mysql_order DESC
				LIMIT 1;
				";
				$aLowerField=dbQuery($query);

				//hole von den Tieferen nur die, die den letzttieferen Wert gespeichert haben
				//print_r($aLowerFields);
				if (!is_array($aLowerField)) {
					//hat keine Tieferen Felder
					continue;
				} else {
					/*Hole alle Felder mit dem letztieferen Wert*/
					$query="SELECT * FROM conf_fields WHERE 
					id_table = '" . $aTableProperties['id'] . "' 
					and mysql_order = '".$aLowerField[0][mysql_order]."'
					";
					$aLowerFields=dbQuery($query);
					//finde heraus welches von den Feldern das letzte in der Tabelle ist
					$of = "";
					foreach ($aLowerFields as $k => $v) {
						if (is_array($v))
							$of .= " field = '".e($v[name])."' OR ";
					}
					$queryl="SHOW COLUMNS FROM $aTableProperties[name] WHERE $of field = 'sadfasfdasfasf'";
					$aFieldsMysql=dbQuery($queryl);
					foreach ($aFieldsMysql as $k => $v) {
						$aLastField = $v;
					}
					//finde heraus ob Feld nicht bereits in der position ist
					$query="SHOW COLUMNS FROM $aTableProperties[name]";
					$aFieldsMysql=dbQuery($query,"",1);
					$checkNext = 0;
					$position_correct = 0;
					foreach ($aFieldsMysql as $k => $v) {
						//if ($aFieldProperties[name]	== "client_name")
							//echo "$v[Field] == $aLastField[Field]<br>";
						if ($v[Field] == $aLastField[Field]) {
							//if ($aFieldProperties[name]	== "client_name")
							//echo "checknext $v[Field]";
							$checkNext = 1;
							continue;
						}
						if ($checkNext) {
							//if ($aFieldProperties[name]	== "client_name")
							//das nächste Feld zwischen welches und LastField eingefügt werden soll. Wenn selben mysql_order hat muss nicht position ändern, ansonsten würde es felder mit selben mysql_order immer neu ordnen
							$nextField = $v[Field];
							$nextFieldProp = getFieldProperties($aTableProperties['id'], $nextField);
							if ($nextFieldProp[mysql_order] == $aFieldProperties[mysql_order]) {
								$position_correct = 1;
							}
							if ($v[Field] == $aFieldProperties[name]) {
								//if ($aFieldProperties[name]	== "client_name")
								//	echo "Position_correct $v[Field]";
								$position_correct = 1;
							}
							break;
						}
					}
					//finde heraus ob felder zwischen dem aktuellen und dem nachsttieferen haben selben mysql_order wert wie aktuelles -> position korrekt
					//Felder zwischen diesem und tieferem auswählen
					//pre($aFieldsMysql);
					/*foreach ($aFieldsMysql as $k => $v) {
						if ($v[Field] == $aFieldProperties[name] or $v[Field] == $aLastField[Field])
							$select = 1;
					}*/
					//echo "<br>$aTableProperties[name] $aFieldProperties[name] $aFieldProperties[mysql_order] position_correct = $position_correct<br>";
					//einfügen des aktuellen Felds nach dem letzten Feld mit dem letzttieferen Wert
					if ($position_correct != 1) {
						$query="SHOW COLUMNS FROM $aTableProperties[name] WHERE field = '$aFieldProperties[name]'";
						$af=dbQuery($query);
						//if ($aFieldProperties[name] == "id")
							//print_r($af);
						if ($aFieldProperties[name] != $aLastField[Field]) {
							if ($af['Default'])
								$dq = " DEFAULT $af[Default] ";
							else
								$dq = "";
							if ($af[Null] == "YES")
								$nq = " NULL ";
							else
								$nq = " NOT NULL ";
							$q ="ALTER TABLE `$aTableProperties[name]` 
							MODIFY `$aFieldProperties[name]` ".$af[0][Type]." $dq $nq ".$af[0][Extra]."
							AFTER `$aLastField[Field]`";
							//echo "<br>";
							//$aQueryStack[] = $q.";";
							//$iQueryAction++;
							$iTestOrder = 1;
							mysqli_query($DB, $q);
						}
						/*echo $q;
						echo $queryl;

						echo "<br><br>aTableProperties[name]<br>";
						echo $aTableProperties[name];
						echo "<br><br>aFieldProperties<br>";
						print_r($aFieldProperties);
						echo "<br><br>aLowerField<br>";
						print_r($aLowerField);
						echo "<br><br>aLowerFields<br>";
						print_r($aLowerFields);
						echo "<br><br>aLastField<br>";
						print_r($aLastField);
						echo "<br><br>aFieldsMysql<br>";
						print_r($aFieldsMysql);
						echo $position_correct;
						//exit();*/
					}
				}
			}
		//ALTER TABLE `table_name` MODIFY `column_you_want_to_move` DATATYPE AFTER `column`
		//Pr&uuml;fen ob ntom Tabelle erstellen muss
		$iTestNotProvenAssign = 0;
		if (is_array($aRelations))
		foreach ($aRelations as $iCountRelation => $aRelationContent) {
				if ($aRelationContent[type] == "ntom" and ( $aTableProperties['id'] == $aRelationContent[table1] or $aTableProperties['id'] == $aRelationContent[table2])) {
					$query="SELECT name FROM conf_tables WHERE id = '$aRelationContent[table1]'";
					$aTableName1 = dbQuery($query);
					$sTableName1 = $aTableName1[0][name];
					$query="SELECT name FROM conf_tables WHERE id = '$aRelationContent[table2]'";
					$aTableName2 = dbQuery($query);
					$sTableName2 = $aTableName2[0][name];
					$sAssignTableName = "assign_".$sTableName1."_".$sTableName2;
					if (@in_array($sAssignTableName, $aMysqlListTables)) {
					} else {
						//print_r($aAlreadyDoneIds);
						//echo "($aAlreadyDoneIds, $aRelationContent['id'])";
						if ($aRelationContent['id'] != "") {
							if (@in_array($aRelationContent['id'], $aAlreadyDoneIds)) {
							} else {

								//Muss erstellen
								$query = "
			CREATE TABLE `".$aDatabase['dbname']."`.`$sAssignTableName` ( \n
			`id` INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,\n
			`id_$sTableName1` INT( 20 ) NOT NULL,\n
			`id_$sTableName2` INT( 20 ) NOT NULL \n
			) ENGINE = MYISAM";
								$iTestNotProvenAssign=1;
								$aQueryStack[] = $query.";";
								$iQueryAction++;
								$aAlreadyDoneIds[] = $aRelationContent['id'];
							}
						}
					}
				}
		}
	}

	//Ob Felder l&ouml;schen muss
	$iTestNotProvenDelete = 0;
	foreach ($aTables as $iTableCount => $aTableProperties) {
		//if ($aTableProperties[name]) {
			$aTableColumnNames = dbQuery("SHOW COLUMNS FROM $aTableProperties[name]","",1);
			foreach ($aTableColumnNames as $iMysqlFieldCount => $aMysqlFieldProperties) {
				if ($aMysqlFieldProperties[Field] != "id") {
				 	$query = "SELECT * FROM conf_fields WHERE name = '$aMysqlFieldProperties[Field]' AND id_table = '".$aTableProperties['id']."'";
					$aFieldExists = dbQuery($query);
					//pre($aFieldExists);
					$q = "SELECT * FROM conf_tables WHERE name = '$aTableProperties[name]'";
					$aTablesWithSameName = q($q,"",1);
					$dontDrop = 0;
					//pre($aTablesWithSameName);
					if (is_array($aTablesWithSameName))
					foreach ($aTablesWithSameName as $k => $v) {
						 $query = "SELECT * FROM conf_fields WHERE name = '$aMysqlFieldProperties[Field]' AND id_table = '".$v['id']."'";
						 $aFieldExistsOtherTables = dbQuery($query);
						 if (count($aFieldExistsOtherTables[0])) {
						 	//Feld existiert in anderer konfigurierten Tabelle mit selben Tabellenname
							$dontDrop = 1;
						}
					}
					if (count($aFieldExists[0]) == 0 and !$dontDrop) {
						$query = "ALTER TABLE `$aTableProperties[name]`
						DROP `$aMysqlFieldProperties[Field]`";
						$iTestNotProvenDelete=1;
						$aQueryStack[] = $query.";";
						$iQueryAction++;
					}
				}
			}
		//}
	}
	if ($iTestOrder == 1)
		echo "<br />-> REIHENFOLGE DER FELDER ge&auml;ndert.";
	else
		echo "<br />-> Keine &Auml;nderung an den der Reihenfolge der Felder.";
	if ($iTestNotProvenAssign == 1)
		echo "<br />-> Es wurden N ZU M TABELLEN HINZUGEF&Uuml;GT.";
	else
		echo "<br />-> Keine &Auml;nderung an den n zu m Tabellen.";

	if ($iTestNotProvenAdd == 1)
		echo "<br />-> Es wurden FELDER HINZUGEF&Uuml;GT.";
	else
		echo "<br />-> Keine &Auml;nderung an den Feldern.";

	if ($iTestNotProvenDelete == 1)
		echo "<br />-> Es wurden FELDER GEL&Ouml;SCHT.";
	else
		echo "<br />-> Keine L&ouml;schungen von Feldern.";


	if ($iTestNotProvenChange == 1)
		echo "<br />-> Es wurden FELD-EIGENSCHAFTEN ver&auml;ndert.";
	else
		echo "<br />-> Keine &Auml;nderung an den Feld-Eigenschaften.";

	if ($iQueryAction > 0)
		echo "<br />-> DIE DATENBANK-STRUKTUR MUSS GE&Auml;NDERT WERDEN. $iQueryAction Aktionen. Sie k&ouml;nnen zur letzen Datenbank Version zur&uuml;ckkehren via Datenbank Backup.<br />";
	else
		echo "<br />-> Keine Aktion. Alle Tabellen und Felder gleich.";
	if (is_array($aQueryStack)) {
		$_SESSION['aQueryStack'] = $aQueryStack;
		foreach ($aQueryStack as $key => $value) {
			$sQueryStack .= $value."<br />";
		}
		echo "<br><a href=\"edit_fields.php?sendQuerystack=true\" onClick=\"javascript: ajax_send_scrollpos('".$_SERVER['PHP_SELF']."');\" target=\"_blank\">Backup und Querystack abschicken</a><br /><br /><pre>".$sQueryStack."</pre>";
	} else {
		echo "<br /><br />Querystack ist leer.<br />";
	}
}
function mysqlFieldPropsForType($sType) {

	$aMySQLStatements['fields']['varchar250'][0] = "VARCHAR";
	$aMySQLStatements['fields']['varchar250'][1] = "250";
	$aMySQLStatements['fields']['varchar250'][2] = "NOT NULL";
	$aMySQLStatements['fields']['text'][0] = "TEXT";
	$aMySQLStatements['fields']['text'][1] = "";
	$aMySQLStatements['fields']['text'][2] = "NOT NULL";
	$aMySQLStatements['fields']['number'][0] = "INT";
	$aMySQLStatements['fields']['number'][1] = "20";
	$aMySQLStatements['fields']['number'][2] = "NOT NULL";
	$aMySQLStatements['fields']['checkbox'][0] = "SET";
	$aMySQLStatements['fields']['checkbox'][1] = "\'\', \'on\', \'off\'";
	$aMySQLStatements['fields']['checkbox'][2] = "NOT NULL";
	$aMySQLStatements['fields']['price'][0] = "DECIMAL";
	$aMySQLStatements['fields']['price'][1] = "10,2";
	$aMySQLStatements['fields']['price'][2] = "NOT NULL";
	$aMySQLStatements['fields']['html'][0] = "TEXT";
	$aMySQLStatements['fields']['html'][1] = "";
	$aMySQLStatements['fields']['html'][2] = "NOT NULL";
	$aMySQLStatements['fields']['set'][0] = "SET";
	$aMySQLStatements['fields']['set'][1] = "''";
	$aMySQLStatements['fields']['set'][2] = "NOT NULL";

	//$aMySQLStatements['fields']['set'] = " SET( '', 'on', 'off' ) NOT NULL ";
	if ($sType == "image" or
	$sType == "file" or
	$sType == "text" or
	$sType == "password") {
		$sMysqlFieldType="varchar250";
	}
	if ($sType == "date" or
	$sType == "nto1" or
	$sType == "number" ) {
		$sMysqlFieldType="number";
	}
	if ($sType == "price") {
		$sMysqlFieldType="price";
	}
	if ($sType == "checkbox") {
		$sMysqlFieldType="checkbox";
	}
	if ($sType == "html" or $sType == "tinymce" or $sType == "textarea"  ) {
		$sMysqlFieldType="text";
	}
	if ($sType == "textfield" or $sType == "ip") {
		$sMysqlFieldType="varchar250";
	}
	if ($sType == "url") {
		$sMysqlFieldType="text";
	}
	if ($sType == "dropdown") {
		$sMysqlFieldType="set";
	}
	//echo $sMysqlFieldType;
	$aOutput[0] = $aMySQLStatements['fields'][$sMysqlFieldType][0];
	$aOutput[1] = $aMySQLStatements['fields'][$sMysqlFieldType][1];
	$aOutput[2] = $aMySQLStatements['fields'][$sMysqlFieldType][2];
	if ($aMySQLStatements['fields'][$sMysqlFieldType][1] != "")
		$sOutDecimalrows="( ".$aMySQLStatements['fields'][$sMysqlFieldType][1]." )";
	else
		$sOutDecimalrows="";
	$aOutput[3] = " ".$aMySQLStatements['fields'][$sMysqlFieldType][0].$sOutDecimalrows." ".$aMySQLStatements['fields'][$sMysqlFieldType][2]." ";
	return $aOutput;
}
function getTypeFromMysqlProps($sMysqlType, $sFieldname) {
	if (strpos($sMysqlType,"(") != "") {
		$aMysqlType=explode("(",$sMysqlType);
		$aMysqlType[1]=str_replace(")","",$aMysqlType[1]);

		if ($aMysqlType[0] == "decimal") {
			$sReturnType = "price";
		}
		if ($aMysqlType[0] == "set") {
			$sReturnType = "set";
			$aMysqlTypeOption = explode(",",str_replace("'","",$aMysqlType[1]));
			if (count($aMysqlTypeOption) == 3 and in_array("on", $aMysqlTypeOption) and in_array("off", $aMysqlTypeOption)) {
				$sReturnType = "checkbox";
			}

		}
		if ($aMysqlType[0] == "varchar") {
			$sReturnType = "text";
		}
		if (checkInt($aMysqlType[0])) {
			$sReturnType = "number";
		}
	} else {
		if ($sMysqlType == "text") {
			$sReturnType = "html";
		}

	}
	//check auf nto1
	$aFieldname = explode("_", $sFieldname);
	if ($aFieldname[0] == "id" and count($aFieldname) == 2) {
		$sReturnType  = "nto1";
	}
	//echo $sReturnType;
	return $sReturnType;
}
function sendDatabaseStructureStack() {

	if ($_GET['confirmed_querystack'] != true) {
		echo "<script type='text/javascript'>
			Check = confirm('Wollen Sie die neue Datenbank-Struktur wirklich laden? Es wird zuvor ein automatisch ein Backup der Datenbank erstellt.');
			if (Check == false) {
				window.close();
			 } else {
				window.location.href='edit_fields.php?sendQuerystack=true&confirmed_querystack=true';
			 }
			</script>";
	} else {
		saveBackup("off");
		foreach ($_SESSION['aQueryStack'] as $key => $value) {
			dbQuery($value);
			echo "<br /><b>Der Query wird geschickt.</b><br /> ".$value;
			if (mysqli_error($DB))
			    echo "<div style=\"color:red\"><b>".mysqli_error($DB)."</b></div>";
			else
				echo "<div style=\"color:green\"><b>Keine Fehlermeldung</b></div>";
		}
		echo "<script type=\"text/javascript\">window.opener.location.reload()</script>
		<br>
		<input onClick=\"window.close()\" type='button' value='Zur&uuml;ck'>
		";
	}
}
function get_tables() {
	global $DB;

}
function getMysqlTableNames() {
	global $aDatabase, $DB;

	$tableList = array();
	$res = mysqli_query($DB,"SHOW TABLES");
	while($cRow = mysqli_fetch_array($res)) {
		$tableList[] = $cRow[0];
	}
	return $tableList;

	/*$RS = mysql_list_tables($aDatabase['dbname']);
	$aMysqlListTables = array();
	while ($a=mysqli_fetch_assoc($DB, $RS)) {
		foreach ($a as $key => $value) {
			array_push($aMysqlListTables, $value);
		}
	}
	return $aMysqlListTables;*/
}
function getDatabaseStructure() {
	global $DB;
	//aufr&auml;umen
	echo "<h2>Datenbankstruktur holen</h2>";
	cleanUpDB();
	//Arrays initialisieren aus den Daten der conf Tabellen
	$query="SELECT * FROM conf_tables";
	$aTables=dbQuery($query,"",1);
	//print_r($aTables);
	$query="SELECT * FROM conf_fields";
	$aFields=dbQuery($query,"",1);
	$query="SELECT * FROM conf_relations";
	$aRelations=dbQuery($query,"",1);
	 //Liste der Tabellen die bereits im mysql existieren
	$aMysqlListTables = getMysqlTableNames();
	//gibt es die Table im Test System KEINE AKTION
	/*foreach ($aMysqlListTables as $iListTablesCount => $aListTablesProperties) {
		$iFoundTable=0;
		foreach ($aTables as $iTableCount => $aTableProperties) {
			if (in_array($aListTablesProperties[name] == $aTableProperties[name])) {
				//Gibt es nicht in den
				$iFoundTable=1;
			}
		}
	}*/
	$iNotProvenNtom=0;
	$iNotProvenFieldProperties=0;
	//gibt es das Feld im Test System
	foreach ($aTables as $iTableCount => $aTableProperties) {
		//if ($aTableProperties[name]) {
			$query="SHOW COLUMNS FROM ".$aTableProperties[name];
			$aMysqlFields = dbQuery($query,"",1);
			foreach ($aFields as $iFieldCount => $aFieldProperties) {
				//print_r($aFieldProperties);
				if ($aFieldProperties[id_table] == $aTableProperties['id']) {
					$iFieldFound = 0;
					//echo "<br>tabelle: ".$aTableProperties[name];
					if (is_array($aMysqlFields)) {
						foreach ($aMysqlFields as $iMysqlFieldsCount => $aMysqlFieldProperties) {
							//echo "<pre>";
							//print_r($aMysqlFieldProperties);

							//echo "</pre>";
							if ($aMysqlFieldProperties[Field] == $aFieldProperties[name]) {
								//Feld gefunden
								$iFieldFound = 1;

								//length values einfügen
								preg_match('/\((.+)\)/',$aMysqlFieldProperties[Type],$t);
								//print_r($t);
								if ($aFieldProperties[length_values] !=  $t[1]) {
									$sQueryStack[]="UPDATE conf_fields SET length_values = '".mysqli_real_escape_string($DB,$t[1])."' WHERE id = '" . $aFieldProperties['id'] . "';\n";
								//echo "<br />Feld '$aFieldProperties[name]' gefunden in '$aTableProperties[name]' ";
								echo "<br />Feld '$aFieldProperties[name]' in '$aTableProperties[name]' length_values ist falsch";
									$iActionCount++;
								}
								//echo " $aMysqlFieldProperties[Type], $aFieldProperties[mysql_type_bez]";
								if (@strpos(" ".$aMysqlFieldProperties[Type], strtolower($aFieldProperties[mysql_type_bez])) != 1  or !$aFieldProperties[mysql_type_bez]) {
									if (strpos($aMysqlFieldProperties[Type],"("))
										$nt = strtoupper(substr($aMysqlFieldProperties[Type],0,strpos($aMysqlFieldProperties[Type],"(")));
									else
										$nt = strtoupper($aMysqlFieldProperties[Type]);
									$sQueryStack[]="UPDATE conf_fields SET mysql_type_bez = '".$nt."' WHERE id = '" . $aFieldProperties['id'] . "';\n";
								//echo "<br />Feld '$aFieldProperties[name]' gefunden in '$aTableProperties[name]' ";
									echo "<br />Feld '$aFieldProperties[name]' in '$aTableProperties[name]' mysql_type_bez ist falsch";
									$iActionCount++;
								}
								$aMysqlFieldProps = mysqlFieldPropsForType($aFieldProperties[type]);
								if (strtolower($aMysqlFieldProps[0]) == "decimal") {
									//echo "halos";
									$sMysqlTypeFilter = str_replace(",0)",")",$aMysqlFieldProperties[Type]);
								} else {
									$sMysqlTypeFilter = $aMysqlFieldProperties[Type];
								}
								//nto1
								$aFieldname = explode("_",$aFieldProperties[name]);
								if ($aFieldname[0] == "id" and count($aFieldname) == 2) {
									if ($aFieldProperties[type] != "nto1") {
									}
								} elseif ($aMysqlFieldProperties[Type] != $aFieldProperties[mysqlType]) {
									echo "<br />Feld '$aFieldProperties[name]' in '$aTableProperties[name]'";
									echo " Type ist falsch $aMysqlFieldProperties[Type] != $aFieldProperties[mysqlType]";

									//type updaten in Test Datenbank
									$sQueryStack[]="UPDATE conf_fields SET mysqlType = '".mysqli_real_escape_string($DB,$aMysqlFieldProperties[Type])."' WHERE id = '" . $aFieldProperties['id'] . "';\n";
									$iActionCount++;
									$iNotProvenFieldProperties=1;
								} else {
									//echo "Alles in Ordnung.";
									$iFieldFound = 1;
								}
								/*if ($aMysqlFieldProps[2]) {
									echo "null ist falsch";
								}*/
							}
						}
					}
				}
			}
		//}
	}
	//Gibt es nicht in dem Test System
	foreach ($aTables as $iTableCount => $aTableProperties) {
		//if ($aTableProperties[name]) {
			$query="SHOW COLUMNS FROM ".$aTableProperties[name];
			$aMysqlFields = dbQuery($query,"",1);
			if (is_array($aMysqlFields)) {
				foreach ($aMysqlFields as $iMysqlFieldsCount => $aMysqlFieldsProperties) {
					$iFoundField=0;
					foreach ($aFields as $iFieldCount => $aFieldProperties) {
						if ($aFieldProperties[id_table] == $aTableProperties['id']) {
							if ($aFieldProperties['name'] == $aMysqlFieldsProperties['Field']) {
								$iFoundField=1;
							}
						}
					}
					if ($iFoundField==0) {
						//if ($aMysqlFieldsProperties[Field] != "id") {
							//print_r($aMysqlFieldsProperties);
							//echo "getTypeFromMysqlProps($aMysqlFieldsProperties[Type], $aMysqlFieldsProperties[Field]);";
							//print_r($aMysqlFieldsProperties);
							$sNewType = getTypeFromMysqlProps($aMysqlFieldsProperties[Type], $aMysqlFieldsProperties[Field]);
							echo "<div style=\"color:red\">Feld '$aMysqlFieldsProperties[Field]' nicht gefunden in '$aTableProperties[name]'</div>";
							$sQueryStack[]="INSERT INTO conf_fields SET id_table = '" . $aTableProperties['id'] . "', name='" . $aMysqlFieldsProperties[Field] . "', mysqlType='".mysqli_real_escape_string($DB,$aMysqlFieldsProperties[Type])."' ;\n";
							$iActionCount++;
							$iNotProvenFieldProperties=1;
						//}
					}
				}
			//}
		} else {
			echo "geht nicht";
		}

		//Pr&uuml;fen ob ntom Eintrag erstellen muss
		foreach ($aMysqlListTables as $key => $value) {
			if (strpos($value, "ssign_") == "1") {
				$aAssignTableName=explode("_",$value);
				if (count($aAssignTableName) == 3) {
					$query="SELECT id FROM conf_tables WHERE name = '$aAssignTableName[1]'";
					$aId1 = dbQuery($query,"",1);
					$query="SELECT id FROM conf_tables WHERE name = '$aAssignTableName[2]'";
					$aId2 = dbQuery($query,"",1);
					$query="SELECT * FROM conf_relations WHERE type = 'ntom' and (table1 = '".$aId1[0]['id']."' or table2 = '".$aId1[0]['id']."') and (table1 = '".$aId2[0]['id']."' or table2 = '".$aId2[0]['id']."')";
					$aConfRelations=dbQuery($query,"",1);
					//print_r($aConfRelations);
					//echo count($aConfRelations);
					//echo $aConfRelations;
					if (!is_array($aConfRelations)) {
						//muzss erstellen
						$iNotProvenNtom=1;
						$sQueryStack[]="INSERT INTO conf_relations SET type = 'ntom', table1='".$aId1[0]['id']."', table2='".$aId2[0]['id']."';\n";
					}
				}
			}
		}
	}

	//Ob Felder l&ouml;schen muss in der conf tabelle
	foreach ($aTables as $iTableCount => $aTableProperties) {
		//if ($aTableProperties[name] != "") {
			$query="SHOW COLUMNS FROM ".$aTableProperties[name];
			$aMysqlFields = dbQuery($query,"",1);
			foreach ($aFields as $iFieldCount => $aFieldProperties) {
				if ($aFieldProperties[id_table] == $aTableProperties['id']) {
					if (is_array($aMysqlFields)) {
						$iFieldFound=0;
						foreach ($aMysqlFields as $key => $value) {
							if ($value[Field] == $aFieldProperties[name]) {
								$iFieldFound=1;
							}
						}
						if ($iFieldFound==0) {
							$iNotProvenDelete=1;
							$sQueryStack[]="DELETE FROM conf_fields WHERE id = '" . $aFieldProperties['id'] . "'; \n";
						}
					}
				}
			}
		//}
	}

	if ($iNotProvenDelete == 1)
		echo "-> AKTION Musste aus der Test Tabelle l&ouml;schen.";
	else
		echo "<br />-> Musste keine Felder aus der Test Tabelle l&ouml;schen.";
	if ($iNotProvenFieldProperties == 1)
		echo "<br />-> AKTION erforderlich an den Feld-Eigenschaften.";
	else
		echo "<br />-> Alles in Ordnung mit den Feldern.";
	if ($iNotProvenNtom == 1)
		echo "<br />-> AKTION erforderlich n zu m Relationen.";
	else
		echo "<br />-> Alles in Ordnung mit den n zu m Relationen.";
	if (is_array($sQueryStack)) {
		echo "<pre>";
		foreach ($sQueryStack as $key => $value) {
			echo $value;
			dbQuery($value,"",1);
			echo mysqli_error($DB);
		}
		echo "</pre>";
	}
}
function generateUserDropdown($sNameSelect, $sSelectedUsers, $sAddToSelect="", $aDisplayedUsers="") {

    $re = "<input type='hidden' name='".$sNameSelect."' value=''><select multiple name=\"".$sNameSelect."[]\" $sAddToSelect >";
    //echo $sSelectedUsers;
	if (!is_array($sSelectedUsers))
    	$aSelectedUsers = unserialize($sSelectedUsers);
	else
        $aSelectedUsers = $sSelectedUsers;

    if (in_array('webuser', $aSelectedUsers))
        $s = "selected";
    else
        $s = "";
    $re .= "<option name='webuser' $s>webuser</option>";

    //pre($aSelectedUsers);
    $q = "SELECT * FROM bncms_user_groups";
    $aAllUserGroups = q($q,"",1);


    foreach ($aAllUserGroups as $k => $vUserGroup){
        $q = "SELECT * FROM bncms_user WHERE gruppe = '" . $vUserGroup['id'] . "'";
        $aAllUsers = q($q, "", 1);
    	//Nur Gruppe anzeigen wenn in DisplayedUsers Array oder wenn DisplayUsers leer
        //checken ob Benutzername von Gruppe aktiv ist und dann Gruppe auch anzeigen
		//wenn Gruppe selbst nicht aktiv ist

        $iActiveUserInGroup = 0;
        foreach ($aAllUsers as $k => $vUser) {
            if (is_array($aSelectedUsers) and in_array($vUser[username], $aSelectedUsers))
            	$iActiveUserInGroup = 1;
        }
        //echo $iActiveUserInGroup."<br>";
        //pre($aSelectedUsers);
		if (is_array($aDisplayedUsers) and in_array($vUserGroup['name'], $aDisplayedUsers)
			or !is_array($aDisplayedUsers)
			or $iActiveUserInGroup == 1) {
            if (in_array($vUserGroup['name'], $aSelectedUsers))
                $s = "selected";
            else
                $s = "";
            $re .= "<option $s >$vUserGroup[name]</option>";

            foreach ($aAllUsers as $k => $vUser) {
                if (in_array($vUser[username], $aSelectedUsers))
                    $s = "selected";
                else
                    $s = "";
                if (is_array($aDisplayedUsers)) {
                    if (in_array($vUser[username], $aDisplayedUsers))
                        $re .= "<option $s >&nbsp;&nbsp;&nbsp;&nbsp;$vUser[username]</option>";
                } else {
                    $re .= "<option $s >&nbsp;&nbsp;&nbsp;&nbsp;$vUser[username]</option>";
                }
            }
        }

    }
    return $re .= "</select>";
}
function editTable() {
	$query="SELECT * FROM conf_tables WHERE id = '" . e($_GET['id']) . "'";
	$aTableConf=dbQuery($query);
	echo "<h2>Tabellen Eigenschaften ".$aTableConf[0][name]."</h2>";
	echo "<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"post\">";
	echo "<div>mySQL Tabellenname: <input type=\"text\" name=\"table_name\" value=\"".$aTableConf[0][name]."\">";
	echo "<br />Angezeigter Name: <input type=\"text\" name=\"table_lang\" value=\"".$aTableConf[0][lang]."\">";
	echo "<br />Spaltenname der Prim&auml;ren ID: <input type=\"text\" name=\"table_columnNameOfId\" value=\"".$aTableConf[0][columnNameOfId]."\">";
	echo "<br />MySQL-Bedingung: <input type=\"text\" name=\"table_mysql_condition\" value=\"".$aTableConf[0][mysql_condition]."\">";
	echo "<br />Sortierungsschl&uuml;ssel: <input type=\"text\" name=\"table_orderkey\" value=\"".$aTableConf[0][orderkey]."\">";
	echo "<br />Farbe Interaktives Frontend: <input type=\"text\" name=\"table_color\" value=\"".$aTableConf[0][color]."\">";


	echo "<br />Einträge pro Seite: <input type=\"number\" name=\"table_entries_per_page\" value=\"".$aTableConf[0][entries_per_page]."\">";
	echo "<br />Standartsortierung: ";

	echo "<input type='hidden' name='table_sort_order' value=''><select  name=\"table_sort_order\"><option></option>";
	$q = "SELECT * FROM conf_fields WHERE id_table = '" . e($_GET['id']). "'";
	$a = q($q);
	$aF = $aTableConf[0]['sort_order'];
	foreach ($a as $k => $v)	{
		if ($v['name'] == $aF)
			$s = "selected";
		else
			$s = "";
		echo "<option $s>$v[name]</option>";
	}
	echo "</select>";

	echo "<br />Standartsortierung Aufsteigend / Absteigend: ";

	echo "<input type='hidden' name='table_sort_order_ascdesc' value=''><select  name=\"table_sort_order_ascdesc\"><option></option>";
	if ($aTableConf[0]['sort_order_ascdesc'] == "asc")
		$sa = "selected";
	if ($aTableConf[0]['sort_order_ascdesc'] == "desc")
		$sd = "selected";
	echo "<option value=asc $sa>Aufsteigend</option>";
	echo "<option value=desc $sd>Absteigend</option>";
	echo "</select>";

	echo "<br />Export als XLS: ";
	if ($aTableConf[0][export_xls] == "on")
		$c = "checked";
	else
		$c = "";
	echo "<input type='checkbox' name='table_export_xls' $c>";

	echo "<br />Export als CSV: ";
	if ($aTableConf[0][export_csv] == "on")
		$c = "checked";
	else
		$c = "";
	echo "<input type='checkbox' name='table_export_csv' $c>";

    echo "<br /><br>Sichtbar f&uuml;r die Benutzer:<br>";
    echo generateUserDropdown("table_users",$aTableConf[0][users], "class='userDropdown'");

    echo "<br /><br>Editieren k&ouml;nnen die Benutzer:<br>";
    echo generateUserDropdown("table_editors",$aTableConf[0][editors], "class='userDropdown'");

    echo "<br /><br>L&ouml;schen k&ouml;nnen die Benutzer:<br>";
    echo generateUserDropdown("table_deletors",$aTableConf[0][deletors], "class='userDropdown'");

    echo "<br /><br>Hinzuf&uuml;gen k&ouml;nnen die Benutzer:<br>";
    echo generateUserDropdown("table_addors",$aTableConf[0][addors], "class='userDropdown'");

	echo "<br /><br>Aktualisieren Button anzeigen: ";
	if ($aTableConf[0]['actualize'] == "on")
		$c = "checked";
	else
		$c = "";
	echo "<input type='checkbox' name='table_actualize' $c>";

	if ($_GET['id'] != "")
		echo "<input type=\"hidden\" name=\"id\" value=\"" . $_GET['id'] . "\" >";
	echo "<br /><br /><br><input type=\"Submit\" class='submit' value=\"Speichern\"></div>";
	echo "</form>";
}

function cleanUserPermissionsSaveArray($a) {
	foreach ($a as $k => $v) {
		$v = htmlentities($v, null, 'utf-8');
		$a[$k] = str_replace("&nbsp;","",$v);
	}
	return $a;
}

function saveTable() {
	global $DB;

	$_POST['table_users'] = cleanUserPermissionsSaveArray($_POST['table_users']);
	$_POST['table_editors'] = cleanUserPermissionsSaveArray($_POST['table_editors']);
	$_POST['table_addors'] = cleanUserPermissionsSaveArray($_POST['table_addors']);
	$_POST['table_deletors'] = cleanUserPermissionsSaveArray($_POST['table_deletors']);

	foreach ($_POST as $k => $v) {
		if (strpos("a".$k, "table_") == 1) {
			if (is_array($v))
				$v = serialize($v);
			$queryAdd .= " ".str_replace("table_","",$k)." = '".e($v)."', ";
		}
	}
	if (!$_POST['table_export_xls'])
		$queryAdd .= " export_xls = '', ";
	if (!$_POST['table_export_csv'])
		$queryAdd .= " export_csv = '', ";
	if (!$_POST['table_actualize'])
		$queryAdd .= " actualize = '', ";
	$queryAdd = rtrim($queryAdd, ", ");

	if ($_POST['id'] == "") {
		$query = "INSERT INTO conf_tables SET $queryAdd";
	} else {
		$query = "UPDATE conf_tables SET $queryAdd WHERE id = '" . e($_POST['id']) . "'";
	}

	dbQuery($query);
	echo "<script type=\"text/javascript\">window.location.href='edit_fields.php';</script>";
	exit();
}
function removeTable() {
	if ($_GET['confirmed'] != true) {
		echo "<script type='text/javascript'>
			Check = confirm('Wollen Sie die Tabelle wirklich aus der Test-Struktur l'+unescape(\"%F6\")+'schen?');
			if (Check == false) {
				window.close();
			 } else {
				window.location.href='edit_fields.php?removeTable=true&confirmed=true&id=" . $_GET['id']. "';
			 }
			</script>";
	} else {
		$query="DELETE FROM conf_tables WHERE id = '" . e($_GET['id']) . "'";
		dbQuery($query);
		echo "<script type=\"text/javascript\">window.opener.location.reload()</script>";
		echo "<script type=\"text/javascript\">window.close()</script>";
	}
}
function duplicateTable() {
	global $DB;
	$query="SELECT * FROM conf_fields WHERE id_table = '" . e($_GET['id']) . "'";
	$aFieldsDest=dbQuery($query);
	$query="SELECT * FROM conf_tables WHERE id = '" . e($_GET['id']). "'";
	$aTableDest=dbQuery($query);
	$sNewTableName = $aTableDest[0][lang ]." Kopie";
	while (count($aTablesCount[0]) > 0) {
		$sNewTableName = $sNewTableName."copy";
		$query="SELECT * FROM conf_tables WHERE name = '$sNewTableName'";
		$aTablesCount=dbQuery($query);
	}

	$query="INSERT INTO conf_tables SET ";
	foreach ($aTableDest[0] as $key => $value) {
		if ($key == "lang") {
			$value = $sNewTableName;
		}
		if ($key != "id") {
			$query.=" `$key` = '".e($value)."', ";
		}
	}
	$query=preg_replace('/(, )$/im','',$query);
	dbQuery($query);

	$iNewTableId=mysqli_insert_id($DB);
	foreach ($aFieldsDest as $key2 => $value2) {
		$value2['id_table'] = $iNewTableId;
		$query="INSERT INTO conf_fields SET ";
		foreach ($value2 as $key => $value) {
			if ($key != "id") {
				$query.=" $key = '".e($value)."', ";
			}
		}
		$query=preg_replace('/(, )$/im','',$query);
		dbQuery($query);
	}
}
//todo tabellennamen d&uuml;rfen keine underscores und so enthalten
function getAssignmentTableName($table1, $table2) {
	global $aRel;
	foreach ($aRel['NToM'] as $k => $v) {
		if ($k == $table1) {
			foreach ($v as $k2 => $v2) {
				if ($v2['destTable'] == $table2) {
					return $v2['assignTable'];
				}
			}
		}
	}
}
function getAssignmentFieldNames($table1, $table2) {
	global $aRel;
	foreach ($aRel['NToM'] as $k => $v)
		if ($k == $table1)
			foreach ($v as $k2 => $v2)
				if ($v2['destTable'] == $table2)
					return array("sourceFieldname" => $v2['sourceFieldname'], "destFieldname" => $v2['destFieldname']);
}
function generateStructureGif() {
	global $aTable, $aRel;
	/*print_r($aRel);
	print_r($aTable);*/

	$aTableRelCounts=array();
	//Zusammensetzen der Level / Tabellen Arrays
	//rausfinden wieviele relationen bestehen pro tabelle
	foreach ($aTable as $iCountTable => $aTableContent) {
		foreach ($aRel[NTo1] as $sStartTable => $aRelContent) {
			//echo "$sStartTable == $aTableContent[name]";
			if ($sStartTable == $aTableContent[name]) {
				//echo "dsaf";
				$aTableRelCounts[$aTableContent[name]] = $aTableRelCounts[$aTableContent[name]] + 1;
			}
			foreach ($aRelContent as $key => $value) {
				if ($value == $aTableContent[name]){
					$aTableRelCounts[$aTableContent[name]] = $aTableRelCounts[$aTableContent[name]] + 1;
				}
			}
		}
	}
	//print_r($aTableRelCounts);
	asort($aTableRelCounts);
	$aTableRelCounts=array_reverse($aTableRelCounts);
	//print_r($aTableRelCounts);

	$aTemp = array();
	foreach ($aTableRelCounts as $key => $value){
		$aTemp[$value][$key][x] = 1;
	}
	foreach ($aTemp as $key => $value){
		$count++;
		$aTableLevels[$count][relations] = $key;
		$aTableLevels[$count][tables] = $value;
		$tableCount = 0;
		foreach ($value as $key2 => $value2) {
			$tableCount++;
		}
		$aTableLevels[$count][tableCount] = $tableCount;
	}

	foreach ($aTableRelCounts as $key => $value){
		//oberste Ebene, der mit den meisten Relationen in die mitte
	}
	foreach ($aTableLevels as $iLevel => $aLevelContent) {
		$iYVerschiebung = $iYVerschiebung + ($iLevel * 10);
		$iXVerschiebung = $iXVerschiebung + ($iLevel * 30);
		//Alles aus der Sicht der St&auml;rksten Tabelle anschauen
		$aCoords = calculateCircleCoords($aLevelContent[tableCount], $iXVerschiebung, $iYVerschiebung, 50);
		$count=0;
		foreach ($aLevelContent[tables] as $key => $value) {
			$aTempLevelContent[$iLevel][tables][$key][x] = $aCoords[$count][x];
			$aTempLevelContent[$iLevel][tables][$key][y] = $aCoords[$count][y];
			$count++;
		}
	}
	foreach ($aTempLevelContent as $iLevel => $aLevelContent) {
		$aTableLevels[$iLevel][tables] = $aLevelContent[tables];
	}

	//Zeichnen der Tabellen und Relationen
	//Bild starten
	header ("Content-type: image/png");
	$mein_bild = ImageCreate (980, 400);
	$blau = ImageColorAllocate ($mein_bild, 255, 255, 255);
	$gruen = ImageColorAllocate ($mein_bild, 50,148,0);
	$rot = ImageColorAllocate ($mein_bild, 255,0,25);
	$hellblau = ImageColorAllocate ($mein_bild,0,255,242);
	imagefilledarc($mein_bild,$aCoords[x],$aCoords[y],10,10,0,360,$rot, IMG_ARC_PIE);
	//Tabellen zeichnen
	foreach ($aTableLevels as $iLevel => $aLevelContent) {
		foreach ($aLevelContent[tables] as $sTableName => $aCoords) {
			imagefilledarc($mein_bild,$aCoords[x],$aCoords[y],10,10,0,360,$rot, IMG_ARC_PIE);
			// Name the font to be used (note the lack of the .ttf extension)
			$font = 'SomeFont';
			$sFontFile = "arial.ttf";
			imagefttext($mein_bild, 13, 0, $aCoords[x] + 10,$aCoords[y], $rot, $sFontFile, $sTableName);
		}
	}
	//Relationen zeichnen
	foreach ($aRel[NTo1] as $sStartTable => $aRelContent) {
		foreach ($aRelContent as $sFiels => $sDestTable) {
			//Suche nach Coords im Levels Array
			$iCoordsDestFound = 0;
			$iCoordsStartFound = 0;
			foreach ($aTableLevels as $iLevel => $aLevelContent) {
				foreach ($aLevelContent[tables] as $sTableName => $aCoords) {
					if ($sTableName == $sStartTable) {
						$iStartX = $aCoords[x];
						$iStartY = $aCoords[y];
						$iCoordsStartFound = 1;
					}
					if ($sTableName == $sDestTable) {
						$iDestX = $aCoords[x];
						$iDestY = $aCoords[y];
						$iCoordsDestFound = 1;
					}
					if ($iCoordsDestFound == 1 and $iCoordsStartFound == 1) {
						break;
					}
				}
				if ($iCoordsDestFound == 1 and $iCoordsStartFound == 1) {
					break;
				}
			}
			$oRandColor = ImageColorAllocate ($mein_bild, rand(0,255),rand(0,255),rand(0,255));
			imageline($mein_bild,$iStartX,$iStartY,$iDestX,$iDestY,$oRandColor);
		}
	}
	ImagePNG ($mein_bild);
}

function calculateCircleCoords($iElements, $iStartX, $iStartY, $iWidth) {
	$iAngle = 360 / $iElements;
	for ($i = 0; $i < $iElements; $i++) {
		$iDefWinkel = $i * $iAngle;
		$iNewX = $iStartX + ($iWidth * cos($iDefWinkel));
		$iNewY = $iStartY + ($iWidth * sin($iDefWinkel));
		$aElementCoords[$i][x] = $iNewX;
		$aElementCoords[$i][y] = $iNewY;
	}
	return $aElementCoords;
}
?>