<?php

function displayTable(
    $tableOrId/*1*/,
    $columnNameOfId/*2*/,
    $condition = ""/*3 obsolet*/,
    $queryOver = ""/*4*/,
    $queryOver2 = ""/*5*/,
    $editable = ""/*6 obsolet*/,
    $sComingFrom = ""/*7*/,
    $isNToMDisplayEditEntry = "no"/*8*/,
    $paging = "yes"/*9 obsolet*/,
    $sTableType = "normal"/*10*/,
    $limit = 10/*11 obsolet*/,
    $ajaxExec = 0/*12*/,
    $searchParams = 0/*13*/,
    $aGlobal = 0/*14*/,
    $checkable = 0/*15 obsolet*/,
    $searchable = "yes"/*16 obsolet*/,
    $showRelations = "yes"/*17 obsolet*/,
    $checkableFunction = ""/*18 obsolet*/,
    $additionalColumn = ""/*19*/,
    $columnsToDisplay = ""/*20*/,
    $aManualFieldProperties = array()/*21*/,
    $sUserFunction = ""/*22*/,
    $deletable = ""/*23*/,
    $addable = ""/*24*/,
    $sBeforeAjaxQueryString = ""/*25*/,
    $sComingFromRelations = ""/*26*/
)
{
    if ($condition == "null"/*3*/)
        $condition = "";
    if ($queryOver == "null"/*4*/)
        $queryOver = "";
    if ($queryOver2 == "null"/*5*/)
        $queryOver2 = "";
    if ($editable == "null"/*6*/)
        $editable = "";
    if ($sComingFrom == "null"/*7*/)
        $sComingFrom = "";
    if ($isNToMDisplayEditEntry == "null"/*8*/)
        $isNToMDisplayEditEntry = "no";
    if ($paging == "null"/*9*/)
        $paging = "yes";
    if ($sTableType == "null"/*10*/)
        $sTableType = "normal";
    if ($limit == "null"/*11*/)
        $limit = "10";
    if ($ajaxExec == "null"/*12*/)
        $ajaxExec = "0";
    if ($searchParams == "null"/*13*/)
        $searchParams = "0";
    if ($aGlobal == "null"/*14*/)
        $aGlobal = "0";
    if ($checkable == "null"/*15*/)
        $checkable = "0";
    if ($searchable == "null"/*16*/)
        $searchable = "yes";
    if ($showRelations == "null"/*17*/)
        $showRelations = "yes";
    if ($checkableFunction == "null"/*18*/)
        $checkableFunction = "";
    if ($additionalColumn == "null"/*19*/)
        $additionalColumn = "";
    if ($columnsToDisplay == "null"/*20*/)
        $columnsToDisplay = "";
    if ($aManualFieldProperties == "null"/*21*/)
        $aManualFieldProperties = "";
    if ($sUserFunction == "null"/*22*/)
        $sUserFunction = "";
    if ($deletable == "null"/*23*/)
        $deletable = "";
    if ($addable == "null"/*24*/)
        $addable = "";
    if ($sBeforeAjaxQueryString == "null"/*25*/)
        $sBeforeAjaxQueryString = "";
    if ($sComingFromRelations == "null"/*26*/)
        $sComingFromRelations = "";

    global $DB, $aRel, $aProp, $iInstaceId, $aTable, $aRightsHidden, $aRightsUnchangeable, $lastRowIdNToM, $lastTableNameNToM;
    global $sDisplayTableRecursivePath;
    global $aNTo1TablePath;
    global $aPagingRecursivePath;
    global $webuser;
    global $countDisplayTablesOnPage;
	
	// sBeforeAjaxQueryString wird nur gespeichert wenn nicht ajax-Datei ist oder leer ist
	if (strpos($sBeforeAjaxQueryString, "/ajax.php") == "" and $sBeforeAjaxQueryString)  {
		$GLOBALS['sBeforeAjaxQueryString'] = $sBeforeAjaxQueryString;
	}

    $countDisplayTablesOnPage++;

    //Muss Global machen damit überall verfügbar
    $GLOBALS['aManualFieldProperties'] = $aManualFieldProperties;

    /*$aTableProperties = getTableProperties($tableOrId);
	if (is_numeric($tableOrId)) {
		$table = $aTableProperties[name];
		$tableId = $aTableProperties[id];
	} else {
		$table = $tableOrId;
		$tableId = "";
	}*/
    //if ($webuser) {
    //echo $table,$condition;
    //	$a = selectRec($table,$condition);
    //	if (count($a) == 0) {
    //echo "Kein Eintrag.";
    //return;
    //}
    //}

    if ($aManualFieldProperties)
        if (is_array(unserialize($aManualFieldProperties)))
            $aManualFieldProperties = unserialize($aManualFieldProperties);
    //if ($aManualFieldProperties)
    //$aManualFieldProperties = unserialize($aManualFieldProperties);

    //ManualFieldProperties vorbereiten
    $aManualFieldProperties = prepareManualFieldProperties($aManualFieldProperties);
    overwriteRights($aManualFieldProperties);
    overwriteRelations($aManualFieldProperties);

    //Suchparameter durch ajax
    if ($searchParams and $searchParams != 's:1:"0";' and $searchParams != 's:0:"";') {
        $searchParams = unserialize($searchParams);
        foreach ($searchParams as $k => $v)
            if ($k != "page" and $k != "" and $k != "page_table" and $k != "place")
                $comparationSP[$k] = $v;
        if (!is_array($_SESSION[lastSP][$searchParams[page_table] . "-" . $searchParams[place]]))
            $_SESSION[lastSP][$searchParams[page_table] . "-" . $searchParams[place]] = array();
        $a = @array_diff($comparationSP, $_SESSION[lastSP][$searchParams[page_table] . "-" . $searchParams[place]]);
        $_GET[page] = $searchParams[page];
        if (count($a)) {
            $_GET[page] = 1;
        }
        $_SESSION[lastSP][$searchParams[page_table] . "-" . $searchParams[place]] = $comparationSP;
        if ($_GET[page] < 1)
            $_GET[page] = 1;
        $_GET[place] = $searchParams[place];
        $_GET[page_table] = $searchParams[page_table];
    }

    if ($aGlobal)
        $aGlobal = unserialize($aGlobal);
    if ($aGlobal) {
        //damit globale variablen funktionieren mit ajax
        if ($aGlobal['aPagingRecursivePath'])
            $aPagingRecursivePath = $aGlobal['aPagingRecursivePath'];
        if ($aGlobal['aNTo1TablePath'])
            $aNTo1TablePath = $aGlobal['aNTo1TablePath'];
        if ($aGlobal['sDisplayTableRecursivePath'])
            $sDisplayTableRecursivePath = $aGlobal['sDisplayTableRecursivePath'];
        if ($aGlobal['lastRowIdNToM'])
            $lastRowIdNToM = $aGlobal['lastRowIdNToM'];
        if ($aGlobal['lastTableNameNToM'])
            $lastTableNameNToM = $aGlobal['lastTableNameNToM'];
    }

    $p = func_get_args();
    //echo "<pre>";
    //print_r($p);
    //echo "</pre>";
    for ($i = 0; $i < 24; $i++) {
        if (!$p[$i])
            $p[$i] = "null";
    }

    if (!$ajaxExec) {
        $s = "";
        //$id = md5(rand(0,10000000000));
        //Generiere identifier für diese Auflistung aus Name der Seite, Position der Auflistung bei mehreren und Checksumme aus Parametern
        //$countDisplayTablesOnPage;
        $id = md5($_SERVER['PHP_SELF'] . $countDisplayTablesOnPage . md5(serialize($p)));
        $p[11] = $id; //ajaxExec
        if (strpos($_SERVER['REQUEST_URI'], "ajax.php")) {
            $p[24] = $sBeforeAjaxQueryString;
        } else {
            $p[24] = $_SERVER['REQUEST_URI']; //sBeforeAjaxQueryString
        }
        //pre($p);
        //$arg_list = urlencode( serialize($p));
        //speicher parameter in session und gib dem ajax aufruf nur den md5 mit
        $_SESSION[ajaxParameter][$id] = $p;
        /*echo "session vor ajax";
		$_SESSION[test] ="test";
		pre($_SESSION);
		echo "cookie vor ajax";
		print_r($_COOKIE);*/
        $par[id] = $id;
        //$par[sp] = serialize($searchParams);
        $arg_list = urlencode(serialize($par));
        $op .= "<img src='" . RELATIVEPATH . "/image/loading.gif' class='loa' style='$s;' id='l_$id'><div id='s_$id' style='$s;display:block;width:20px;'></div><div id='" . $id . "' style='margin:1px !important;'></div>";
        if (!$webuser) {
            $pa = "/admin";
        } else
            $pp = "?projectpath=";

        $op .= "<script>
		jQuery(function() {
			var in" . $id . " = setInterval(ci, 1000);
			function ci() {
				if (jQuery('#s_" . $id . "').visible()) {
					window.clearInterval(in" . $id . ");
					jQuery.ajax({
						method: \"POST\",
						url: \"" . RELATIVEPATH . $pa . "/ajax.php" . $pp . "\", 
						success: function(msg) {
							jQuery('#s_" . $id . "').hide();
							jQuery('#l_" . $id . "').hide();
							jQuery('#" . $id . "').hide().html(msg).slideDown('slow');
						},
						data: { 
							func: \"dt\", 
							param: \"$arg_list\" 
						}
					});
				}
			}
		});
		</script>";
        if ($sTableType == "ntom" or $sTableType == "nto1input" or $sTableType == "nto1output" or $sTableType == "noecho") {
            return $op;
        } else {
            echo $op;
            return;
        }
    } else {
        if ($tableOrId == "e") {
            //sicherheit, darf in ajax nur displayTable ausführen und die variablen werden zuvor in Sitzung geschrieben nur ajaxexec wird mitgegeben, muss testen ob irgendwie variablen manipulieren kann, todo
            $pn = $_SESSION[ajaxParameter][$p[11]];
            $pn[11] = $p[11];
            if ($p[12] and $p[12] != null)
                $pn[12] = $p[12];
            //print_r($pn);
            /*pre($_SESSION[ajaxParameter]);*/

            if ($pn[9] == "ntom" or $pn[9] == "nto1input" or $pn[9] == "nto1output" or $pn[9] == "noecho") {
                call_user_func_array('displayTable', $pn);
            } else {
                echo call_user_func_array('displayTable', $pn);
            }
            exit();
        } else {
            //pre($p);
            //echo $_SESSION[ajaxParameter][$p[11]][0];
            $t = getTableProperties($_SESSION[ajaxParameter][$p[11]][0], $aManualFieldProperties);
            //print_r($t);
            //$t = getTableProperties("sdasdasdaf");
            if ($t) {
                $pn = $_SESSION[ajaxParameter][$p[11]];
                $arg_list = urlencode(serialize($pn));
            } else {
                //wurde wohl ausgeloggt, muss reloaden
                /*print_r( $aManualFieldProperties);
				pre($p);
				pre($_SESSION[ajaxParameter]);
				print_r($t);*/
                //exit();#
                if (!$webuser) {
                    echo "<script>window.location.reload();</script>";
                    exit();
                }
            }
        }
    }

    //echo "Hu";
    //exit("hu");
    //Muss aManualFieldProperties in Sitzung schreiben damit in Edit-Form verfügbar
    $_SESSION[aManualFieldProperties][displayTable][$ajaxExec] = $aManualFieldProperties;

    $table = returnTableAndId($tableOrId, $aManualFieldProperties)[0];
    $tableId = returnTableAndId($tableOrId, $aManualFieldProperties)[1];

    $aTableProperties = getTableProperties($tableOrId, $aManualFieldProperties);

    if (!$sComingFromRelations)
        $sComingFromRelations = $tableOrId;
    //editable, deletable, addable werden erst aus Tabelleneigenschaften gelesen und von Parametern überschrieben
    if (!$webuser)
        $tU = $_SESSION[user];
    else
        $tU = $webuser;
    if (!$editable)
        if (checkPermission($aTableProperties[editors], $tU, $_SESSION[userGroup]))
            $editable = "yes";
    if (!$deletable)
        if (checkPermission($aTableProperties[deletors], $tU, $_SESSION[userGroup]))
            $deletable = "yes";
    if (!$addable)
        if (checkPermission($aTableProperties[addors], $tU, $_SESSION[userGroup]))
            $addable = "yes";

    $aNTo1TablePath[] = c($table) . "-" . $tableId;
    if ($sComingFrom) {
        if (strpos($sComingFrom, "_")) {
            preg_match("/(_[0-9]+$)/", $sComingFrom, $t);
            $aPagingRecursivePath[str_replace($t[1], "", $sComingFrom)] = str_replace("_", "", $t[1]);
        }
    } else
        $aPagingRecursivePath = array();
    //echo $ajaxExec." ";
    $place = md5($ajaxExec . @implode("/", $aPagingRecursivePath));
    //echo "<br>";
    if (strpos("a" . $table, "assign_") == 1) {
        $sIsAssignmentTable = "yes";
    } else {
        $sIsAssignmentTable = "no";
    }
    //echo "<pre>";
    //print_r($aPagingRecursivePath);
    //echo $sComingFrom;
    //echo "<br>".$place;
    //echo "</pre>";
    //print_r($table);
    if (is_array($table)) {
        return;
    }

    $sDisplayTableRecursivePath++;
    $aDisplayTableRecursivePath[$sDisplayTableRecursivePath] = $sComingFrom;
    if ($tableId)
        $aTableProp = getTableProperties($tableId, $aManualFieldProperties);
    else
        $aTableProp = getTableProperties($table, $aManualFieldProperties);
    //paging
    //Seite auf null zur&uuml;cksetzen wenn Relationsfilter ausgef&uuml;hrt wird

    //pre($searchParams);
    //Liest suchparameter in Sitzung
    if (is_array($searchParams)) {
        if ($searchParams[place])
            $_SESSION[aActiveSearchesRelations][$searchParams[place]][$searchParams[page_table]] = $searchParams;
    }

    //limit von conf_tables nehmen
    if ($aTableProp[entries_per_page]) {
        $limit = $aTableProp[entries_per_page];
    }
    if ($paging == "yes") {
        if ($_POST['execFilter'])
            $_GET['page'] = $table . "_page_0";
        if ($_GET['page'] != "") {
            //$aPage=explode("_page_",$_GET['page']);
            if ($_GET[place])
                $_SESSION[aActivePagesRelations][$_GET[place]][$_GET['page_table']] = $_GET['page'];
            else
                $_SESSION[aActivePages][$_GET['page_table']] = $_GET['page'];
        }
        //erstellt Limit für Anfrage von Aktiven Seiten
        //echo "<pre>";
        //print_r($_SESSION[aActivePagesRelations]);
        //print_r($_GET);
        //echo "</pre>";

        $limitSql = buildLimit($place, $table, $tableId, $limit);

    }
    //$op .= $limitSql;
    //Aktuelle Tabelle auf visible schalten
    if ($aPage[0] != "") {
        /*$op .=  "<script type='text/javascript'>
		an('$aPage[0]');
		</script>";*/
    }

    if ($condition == "" and !$queryOver2)
        $sc = " 1=1 ";

    //Wenn Bedingung bereits festgelegt ist durch verbundenen Inhalt, aber manuell oder in Grundkonfiguration zusätzlich eine eingetragen ist, hänge sie zusammen
    if ($aTableProp['mysql_condition'] and $condition)
        $condition = $condition . " AND " . $aTableProp['mysql_condition'];
    elseif ($aTableProp['mysql_condition'] and !$condition)
        $condition = $aTableProp['mysql_condition'];

    //pre($_SESSION[aActiveSearchesRelations]);
    //Erstellt Suchfilter ab den aktiven Suchfeldern für Relationen
    //pre($_SESSION[aActiveSearchesRelations]);
    if (is_array($_SESSION[aActiveSearchesRelations])) {
        foreach ($_SESSION[aActiveSearchesRelations] as $sPlace => $aActiveTable) {
            //echo "$sPlace == $place";
            if ($sPlace == $place) {
                foreach ($aActiveTable as $sActiveTable => $sessionsp) {
                    //echo "$sActiveTable == $table";
                    if ($sActiveTable == $table . "-" . $tableId) {
                        $selectPortion = str_replace(strstr($queryOver2, "FROM"), "", $queryOver2);
                        if ($sessionsp[order]) {
                            $fieldExists = getFieldProperties($table, $sessionsp[order], $aManualFieldProperties);
                            //wenn queryover2 und feldname kommt in queryover select teil vor und wenn Feld nicht gibt in $table
                            //echo "(".count(fieldExists)." and ".strlen($queryOver2)." and ".strpos($selectPortion, $sessionsp[order]);
                            if (!count($fieldExists)
                                and strlen($queryOver2)
                                and strpos($selectPortion, str_replace(" DESC", "", $sessionsp[order])))
                                $orderSql = " " . e($sessionsp[order]) . " ";
                            else
                                $orderSql = " $table." . e($sessionsp[order]) . " ";
                        }
                        if ($sessionsp) {
                            //pre($sessionsp);
                            foreach ($sessionsp as $k => $v) {
                                if (strpos($k, "earch_") == 1) {
                                    $fieldName = str_replace("search_", "", $k);
                                    $fieldName = str_replace("_bncmstodate", "", $fieldName);
                                    $fieldName = str_replace("_bncmsfromdate", "", $fieldName);
                                    $fieldName = str_replace("_bncmstoint", "", $fieldName);
                                    $fieldName = str_replace("_bncmsfromint", "", $fieldName);
                                    $fieldExists = getFieldProperties($tableOrId, $fieldName, $aManualFieldProperties);
                                    //echo "$table, $fieldName";
                                    //pre($fieldExists);
                                    //echo !count($fieldExists)." and ".strlen($queryOver2)." and ".strpos($selectPortion, $fieldName);
                                    //Hol aus queryover betreffende Funktion der Auswahlbezeichnung
                                    preg_match("/([\(\)a-zA-Z0-9_\.]+) *[as]{0,2} +$fieldName/", $selectPortion, $t);
                                    //pre( $fieldExists);
                                    //echo "$table, $fieldName";
                                    if (!count($fieldExists)
                                        and strlen($queryOver2)
                                        and strpos($selectPortion, $fieldName)) {
                                        if ($t[1])
                                            $sh .= " HAVING " . $t[1] . " = '" . e($v) . "' ";
                                    } else {
                                        if ($fieldExists[type] == "date") {
                                            if ($sessionsp["search_" . $fieldName . "_bncmsfromdate"]) {
                                                $a = explode(" ", $sessionsp["search_" . $fieldName . "_bncmsfromdate"]);
                                                $afd = explode(".", $a[0]);
                                                $aft = explode(":", $a[1]);
                                                $sc .= " AND $table." . e($fieldName) . " >= '" . mktime($aft[0], $aft[1], $aft[2], $afd[1], $afd[0], $afd[2]) . "' ";
                                            }
                                            if ($sessionsp["search_" . $fieldName . "_bncmstodate"]) {
                                                $a = explode(" ", $sessionsp["search_" . $fieldName . "_bncmstodate"]);
                                                $atd = explode(".", $a[0]);
                                                $att = explode(":", $a[1]);
                                                $sc .= " AND $table." . e($fieldName) . " <= '" . mktime($att[0], $att[1], $att[2], $atd[1], $atd[0], $atd[2]) . "' ";
                                            }
                                        } elseif ($fieldExists[type] == "nto1" and $fieldExists[nto1DropdownTitleField]) {
                                            //echo "hu";
                                            $av = explode(",", $v);
                                            //pre($av);
                                            $aT = getTableProperties($fieldExists[nto1TargetTable], $aManualFieldProperties);
                                            $aF = getFieldProperties($fieldExists[nto1TargetTable], $fieldExists[nto1TargetField], $aManualFieldProperties);
                                            $sv = "";
                                            foreach ($av as $k1 => $v1) {
                                                //$v1 = str_replace(" ","",$v1);
                                                if ($v1)
                                                    $sv .= " '$v1' = (SELECT $aT[name].$fieldExists[nto1DropdownTitleField] FROM $aT[name] WHERE $aT[name].$aF[name] = $table." . e($fieldName) . ") OR";
                                            }
                                            if ($sv)
                                                $sc .= " AND ($sv 1=0) ";
                                        } elseif (checkInt($fieldExists['mysql_type_bez']) or $fieldExists['type'] == "number") {
                                            if ($sessionsp["search_" . $fieldName . "_bncmsfromint"]) {
                                                $sc .= " AND $table." . e($fieldName) . " >= '" . $sessionsp["search_" . $fieldName . "_bncmsfromint"] . "' ";
                                            }
                                            if ($sessionsp["search_" . $fieldName . "_bncmstoint"]) {
                                                $sc .= " AND $table." . e($fieldName) . " <= '" . $sessionsp["search_" . $fieldName . "_bncmstoint"] . "' ";
                                            }
                                        } else {
                                            $sc .= " AND $table." . $fieldName . " LIKE '%" . e($v) . "%' ";
                                        }
                                    }
                                }
                            }
                        }
                        $dontDelete = 1;
                        break;
                    }
                }
                if ($dontDelete != 1)
                    $sessionsp = "";
            }
        }
    }
    //echo $orderSql;
    //nimm standart sortierung und limit aus conf_tables
    if (!$orderSql and $aTableProp[sort_order]) {
        $orderSql = " $table.$aTableProp[sort_order] $aTableProp[sort_order_ascdesc] ";
    }

    /*echo "<pre>";
echo "$table-$tableId";
echo "searchparams";
print_r($searchParams);
		print_r($_SESSION[aActiveSearchesRelations]);
		echo "sessionsp";
		print_r($sessionsp);
		echo "sc";
		print_r($sc);
	echo "</pre>";*/
    //echo "vor query";
    $sc = $sc . $sh;
    if ($queryOver2 != "") {
        //echo "queryover";
        $arrTableCount = array();
        //print_r($queryOver2);
        if ($limitSql)
            $ls = " LIMIT " . $limitSql;
        if ($orderSql)
            $os = " ORDER BY " . $orderSql;
        if ($sc) {
            if (strpos($sc, "AND") != 1 and strpos($sc, "AND") != 2 and strpos($sc, "and") != 1 and strpos($sc, "and") != 2)
                $sco = " AND " . $sc;
            else
                $sco = " " . $sc;
        }
        $queryOverWithoutSearch = $queryOver2;
        //Todo order muss auch mitgegeben werden wenn seite und suche geändert werden
        if (preg_match('/(.+)group by (.+)/is', $queryOver2, $t)) {
            $queryOver2 = $t[1] . $sco . " GROUP BY " . $t[2];
        } else
            $queryOver2 = $queryOver2 . $sco;

        $q = $queryOver2 . $os . $ls;
        //$op .=  $q;
        if (strpos($q, "ORDER"))
            $q = str_replace(strrchr($q, "ORDER"), "", $q);
        $q2 = "SELECT count(*) " . strstr($q, "FROM");
        //$q2 = str_replace("SELECT", "SELECT count(*),", $q);
        $countAllEntries = dbQuery($q2, "", 1);

        if ($_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] * $limit > $countAllEntries[0]['count(*)']) {
            $_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] = ceil($countAllEntries[0]['count(*)'] / $limit);
            $limitSql = buildLimit($place, $table, $tableId, $limit);
            if ($limitSql)
                $ls = " LIMIT " . $limitSql;
            $q = $queryOver2 . $os . $ls;
            $query_export = $queryOver2 . $os;
        }
        //echo $q;
        $arrTableContent = dbQuery($q);

        //print_r($countAllEntries);
    } else {
        //echo "normal";
        if ($_SESSION['NTo1Filter'][$table] != "") {
            $identifierNTo1 = getIdentifierFromSourceTableNTo1($table);
            $condition = $condition . " $identifierNTo1 = '" . $_SESSION[NTo1Filter][$table] . "' ";
        }
        if ($condition)
            $condition = "(" . $condition . ")";

        if (preg_match("/ id = '[0-9]+'/", $condition)) {
            $limitSql = "";
            $q = "SELECT count(*) as count FROM $table WHERE $condition";
            $countAllEntries = dbQuery($q, "", 1);
        } else {
            $conditionOut = " WHERE " . $condition . " " . $sc;
            $q = "SELECT count(*) as count FROM $table $conditionOut";
            $countAllEntries = dbQuery($q, "", 1);
        }
        //muss limit neu bilden wenn zu gross eingegeben wird
        if ($_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] * $limit > $countAllEntries[0]['count']) {
            $_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] = ceil($countAllEntries[0]['count'] / $limit);
            $limitSql = buildLimit($place, $table, $tableId, $limit);
        }
        //echo  "$table, $condition $sc, $limitSql, $orderSql";
        //echo $condition." ".$sc;
        $arrTableContent = selectRec($table, $condition . " " . $sc, $limitSql, $orderSql);
        $query_export = "SELECT * FROM $table WHERE 1=1 AND $condition $sc";
        if ($orderSql)
            $query_export .= " ORDER BY $orderSql";
    }

    if ($countAllEntries[0]['count'] == 0 and $countAllEntries[0]['count(*)'] == 0) {
        $ke = "<tr><td colspan=100><center>Keine Einträge.</center></td></tr>";
    }
    //pre($arrTableContent);
    //$op .=  "</pre>";
    $arrDepth = ArrayDepth($arrTableContent);
    if ($arrDepth == 1) {
        $arrTemp[1] = $arrTableContent;
        $arrTableContent = $arrTemp;
    }

    //Den Schl&uuml;ssel des obersten Arrays in den Wert der Id umwandeln
    if (is_array($arrTableContent)) {
        foreach ($arrTableContent as $key => $value) {
            if (is_numeric($key)) {

                foreach ($value as $key2 => $value2) {
                    if ($key2 == getIdName($table, $aManualFieldProperties)) {
                        //Wenn Werte aus Zuordnungstabelle muss Id von ZielTabelle plus Id von Zuordnungstabelle nehmen ansonsten kann nicht mehrmals selben Zieleintrag anzeigen
                        if ($value[bncms_assign_id])
                            $newKey = $value2 . "-" . $value[bncms_assign_id];
                        else
                            $newKey = $value2;
                        $value = removeAssociativeKeyFromArray($value, 'bncms_assign_id');
                        $arrNewTableContent[$newKey] = $value;
                    }
                }
            }
        }
    }
    $arrTableContent = $arrNewTableContent;
    if (@$sessionsp[order])
        $ot = $sessionsp[order];
    $op .= "<form method='post' action='' id='f_" . $ajaxExec . "'><input type=hidden value='" . $arg_list . "' id=arg_list_" . $ajaxExec . "><input type=hidden id=page_table value='$table-$tableId' class='c" . $ajaxExec . "'>
			<input type=hidden id=order name=order value='$ot' class='c" . $ajaxExec . "'>
			<input type=hidden id=place value=$place class='c" . $ajaxExec . "'>";
    if ($aTableProp[export_xls] or $aTableProp[export_csv]) {
        $op .= "<div class='expd'>";
        if ($aTableProp[export_xls]) {
            if (!$webuser)
                $op .= "<a href='" . RELATIVEPATH . "/admin/export.php?t=xls&f=$ajaxExec' target='blank' class='exp'>Export als XLS</a> ";
            else
                $op .= "<a href='" . RELATIVEPATH . "/export.php?t=xls&f=$ajaxExec' target='blank' class='exp'>Export als XLS</a> ";

            $_SESSION[$ajaxExec][export][query] = $query_export;
            $_SESSION[$ajaxExec][export][table] = $tableId;
            $_SESSION[$ajaxExec][export][userFunction] = $sUserFunction;
            $_SESSION[$ajaxExec][export][manualFieldProperties] = $aManualFieldProperties;
        }
        if ($aTableProp[export_csv]) {
            if (!$webuser)
                $op .= "<a href='" . RELATIVEPATH . "/admin/export.php?t=csv&f=$ajaxExec' target='blank' class='exp'>Export als CSV</a> ";
            else
                $op .= "<a href='" . RELATIVEPATH . "/export.php?t=csv&f=$ajaxExec' target='blank' class='exp'>Export als CSV</a> ";
            $_SESSION[$ajaxExec][export][query] = $query_export;
            $_SESSION[$ajaxExec][export][table] = $tableId;
            $_SESSION[$ajaxExec][export][userFunction] = $sUserFunction;
            $_SESSION[$ajaxExec][export][manualFieldProperties] = $aManualFieldProperties;
        }
        $op .= "</div>";
    }

    $op .= "<div id='loading_overlay'></div>
		<div class='bs-example'>
		<div class='table-responsive'>
<table class=\"$table $ajaxExec table table-bordered\" cellspacing=\"0px\" cellpadding=\"0px\" id='loading_overall_table'>
<tbody>
<tr class=\"table_head\">";
    //Paging
    if ($paging == "yes" or ($sessionsp)) {
        //print_r($countAllEntries[0]);
        if ($countAllEntries[0][count])
            $iMaxPages = ceil($countAllEntries[0]['count'] / $limit);
        else
            $iMaxPages = ceil($countAllEntries[0]['count(*)'] / $limit);
        if ($iMaxPages > 1) {
            if ($iMaxPages > 1 or $sessionsp) {
                $op .= "<td colspan=1000 class=td_toppaging><div class=\"table_paging\">";
                //$op .=  "<div style=\"position:absolute; left:-30px;\"><img src=\"".RELATIVEPATH."/image/icons/arrows-left-$_SESSION[style_color].gif\"></div>";
                if ($_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] == "0" or $_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] == "") {
                    $_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] = 1;
                }
                $op .= "
				<div class='toppaging_button_small' onclick=\"jQuery('form#f_" . $ajaxExec . " .toppaging').val(1); ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\"></div>
				<div class='toppaging_button br' onclick=\"jQuery('form#f_" . $ajaxExec . " .toppaging').val(" . $_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] . " -1); ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\"></div>
				<input  class='c" . $ajaxExec . " display_table_paging_search toppaging' type=text id='page' onChange=\"ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\" placeholder='" . $_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] . " / $iMaxPages'>
				<div class='toppaging_button bl' onclick=\"jQuery('form#f_" . $ajaxExec . " .toppaging').val(" . $_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] . " + 1); window.setTimeout(ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "'),25);\"></div>
				<div class='toppaging_button_small' onclick=\"jQuery('form#f_" . $ajaxExec . " .toppaging').val(" . $iMaxPages . "); ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\"></div>
				
				<script>
				
				jQuery('form#f_" . $ajaxExec . " .toppaging').each(function() {adaptWidthToText('placeholder',this)});
				jQuery('form#f_" . $ajaxExec . " .toppaging').keyup(function() {adaptWidthToText('val',this)});
				jQuery('form#f_" . $ajaxExec . " .toppaging').focus(function() {jQuery(this).attr('placeholder',''); adaptWidthToText('val',this)});
				jQuery('form#f_" . $ajaxExec . " .toppaging').blur(function() {
				if (jQuery(this).val() == '') {
					jQuery(this).attr('placeholder','" . $_SESSION[aActivePagesRelations][$place][$table . "-" . $tableId] . " / $iMaxPages');
					adaptWidthToText('placeholder',this);
				} else {
					adaptWidthToText('val',this);
				}
				});
				</script>
				";
                $pagingDisplayed = 1;

                $op .= "
		</div></td></tr><tr class=\"table_head\">";
            }
        }
    }

    //n:1 Filter Dropdown ausgabe
    if (is_array($aRel['NTo1'][$table]) and $editable == "yes" and $sIsAssignmentTable != "yes") {
        /*$destTableNTo1=getDestFromSourceTableNTo1($table);
		$arrNTo1Filter = dbQuery("SELECT * FROM $destTableNTo1");
		$op .=  "<form action='index.php' method='post'>";
		$op .=  "<input type='hidden' name='execFilter' value='$table'>";
		$op .=  "Filter: <select name='NTo1Filter' onChange='this.form.submit()'>";
		$op .=  "<option value=''></option>";
		foreach ($arrNTo1Filter as $key => $value) {
			if ($_SESSION[NTo1Filter][$table] == $value[id])
				$selected = "selected";
			else
				$selected = "";
			$op .=  "<option value='$value[id]' $selected >";
			$outPutFilter = "";
			foreach ($value as $key2 => $value2) {
				if ($value2 != "") {
					$outPutFilter .= $value2.", ";
				}
			}
			if (strlen($outPutFilter) > 100 ){
				$outPutFilter = substr($outPutFilter, 0,97)."...";
			}
			$op .=  $outPutFilter;
			$op .=  "</option>";
		}
		$op .=  "</select>";
		//$op .=  "<input type='Submit'>";
		$op .=  "</form>";*/
    }

    if (!$queryOver2) {
        $q = "SHOW COLUMNS FROM $table";
        $arrTableColumnNames = dbQuery($q);
    } else {
        $atctest = dbQuery($queryOverWithoutSearch);
        foreach ($atctest as $k => $v)
            foreach ($v as $k2 => $v2) {
                $f = 1;
                if (is_array($arrTableColumnNames))
                    foreach ($arrTableColumnNames as $k3 => $v3) {
                        //echo "$v3[Field] == $k2";
                        //wenn noch nicht drin ist
                        if ($v3[Field] == $k2)
                            $f = 0;
                    }
                if ($f == 1)
                    $arrTableColumnNames[][Field] = $k2;
            }
    }

    $op .= "<td class='sidebar td_sort' valign=top>";
    $sideBarActive = 0;
    if ($isNToMDisplayEditEntry == "yes" and $sIsAssignmentTable == "no") {
        $a = extractComingFrom($sComingFrom);
        //print_r($a);
        $origTable = $a[0][0];
        $origTableId = $a[0][1];

        if ($a[0][1])
            $origTableOrId = $a[0][1];
        else
            $origTableOrId = $a[0][0];

        $origIdValue = $a[1];
        foreach ($aRel[NToM] as $k => $v) {
            if ($k == $tableId)
                foreach ($v as $k2 => $v2) {
                    //echo $v2[destTable]." == ".$origTable."-".$origTableId;
                    if ($v2[destTable] == $origTableOrId) {
                        $aIdNameDestTable = $v2[destFieldname];
                        //todo relations müssen über ids angesprochen werden
                        $aActualRelation = getRelationProperties($v2[assignTable], $aManualFieldProperties);
                    }
                }
        }
        if (inSerializedArray($_SESSION[user], $aActualRelation[addors])) {
            $op .= "<a onClick=\"opwin('edit_relation.php?action=new&idName=" . $_GET[id] . "&idValue=" . $origIdValue . "&sourceTable=$origTable&destTable=$table','EditRelation'); return false;\" href='#' title='Neue Referenz erstellen'><img src=\"" . RELATIVEPATH . "/image/icons/add-folder-$_SESSION[style_color].gif\"></a>";
            $sideBarActive = 1;
        }
    }
    if ($aTableProperties['actualize'] == 'on') {
        $sOut .= "<a class=sidebar-item href='javascript:void(0);' onclick=\"ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\" title=\"Aktualisieren\"><img src=\"" . RELATIVEPATH . "/image/icons/up-$_SESSION[style_color].gif\"></a>";
        $sideBarActive = 1;
    }
    if ($webuser)
        $ed = "index";
    else
        $ed = "edit_entry";

    if ($isNToMDisplayEditEntry == "no" and $addable == "yes") {
	        $sOut .= "<a class=sidebar-item href='#' onClick=\"ajax_send_scrollpos('" . $_SERVER['PHP_SELF'] . "');
opwin('$ed.php?action=new&columnNameOfId=$columnNameOfId&table=$tableOrId','Edit'); return false;\" title=\"Neuen Eintrag erstellen\"><img src=\"" . RELATIVEPATH . "/image/icons/add-page-$_SESSION[style_color].gif\"></a>";
        $sideBarActive = 1;
    }

    $op .= $sOut . "</td>";

    //Spaltennamen sortierbar
    foreach ($arrTableColumnNames as $key => $column) {
        if ($column['Field'] != "bncms_assign_id") {
            $f = getFieldProperties($tableOrId, $column['Field'], $aManualFieldProperties);
            if ($queryOver2 and !$f)
                $f = 1;
            if ($aRightsHidden[$tableOrId][$column['Field']] or (!$f and $tableId))
                continue;
            if (@$sessionsp[order] == "" . $column['Field'] . "")
                $oi = "<img src=\"" . RELATIVEPATH . "/image/icons/order-up-$_SESSION[style_color].gif\" style='vertical-align:baseline;margin-right:5px'>";
            elseif (@$sessionsp[order] == "" . $column['Field'] . " DESC")
                $oi = "<img src=\"" . RELATIVEPATH . "/image/icons/order-down-$_SESSION[style_color].gif\" style='vertical-align:baseline;margin-right:5px'>";
            else
                $oi = "";

            $o = "`" . $column['Field'] . "`";
            if ($f[title])
                $of = $f[title];
            else
                $of = $column['Field'];
            $op .= "<td valign='top' class='td_sort $column[Field]'><a href='javascript:void(0);' onclick=\"
			if (jQuery('form#f_" . $ajaxExec . " #order').val() == '$column[Field] DESC')
				jQuery('form#f_" . $ajaxExec . " #order').val('$column[Field]');
			else 
				jQuery('form#f_" . $ajaxExec . " #order').val('$column[Field] DESC'); 
			 ajax_submit('" . $ajaxExec . "','order=" . $o . "','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\" title='$column[Field]'>$oi" . $of . "</a></td>";
        }
    }
    if ($additionalColumn) {
        $op .= "<td class='td_sort additional'></td>";
    }

    //hole spaltennamen von zuordnungstabelle
    if ($sTableType == "ntom") {
        $c = explode("_", $sComingFrom);
        $t = explode("-", $c[0]);
        if (!$t[1])
            $table2 = $t[0];
        else
            $table2 = $t[1];
        $n = getAssignmentTableName($tableOrId, $table2);
        $q = "SHOW COLUMNS FROM $n";
        $aColumnNames = dbQuery($q);
        $fn = getAssignmentFieldNames($tableOrId, $table2);
        foreach ($aColumnNames as $kc => $column) {
            $tp = getTableProperties($n, $aManualFieldProperties);
            $aRelationProperties = getRelationProperties($n, $aManualFieldProperties);
            if ($tp[columnNameOfId] != $column['Field'] and
                $column['Field'] != $fn[sourceFieldname] and
                $column['Field'] != $fn[destFieldname]) {
                $f = getFieldProperties($n, $column['Field'], $aManualFieldProperties);
                if ($f[title])
                    $of = $f[title];
                else
                    $of = $column['Field'];
                if ($isNToMDisplayEditEntry == "yes" and $aRelationProperties[seperateColumns] != "on")
                    $op .= "<td class='td_sort $kc assign' valign=top>" . $of . "</td>";
                $ofc++;
            }
        }
    }

    $op .= "</tr><tr class=table_head><td class=sidebar></td>";
    $op .= '
	<script>
jQuery(function() {
	function split( val ) {
		return val.split( /,\s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}
	var cache = {};
	if (jQuery(".nto1_autocomplete").length) {
		jQuery( ".nto1_autocomplete" ).bind( "keydown", function( event ) {
			//alert("hu");
			if ( event.keyCode === jQuery.ui.keyCode.TAB && jQuery( this ).autocomplete( "instance" ).menu.active ) {
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
				var nto1id = this.element[0].name.replace(\'nto1_\',\'\');
				jQuery.getJSON( RELATIVEPATH+"/ajax.php?nto1AjaxSearch="+nto1id+"&value="+encodeURI(term)+"&formid=' . $ajaxExec . '", {
					term: extractLast( request.term )
				}, function( data, status, xhr ) {
					cache[ term ] = data;
					response( data );
				});
			}
		});
	}
});
</script>
	';

    //search
    if ($searchable == "yes") {
        foreach ($arrTableColumnNames as $key => $column) {
            if ($column['Field'] != "bncms_assign_id") {
                $f = getFieldProperties($tableOrId, $column['Field'], $aManualFieldProperties);
                if ($queryOver2 and !$f)
                    $f = 1;
                if ($aRightsHidden[$tableOrId][$column['Field']] or (!$f and $tableId))
                    continue;
                if (@$sessionsp["search_" . $column['Field']] != "")
                    $v = " value='" . $sessionsp["search_" . $column['Field']] . "' ";
                else
                    $v = "";
                $op .= "
				<td valign='top' class='td_search $column[Field]'>";
                $op .= generateSearchField($tableOrId, $column['Field'], $ajaxExec, $sessionsp, $aManualFieldProperties);
                $op .= "</td>";
            }
        }
        if ($additionalColumn) {
            $op .= "<td class='td_search table_head'></td>";
        }
        if ($ofc) {
            for ($i = 0; $i < $ofc; $i++) {
                if ($isNToMDisplayEditEntry == "yes" and $aRelationProperties[seperateColumns] != "on")
                    $op .= "<td class='td_search table_head assign'></td>";
            }
        }
    }
    //echo "hu";
    //Am ende des Tableheaders Zusatz Zelle f&uuml;r Ausdehnung der Tabelle nach rechts durch andere Tabellen
    if ($queryOver != "") {
        //todo muss Relationen mit IDs ansprechen nicht über Tabellennamen, weil so kann nur eine Relation pro Tabellenpaar haben
        //echo $sComingFrom;
        $a = extractComingFrom($sComingFrom);
        //wenn id leer ist muss name nehmen
        if ($a[0][1])
            $tableOrId2 = $a[0][1];
        else
            $tableOrId2 = $a[0][0];
        //echo "$tableOrId,$tableOrId2";
        $aIdentifierNToM = getIdentifierNToM($tableOrId, $tableOrId2);
        //pre($aIdentifierNToM);
        //echo "$tableId, ".$a[0][1];
        $sAssignTable = getAssignTableNToM($tableOrId, $tableOrId2);
        $aRelationProperties = getRelationProperties($sAssignTable, $aManualFieldProperties);
        //echo "<pre>";
        //print_r($aRelationProperties);
        //echo "</pre>";
        $q = $queryOver . $sco . $os . $ls;
        $arrTableContentAss = dbQuery($q, "", 1);
        //pre ($arrTableContentAss);
        //$op .= $q;
        //print_r($arrTableContentAss);
        //exit();
        $arrDepth = ArrayDepth($arrTableContentAss);
        if ($arrDepth == 1) {
            $arrTemp[1] = $arrTableContentAss;
            $arrTableContentAss = $arrTemp;
        }
        //assignment werte anhängen
        if (is_array($arrTableContentAss)) {
            foreach ($arrTableContentAss as $key => $value) {
                if (is_numeric($key))
                    $arrTableContent[$value[$aIdentifierNToM[0]] . "-" . $value[id]][assign] = $value;
            }
        }
    }

    //pre($arrTableContent);
    $op .= " </form></tr>";
    if ($isNToMDisplayEditEntry == "yes") {
        $op .= "
		
		<form action='index.php' method='post'>";
    }
    $iShowRelationsForPage = 0;
    $op .= $ke;
    if (is_array($arrTableContent)) {
        foreach ($arrTableContent as $key => $row) {
            if ($iZebra == 1) {
                $sStyle = " td_zebra ";
                $$iZebra = 0;
            } else {
                $sStyle = "";
                $$iZebra = 1;
            }
            $op .= "
		<tr id=\"tr_" . $table . "-" . $tableId . "_" . $rowcount . "\" class=\"$sStyle tr_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "\" >";
            $sDisplayTableRecursivePathOut = md5(implode($aDisplayTableRecursivePath));
            //pre($row);
            if ($row[$columnNameOfId]) {
                $op .= "<td class=sidebar valign=top><nobr>";
                if ($checkable) {
                    if ($checkable == "checkbox") {
                        $cf = str_replace("{id}", $row[$columnNameOfId], $checkableFunction);
                        $op .= "<input type=checkbox name='checkbox_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "' onclick=\"" . $cf . "\" id='checkbox_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "' class='checkbox'><label for=\"checkbox_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "\"></label>";
                        $sideBarActive = 1;
                    }
                    if ($checkable == "radio") {

                        //echo $checkableFunction = preg_replace('/\{([\w\]\[]+)\}/',"\$$1",$checkableFunction);
                        /*preg_match_all('/\{([\$\'\w\]\[]+)\}/',$checkableFunction,$matches);
						print_r($matches[1][0]);
						if ( isset($matches[1][0])) {
							$r = compact("row[id]");
							print_r($r);
							foreach ( $r as $var => $value ) {
								$checkableFunction = str_replace('{$'.$var.'}',$value,$checkableFunction);
							}
						}*/
                        $cf = str_replace("{id}", $row[$columnNameOfId], $checkableFunction);
                        $op .= "<input type=radio name='$table-$tableId' value='" . $row[$columnNameOfId] . "' onclick=\"" . $cf . "\" id='radio_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "'><label for=\"radio_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "\"></label>";
                        $sideBarActive = 1;
                    }
                }

                //anzeige relationen mit separaten icons
                $q = "SELECT * FROM conf_relation_visibility WHERE path REGEXP '^" . (preg_replace('/\-a$/', "", $sComingFromRelations)) . "-[0-9]+$'  AND (showWithEditIcons = 'Separat' OR showWithEditIcons = 'Beides')";
                $a = q($q, "", 1);
                //pre($a);
                //echo $sDisplayTableRecursivePathOut;
                if (count($a)) {
                    foreach ($a as $k => $v) {
                        //pre($v);
                        $op .= "<div class=sidebar-item id=\"icon_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "_relations_" . $sDisplayTableRecursivePathOut . "_" . $v[path] . "_$ajaxExec\" class='td_$v[path]' style=\"display:none\">";
                        $op .= displayVisibilityButtons(
                            "",
                            "div_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "_relations_" . $sDisplayTableRecursivePathOut . "_" . $v[path] . "_" . $ajaxExec,
                            $v[title],
                            1,
                            $v[icon]
                        );
                        $op .= "</div>";
                    }
                }
                if ($showRelations == "yes") {
                    //echo "icon_".$table."-".$tableId."_".$row[$columnNameOfId]."_relations_".$sDisplayTableRecursivePathOut."_$ajaxExec";
                    $op .= "<div class='sidebar-item' id=\"icon_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "_relations_" . $sDisplayTableRecursivePathOut . "_$ajaxExec\" style=\"display:none\">";
                    $op .= displayVisibilityButtons(
                        "",
                        "div_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "_relations_" . $sDisplayTableRecursivePathOut . "_" . $ajaxExec,
                        "Relationen des Eintrags &ouml;ffnen",
                        1);
                    $op .= "</div>";
                }
                if ($isNToMDisplayEditEntry == "yes") {
                    if (inSerializedArray($_SESSION[user], $aActualRelation[deletors])) {
                        $op .= "<a class=sidebar-item href='#' onClick=\"ajax_send_scrollpos('" . $_SERVER['PHP_SELF'] . "');
		opwin('index.php?id=" . $row[assign][getIdName($sAssignTable, $aManualFieldProperties)] . "&table=" . $sAssignTable . "&action=delete','Delete'); return false;\" title=\"Referenz l&ouml;schen\"><img src=\"" . RELATIVEPATH . "/image/icons/delete-page-$_SESSION[style_color].gif\"></a>";
                        $sideBarActive = 1;
                    }
                }

                if ($editable == "yes") {
                    if ($sIsAssignmentTable == "yes") {
                        $sWindowName = "Editrelation";
                    } else {
                        $sWindowName = "Edit";
                    }
                    $op .= "<a class=sidebar-item onClick=\"opwin('$ed.php?action=edit&id=$row[$columnNameOfId]&columnNameOfId=$columnNameOfId&table=$tableOrId&ajaxExec=$ajaxExec','" . $sWindowName . "'); return false;\" href=\"#\" title=\"Eintrag &auml;ndern\"><img src=\"" . RELATIVEPATH . "/image/icons/edit-page-$_SESSION[style_color].gif\"></a>";
                    $sideBarActive = 1;
                }
                if ($deletable == "yes") {
                    $op .= "<a class=sidebar-item onClick=\"opwin('index.php?action=delete&id=$row[$columnNameOfId]&columnNameOfId=$columnNameOfId&table=$tableOrId','Delete'); return false;\" href=\"#\" title=\"Eintrag l&ouml;schen\"><img src=\"" . RELATIVEPATH . "/image/icons/delete-page-$_SESSION[style_color].gif\"></a>";
                    $sideBarActive = 1;
                }
                if ($addable == "yes") {
                    $op .= "<a class=sidebar-item onClick=\"opwin('$ed.php?duplicate=true&id=$row[$columnNameOfId]&columnNameOfId=$columnNameOfId&table=$tableOrId','Edit'); return false;\" href=\"#\" title=\"Eintrag duplizieren\"><img src=\"" . RELATIVEPATH . "/image/icons/duplicate-$_SESSION[style_color].gif\"></a>";
                    $sideBarActive = 1;
                }
            }//if ($row[$columnNameOfId]) {

            $iZebra = $iZebra + 1;
            if ($iZebra > 1) {
                $iZebra = 0;
            }
            //pre($row);

            foreach ($row as $key => $field) {
                $iNtoMFound = 0;

                if (!is_numeric($key)) {
                    //Pr&uuml;fen ob Werte aus der Assignment Tabelle vorhanden sind
                    $fieldFromArray = "";

                    if (is_array($field)) {

                        //echo $sComingFromRelations;

                        $op .= "<td class=sidebar valign=top>";
                        preg_match("/\-([0-9])+\-a$/", $sComingFromRelations, $t);
                        //$q = "SELECT * FROM conf_relations WHERE id = '$t[1]'";
                        //$ar = q($q);
                        $ar = getRelationPropertiesById($t[1], $aManualFieldProperties);
                        //pre($a);
                        $q = "SELECT * FROM conf_tables WHERE name = '$ar[name]'";
                        $b = q($q, "", 1);
                        //pre($b);
                        //todo muss das auflösen, es gibt zwei konfigurierte Relationen die beide auf die selbe zuordnungstabelle zeigen, es ist daher nicht möglich tableId zuverlässig zu holen, man weiss eigentlich nicht zu welcher Relation die angezeigten Einträge gehören

                        //anzeige relationen mit separaten icons
                        $q = "SELECT * FROM conf_relation_visibility WHERE path REGEXP '^$sComingFromRelations-[0-9]+$'  AND (showWithEditIcons = 'Separat' OR showWithEditIcons = 'Beides')";
                        $a = q($q, "", 1);
                        //pre($a);
                        if (count($a)) {
                            foreach ($a as $k => $v) {
                                //pre($v);

                                //$op .= "div_".$ar[name]."-".$b[0][id]."_".$field[$b[0][columnNameOfId]]."_relations_".$sDisplayTableRecursivePathOut."_".$v[path];
                                $op .= "<div class=sidebar-item id=\"icon_" . $ar[name] . "-" . $b[0][id] . "_" . $field[$b[0][columnNameOfId]] . "_relations_" . $sDisplayTableRecursivePathOut . "_" . $v[path] . "_$ajaxExec\" style=\"display:none\">";
                                $op .= displayVisibilityButtons(
                                    "",
                                    "div_" . $ar[name] . "-" . $b[0][id] . "_" . $field[$b[0][columnNameOfId]] . "_relations_" . $sDisplayTableRecursivePathOut . "_" . $v[path] . "_" . $ajaxExec,
                                    $v[title],
                                    1,
                                    $v[icon]
                                );
                                $op .= "</div>";
                            }
                        }
                        if ($showRelations == "yes") {
                            $op .= "<div class=sidebar-item id=\"icon_" . $ar[name] . "-" . $b[0][id] . "_" . $field[$b[0][columnNameOfId]] . "_relations_" . $sDisplayTableRecursivePathOut . "_$ajaxExec\" style=\"display:none\">";
                            $op .= displayVisibilityButtons(
                                "",
                                "div_" . $ar[name] . "-" . $b[0][id] . "_" . $field[$b[0][columnNameOfId]] . "_relations_" . $sDisplayTableRecursivePathOut . "_" . $ajaxExec,
                                "Relationen des Eintrags &ouml;ffnen",
                                1,
                                    "",
                                    "",
                                    $sBeforeAjaxQueryString
                            );
                            $op .= "</div>";
                        }
                        $op .= "</nobr></td>";


                        $oa = generateRelatedContent($sComingFromRelations, $b[0][id], $field, $sDisplayTableRecursivePathOut, $aNTo1TablePath, $arrTableColumnNames, $b[0][columnNameOfId], $iSwitchInColumnSeparatAssignment, $aManualFieldProperties, $ajaxExec);
                        //pre(htmlentities($oa[0]));
                        if ($oa) {
                            $sRelatedContentNToMAssignment = $oa;
                            $sideBarActive = 1;
                        }
                        foreach ($field as $key2 => $value) {

                            if (@!in_array($key2, $aIdentifierNToM)) {
                                //print_r($aRelationProperties);

                                if ($aRelationProperties[seperateColumns] == "on") {
                                    $fp = getFieldProperties($sAssignTable, $key2, $aManualFieldProperties);
                                    if ($fp[title])
                                        $kou = "<b>" . $fp[title] . "</b><br>";
                                    elseif ($fp[name])
                                        $kou = "<b>" . $fp[name] . "</b><br>";
                                    if ($value)
                                        $display = 1;
                                    else
                                        $display = 0;
                                } else {
                                    $kou = "";
                                    $display = 1;
                                }
                                //echo $isNToMDisplayEditEntry ;
                                if ($isNToMDisplayEditEntry == "yes") {
                                    //Schreibrechte
                                    //echo $sAssignTable;
                                    //echo getIdName($sAssignTable,$aManualFieldProperties);
                                    /*echo "<br>$key2 != 'id' and
									".getIdName($sAssignTable,$aManualFieldProperties)." != $key2 and
									$key2 != $aIdentifierNToM[0] and
									$key2 != $aIdentifierNToM[1]";*/
                                    if ($key2 != 'id' and
                                        getIdName($sAssignTable, $aManualFieldProperties) != $key2 and
                                        $key2 != $aIdentifierNToM[0] and
                                        $key2 != $aIdentifierNToM[1]) {

                                        if ($display) {


                                            if (!$aRightsHidden[$sAssignTable][$key2])
                                                $op .= "<td valign='top' class='$sStyle assign'>
										$kou" .
                                                    generateField(
                                                        $sAssignTable,
                                                        $key2,
                                                        $value,
                                                        "edit",
                                                        $aManualFieldProperties,
                                                        $field[getIdName($sAssignTable, $aManualFieldProperties)] . "_$key2",
                                                        "",
                                                        "",
                                                        $row,
                                                        $sUserFunction,
                                                        $sComingFrom . "_" . c($table) . "-" . $tableId . "_" . $row[id])
                                                    . "</td>";
                                        }
                                    }
                                } else {
                                    //Leserechte
                                    //echo $key2;
                                    if ($key2 != 'id' and
                                        getIdName($sAssignTable, $aManualFieldProperties) != $key2 and
                                        $key2 != $aIdentifierNToM[0] and
                                        $key2 != $aIdentifierNToM[1]) {
                                        if ($display) {

                                            if (!$aRightsHidden[$sAssignTable][$key2])
                                                $op .= "<td valign='top' class='$sStyle assign'>$kou" .
                                                    generateField(
                                                        $sAssignTable,
                                                        $key2,
                                                        $value,
                                                        "view",
                                                        $aManualFieldProperties,
                                                        "",
                                                        "",
                                                        "",
                                                        $row,
                                                        $sUserFunction,
                                                        $sComingFrom . "_" . c($table) . "-" . $tableId . "_" . $row[id])
                                                    . "</td>";
                                        }
                                    }
                                }
                                $iNtoMFound = 1;
                                //Ermitteln ob Feld eine n zu 1 enth&auml;lt
                                $aLastFieldArray = $field;
                            }
                        }
                    }

                    if (is_array($field))
                        continue;

                    $field = generateField(
                        $tableOrId,
                        $key,
                        $field,
                        "view",
                        $aManualFieldProperties,
                        "",
                        "",
                        "",
                        $row,
                        $sUserFunction,
                        $sComingFrom);

                    $f = getFieldProperties($tableId, $key, $aManualFieldProperties);
                    if ($queryOver2 and !$f)
                        $f = 1;


                    if (!$aRightsHidden[$tableOrId][$key]) {
                        $op .= "
						<td valign='top' class='$key'><div>";
                        $op .= "
						" . $field . "</div></td>";
                    }
                }
            }
            if ($additionalColumn) {

                $o = str_replace("[id]", $row[$columnNameOfId], $additionalColumn);
                if ($sUserFunction) {
                    $o = call_user_func_array($sUserFunction, array($o, 'additional', $row));
                }
                $op .= "<td class='additional' valign='top'>$o</td>";
            }

            //Am ende der Zeilen Zusatz Zelle f&uuml;r Ausdehnung der Tabelle nach rechts durch andere Tabellen
            //$showRelations = 0;
            //exit("hu");
            //echo $showRelations."show";

            if ($showRelations == "yes") {
                if ($isNToMDisplayEditEntry == "yes") {
                    if (strpos("a" . $_GET[table], "assign_") != 1
                        and strpos("a" . $table, "assign_") != 1
                        and $_GET[id]
                        and $row[$columnNameOfId]) {
                        //Einh&auml;ngepunkt EditRow Assignmenttabelle
                        $query = "SELECT * FROM conf_tables WHERE name = 'assign_$_GET[table]_$table' or name = 'assign_" . $table . "_" . $_GET[table] . "'";
                        $aAssignTable = dbQuery($query);
                        $op .= "</tr><tr><td colspan=\"10\">";
                        $op .= displayTable(
                            $aAssignTable[0]['id'],
                            $aAssignTable[0]['columnNameOfId'],
                            " id_$_GET[table] = '" . $_GET[id] . "' AND id_$table = '" . $row[$columnNameOfId] . "' ",
                            "",
                            "",
                            "yes",
                            "",
                            "yes",
                            "",
                            "",//10
                            10,
                            0,
                            0,
                            packGlobals(),
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            $aManualFieldProperties
                        );
                        $op .= "</td></tr><tr>";
                    }
                }

                $op .= "
		</tr><tr><td valign='top' colspan='" . (count($arrTableColumnNames) + 3) . "'  class=\"table_relations_spacer\">";
                $test = 1;

                $oa = generateRelatedContent(preg_replace("/-a$/", "", $sComingFromRelations), $tableOrId, $row, $sDisplayTableRecursivePathOut, $aNTo1TablePath, $arrTableColumnNames, $columnNameOfId, $iSwitchInColumnSeparat, $aManualFieldProperties, $ajaxExec);
                //$op .= "$sComingFromRelations, $table, $tableId, $row, $sDisplayTableRecursivePathOut, $aNTo1TablePath, $arrTableColumnNames, $columnNameOfId, $iSwitchInColumnSeparat";
                if ($oa)
                    $sideBarActive = 1;
                $op .= $oa;
                //echo htmlentities($oa);
                $op .= $sRelatedContentNToMAssignment;
                $op .= "</td>";
                if ($sTableType == "normal") {
                    $aNTo1TablePath = array(st($table) . "-" . $tableId);
                }
            }//showRelations
        }
    }//Foreach arrTableContent eof

    //sidebar ausblenden falls keine Icons

    if ($sideBarActive != 1)
        echo "<script>jQuery('.$ajaxExec .sidebar').css('display','none');</script>";
    if ($isNToMDisplayEditEntry == "yes") {
        $a = extractComingFrom($sComingFrom);
        if ($a[0][1])
            $tableOrId2 = $a[0][1];
        else
            $tableOrId2 = $a[0][0];
        $sAssignTableNToM = getAssignTableNToM($tableOrId, $tableOrId2);
        $aIdNameNToM = getIdentifierNToM($tableOrId, $tableOrId2);
        $_SESSION['assignTableName'] = $sAssignTableNToM;
        $_SESSION['sourceTableName'] = $lastTableNameNToM;
        $op .= "
		</tr>
		<td valign='top' colspan='8'>
		<input type='hidden' name='table' value='$table'>
		<input type='hidden' name='assignTable' value='$sAssignTableNToM'>
		<input type='hidden' name='columnNameOfId' value='$columnNameOfId'>	
		<input type='hidden' name='id' value='$id'>	
		<input type='hidden' name='saveAssignTableValues' value='1'>
		<input type='Submit' value='Verbindungstabellen Daten speichern' class='submit'></td>
		";
    }
    $op .= "</tbody>
</table></div></div>";
    //alle $sDisplayTableRecursivePathOut relationen ebenen einblenden nachdem durch ajax dargestellt wurde
    //pre($_SESSION[aVisibleLayers][$sBeforeAjaxQueryString]);
    if (is_array($_SESSION[aVisibleLayers][$sBeforeAjaxQueryString])) {
        foreach ($_SESSION[aVisibleLayers][$sBeforeAjaxQueryString] as $key => $value) {
            if (strpos($value, $sDisplayTableRecursivePathOut))
                $op .= "<script type='text/javascript'>
				anschalten('$value');
				</script>";
        }
    }
    $aDisplayTableRecursivePath[$sDisplayTableRecursivePath] = "";
    $sDisplayTableRecursivePath = $sDisplayTableRecursivePath - 1;
    //if ($ajaxExec != 0)
    //	return $op;
    //else

    echo $op;
}


function displayAssignRow($columnNameOfId, $id, $table, $sourceTableName, $action)
{
    global $aRightsUnchangeable, $aRel, $aProp, $aTable, $lastRowIdNToM;
    $aTablesNToM = array($table, $sourceTableName);

    //Tabellenname f&uuml;r Output bereitstellen
    foreach ($aTable as $key => $value) {
        if (in_array($value[name], $aTablesNToM)) {
            $langTable .= $value[lang] . ", ";
        }
    }
    $out = "";
    $out .= "<div class=\"display_assign_row\">
<table>
<tr><form action='index.php' target='_self' method='post'>
	<td valign='top' colspan=2><h2>Bezug &auml;ndern</h2>
	</td>
<tr>
</tr>
	<td valign='top'><div>Tabellen</div>
	</td>
	<td valign='top'><div>$_GET[sourceTable], $_GET[destTable]</div>
	</td>
</tr>

";
    $query = "SELECT * FROM $_GET[sourceTable] WHERE id = '$_GET[idValue]'";
    $aSourceData = dbQuery($query);
    foreach ($aSourceData[0] as $key => $content) {
        $sSourceData .= $content . ", ";
    }
    $out .= "<tr><td>id_$_GET[sourceTable]<input type=\"hidden\" name=\"id_$_GET[sourceTable]\" value=\"" . $_GET["idValue"] . "\"></td><td>$sSourceData</td></tr>";
    $query = "SELECT * FROM $_GET[destTable]";
    $aDestData = dbQuery($query);
    $sDestData .= "<select name=\"id_$_GET[destTable]\">";
    foreach ($aDestData as $count => $content) {
        //print_r($content);
        $i = getIdName($_GET[destTable], $aManualFieldProperties);
        $sDestData .= "<option value=\"" . $content[$i] . "\">";
        foreach ($content as $countField => $contentField) {
            $sDestData .= $contentField . ", ";
        }
        $sDestData .= "</option>";
    }
    $sDestData .= "</select>";
    $out .= "<tr><td>id_$_GET[destTable]</td><td>$sDestData</td></tr>";
    if ($action == "new") {
        $out .= "<input type=\"hidden\" name=\"action\" value=\"new\">";
    }
    /*

	$arrRowContent=selectRec($table,"id='$id'");
	foreach ($arrRowContent[0] as $fieldName => $field) {
	$out .= "
<tr>
	<td valign='top'><div>".ucfirst($fieldName)."</div>
	</td>
	<td valign='top'>";
		//Ermitteln ob n:1 Relation vorhanden ist und Dropdown ausgeben
		//echo "[$table][$fieldName]";
		//print_r($aRel);
		if ($aRel['NTo1'][$table][$fieldName] != "") {
			$aRelTable=dbQuery("SELECT * FROM ".$aRel['NTo1'][$table][$fieldName]);
			$out .= "<select name='$fieldName'><option></option>";
			$arrDepth=ArrayDepth($aRelTable);
			if ($arrDepth == 1) {
				$arrTemp[1]=$aRelTable;
				$aRelTable=$arrTemp;
			}
			foreach ($aRelTable as $key => $value) {
				$selected = "";
				if ($field == $value[id]) {
					$selected = " selected ";
				}
				$out .= "<option value='$value[id]' $selected>";
				foreach ($value as $key2 => $value2) {
					if (is_numeric($value2)) {
						if ($key2 == "id") {
							$out .= $value2.", ";
						}
					} else {
						if (strlen($value2) > 70) {
							$value2 = substr($value2,0,70)."...";
						} else {
							$value2 = $value2;
						}
						if ($value[type] != "image")
							$out .= $value2.", ";
					}
				}
				$out .= "</option>";
			}
			$out .= "</select>";
		}
		//Ermitteln ob das Feld ein Unix-Timestamp beinhaltet
		if ($aProp[$tableId][$fieldName] == "unixtimestamp") {
			$field = date("d.m.Y H:i s", $field);
		}
		//Ermitteln ob n:m identifier editierbar ist (destination)
		$aIdNamesNToM = getIdentifierNToM($sourceTableName);
		$destTable=getOtherTableNToM($sourceTableName);
		$iDisplayNToM = "on";

		if (@in_array($fieldName, $aIdNamesNToM)) {
			$iDisplayNToM = "off";
			if ($fieldName != $aIdNamesNToM[0]) { //Ausschluss der source Identifier

				$aRelTable=dbQuery("SELECT * FROM ".$destTable);
				$out .= "<select name='$fieldName'><option></option>";
				foreach ($aRelTable as $key => $value) {
					$selected = "";
					if ($field == $value[id]) {
						$selected = " selected ";
					}
					$out .= "<option value='$value[id]' $selected>";
					foreach ($value as $key2 => $value2) {
						if (is_numeric($value2)) {
							if ($key2 == "id") {
								$out .= $value2.", ";
							}
						} else {
							if (strlen($value2) > 70) {
								$value2 = substr($value2,0,70)."...";
							} else {
								$value2 = $value2;
							}
							$out .= $value2.", ";
						}
					}
					$out .= "</option>";
				}
				$out .= "</select>";
			} else {
			$out .= "
<input type='text' value='$field' name='$fieldName' disabled>
<input type='hidden' value='$field' name='$fieldName'>";
			}
		}

		if ($iDisplayNToM == "off"){ //Ausschluss der n:m Identifier
			$out ."";
		} elseif (isset($aRel['NTo1'][$table][$fieldName])){ //Ausschluss der n:1 Identifier
			$out ."";
		} elseif (@in_array($fieldName,$aRightsUnchangeable[$tableId])) { //Leserechte
			$out .= "
<div>$field</div>";
		} elseif ($aProp[$tableId][$fieldName] == "tinymce") { //Felder mit TinyMce
			$out .= "<textarea id='elm1' name='$fieldName' rows='5' cols='80' style='padding:0px 0px 0px 0px'>$field</textarea>";
		} else { //Schreibrechte
			$out .= "
<input type='text' name='$fieldName' value='$field'>";
		}
		$out .= "
	</td>
</tr>";
	}*/
    $out .= "
<input type='hidden' name='savePost' value='on'>	
<input type='hidden' name='table1' value='$_GET[sourceTable]'>
<input type='hidden' name='table2' value='$_GET[destTable]'>
<input type='hidden' name='columnNameOfId' value='id'>	
<input type='hidden' name='id' value='$id'>	

<tr>
	<td valign='top'>
	</td>
	<td valign='top' valign='top'><input type='Submit' value='Speichern'><br>
	<input onClick=\"window.close()\" type='button' class='submit' value='Zur&uuml;ck'>
	</td></form>
</tr></table></div>";
    echo $out;
}

function getNextAutoincrementValue($table)
{
    global $aDatabase;
    $q = "SHOW TABLE STATUS FROM `$aDatabase[dbname]` LIKE '$table'";
    $a = dbQuery($q);
    if ($a[0][AUTO_INCREMENT])
        return $a[0][AUTO_INCREMENT];
    elseif ($a[0][Auto_increment])
        return $a[0][Auto_increment];
    elseif ($a[0][auto_increment])
        return $a[0][auto_increment];
}

function displayRow(
    $id,/*1*/
    $columnNameOfId, /*2*/
    $tableOrId, /*3*/
    $hiddenCode = "", /*4*/
    $viewtype = "edit",/*5*/
    $aManualFieldProperties = array(),/*6*/
    $formAction = "",/*7*/
    $sUserFunction = "",/*8*/
    $sSavePost = "on"/*9*/,
    $sSubmitValue = "Speichern"/*10*/,
    $showRelations = "yes"/*11 obsolet*/
)
{

    global $webuser, $aRightsUnchangeable, $aRightsHidden, $aRel, $aProp, $aTable, $lastRowIdNToM, $lastTableNameNToM, $aFormIds;


    //ManualFieldProperties vorbereiten
    $aManualFieldProperties = prepareManualFieldProperties($aManualFieldProperties);
    overwriteRights($aManualFieldProperties);
    overwriteRelations($aManualFieldProperties);

    //pre($aManualFieldProperties);
    $aTableProperties = getTableProperties($tableOrId, $aManualFieldProperties);
    overwriteRights($aManualFieldProperties);
    //pre($aTableProperties);
    if (is_numeric($tableOrId)) {
        $table = @$aTableProperties['name'];
        $tableId = @$aTableProperties['id'];
    } else {
        $table = $tableOrId;
        $tableId = "";
    }

    if (!$viewtype)
        $viewtype = "edit";

    $aFormIds[$_SERVER['PHP_SELF']][$table][] = 1;
    $fi = md5($_SERVER['PHP_SELF'] . $table . count($aFormIds[$_SERVER['PHP_SELF']][$table]));

    if ($sSavePost == "" or $sSavePost == null)
        $sSavePost = "on";

    $lastRowIdNToM = $id;
    $_SESSION[lastRowIdNToM] = $lastRowIdNToM;
    $lastTableNameNToM = $table;

    if (!$id) {
        $q = "SHOW COLUMNS FROM $table";
        $a = dbQuery($q);
        foreach ($a as $k => $v)
            $arrRowContent[0][$v[Field]] = $v['Default'];
    } else
        $arrRowContent = selectRec($table, "$columnNameOfId='$id'");

    //Tabellenname f&uuml;r Output bereitstellen
    foreach ($aTable as $key => $value) {
        if ($value[name] == $table) {
            $langTable = $value[lang];
        }
    }
    //print_r($arrRowContent);
//$fi = rand(1,10000).$table;
    $out = "<div class=\"display_row\"><div class=\"table_overall\">";
    if ($webuser) {
        $t = '';
        $os = " onSubmit=\"validate('" . $table . "','$fi','" . RELATIVEPATHAPP . "'); return false;\" ";
    } else
        $t = "index.php";
    if ($formAction)
        $t = $formAction;

    //Muss manualFieldProperties in Session schreiben damit in ajax Datei vorhanden
    if ($aManualFieldProperties) {
        $_SESSION[manualFieldProperties][$fi] = $aManualFieldProperties;
    }

    $_SESSION[tempRights][$fi] = array();
    $out .= "
<form action=\"$t\" target=\"_self\" method=\"post\" enctype=\"multipart/form-data\" id='$fi'
$os
>
<table>";
    if (!$webuser)
        $out .= "<tr>
		<td valign='top' align='right'><b>Tabelle</b>
		</td>
		<td valign='top'><h2>$langTable</h2>
		</td>
	</tr>";


    foreach ($arrRowContent[0] as $fieldName => $field) {
        if (!$aRightsHidden[$tableOrId][$fieldName]) {
            $aT = getFieldProperties($tableOrId, $fieldName, $aManualFieldProperties);
            if ($aT[title])
                if ($webuser)
                    $f = $aT[title];
                else
                    $f = $aT[title] . " ($fieldName)";
            else
                $f = $fieldName;
            $out .= "<tr class='tr_$table_$fieldName'>";
            $out .= "<td valign='top' align='right' class='td_$table_$fieldName label'><div class='b'>" . $f . "</div></td>
			<td valign='top' align='left' class='td_$table_$fieldName'>";
            $fo = "";
            $fo = generateField(
                $tableOrId,
                $fieldName,
                $field,
                $viewtype,
                $aManualFieldProperties,
                "",
                $fi,
                $webuser,
                $arrRowContent,
                $sUserFunction);
            $out .= "$fo
			</td>
			</tr>";
        }
    }

    //wenn es eine ntom gibt mit Ajax
    //print_r($aRel['NToM'][$tableId]);
    //echo "$tableId";
    if (is_array($aRel['NToM'][$tableId])) {

        foreach ($aRel['NToM'][$tableId] as $k => $v) {
            if ($v[ntomDisplayType] == "ajax") {
                //pre($v);
                $aTargetTable = getTableProperties($v[destTable], $aManualFieldProperties);
                $out .= "<tr class='tr_$table_$aTargetTable[name]'>";
                $out .= "<td valign='top' align='right' class='td_$table_$aTargetTable[name]'><div class='b'>" . $aTargetTable[lang] . "</div></td>
				<td valign='top' align='left' class='td_$table_$aTargetTable[name]'>";

                $q = "SELECT * FROM $v[assignTable] WHERE $v[sourceFieldname] = '$id'";
                $a = dbQuery($q);
                if (count($a))
                    foreach ($a as $k1 => $v1) {
                        if ($v1[$v[destFieldname]] != 0) {
                            $q = "SELECT * FROM $aTargetTable[name] WHERE id = '" . $v1[$v[destFieldname]] . "'";
                            $aD = dbQuery($q);
                            $r .= $aD[0][name] . ", ";
                        }
                    }
                if ($webuser) {
                    $val = "<div id='val_" . $fi . "_ntom_$v[relationId]' class=val></div>";
                    $_SESSION[tempRights][$fi][allow][] = "ntom_$v[relationId]";
                }
                $out .= "<input type='text' name='ntom_$v[relationId]' id='ntom_$v[relationId]' value='$r' style='width:20em;' autocomplete='off' class='ntom_autocomplete'>$val
				</td>
				</tr>";
            }
        }
    }

    if ($hiddenCode) {
        $out .= $hiddenCode;
        /*sicherheit*/
        //die name paare aus dem hiddencode holen
        preg_match_all('/name=[\'|"| ]?(\w+)[\'|"| ]?/is', $hiddenCode, $t);
        foreach ($t[1] as $k => $v) {
            $_SESSION[tempRights][$fi][allow][] = $v;
        }
    }
    if ($webuser) {
        $_SESSION[tempRights][$fi][table] = $table;
        $_SESSION[tempRights][$fi][tableId] = $tableId;
        $_SESSION[tempRights][$fi][columnNameOfId] = $columnNameOfId;
        $_SESSION[tempRights][$fi][id] = $id;
        $_SESSION[tempRights][$fi][ts] = time();
        $of = "<input type='hidden' name='bncms_form' value='$fi'>";

    } else {
        $of = "<input type='hidden' name='table' value='$table'>
		<input type='hidden' name='tableId' value='$tableId'>
<input type='hidden' name='columnNameOfId' value='$columnNameOfId'> 
<input type='hidden' name='id' value='$id'>	";
    }

    if ($sSavePost == "on") {
        $of .= "<input type='hidden' name='savePost' value='on'>";
    }
    if ($viewtype != "view") {
        $out .= "
<tr>
	<td class='label'>
	$of
	</td>
	<td valign='top' align='left'>";

        if (!$sSubmitValue)
            $sSubmitValue = "Abschicken";

        if ($webuser)
            $v = "<button id='bu_$fi' class='submit' type='submit' onclick=\"jQuery('.lightbox').html('');\">$sSubmitValue</button><img src='" . RELATIVEPATH . "/image/loading.gif' style='display:none;opacity:0.3;width:2em' id='lo_$fi'>";
        else
            $v = "<input type='hidden' name='bncms_closeafter' id='bncms_closeafter' value='0'><nobr><input type='Submit' class='submit' value='$sSubmitValue' onclick=\"jQuery('.lightbox').html('');\"> <input type=\"Button\" class='submit' value=\"Speichern und Zur&uuml;ck\" onclick='jQuery(\".lightbox\").html(\"\");document.getElementById(\"bncms_closeafter\").value = \"1\"; this.form.submit();'></nobr>";
        if ($viewtype == "edit")
            $out .= $v;
        if (!$webuser)
            $out .= "<input onClick=\"window.close()\" type='button' class='submit' value='Zur&uuml;ck'>";

        $out .= "<br><br></td>
</tr></form>";
    }
    if (function_exists("adminUserFuncDisplayRow")) {
        $r = call_user_func_array("adminUserFuncDisplayRow", array($tableId, $id, $row));
        if ($r)
            $out .= "<tr><td colspan=50><h2>Spezialfunktionen:</h2>$r</td></tr>";
    }


    $out .= generateRelatedContent(
        $tableOrId,
        $tableOrId,
        $arrRowContent[0],
        "",
        "",
        "",
        $columnNameOfId,
        "",
        $aManualFieldProperties,
        $ajaxExec
    );
    /*
	if ($id and $showRelations == "yes") {
		//Ermittlen ob n:m vorhanden ist und Tabelle ausgeben
		if (is_array($aRel['NToM'][$tableId])) {
			//print_r($aRel['NToM'][$tableId]);
			//echo $tableId;
			foreach ($aRel['NToM'][$tableId] as $count => $content) {
				///if ($content['destTable'] == $tableId) {

					$assignTable=$content[assignTable];
					$destTable=getNameFromTableString($content[destTable]);
					$destTableId=getIdFromTableString($content[destTable]);
					$identifier=$content[sourceFieldname];
					$destIdentifier=$content[destFieldname];


					$sQueryOver="SELECT $destTable.".getIdName($destTable,$aManualFieldProperties).", $assignTable.* FROM $table, $assignTable, $destTable
					WHERE $table.".getIdName($table,$aManualFieldProperties)." = '$id'
					AND $assignTable.$identifier = $table.".getIdName($table,$aManualFieldProperties)."
					AND $assignTable.$destIdentifier = $destTable.".getIdName($destTable,$aManualFieldProperties)."";

					$sQueryOver2="SELECT $destTable.*, $assignTable.".getIdName($destTable,$aManualFieldProperties)." as bncms_assign_id  FROM $table, $assignTable, $destTable
					WHERE $table.id = '$id'
					AND $assignTable.$identifier = $table.id
					AND $assignTable.$destIdentifier = $destTable.id";
					//Tabellenname f&uuml;r Output bereitstellen
					foreach ($aTable as $key => $value) {
						if ($value[name] == $destTable) {
							$langTable = $value[lang];
						}
					}
					$out .= "
				<tr>
					<td valign='top' colspan='".count($arrTableColumnNames)."'>";
					$out .= "<div class=\"table_overall\">";
					$out .= displayVisibilityButtons("Zuordnungen zu ".$langTable, $destTable."Edit","",1);
					$out .= "<div id='".$destTable."Edit' style='display:none;'>";
					$out .= displayTable(
					$destTableId,
					getIdName($destTable,$aManualFieldProperties),
					"",
					$sQueryOver,
					$sQueryOver2,
					"no",
					c($table)."-".$tableId."_".$id,
					"yes",
					null,
					"ntom",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					$aManualFieldProperties

					);
					$out .= "
					</div></div>";
					$out .= "
					</td>
					</tr>
					";
				//}
			}
		}

		//Ermitten ob eingehende nzu1-Einträge hat
		if (is_array($aRel))
		foreach ($aRel[NTo1] as $k => $v) {
			foreach ($v as $k2 => $v2) {
				if ($v2 == $tableId) {
					$aLinkingTable = getTableProperties($k, $aManualFieldProperties);
					if ($aLinkingTable[lang])
						$t = $aLinkingTable[lang];
					else
						$t = $aLinkingTable[name];
					$out .= "
				<tr>
					<td valign='top' colspan='2'>";
					$out .= "<div class=\"table_overall\">";
					$out .= displayVisibilityButtons("Ausgehende Zuordnungen von ".$t, $aLinkingTable[id],"",1);
					$out .= "<div id='".$aLinkingTable[id]."' style='display:none;'>";
					$out .= displayTable(
					$aLinkingTable[id],
					getIdName($aLinkingTable[id],$aManualFieldProperties),
					" $k2 = '$id' ",
					"",
					"",
					"",
					c($table)."-".$tableId."_".$id,
					"no",
					null,
					"nto1output",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					$aManualFieldProperties

					);
					$out .= "
					</div></div>";
					$out .= "
					</td>
					</tr>";
				}
			}
		}
		}*/
    $out .= "</table></div></div>
	<script>
		jQuery('#" . $fi . " input[type=text]').each(function() {
			var o='';
			if (jQuery(this).attr('maxlength')) {
				for (i = 0; i < jQuery(this).attr('maxlength'); i++) {
					if (i < 100)
					o = o + 'a';
				} 
				var w = jQuery(this).textWidth(o);
				if (w > jQuery(document).width()*0.7)
					w = jQuery(document).width()*0.7;
				jQuery(this).css('width',w);
			}
		});
	</script>
	";
    //print_r($_SESSION[tempRights]);
    echo $out;
}

function generateField(
    $tableOrId, //1
    $fieldName, //2
    $field, //3
    $viewtype = "edit", //4
    $aManualFieldProperties = "", //5
    $outputFieldname = "", //6
    $fi = "", //7
    $webuser = "", //8
    $arrRowContent = "", //9
    $sUserFunction = "", //10
    $sComingFrom = "" //11
)
{

    global $dateIncludeJsDone, $tinymceIncludeJsDone, $aRel, $aProp, $aRightsHidden, $aRightsUnchangeable;
    //echo "$tableOrId, //1 $fieldName, //2 $field, //3 ";
    overwriteRights($aManualFieldProperties);
    $aTableProperties = getTableProperties($tableOrId, $aManualFieldProperties);
    $aFieldProperties = getFieldProperties($tableOrId, $fieldName, $aManualFieldProperties);
    //pre($aFieldProperties);
    //echo $tableOrId;
    if (is_numeric($tableOrId)) {
        $table = $aTableProperties[name];
        $tableId = $aTableProperties[id];
    } else {
        $table = $tableOrId;
        $tableId = "";
    }

    //sicherheit
    if ($aFieldProperties['type'] != "tinymce")
        $field = @strip_tags($field);

    //print_r($aManualFieldProperties);
    //typ überschreiben mit aManualFieldProperies
    /*if (@$aManualFieldProperties[$table."-".$tableId][$fieldName]['type']) {
		$aProp[$tableId][$fieldName] = $aManualFieldProperties[$table."-".$tableId][$fieldName]['type'];
		$aFieldProperties['type'] = $aManualFieldProperties[$table."-".$tableId][$fieldName]['type'];
	}*/
    if ($aRightsUnchangeable[$tableOrId][$fieldName] == "1" and $viewtype == "edit")
        $viewtype = "view";
    if ($viewtype == "edit") {
        if (!$outputFieldname) {
            $outputFieldname = $fieldName;
        }
        $maxlength = getLengthFromField($fieldName, $tableOrId, $aManualFieldProperties);

        if ($aFieldProperties['type'] == 'nto1') {
            if ($aFieldProperties[nto1DisplayType] == "radio" or !$aFieldProperties[nto1DisplayType]) {
                //nzu1 Zuweisung
                $fo .= "<input type=\"text\" id=\"" . $fi . "_" . $fieldName . "\" maxlength=\"" . $maxlength . "\"  name=\"$outputFieldname\" value=\"$field\" > <a href=\"javascript:void(0)\"onClick='show_lightbox(\"l_" . $fi . "_" . $fieldName . "\")'>Eintrag&nbsp;w&auml;hlen</a>";
                //echo $aRel['NTo1'][$tableOrId][$fieldName];
                //pre($aRel);
                //pre($tableOrId);
                $fo .= displayLightbox("l_" . $fi . "_" . $fieldName,
                    displayTable(
                        $aRel['NTo1'][$tableOrId][$fieldName],
                        getIdName($aRel['NTo1'][$tableOrId][$fieldName], $aManualFieldProperties),
                        "",
                        "",
                        "",
                        "no",
                        "",
                        "",
                        "",
                        "noecho",//10
                        "",
                        "",
                        "",
                        "",
                        "radio",
                        "yes",
                        0,
                        "document.getElementById('" . $fi . "_" . $fieldName . "').value = this.value; document.getElementById('l_" . $fi . "_" . $fieldName . "').style.display = 'none';",
                        "",
                        "",//20
                        $aManualFieldProperties
                    )
                );
            } elseif ($aFieldProperties[nto1DisplayType] == "dropdown") {
                $fo .= "<select name=\"$outputFieldname\">";
                $tt = getTableProperties($aFieldProperties['nto1TargetTable'], $aManualFieldProperties);
                $q = "SELECT * FROM $tt[name] ORDER BY $aFieldProperties[nto1DropdownTitleField]";
                $a = dbQuery($q);
                $fo .= "<option></option>";
                $aNTo1TargetFieldProperties = getFieldProperties($aFieldProperties['nto1TargetTable'], $aFieldProperties['nto1TargetField']);

                foreach ($a as $k => $v) {
                    if ($v[getIdName($aFieldProperties['nto1TargetTable'], $aManualFieldProperties)] == $field)
                        $s = "selected";
                    else
                        $s = "";
                    $fo .= "<option value='" . $v[$aNTo1TargetFieldProperties['name']] . "' $s>" . $v[$aFieldProperties['nto1DropdownTitleField']] . "</option>";
                }

                $fo .= "</select>";
            }
        }
        if ($fieldName == getIdName($tableOrId, $aManualFieldProperties)) {
            //Leserechte
            $fo .= "<div>$field</div>";

        } elseif (@$aRightsHidden[$tableOrId][$fieldName]) {
            //Versteckte Felder
            //nichts

        } elseif ($aFieldProperties['type'] == "date") {
            global $dateIncludeJsDone;
            if ($dateIncludeJsDone != 1) {
                $fo .= '
				
				<link rel="stylesheet" type="text/css" href="/bncms/lib/datetimepicker-master/jquery.datetimepicker.css"/ >
<script src="/bncms/lib/datetimepicker-master/build/jquery.datetimepicker.full.min.js"></script>
 
				';
                $dateIncludeJsDone = 1;
            }
            $date = date("d.m.Y H:i:s", $field);

            $fo .= "<input type=\"text\" id=\"" . $fi . "_" . $fieldName . "\" name=\"$fieldName\" maxlength=\"" . '19' . "\" style=\"cursor:pointer; \" value=\"$date\"  > <img src=\"/bncms/image/calendar.gif\" style=\"vertical-align:middle\"/>
			<script>jQuery('#" . $fi . "_" . $fieldName . "').datetimepicker({
				format: 'd.m.Y H:i:s'
});</script>
			";

        } elseif ($aFieldProperties['type'] == 'nto1') {
            //Ausschluss der n:1 Identifier
            $fo .= "";

        } elseif ($aFieldProperties['type'] == "checkbox") {
            //checkbox
            if ($field == "on")
                $o = "checked";
            else
                $o = "";
            $fo .= "<input type='hidden' name='$outputFieldname' value='off'><input type='checkbox' name=\"$outputFieldname\" style=\"\"  class='checkbox' id='$fi_$outputFieldname' $o><label for=\"$fi_$outputFieldname\"></label>";

        } elseif ($aFieldProperties['type'] == "tinymce") {
            //TinyMce
            if ($tinymceIncludeJsDone != 1) {
                $fo .= '<script type="text/javascript" src="/bncms/lib/tinymce/tinymce.min.js"></script>
	<script type="text/javascript">
		tinymce.init({
			selector: "#' . $fieldName . '",
			plugins: "code"
		});
	</script>';
                $tinymceIncludeJsDone = 1;
            }

            $fo .= "<textarea id='$fieldName' name='$outputFieldname' rows='5' cols='80' style='padding:0px 0px 0px 0px'>$field</textarea>";

        } elseif ($aFieldProperties['type'] == "ip") {
            //Bild
            $fo .= "<input type=text name=\"$outputFieldname\" value='$field' class='bncms_ip_address'> <a href='http://www.utrace.de/?query=$field' target='_blank'>utrace.de</a>
			
			<script>";
            $fo .= '
			(function(g){"function"===typeof define&&define.amd?define(["jquery"],g):g(window.jQuery||window.Zepto)})(function(g){var y=function(a,f,d){var k=this,x;a=g(a);f="function"===typeof f?f(a.val(),void 0,a,d):f;k.init=function(){d=d||{};k.byPassKeys=[9,16,17,18,36,37,38,39,40,91];k.translation={0:{pattern:/\d/},9:{pattern:/\d/,optional:!0},"#":{pattern:/\d/,recursive:!0},A:{pattern:/[a-zA-Z0-9]/},S:{pattern:/[a-zA-Z]/}};k.translation=g.extend({},k.translation,d.translation);k=g.extend(!0,{},k,d);a.each(function(){!1!==
d.maxlength&&a.attr("maxlength",f.length);d.placeholder&&a.attr("placeholder",d.placeholder);a.attr("autocomplete","off");c.destroyEvents();c.events();var b=c.getCaret();c.val(c.getMasked());c.setCaret(b+c.getMaskCharactersBeforeCount(b,!0))})};var c={getCaret:function(){var b;b=0;var e=a.get(0),c=document.selection,e=e.selectionStart;if(c&&!~navigator.appVersion.indexOf("MSIE 10"))b=c.createRange(),b.moveStart("character",a.is("input")?-a.val().length:-a.text().length),b=b.text.length;else if(e||
"0"===e)b=e;return b},setCaret:function(b){if(a.is(":focus")){var e;e=a.get(0);e.setSelectionRange?e.setSelectionRange(b,b):e.createTextRange&&(e=e.createTextRange(),e.collapse(!0),e.moveEnd("character",b),e.moveStart("character",b),e.select())}},events:function(){a.on("keydown.mask",function(){x=c.val()});a.on("keyup.mask",c.behaviour);a.on("paste.mask drop.mask",function(){setTimeout(function(){a.keydown().keyup()},100)});a.on("change.mask",function(){a.data("changeCalled",!0)});a.on("blur.mask",
function(b){b=g(b.target);b.prop("defaultValue")!==b.val()&&(b.prop("defaultValue",b.val()),b.data("changeCalled")||b.trigger("change"));b.data("changeCalled",!1)});a.on("focusout.mask",function(){d.clearIfNotMatch&&c.val().length<f.length&&c.val("")})},destroyEvents:function(){a.off("keydown.mask keyup.mask paste.mask drop.mask change.mask blur.mask focusout.mask").removeData("changeCalled")},val:function(b){var e=a.is("input");return 0<arguments.length?e?a.val(b):a.text(b):e?a.val():a.text()},getMaskCharactersBeforeCount:function(b,
e){for(var a=0,c=0,d=f.length;c<d&&c<b;c++)k.translation[f.charAt(c)]||(b=e?b+1:b,a++);return a},determineCaretPos:function(b,a,d,h){return k.translation[f.charAt(Math.min(b-1,f.length-1))]?Math.min(b+d-a-h,d):c.determineCaretPos(b+1,a,d,h)},behaviour:function(b){b=b||window.event;var a=b.keyCode||b.which;if(-1===g.inArray(a,k.byPassKeys)){var d=c.getCaret(),f=c.val(),n=f.length,l=d<n,p=c.getMasked(),m=p.length,q=c.getMaskCharactersBeforeCount(m-1)-c.getMaskCharactersBeforeCount(n-1);p!==f&&c.val(p);
!l||65===a&&b.ctrlKey||(8!==a&&46!==a&&(d=c.determineCaretPos(d,n,m,q)),c.setCaret(d));return c.callbacks(b)}},getMasked:function(b){var a=[],g=c.val(),h=0,n=f.length,l=0,p=g.length,m=1,q="push",s=-1,r,u;d.reverse?(q="unshift",m=-1,r=0,h=n-1,l=p-1,u=function(){return-1<h&&-1<l}):(r=n-1,u=function(){return h<n&&l<p});for(;u();){var v=f.charAt(h),w=g.charAt(l),t=k.translation[v];if(t)w.match(t.pattern)?(a[q](w),t.recursive&&(-1===s?s=h:h===r&&(h=s-m),r===s&&(h-=m)),h+=m):t.optional&&(h+=m,l-=m),l+=
m;else{if(!b)a[q](v);w===v&&(l+=m);h+=m}}b=f.charAt(r);n!==p+1||k.translation[b]||a.push(b);return a.join("")},callbacks:function(b){var e=c.val(),g=c.val()!==x;if(!0===g&&"function"===typeof d.onChange)d.onChange(e,b,a,d);if(!0===g&&"function"===typeof d.onKeyPress)d.onKeyPress(e,b,a,d);if("function"===typeof d.onComplete&&e.length===f.length)d.onComplete(e,b,a,d)}};k.remove=function(){var a=c.getCaret(),d=c.getMaskCharactersBeforeCount(a);c.destroyEvents();c.val(k.getCleanVal()).removeAttr("maxlength");
c.setCaret(a-d)};k.getCleanVal=function(){return c.getMasked(!0)};k.init()};g.fn.mask=function(a,f){this.unmask();return this.each(function(){g(this).data("mask",new y(this,a,f))})};g.fn.unmask=function(){return this.each(function(){try{g(this).data("mask").remove()}catch(a){}})};g.fn.cleanVal=function(){return g(this).data("mask").getCleanVal()};g("*[data-mask]").each(function(){var a=g(this),f={};"true"===a.attr("data-mask-reverse")&&(f.reverse=!0);"false"===a.attr("data-mask-maxlength")&&(f.maxlength=
!1);"true"===a.attr("data-mask-clearifnotmatch")&&(f.clearIfNotMatch=!0);a.mask(a.attr("data-mask"),f)})});

jQuery(".bncms_ip_address").mask("099.099.099.099")';
            $fo .= "</script>
			";
        } elseif ($aFieldProperties['type'] == "image") {
            //Bild
            $fo .= "<input type=\"file\" name=\"$outputFieldname\">";
            if (file_exists(PATH . "/" . $field) and $field != "") {
                $fo .= "<br /><a href=\"" . RELATIVEPATH . "/$field\" target=\"_blank\" onClick=\"javascript: ajax_send_scrollpos('" . $_SERVER['PHP_SELF'] . "');\"><img src=\"" . RELATIVEPATH . "/" . str_replace("file/", "file/th_", $field) . "\" class=bnul></a>";
                $fo .= "<div style=\"clear:both;\" align=\"left\"><input type=\"checkbox\" style=\"\" name=\"deleteFile[$fieldName]\" id='" . $fi . "_deleteFile[" . $fieldName . "]' class='checkbox'><label for=\"" . $fi . "_deleteFile[" . $fieldName . "]\"> Bild l&ouml;schen?</label></div>";
            }
        } elseif ($aFieldProperties['type'] == "file") {
            //Datei
            $fo .= "<input type=\"file\" name=\"$outputFieldname\">";
            if (file_exists(PATH . "/" . $field) and $field != "") {
                $fo .= "<br /><a href=\"" . RELATIVEPATH . "/$field\" target=\"_blank\" onClick=\"javascript: ajax_send_scrollpos('" . $_SERVER['PHP_SELF'] . "');\">" . str_replace("file/", "", $field) . "</a>";
                $fo .= "<br><input type=\"checkbox\"  class='checkbox' style=\"width:20px;\" name=\"deleteFile[$fieldName]\" value=\"1\">Datei l&ouml;schen?";
            }

        } elseif ($aFieldProperties['type'] == "price") {
            //Preis
            $sf = number_format($field, 2);
            $fo .= "<input type='text' 
			id=\"$fieldName\" 
			name=\"$outputFieldname\" 
			value='" . $sf . "' 
			onkeyup='allowChars(this,/[^0-9\.\,]/gi)' 
			onchange='allowChars(this,/[^0-9\.\,]/gi)'>";
        } elseif ($aFieldProperties['type'] == "url" or $aFieldProperties['type'] == "textarea" or $aFieldProperties['type'] == "text") {
            $fo .= "<textarea name=\"$outputFieldname\">$field</textarea>";

        } elseif ($aFieldProperties['type'] == "password") {
            //Passwort
            if ($aManualFieldProperties[$table . "-" . $tableId][$fieldName][entry])
                $fo .= "
			<input type='password' id=\"" . $fi . $fieldName . "\" name=\"" . $outputFieldname . "\" value='' > ";
            else
                $fo .= "
			<input type='password' id=\"" . $fi . $fieldName . "\" name=\"" . $outputFieldname . "\" disabled='disabled' value=''   style='background-color:lightgrey'> 
			<a id='lid" . $fi . $fieldName . "' href='javascript:Void(0)' onClick='
			document.getElementById(\"" . $fi . $fieldName . "\").setAttribute(\"disabled\",\"disabled\");
			this.style.display=\"none\"; 
			document.getElementById(\"lia" . $fi . $fieldName . "\").style.display=\"inline\";
			document.getElementById(\"" . $fi . $fieldName . "\").style.backgroundColor=\"lightgrey\";' style=\"display:none\">Doch nicht speichern?</a>
			<a id='lia" . $fi . $fieldName . "' href='javascript:Void(0)' onClick='
			document.getElementById(\"" . $fi . $fieldName . "\").removeAttribute(\"disabled\"	);  
			document.getElementById(\"lid" . $fi . $fieldName . "\").style.display=\"inline\"; 
			document.getElementById(\"" . $fi . $fieldName . "\").style.backgroundColor=\"white\"; 
			this.style.display=\"none\";
			'>Neu&nbsp;speichern?</a>";

        } elseif ($aFieldProperties['type'] == "dropdown") {
            //set

            $q = "SHOW FIELDS
	FROM $table where Field ='$fieldName'";
            $a = dbQuery($q);
            $t = str_replace('"', "", str_replace("'", "", str_replace(")", "", str_replace("set(", "", $a[0][Type]))));
            $a = explode(",", $t);

            $fo .= "<select id=\"$fieldName\" name=\"$outputFieldname\">";
            foreach ($a as $k => $v) {
                if ($field == $v)
                    $s = "selected";
                else
                    $s = "";

                $fo .= "<option $s>$v</option>";
            }
            $fo .= "</select>";

        } elseif ($aFieldProperties['type'] == "number") {
            //Schreibrechte
            $fo .= "
	<input type='number' name='$outputFieldname' value='$field' maxlength=\"" . $maxlength . "\"  onkeyup='allowChars(this,/[^0-9\.]/gi)' onchange='allowChars(this,/[^0-9\.]/gi)'	>";
        } else {
            //Schreibrechte
            $aTableProp = getTableProperties($tableId, $aManualFieldProperties);
            if ($aFieldProperties['type'] != "date" and
                $aTableProp[columnNameOfId] != $fieldName or
                $aFieldProperties['type'] == "textfield") {
                if (checkInt($aFieldProperties[mysql_type_bez]))
                    $ty = "number";
                else
                    $ty = "text";
                $fo .= "
	<input type='$ty' name='$outputFieldname' value='$field' maxlength=\"" . $maxlength . "\" >";
            }
            if ($aTableProp[columnNameOfId] == $fieldName)
                $fo .= $field;
        }


        if (!$aRightsHidden[$tableOrId][$fieldName]) {
            if ($webuser) {
                $val = "<div id='val_" . $fi . "_$fieldName' class=val></div>";
                $_SESSION[tempRights][$fi][allow][] = $fieldName;
            }
            if ($sUserFunction) {
                $fo = call_user_func_array($sUserFunction, array($fo, $fieldName, $arrRowContent));
            }
            if (function_exists("adminUserFuncGenerateField")) {
                $fo = call_user_func_array("adminUserFuncGenerateField", array($tableId, $fo, $fieldName, $arrRowContent, $sComingFrom, "edit"));
            }
        }
        return $fo . $val;
    }

    if ($viewtype == "view") {

        //Datum
        if ($aFieldProperties['type'] == "date")
            if ($field != "0")
                $field = date("d.m.Y H:i s", $field);

        //Länge beschneiden
        $fieldOld = $field;

        if (strlen($field) > 70) {
            $field = substr($field, 0, 70) . "...";
        } else {
            $field = $field;
        }
		
		//tinymce
        if ($aFieldProperties['type'] == "tinymce")
            if ($field != "")
                $field = strip_tags($field);

        //Datei
        if ($aFieldProperties['type'] == "url")
            if ($field != "")
                $field = "<a href=\"$fieldOld\" target='_blank'>$field</a>";

        //Datei
        if ($aFieldProperties['type'] == "file")
            if ($field != "")
                $field = "<a href=\"" . RELATIVEPATH . "/" . $field . "\" >" . str_replace("file/", "", $field) . "</a>";

        //IP
        if ($aFieldProperties['type'] == "ip")
            if ($field != "")
                $field = "<a href='http://network-tools.com/default.asp?host=$field' target='_blank'>" . $field . "</a>";

        //Bild
        if ($aFieldProperties['type'] == "image")
            if ($field != "")
                $field = $field . "<br><img src=\"" . RELATIVEPATH . "/" . str_replace("file/", "file/th_", $field) . "\" class=bnul>";

        //Preis
        if ($aFieldProperties['type'] == "price")
            if ($field != "")
                $field = number_format($field, 2);

        //Ermitteln ob Feld eine n zu 1 enth&auml;lt
        if ($aRel[NTo1][$tableId][$fieldName] != "") {
        }
        $fieldBeforeUserfunction = $field;
        if ($sUserFunction) {
            $field = call_user_func_array($sUserFunction, array($field, $fieldName, $arrRowContent));
        }
        if (function_exists("adminUserFuncGenerateField")) {
            $field = call_user_func_array("adminUserFuncGenerateField", array($tableId, $field, $fieldName, $arrRowContent, $sComingFrom, "view"));
        }
        //Wenn Feld nicht durch Benutzerfunktion geändert wird aber nto1DropdownTitleField gewählt ist, wandle in Titel um
        if ($field == $fieldBeforeUserfunction and $aFieldProperties['type'] == "nto1" and $aFieldProperties[nto1DropdownTitleField]) {
            $aT = getTableProperties($aFieldProperties[nto1TargetTable], $aManualFieldProperties);

            $aF = getFieldProperties($aFieldProperties[nto1TargetTable], $aFieldProperties[nto1TargetField], $aManualFieldProperties);
            //echo "$aFieldProperties[nto1TargetTable], $aFieldProperties[nto1TargetField],  ";
            //pre($aManualFieldProperties);
            $q = "SELECT * FROM $aT[name] WHERE $aF[name] = '$field'";
            $a = q($q);
            $field = $a[$aFieldProperties[nto1DropdownTitleField]];
        }
        return $field;
    }
}

function generateSearchField($tableOrId, $fieldName, $ajaxExec, $sessionsp, $aManualFieldProperties)
{
    $fp = getFieldProperties($tableOrId, $fieldName, $aManualFieldProperties);
    //pre($aManualFieldProperties);
    if ($fp['type'] == "nto1" and $fp['nto1DropdownTitleField']) {
        $aT = getTableProperties($fp['nto1TargetTable'], $aManualFieldProperties);
        $aF = getFieldProperties($fp['nto1TargetTable'], $fp['nto1DropdownTitleField'], $aManualFieldProperties);
        $rid = getNTo1RelationId($fp['nto1TargetTable'], $fp['name'], $tableOrId, $aManualFieldProperties);
        $val = "<div id='val_" . $ajaxExec . "_nto1_$rid' class=val></div>";
        $o .= "<input type='text' name='nto1_" . $rid . "' value='" . @$sessionsp["search_" . $fieldName] . "' id='search_" . $fieldName . "' value='" . $r . "' autocomplete='off' class='nto1_autocomplete display_table_paging_search c" . $ajaxExec . " search nto1' onChange=\"ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\">$val
		";
    } elseif ($fp['type'] == "date") {
        global $dateIncludeJsDone;
        if ($dateIncludeJsDone != 1) {
            $o .= '<link rel="stylesheet" type="text/css" href="/bncms/lib/datetimepicker-master/jquery.datetimepicker.css"/ >
<script src="/bncms/lib/datetimepicker-master/build/jquery.datetimepicker.full.min.js"></script>';
            $dateIncludeJsDone = 1;
        }
        $o .= "<input placeholder='Von' type='text' value='" . @$sessionsp["search_" . $fieldName . "_bncmsfromdate"] . "' id='search_" . $fieldName . "_bncmsfromdate' class='display_table_paging_search c" . $ajaxExec . " search date' onChange=\"ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\"><input type='text' placeholder='Bis' value='" . @$sessionsp["search_" . $fieldName . "_bncmstodate"] . "' id='search_" . $fieldName . "_bncmstodate' class='display_table_paging_search c" . $ajaxExec . " search date' onChange=\"ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\"><script>
		jQuery('.c" . $ajaxExec . ".search.date').datetimepicker({
			format: 'd.m.Y H:i:s',
			autoclose: true
		}).on('change', function(){
			jQuery('.xdsoft_datetimepicker').hide();
		});
		</script>";
    } elseif (strtolower($fp['mysql_type_bez']) == "set") {
        $lv = explode(",", $fp['length_values']);
        $o = "<select id='search_$fieldName' class='display_table_paging_search c" . $ajaxExec . " search' onChange=\"ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\"><option></option>";
        foreach ($lv as $k => $v) {
            $v = str_replace("'", "", $v);
            if (@$sessionsp["search_" . $fieldName] == $v)
                $ce = "selected";
            else
                $ce = "";
            $o .= "<option $ce>" . $v . "</option>";
        }
        $o .= "</select>";
    } elseif (checkInt($fp['mysql_type_bez']) or $fp['type'] == "number") {

        $o .= "<input placeholder='Von' type='text' value='" . @$sessionsp["search_" . $fieldName . "_bncmsfromint"] . "' id='search_" . $fieldName . "_bncmsfromint' class='display_table_paging_search c" . $ajaxExec . " search' onChange=\"ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\"><input type='text' placeholder='Bis' value='" . @$sessionsp["search_" . $fieldName . "_bncmstoint"] . "' id='search_" . $fieldName . "_bncmstoint' class='display_table_paging_search c" . $ajaxExec . " search' onChange=\"ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\">";
    } else {
        if (@$sessionsp["search_" . $fieldName] != "")
            $v = " value='" . $sessionsp["search_" . $fieldName] . "' ";
        else
            $v = "";
        $o = "<input type='text' $v id='search_$fieldName' class='display_table_paging_search c" . $ajaxExec . " search' onChange=\"ajax_submit('" . $ajaxExec . "','','" . RELATIVEPATHAJAX . "','" . RELATIVEPATHAPP . "');\">";
    }
    return $o;
}

function generateRelatedContent(
    $sComingFromRelations,
    $tableOrId,
    $row,
    $sDisplayTableRecursivePathOut,
    $aNTo1TablePath,
    $arrTableColumnNames,
    $columnNameOfId,
    $iSwitchInColumnSeparat,
    $aManualFieldProperties = "",
    $ajaxExec
)
{
    global $aRel;

    $table = returnTableAndId($tableOrId, $aManualFieldProperties)[0];
    $tableId = returnTableAndId($tableOrId, $aManualFieldProperties)[1];

    //Anzeige der Relationen mit separaten Icons
    $q = "SELECT * FROM conf_relation_visibility WHERE path REGEXP '^$sComingFromRelations-[0-9]+$'  AND (showWithEditIcons = 'Separat' OR showWithEditIcons = 'Beides')";
    $a = q($q, "", 1);
    getRelationVisibility($sComingFromRelations, $aManualFieldProperties);
    //pre($a);
    if (count($a)) {
        foreach ($a as $k => $v) {
            $op .= "<div id=\"div_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "_relations_" . $sDisplayTableRecursivePathOut . "_" . $v[path] . "_$ajaxExec\" style=\"display:none\"><table class=\"table_spacer table-responsiv\">";
            $r = preg_match('/-([0-9]+)$/', $v[path], $t);

            //$q = "SELECT * FROM conf_relations WHERE id = '$t[1]'";
            //$aRelation = q($q);
            $aRelation = getRelationPropertiesById($t[1], $aManualFieldProperties);
            //pre($aRelation);
            $iSwitchInSeparat = 0;
            if ($aRelation[type] == "nto1") {
                if ($aRelation[nto1TargetTable] == $tableId) {
                    //ist eingehend
                    if ($t = displayNTo1InputRelation($aNTo1TablePath, $sComingFromRelations, $tableOrId, $row, $aRelation[nto1SourceField], $aRelation[nto1SourceTable], $columnNameOfId, 'Separat', $aManualFieldProperties)) {
                        $op .= $t;
                        $iSwitchInSeparat = 1;
                    }
                } else {
                    //ist ausgehend
                    if ($t = displayNTo1OutputRelation($row[$aRelation[nto1SourceField]], $aNTo1TablePath, $sComingFromRelations, $tableOrId, $row, $aRelation[nto1SourceField], $arrTableColumnNames, $columnNameOfId, 'Separat', $aManualFieldProperties)) {
                        $op .= $t;
                        $iSwitchInSeparat = 1;
                    }
                }
            } else {
                //pre($aRel[NToM][$tableId]);
                //pre( $aRelation);
                foreach ($aRel[NToM][$tableId] as $k2 => $v2) {
                    if ($v2[relationId] == $aRelation[id]) {
                        $content = $v2;
                    }
                }
                if ($t = displayNToMRelation($content, $aNTo1TablePath, $sComingFromRelations, $tableOrId, $row, 'Separat', $aManualFieldProperties)) {
                    $op .= $t;
                    $iSwitchInSeparat = 1;
                }
            }
            //$op .= pre($aRelation,1);
            if ($iSwitchInSeparat) {
                //<tr><td>'icon_".$table."-".$tableId."_".$row[$columnNameOfId]."_relations_".$sDisplayTableRecursivePathOut."_".$v[path]."'</td></tr>

                $op .= "<script>jQuery('#icon_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "_relations_" . $sDisplayTableRecursivePathOut . "_" . $v[path] . "_$ajaxExec').css('display','inline');</script>";
            }
            $op .= "</table>";
            $op .= "</div>";
        }
    }

    //n:m Relation
    //Ermitteln ob n:m Relation vorhanden ist
    //pre($aRel['NToM'][$tableOrId]);
    if (is_array($aRel['NToM'][$tableOrId])) {
        foreach ($aRel['NToM'][$tableOrId] as $count => $content) {
            if ($t = displayNToMRelation(
                $content,
                $aNTo1TablePath,
                $sComingFromRelations,
                $tableOrId,
                $row,
                'Normal',
                $aManualFieldProperties
            )) {
                $opo .= $t;
                $iSwitchInDot = 1;
            }
        }
    }
    //Ermitteln ob n:1 Relation vorhanden ist (Output
    //n:1 Relation
    //pre($aRel);
    foreach ($row as $field => $content) {
        //Ermitteln ob n:1 Relation vorhanden ist
        if (isset($aRel[NTo1][$tableOrId][$field])) {

            if ($t = displayNTo1OutputRelation(
                $content,
                $aNTo1TablePath,
                $sComingFromRelations,
                $tableOrId,
                $row,
                $field,
                $arrTableColumnNames,
                $columnNameOfId,
                'Normal',
                $aManualFieldProperties
            )) {
                $opo .= $t;
                $iSwitchInDot = 1;
            }
        }
    }//Foreach row eof
    //Ermitteln ob n:1 Relation vorhanden ist (Input)
    //n:1 Relation
    if (is_array($aRel))
        foreach ($aRel[NTo1] as $linkingTable => $value) {
            //pre($value);
            foreach ($value as $linkingField => $targetTable) {
                if ($targetTable == $tableOrId) {

                    if ($t = displayNTo1InputRelation(
                        $aNTo1TablePath,
                        $sComingFromRelations,
                        $tableOrId,
                        $row,
                        $linkingField,
                        $linkingTable,
                        $columnNameOfId,
                        'Normal',
                        $aManualFieldProperties
                    )) {
                        $opo .= $t;
                        //echo "$aNTo1TablePath, $sComingFromRelations, $table, $tableId, $row, $linkingField, $linkingTable, $columnNameOfId, 'Normal'";
                        $iSwitchInDot = 1;
                    }
                }
            }
        }
    //pre($iSwitchInDot);
    if ($iSwitchInDot == 1) {

        $op .= "<div id=\"div_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "_relations_" . $sDisplayTableRecursivePathOut . "_$ajaxExec\" style=\"display:none\"><table class=\"table_spacer\">";
        $op .= "<script>jQuery('#icon_" . $table . "-" . $tableId . "_" . $row[$columnNameOfId] . "_relations_" . $sDisplayTableRecursivePathOut . "_$ajaxExec').css('display','inline');</script>";
        $op .= "$opo</table></div>";

    }
    return $op;
}

function displayNTo1InputRelation(
    $aNTo1TablePath,
    $sComingFromRelations,
    $tableOrId,
    $row,
    $linkingField,
    $linkingTable,
    $columnNameOfId,
    $showWithEditIcons,
    $aManualFieldProperties = ""
)
{
    global $aTable;

    $table = returnTableAndId($tableOrId, $aManualFieldProperties)[0];
    $tableId = returnTableAndId($tableOrId, $aManualFieldProperties)[1];

    //echo "$aNTo1TablePath, $sComingFromRelations, $table, $tableId, $row, $linkingField, $linkingTable, $columnNameOfId, $showWithEditIcons";
    $cr = getNTo1RelationId($tableOrId, $linkingField, $linkingTable, $aManualFieldProperties);
    //echo "$sComingFromRelations-$cr,$showWithEditIcons".checkRelationVisibility($sComingFromRelations."-".$cr,$showWithEditIcons);
    if (checkRelationVisibility($sComingFromRelations . "-" . $cr, $showWithEditIcons, $aManualFieldProperties)) {
        //echo $q = "SELECT * FROM conf_relation_visibility WHERE path = '".$sComingFromRelations."-".$cr."'";
        $aVisibility = getRelationVisibility($sComingFromRelations . "-" . $cr, $aManualFieldProperties);
        //pre($row);
        //$q = "SELECT * FROM conf_relations WHERE id = '$cr'";
        //$aRelation = q($q);
        $aRelation = getRelationPropertiesById($cr, $aManualFieldProperties);
        //pre($row);
        //pre($aRelation);
        $query = "SELECT " . getIdName(getNameFromTableString($linkingTable), $aManualFieldProperties) . " FROM " . getNameFromTableString($linkingTable) . " WHERE $linkingField = '" . $row[$aRelation[nto1TargetField]] . "'";
        $RS5 = dbQuery($query);
        //echo pre($RS5,1);
        if (count($RS5) > 0) {
            $q = "SELECT name FROM conf_tables WHERE id = '$linkingTable'";
            $a = dbQuery($q);
            //if (!in_array(c($a[0][name])."-".$linkingTable, $aNTo1TablePath)) {

            $langTable = formTableName($linkingTable, $aManualFieldProperties);

            if ($aVisibility[title])
                $sT = $aVisibility[title];
            else
                $sT = "<= " . $langTable . " <span style='font-size:13px; font-weight:normal'>(" . formTableName($linkingTable, $aManualFieldProperties) . ": <b>" . formFieldName($linkingTable, $linkingField, $aManualFieldProperties) . "</b>)</span>";
            $op .= "
				<tr>
					<td valign=\"top\" colspan=\"" . (50) . "\"  class=\"table_relations\"><div class=\"table_overall leftborder\">";

            $op .= "<h5>$sT</h5>";

            $op .=
                displayTable(
                    $linkingTable,
                    getIdName($linkingTable, $aManualFieldProperties),
                    $linkingField . " = '" . $row[$aRelation[nto1TargetField]] . "' ",
                    "",
                    "",
                    "",
                    c($table) . "-" . $tableOrId . "_" . $row[$columnNameOfId],
                    "",
                    "yes",
                    "nto1input",//10
                    5,
                    0,
                    0,
                    packGlobals(),
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",//20
                    $aManualFieldProperties,
                    "",
                    "",
                    "",
                    "",
                    $sComingFromRelations . "-" . $cr
                );

            $op .= "
					</div></td>
				</tr>";
            //	}
        }
    }
    return $op;
}

function displayNTo1OutputRelation(
    $content,
    $aNTo1TablePath,
    $sComingFromRelations,
    $tableOrId,
    $row,
    $field,
    $arrTableColumnNames,
    $columnNameOfId,
    $showWithEditIcons,
    $aManualFieldProperties = ""
)
{
    global $aTable, $aRel;

    $table = returnTableAndId($tableOrId, $aManualFieldProperties)[0];
    $tableId = returnTableAndId($tableOrId, $aManualFieldProperties)[1];

    $cr = getNTo1RelationId(getIdFromTableString($aRel['NTo1'][$tableOrId][$field]), $field, $tableOrId, $aManualFieldProperties);
    //echo "hu".checkRelationVisibility($sComingFromRelations."-".$cr,$showWithEditIcons,$aManualFieldProperties);
    //pre($aManualFieldProperties);

    if (checkRelationVisibility($sComingFromRelations . "-" . $cr, $showWithEditIcons, $aManualFieldProperties)) {

        $aVisibility = getRelationVisibility($sComingFromRelations . "-" . $cr, $aManualFieldProperties);

        /*foreach ($aTable as $key => $value) {
			if ($value[id] == $aRel['NTo1'][$tableOrId][$field]) {
				$langTable = $value[lang];
			}
		}*/
        $langTable = formTableName($aRel['NTo1'][$tableOrId][$field], $aManualFieldProperties);

        /*if (!is_numeric($aRel['NTo1'][$tableOrId][$field]))
			$langTable = $aRel['NTo1'][$tableOrId][$field];*/
        $aFieldProp = getFieldProperties($tableOrId, $field, $aManualFieldProperties);
        $aTargetField = getFieldProperties($aFieldProp[nto1TargetTable], $aFieldProp[nto1TargetField]);
        $query = " SELECT * FROM " . getNameFromTableString($aRel['NTo1'][$tableOrId][$field]) . " WHERE " . $aTargetField[name] . " = '$content' ";
        $RS = dbQuery($query);
        if (count($RS) > 0) {

            if (!in_array(c(getNameFromTableString($aRel['NTo1'][$tableOrId][$field])) . "-" . $aRel['NTo1'][$tableOrId][$field], $aNTo1TablePath)) {

                if ($aVisibility[title])
                    $sT = $aVisibility[title];
                else
                    $sT = "=> " . $langTable . " <span style='font-size:13px; font-weight:normal'>(" . formTableName($tableOrId, $aManualFieldProperties) . ": <b>" . formFieldName($tableOrId, $field, $aManualFieldProperties) . "</b>)</span>";
                $op .= "
				<tr>
					<td valign=\"top\" colspan=\"" . (count($arrTableColumnNames) + 2) . "\"  class=\"table_relations\"><div class=\"table_overall leftborder\">";
                $op .= "<h5>$sT</h5>";

                $op .= displayTable(
                    getIdFromTableString($aRel['NTo1'][$tableOrId][$field]),
                    getIdName(getNameFromTableString($aRel['NTo1'][$tableOrId][$field]), $aManualFieldProperties),
                    " $aTargetField[name] = '$content' ",
                    "",
                    "",
                    "",
                    c($table) . "-" . $tableOrId . "_" . $row[$columnNameOfId],
                    "",
                    "yes",
                    "nto1output",//10
                    5,
                    0,
                    0,
                    packGlobals(),
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",//20
                    $aManualFieldProperties,
                    "",
                    "",
                    "",
                    "",
                    $sComingFromRelations . "-" . $cr
                );
                $op .= "
					</div></td>
				</tr>";
            }
        }
    }
    //echo htmlentities($op);
    return $op;
}

function displayNToMRelation(
    $content,
    $aNTo1TablePath,
    $sComingFromRelations,
    $tableOrId,
    $row,
    $showWithEditIcons,
    $aManualFieldProperties = ""
)
{
    global $aTable;

    $table = returnTableAndId($tableOrId, $aManualFieldProperties)[0];
    $tableId = returnTableAndId($tableOrId, $aManualFieldProperties)[1];

    //echo "$content, $aNTo1TablePath, $sComingFromRelations, $table,$tableId, $row, $showWithEditIcons";
    $assignTable = $content[assignTable];
    $destTable = getNameFromTableString($content[destTable]);
    $destTableId = getIdFromTableString($content[destTable]);
    $identifier = $content[sourceFieldname];
    $destIdentifier = $content[destFieldname];

    if (!in_array(c($destTable) . "-" . $destTableId, $aNTo1TablePath)) {
        //echo $sComingFromRelations."-".$content[relationId]."-a";
        //echo "hu";
        //pre($aManualFieldProperties);
        if (checkRelationVisibility($sComingFromRelations . "-" . $content[relationId] . "-a", $showWithEditIcons, $aManualFieldProperties)) {
            //echo $q = "SELECT * FROM conf_relation_visibility WHERE path = '".$sComingFromRelations."-".$content[relationId]."-a'";
            //$aVisibility = q($q);
            $aVisibility = getRelationVisibility($sComingFromRelations . "-" . $content[relationId] . "-a", $aManualFieldProperties);
            //pre($aVisibility);
            $sQueryOver = "SELECT $destTable." . getIdName($destTable, $aManualFieldProperties) . ", $assignTable.* FROM $table, $assignTable, $destTable
			WHERE $table." . getIdName($table, $aManualFieldProperties) . " = '" . $row[getIdName($table, $aManualFieldProperties)] . "'
			AND $assignTable.$identifier = $table." . getIdName($table, $aManualFieldProperties) . "
			AND $assignTable.$destIdentifier = $destTable." . getIdName($destTable, $aManualFieldProperties) . "";
            $sQueryOver2 = "SELECT $destTable.*, $assignTable." . getIdName($destTable, $aManualFieldProperties) . " as bncms_assign_id  FROM $table, $assignTable, $destTable
			WHERE $table." . getIdName($table, $aManualFieldProperties) . " = '" . $row[getIdName($table, $aManualFieldProperties)] . "'
			AND $assignTable.$identifier = $table." . getIdName($table, $aManualFieldProperties) . "
			AND $assignTable.$destIdentifier = $destTable." . getIdName($destTable, $aManualFieldProperties) . "";
            //Tabellenname f&uuml;r Output bereitstellen
            /*foreach ($aTable as $key => $value) {
				if ($value[name]."-".$value[id] == $destTable."-".$destTableId) {
					$langTable = $value[lang];
				}
			}*/
            if ($destTableId)
                $destTableOrId = $destTableId;
            else
                $destTableOrId = $destTable;
            $langTable = formTableName($destTableOrId, $aManualFieldProperties);
            $RS = dbQuery($sQueryOver);
            if (count($RS) > 0) {
                if ($aVisibility[title])
                    $sT = $aVisibility[title];
                else
                    $sT = "<=o=> " . $langTable . " <span style='font-size:13px;'>($assignTable)</span>";
                $op .= "
				<tr>
					<td valign=\"top\" colspan=\"" . (count($arrTableColumnNames) + 2) . "\"  class=\"table_relations\"><div class=\"table_overall leftborder\">";
                $op .= "<h5>$sT</h5>";

                $op .= displayTable(
                    $destTableId,
                    getIdName($destTableId, $aManualFieldProperties),
                    "",
                    $sQueryOver,
                    $sQueryOver2,
                    "",
                    c($table) . "-" . $tableId . "_" . $row[getIdName($tableOrId, $aManualFieldProperties)],
                    "yes",
                    "",
                    "ntom",//10
                    5,
                    0,
                    0,
                    packGlobals(),
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",//20
                    $aManualFieldProperties,
                    "",
                    "",
                    "",
                    "",
                    $sComingFromRelations . "-" . $content[relationId] . "-a"
                );

                $op .= "</div>
				</td>
				</tr>";

            }
        }
    }
    return $op;
}

function buildLimit($place, $table, $tableId, $limit)
{
    if (is_array($_SESSION[aActivePagesRelations])) {
        foreach ($_SESSION[aActivePagesRelations] as $sPlace => $aActiveTable) {
            if ($sPlace == $place) {
                foreach ($aActiveTable as $sActiveTable => $sActivePage) {
                    //$op .= $sActiveTable." == ".$table."-".$tableId;
                    if ($sActiveTable == $table . "-" . $tableId)
                        $limitSql .= " " . $limit * ($sActivePage - 1) . "," . $limit;
                }
            }
        }
    }
    if ($limitSql == "")
        $limitSql = "0," . $limit;
    if ($limitSql == "-$limit," . $limit)
        $limitSql = "0," . $limit;
    $limitSql = preg_replace('/(\-[0-9]+),/', "0", $limitSql);
    return $limitSql;
}

?>