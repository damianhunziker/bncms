<?php

if (@$_GET['repairDB'] != "") {
	//todo lösche nzu1 zuweisungen die keine felder mehr haben, macht sonst unnötig relationen
	
	//n:1 Reparieren
	foreach ($aRel['NTo1'] as $key => $value) {
		$query="SELECT * FROM $key";
		//echo "<br>";
		$aTableRp=dbQuery($query);
		foreach ($aTableRp as $row => $rowContent) {
			foreach ($value as $idName => $destTable) {
				$query="SELECT count(*) as count FROM $destTable WHERE id = '$rowContent[$idName]'";
				$aCount=dbQuery($query);
				//print_r($aCount);
				if ($aCount[count] == 0) {
					//echo "<br>";
					$query="UPDATE $key SET $idName = '' WHERE id = '$rowContent[id]'";
					dbQuery($query);
					//echo "<br>";
				}
				
				//echo "<br>";
			}
		}
	}
	foreach ($aRel['NToM'] as $startTable => $v) {
		foreach ($v as $k => $value) {
			$destTable = $value['destTable'];
			$sAssignTable=getAssignTableNToM($startTable, $destTable);
			$aIdentifierNToM=getIdentifierNToM($startTable, $destTable);
			
			$query="SELECT * FROM $sAssignTable"; 
			$aAssignTable=dbQuery($query);
			foreach ($aAssignTable as $row => $rowContent) {
	
				$query="SELECT count(*) as count FROM $startTable WHERE id = '".$rowContent[$aIdentifierNToM[0]]."'";
				$aCount=dbQuery($query);
				if ($aCount[count] == 0) {
					$query="DELETE FROM $sAssignTable WHERE id = '$rowContent[id]'";
					dbQuery($query);
				}
				
				
				$query="SELECT count(*) as count FROM $destTable WHERE id = '".$rowContent[$aIdentifierNToM[1]]."'";
				$aCount=dbQuery($query);
				if ($aCount[count] == 0) {
					$query="DELETE FROM $sAssignTable WHERE id = '$rowContent[id]'";
					dbQuery($query);
				}
				//$aCount=dbQuery($query);
			}
		}
	}
	
	//Zusatz spezifisch f&uuml;r die Zusammenhang einer n:1 zu einer n:m
	$query="SELECT * FROM ass_product_title";
	$aAssignTable=dbQuery($query);
	foreach ($aAssignTable as $row => $rowContent) {
		$query="SELECT * FROM product WHERE id = '$rowContent[id_product]'";
		$aProduct=dbQuery($query);
		$query="SELECT count(*) as count FROM category WHERE id = '$aProduct[id_category]'";
		$aCount=dbQuery($query);
		if ($aCount[count] == 0) {
			$query="DELETE FROM $sAssignTable WHERE id = '$rowContent[id]'";
			dbQuery($query);
		}
	}
	echo "<script type='text/javascript'>window.location.href='index.php'</script>";
}


if (@$_GET['action'] == "delete") {
	
	if (is_numeric($_GET[table])) {
		$aT = getTableProperties($_GET[table]);
		$name = $aT[name];
	} else
		$name = $_GET[table];
	$_SESSION['toDelTable'] = $name;
	$_SESSION['toDelId'] = $_GET[id];
	echo "<script type='text/javascript'>
	Check = confirm('Wollen Sie den Eintrag wirklich l'+unescape(\"%F6\")+'schen?');
	if (Check == false) {
	  window.close();
	 } else {
	 	window.location.href='index.php?action=delFromSession&table=$name&id=$_GET[id]';
	 }
	</script>";
}

if (@$_GET['action'] == "delFromSession") {
	//Testen ob eine n:m relation besteht
	/*$sAssignTable=getAssignTableNToM($_GET[table]);
	if ($sAssignTable != "") {
		$aIdentifierNToM=getIdentifierNToM($_GET[table]);
		$query="DELETE FROM  $_GET[table] WHERE $aIdentifierNToM[0] = '$_GET[id]'";
		dbQuery($query);
	}*/
	$query="DELETE FROM ".e($_GET[table])." WHERE ".getIdName($_GET[table])." = '$_GET[id]'";
	dbQuery($query);
	echo "<script type='text/javascript'>window.opener.location.reload()</script>";
	echo "<script type='text/javascript'>window.close();</script>";
}

/*if ($_POST['saveAssignTableValues'] != "") {

	foreach ($_POST as $key => $value) {
		if (preg_match('/[0-9]+_[a-zA-Z_]+/',$key)) {
			$aKey=explode("_",$key);
			if (is_array($aKey)) {
				echo $query="UPDATE $_SESSION[assignTableName] 
				SET $aKey[1] = '$value'
				WHERE id = '$aKey[0]'";
				
				dbQuery($query);
			}
		}
	}
	$outTable = getStartTableNToM($_POST[table]);
	$aPropOutTable = getTableProperties($outTable);
	$outColumnNameOfId = $aPropOutTable['columnNameOfId'];
	$_SESSION[assignTableName]="";
	/*echo "<script type='text/javascript'>window.location.href='edit_entry.php?table=$outTable&id=$_SESSION[lastRowIdNToM]&columnNameOfId=$outColumnNameOfId'</script>";
	//echo "<script type='text/javascript'>window.opener.location.reload()</script>";
	
}*/
if ($_POST) {
	if ($_POST['saveAssignTableValues'] != "") {
		$aAssignTable = getTableProperties($_SESSION[assignTableName]);
		//pre($_POST);
		//exit();
		foreach ($_POST as $key => $value) {
			if (preg_match('/[0-9]+_[a-zA-Z_]+/',$key)) {
				$aKey=explode("_",$key);
				$k = str_replace("$aKey[0]_","",$key);
				if (is_array($aKey)) {
					
					//$value = @strip_tags($value);
					
					$query="UPDATE $aAssignTable[name] 
					SET $k = '".mysqli_real_escape_string($DB, $value)."'
					WHERE ".getIdName($_SESSION[assignTableName])." = '$aKey[0]'";
					dbQuery($query);
					//hardcode
					$lastkey = $aKey[0];
				}
			}
		}
		/*$outTable = getStartTableNToM($_POST[table]);
		$aPropOutTable = getTableProperties($outTable);
		$outColumnNameOfId = $aPropOutTable['columnNameOfId'];*/
		$_SESSION[assignTableName]="";
	}
}
if (@$_POST['execFilter'] != "") {
	$_SESSION['NTo1Filter'][$_POST['execFilter']] = $_POST['NTo1Filter'];
}
/*if (@$_POST['savePost'] == "on") {
		if ($_POST['action'] == "new" or !$_POST[id]) {
			if (!$_POST[id]) {
				$_POST[id] = getNextAutoincrementValue($_POST[table]);
			}
			$query="INSERT INTO $_POST[table] SET $_POST[columnNameOfId] = '$_POST[id]'";
			dbQuery($query);
			$_POST[id]=mysqli_insert_id($DB);
		}
 		if ($_POST[deleteFile]) {
			foreach ($_POST[deleteFile] as $k =>  $v) {
				$query="SELECT $k FROM $_POST[table] WHERE ".getIdName($_POST[table])." = '$_POST[id]'";
				$aImagePaths = dbQuery($query);
				unlink("../".$aImagePaths[0][$k]);
				unlink("../th_".$aImagePaths[0][$k]);
				$query="UPDATE $_POST[table] SET $k = '' WHERE ".getIdName($_POST[table])." = '$_POST[id]'";
				$aImagePaths = dbQuery($query);
			}
		}
		
		if ($_FILES) {
			foreach ($_FILES as $k => $v) {
				if ($v['name'] != "") {
					$filename = $v['name']; 
					$filename = preg_replace('/[^a-z0-9A-Z.\-_]/', "", $filename);
					$addFilename = "";
					while (file_exists(PATH."/file/".$addFilename.$filename)) {
						$addFilename = rand(0,1000000);
					}
					if (!move_uploaded_file($v['tmp_name'], PATH."/file/".$addFilename.$filename)) {
						echo "error moveulpoadedfile";
					} 
					$i = getIdName($_POST[table]);
					$query="SELECT $k FROM $_POST[table] WHERE $i = '$_POST[id]'";
					$aImagePaths = dbQuery($query);
					@unlink(PATH."/".$aImagePaths[0][$k]);
					@unlink(PATH."/th_".$aImagePaths[0][$k]);
					
					if ($aProp[$_POST[tableId]][$k] == "image") {
						$a = getimagesize(PATH.'/file/'.$addFilename.$filename);
						//print_r($a);
						$ai = getFieldProperties($_POST[tableId],$k);
						$ff = strtolower(getFileFormat($v['name']));
						if ($a[0] < $ai[min_width] 
							or $a[1] < $ai[min_height] 
							or $a[0] > $ai[max_width]
							or $a[1] > $ai[max_height] or (
								$ff != "jpg" and
								$ff != "jpeg" and
								$ff != "gif" and
								$ff != "png"
							)
						) {
							@unlink("file/".$addFilename.$filename);
							echo "Das Format muss jpg, gif oder png sein und die Gr&ouml;sse zwischen $ai[min_width] x $ai[min_height]px und  $ai[max_width] x $ai[max_height]px. <a href='#' onclick='window.history.back()'>Zur&uuml;ck</a>";
							exit();
						}
						//Beschneiden
						if ($ai[processing] != "Keine")
							resizeRatio(PATH.'/file/'.$addFilename.$filename, str_replace(" Beschneiden","",$ai[processing]));
						//thumb
						resize(PATH.'/file/'.$addFilename.$filename, "200");
					}
					$query="UPDATE $_POST[table] SET $k = 'file/".$addFilename.$filename."' WHERE $i = '$_POST[id]'";
					dbQuery($query);
				}
			}
		}
		
	
	//eof Bildupload
	if ($_POST[table1] != "") {
		//echo "<pre>";
		//print_r($_POST);
		//exit();
		//echo "getAssignmentTableName($_POST[table1], $_POST[table2])";
		$sAssignmentTableName = getAssignmentTableName($_POST[table1], $_POST[table2]);
		$aAssignmentFieldNames = getAssignmentFieldNames($_POST[table1], $_POST[table2]);

		if ($_POST[action] == "new") {
			echo $query = "INSERT INTO $sAssignmentTableName SET $aAssignmentFieldNames[sourceFieldname] = '".$_POST["id_".$_POST[table1]]."', $aAssignmentFieldNames[destFieldname] = '".$_POST["id_".$_POST[table2]]."'";
			dbQuery($query);
		} else {
			echo $query = "UPDATE $sAssignmentTableName SET SET $aAssignmentFieldNames[sourceFieldname] = '".$_POST["id_".$_POST[table1]]."', $aAssignmentFieldNames[destFieldname] = '".$_POST["id_".$_POST[table2]]."' WHERE id = '$_POST[id]'";
			dbQuery($query);
		}
	} else {
		foreach ($_POST as $key => $value) {	
			if (strpos("a".$key,"ntom_") == 1) {
				//Werte aus ntom Ajax Suche speichern
				$a = explode("_",$key);
				$q = "SELECT * FROM conf_relations WHERE id = '$a[1]'";
				$aRelation = dbQuery($q);
				$aT = getTableProperties($aRelation[0][table2]);
				$value = str_replace(", ",",",$value);
				$aV = explode(",",$value);
				foreach ($aV as $k => $v) {
					if ($v) {
						$q = "SELECT * FROM ".$aT[name]." WHERE ".$aRelation[0][ntomAjaxDisplayTitleField]." LIKE '$v'";
						$a = dbQuery($q);
						$aP = getTableProperties($_POST[table]);
						$q = "SELECT * FROM ".$aRelation[0][name]." WHERE ".$aRelation[0][ntomAssignFieldTable1]." = '".$_POST[$aP[columnNameOfId]]."' AND ".$aRelation[0][ntomAssignFieldTable2]." = '".$a[0][$aT[columnNameOfId]]."'";
						$aS = dbQuery($q);
						if (!count($aS)) {
							$q = "INSERT INTO ".$aRelation[0][name]." SET ".$aRelation[0][ntomAssignFieldTable1]." = '".$_POST[$aP[columnNameOfId]]."', ".$aRelation[0][ntomAssignFieldTable2]." = '".$a[0][$aT[columnNameOfId]]."'";
							dbQuery($q);
						}
					}
				}
			} elseif ($key != 'bncms_closeafter' and $key != "deleteFile"  and $key != 'table' and $key != 'tableId' and $key != 'id' and $key != 'savePost' and $key != 'columnNameOfId' ) {
				//Test ob feld existiert
				$a = getTableProperties($_POST[table]);
				if ($aProp[$a[id]][$key] == "password")
					$value = md5($value);
				$query="SELECT `$key` FROM `$_POST[table]` WHERE `".getIdName($_POST[table])."`='$_POST[id]'";
				$aTest=dbQuery($query);
				if ($aProp[$_POST[tableId]][$key] == "date" and !is_numeric($value)) {
					$a = explode(" ",$value);
					$aD = explode(".",$a[0]);
					$aT = explode(":",$a[1]);
					$value = mktime($aT[0],$aT[1],$aT[2],$aD[1],$aD[0],$aD[2]);
				}
				if (is_array($aTest)) {
					if (@!in_array($key,$aRightsUnchangeable[$_POST[table]])) {
						$query="UPDATE `$_POST[table]` 
						SET `$key` = '".mysqli_real_escape_string($DB,$value)."'
						WHERE `".getIdName($_POST[table])."`='$_POST[id]'";
						echo "<br>";
						dbQuery($query);
					}
				} else  {
					echo "<br>Feldname $key nicht gefunden in Tabelle $_POST[table].";
				}
			}
		}
	}
	echo "<script type='text/javascript'>window.opener.location.reload()</script>";
	if ($_POST[bncms_closeafter])
		echo "<script type='text/javascript'>window.close()</script>";
	else
		echo "<script type='text/javascript'>window.history.back()</script>";
}*/
if (@$_POST['savePost'] == "on") {
	
	/*sicherheit*/
	//wenn speichern will alle variablen escapen
	foreach ($_POST as $k => $v) {
		if (!is_array($v))
			$_POST[$k] = e($v);
	} 
	//frontend kann keine Speicherungen wiederholen
	if (@md5(serialize($_POST).serialize($_FILES)) != $_SESSION[lastSavePostVars]) {
		$_SESSION[lastSavePostVars]= md5(serialize($_POST).serialize($_FILES));
		if ($webuser) {
			if ($_SESSION[tempRights][$_POST[bncms_form]]) {
				$_POST[table] = $_SESSION[tempRights][$_POST[bncms_form]][table];
				$_POST[tableId] = $_SESSION[tempRights][$_POST[bncms_form]][tableId];
				//Wenn Tabelle ohne Grundkonfiguration hat keine tableId, wird zu name
				if (!$_POST[tableId])
					$_POST[tableId] = $_POST[table];
				$_POST[columnNameOfId] = $_SESSION[tempRights][$_POST[bncms_form]][columnNameOfId];

				$_POST[id] = $_SESSION[tempRights][$_POST[bncms_form]][id];
				$al = 1;
				//print_r($_SESSION[tempRights][$_POST[bncms_form]]);
			} else {
				exit('prohibited');
			}
			
		}
			
		if ($_POST['action'] == "new" or !$_POST[id]) {
			//if (!$_POST[id]) {
				$_POST[id] = getNextAutoincrementValue($_POST[table]);
			//}
			$query="INSERT INTO $_POST[table] SET $_POST[columnNameOfId] = '$_POST[id]'";
			dbQuery($query);
			//$_POST[id]=mysql_insert_id();
		}
 		if ($_POST[deleteFile]) {
			
			foreach ($_POST[deleteFile] as $k =>  $v) {
				
				$query="SELECT $k FROM $_POST[table] WHERE ".getIdName($_POST[table])." = '$_POST[id]'";
				$aImagePaths = dbQuery($query);
				
				@unlink(PATH."/".$aImagePaths[0][$k]);
				@unlink(PATH."/th_".$aImagePaths[0][$k]);
				$query="UPDATE $_POST[table] SET $k = '' WHERE ".getIdName($_POST[table])." = '$_POST[id]'";
				dbQuery($query);

			}
		}
		if ($_FILES) {
			foreach ($_FILES as $k => $v) {
				if ($v['name'] != "") {
					$filename = $v['name']; 
					$filename = preg_replace('/[^a-z0-9A-Z.\-_]/', "", $filename);
					$addFilename = "";
					while (file_exists(PATH."/file/".$addFilename.$filename)) {
						$addFilename = rand(0,1000000);
					}
					if (!move_uploaded_file($v['tmp_name'], PATH."/file/".$addFilename.$filename)) {
						echo "error moveulpoadedfile";
					} 
					$i = getIdName($_POST[table]);
					$query="SELECT $k FROM $_POST[table] WHERE $i = '$_POST[id]'";
					$aImagePaths = dbQuery($query);
					@unlink(PATH."/".$aImagePaths[0][$k]);
					@unlink(PATH."/th_".$aImagePaths[0][$k]);
					
					if ($aProp[$_POST[tableId]][$k] == "image") {
						$a = getimagesize(PATH.'/file/'.$addFilename.$filename);
						//print_r($a);
						$ai = getFieldProperties($_POST[tableId],$k);
						$ff = strtolower(getFileFormat($v['name']));
						if ($a[0] < $ai[min_width] 
							or $a[1] < $ai[min_height] 
							or $a[0] > $ai[max_width]
							or $a[1] > $ai[max_height] or (
								$ff != "jpg" and
								$ff != "jpeg" and
								$ff != "gif" and
								$ff != "png"
							)
						) {
							@unlink("file/".$addFilename.$filename);
							echo "Das Format muss jpg, gif oder png sein und die Gr&ouml;sse zwischen $ai[min_width] x $ai[min_height]px und  $ai[max_width] x $ai[max_height]px. <a href='#' onclick='window.history.back()'>Zur&uuml;ck</a>";
							exit();
						}
						//Beschneiden
						if ($ai[processing] != "Keine")
							resizeRatio(PATH.'/file/'.$addFilename.$filename, str_replace(" Beschneiden","",$ai[processing]));
						//thumb
						resize(PATH.'/file/'.$addFilename.$filename, "200");
					}
					$query="UPDATE $_POST[table] SET $k = 'file/".$addFilename.$filename."' WHERE $i = '$_POST[id]'";
					dbQuery($query);
				}
			}
		}
		
		
		//eof Bildupload
		if ($_POST[table1] != "") {
			//echo "<pre>";
			//print_r($_POST);
			//exit();
			//echo "getAssignmentTableName($_POST[table1], $_POST[table2])";
			$sAssignmentTableName = getAssignmentTableName($_POST[table1], $_POST[table2]);
			$aAssignmentFieldNames = getAssignmentFieldNames($_POST[table1], $_POST[table2]);
	
			if ($_POST[action] == "new") {
				$query = "INSERT INTO $sAssignmentTableName SET $aAssignmentFieldNames[sourceFieldname] = '".$_POST["id_".$_POST[table1]]."', $aAssignmentFieldNames[destFieldname] = '".$_POST["id_".$_POST[table2]]."'";
				dbQuery($query);
			} else {
				$query = "UPDATE $sAssignmentTableName SET SET $aAssignmentFieldNames[sourceFieldname] = '".$_POST["id_".$_POST[table1]]."', $aAssignmentFieldNames[destFieldname] = '".$_POST["id_".$_POST[table2]]."' WHERE id = '$_POST[id]'";
				dbQuery($query);
			}
		} else {
		
			foreach ($_POST as $key => $value) {	
				//webuser darf nur die felder schreiben, die ihm in der session erlaubt wurden
				/*sicherheit*/
				if ($webuser) { 
					if ( strpos("a".$key,"bncms_") != 1 and $key != "after" and $key != "deleteFile"  and $key != 'table' and $key != 'tableId' and $key != 'id' and $key != 'savePost' and $key != 'columnNameOfId') {
						if (in_array($key, $_SESSION[tempRights][$_POST[bncms_form]][allow])) {
							$allowedpost[$key] = $value;
						} else {
							hacklog("prohibited fieldname $key");
							echo "gprohibited fieldname $key";
						}
					} else 
						$allowedpost[$key] = $value;
				 } else
					$allowedpost[$key] = $value;
			}
			$_POST = $allowedpost;

			//Webuser bevor speichern kann Validierung über User Function. Variablen die er nicht ändern darf einfügen. Muss erst in allowedpost lesen weil sonst hinzugefügte Felder als nicht in session erlaubt erkennen würde.
			$executeSave = 1;
			if ($webuser) {
				if (function_exists("before_".$_POST[table]))
					$executeSave = call_user_func_array("before_".$_POST[table], $_POST);
			}
			//echo "hu";
			if ($executeSave)
			foreach ($_POST as $key => $value) {	
				if (strpos("a".$key,"ntom_") == 1) {
					//Werte aus ntom Ajax Suche speichern
					$a = explode("_",$key);
					$q = "SELECT * FROM conf_relations WHERE id = '$a[1]'";
					$aRelation = dbQuery($q);
					$aT = getTableProperties($aRelation[0][table2]);
					$value = str_replace(", ",",",$value);
					$aV = explode(",",$value);
					foreach ($aV as $k => $v) {
						if ($v) {
							$q = "SELECT * FROM ".$aT[name]." WHERE ".$aRelation[0][ntomAjaxDisplayTitleField]." LIKE '$v'";
							$a = dbQuery($q);
							//pre($a);
							$aP = getTableProperties($_POST[table]);
							$q = "SELECT * FROM ".$aRelation[0][name]." WHERE ".$aRelation[0][ntomAssignFieldTable1]." = '".$_POST[$aP[columnNameOfId]]."' AND ".$aRelation[0][ntomAssignFieldTable2]." = '".$a[0][$aT[columnNameOfId]]."'";
							$aS = dbQuery($q);
							if (!count($aS)) {
								$q = "INSERT INTO ".$aRelation[0][name]." SET ".$aRelation[0][ntomAssignFieldTable1]." = '".$_POST[$aP[columnNameOfId]]."', ".$aRelation[0][ntomAssignFieldTable2]." = '".$a[0][$aT[columnNameOfId]]."'";
								dbQuery($q);
							}
							//exit();
						}
					}
				} elseif ((($al and $webuser) or !$webuser) and $key != "bncms_form" and $key != "after" and $key != "deleteFile"  and $key != 'table' and $key != 'tableId' and $key != 'id' and $key != 'savePost' and $key != 'columnNameOfId' and $key != 'bncms_closeafter') {
					//Test ob feld existiert
					$aF = getFieldProperties($_POST[tableId],$key,$_SESSION[manualFieldProperties][$_POST[bncms_form]]);
					//pre($aF);
					//pre($_POST);
					//pre($_SESSION);
					//pre($aProp);
					if ($aF['type'] == "password")
						$value = md5($value);
					if ($aF['type'] == "date")
						if (strpos($value,":")) { 
							$a = explode(" ", $value);
							$d = explode(".", $a[0]);
							$t = explode(":", $a[1]);
							$value = mktime($t[0], $t[1], $t[2], $d[1], $d[0], $d[2]);
						}
					
					//Sicherheit wenn länger als 50 Zeichen kein leerzeichen vorkommt füge es ein
					//$value = preg_replace("/([^ ]{50,51})/",'$1 ',$value);
					$query="SELECT `$key` FROM `$_POST[table]` WHERE `".getIdName($_POST[table])."`='$_POST[id]'";
				
					
						$aTest=dbQuery($query);
					if (is_array($aTest)) {
						if (@!in_array($key,$aRightsUnchangeable[$_POST[tableId]])) {
							$query="UPDATE `$_POST[table]` 
							SET `$key` = '".($value)."'
							WHERE `".getIdName($_POST[table])."`='$_POST[id]'";
							//echo "<br>";
							dbQuery($query);
							echo mysqli_error($DB);
						}
					} else  {
						echo "<br>Feldname $key nicht gefunden in Tabelle $_POST[table].";
					}
				}
			}
		}
	} else {
	}
	/*echo "<script type='text/javascript'>window.opener.location.reload()</script>";
	echo "<script type='text/javascript'>window.close()</script>";*/
	if (!$webuser) {
		echo "<script type='text/javascript'>window.opener.location.reload()</script>";
		if ($_POST[bncms_closeafter])
			echo "<script type='text/javascript'>window.close()</script>";
		else
			echo "<script type='text/javascript'>window.history.back()</script>";
	}
}	
if (@$_GET['duplicate'] == true) {
	$tn = getTableProperties($_GET[table]);
	$query="SELECT * FROM $tn[name] WHERE id = '$_GET[id]'";
	$aDestId=dbQuery($query);
	$query="SELECT id FROM conf_tables WHERE name = '$_GET[table]'";
	$aTableId=dbQuery($query);
	$query="SELECT * FROM conf_fields WHERE id_table = '".$aTableId[0][id]."' and type='image'";
	$aImageFields = dbQuery($query);
	$query="INSERT INTO $tn[name] SET ";
	foreach ($aDestId[0] as $key => $value) {
		//Abfrage f&uuml;r Bild Duplizierung
		foreach ($aImageFields as $keyField => $valueField) {
				if ($valueField[name] == $key) {
					//todo formate, bildpfad
					$sNewFileName=str_replace(".jpg","",$value).rand(0,100).".jpg";
					while (file_exists(PATH."/".$sNewFileName)) {
						$sNewFileName=str_replace(".jpg","",$value).rand(0,100).".jpg";
					}
					@copy(PATH."/".$value, PATH."/".$sNewFileName);
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
	
	echo "<script type='text/javascript'>window.opener.location.reload()</script>";
	if ($_POST[bncms_closeafter] or $webuser)
		echo "<script type='text/javascript'>window.close()</script>";
	else
		echo "<script type='text/javascript'>window.history.back()</script>";
}

//tempRights säubern
if (@$_SESSION['tempRights']) {
	foreach($_SESSION['tempRights'] as $k => $v) {
		if (($v[ts] + (6*60*60)) < time()) {
			unset($_SESSION['tempRights'][$k]);
		}
	}
}
?>