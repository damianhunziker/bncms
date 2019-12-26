<?php

//fußzeile
function Footer($pagesMod=0) {
	global $pdf, $ts_i, $arrUser, $arrMonteur;
	/*$pdf->SetFont('Arial','I',8);
	$out="Erstellungsdatum: ". date("d.m.Y",$ts_i).', Seite '.$pdf->PageNo().' von {nb}';
	if ($typ != "Mahnung1" and $typ != "Mahnung2" and $typ != "Rechnung" ) {
		//$nextX=$pdf->GetStringWidth("Erstellungsdatum: ". date("d.m.Y",$ts_i).' Seite '.$pdf->PageNo().' von {nb}') + 17;
		$out.=", Ersteller: ". ucfirst($arrUser[lastname])." ".ucfirst($arrUser[firstname]);
		//$nextX=$pdf->GetStringWidth("Ersteller: ". ucfirst($arrUser[lastname])." ".ucfirst($arrUser[firstname])) + 17 + $nextX;
		if ($arrMonteur[lastname] != "") {
			$out.=", Zust&auml;ndiger Monteur: ". ucfirst($arrMonteur[lastname])." ".ucfirst($arrMonteur[firstname]);
		}
	}
	displayTaggedText($out, "b", "strong", "strong", FONT_NAME, 8, 17, 285, 160);
	$pdf->SetFont(FONT_NAME,'B',DEFAULT_FONT_SIZE); */
}

//Berechnung der Anzahl Linien
function returnLineSituation($data, $width, $indexWithText, $additionalLines) {
	global $pdf;
	$linesInTable=0;
	//echo "<pre>";
	//print_r($data);
	foreach ($data as $key => $value) {
		if ($value[$indexWithText] != "") {
			$linesInTable=$linesInTable + $pdf->NbLines($width, $value[$indexWithText]);
			$linesInTable=$linesInTable+1.25/5; // F&uuml;r Abst&auml;nde nach jedem Produkt 1.25
		}
	}
	$linesInTable = $linesInTable + $additionalLines;
	$intMmInTable = $linesInTable * DEFAULT_LINE_HIGHT;

	$intOverflowMm = $intMmInTable + $pdf->GetY() - $pdf->PageBreakTrigger;
	
	//ermitteln ob der &Uuml;bertrag eine ganze Seite &uuml;berlappt
	$intMmOnOnePage = $pdf->PageBreakTrigger - (11 * DEFAULT_LINE_HIGHT);
	
	//echo "$intOverflowMm > $intMmOnOnePage";
	if ($intOverflowMm > $intMmOnOnePage) {
		$intOverflowLines = ceil(($intOverflowMm - $intMmOnOnePage) / DEFAULT_LINE_HIGHT); // Tabelle wird zweimal gebrochen
	} else {
		$intOverflowLines = ceil($intOverflowMm / DEFAULT_LINE_HIGHT); // Tabelle wird einmal gebrochen
	}
	return array($intOverflowLines, $linesInTable);
}
function displayTable($data, $width, $indexWithText, $arrWidths, $header="", $minimalLinesOnOnePage, $echo=0, $spaceBetweenLines, $footer = "", $balance = "0") {
	global $pdf, $pagesmod;
	if ($footer != "") {
		$additionalLines = $additionalLines + 1.5;
	}
	if ($header != "") {
		$additionalLines=$additionalLines + 3;
	}
	//echo "$data, $width, $indexWithText, $additionalLines";
	$arrLineSituation = returnLineSituation($data, $width, $indexWithText, $additionalLines); 
	if ($echo == 1) {
		print_r($arrLineSituation);
	}
	$linesB4Break = $arrLineSituation[1] - $arrLineSituation[0];
	$linesAfterBreak = $arrLineSituation[1] - $linesB4Break;
	//$out="<br>countTableRows: ".$linesInTable;
	//$out.="<br>intMmEndPage: ".$intMmEndPage;
	//$out.="<br>GetY: ".$pdf->GetY();
	//$out.="<br>Trigger: ".$pdf->PageBreakTrigger;
	//$out.="<br>intOverflowMm: ".$intOverflowMm;
	
	if ($echo == 1) {
		print_r($arrLineSituation);
		$out.="<br>intOverflowLines: ".$arrLineSituation[0];
		$out.="<br>linesInTable: ".$arrLineSituation[1];
		$out.="<br>linesB4Break: ".$linesB4Break;
		$out.="<br>linesAfterBreak: ".$linesAfterBreak; 
		echo $out;
	}
	if ($arrLineSituation[0] < 5 and $arrLineSituation[0] > 0 ) {
		//echo "$arrLineSituation[0] Achtung";
	}
	
	
	//Falls es weniger als vier Tabellenzeilen vor und nach dem Seitenumbruch hat,
	//setze Umruch auf mitte der Tabelle
	

	/*if ($linesB4Break < $minimalLinesOnOnePage or $linesAfterBreak < $minimalLinesOnOnePage) {
		if ($linesB4Break < $minimalLinesOnOnePage) {
			Footer($pagesmod);
			$pdf->AddPage();
		} elseif ($linesAfterBreak < $minimalLinesOnOnePage) {
			$lineToBreakAt = ceil($arrLineSituation[1] / 2); // Bricht in der Mitte
		}
	} else {
		$lineToBreakAt = $linesB4Break;
	}
	*/
	
	//Kontrolliert wieviel zeilen nach dem Break &uuml;brig sind und setzt lineToBreakAt
	if ($linesAfterBreak <= 3 and $linesAfterBreak != 0) {
		$lineToBreakAt=$linesB4Break-3;
	}
	//Ausgabe der Tabelle
	$countAusgabeLines=0;
	$pdf->SetWidths($arrWidths);	
	$pdf->SetFont(FONT_NAME,'B',DEFAULT_FONT_SIZE); 
	if ($header != "") {
		$pdf->Row($header);
		$lastRowIsHeader = 1;
		$countAusgabeLines++;
	}
	//echo "<pre>";
	//print_r($data);

	foreach ($data as $key => $value) {
		
		$newPage = 0;
		//echo $indexWithText;
		
		if ($pdf->GetY() > $pdf->PageBreakTrigger) {
			$newPage = 1;
		}
		//&Uuml;bertrag: Berrechnung des Zwischentotals
		if ($balance == "1") {
			$total = $total + $value[3];
		}
		//&Uuml;bertrag eof
		
		//Wenn keine Leerzeile folgt z&auml;hle die Anzahl Ausgabelinien um die H&ouml;he des Textblocks hoch,
		//anstonsten addiere 0.25 f&uuml;r die Leerzeile
		if ($value[0] == "" and $value[1] == "" and $value[2] == "" and $value[3] == "") {
			$countAusgabeLines=$countAusgabeLines + 0.25;
		} else {
			$countAusgabeLines = $countAusgabeLines + $pdf->NbLines($width, $value[$indexWithText]);
		}
		//echo "<br>".$countAusgabeLines." > ".$lineToBreakAt." and ".$lastBreakAt." != ".$lineToBreakAt;
		if ($countAusgabeLines > $lineToBreakAt and $lastBreakAt != $lineToBreakAt) {
			//echo "hallo";
			$newPage = 1;
			$lastBreakAt=$lineToBreakAt;
		}
		if ($echo == 1) {
			echo "<br>$countAusgabeLines == $linesB4Break";
		}
		if ($arrLineSituation[0] > 0 ) {
			//echo "$countAusgabeLines == $lineToBreakAt<br>";
			if ($newPage == 1) {
				Footer($pagesmod);
				$pdf->AddPage();
				//&Uuml;bertrag einf&uuml;gen des Textblocks
				//$pdf->Row("&Uuml;bertrag: $total");
				//&Uuml;bertrag eof
				if ($header != "") {
					$pdf->SetFont(FONT_NAME,'B',DEFAULT_FONT_SIZE);
					$pdf->Row($header);
					$lastRowIsHeader = 1;
				}
			}
		}
		$pdf->SetFont(FONT_NAME,'',DEFAULT_FONT_SIZE);
 
		if ($value[0] == "" and $value[1] == "" and $value[2] == "" and $value[3] == "") {
			$pdf->Row("",1.25);
		} else {
			$pdf->Row($value);
		}
		$lastRowIsHeader = 0;
	}
	if ($footer != "") {
		$pdf->SetFont(FONT_NAME,'B',DEFAULT_FONT_SIZE);
		$pdf->Row($footer);
		$pdf->SetFont(FONT_NAME,'',DEFAULT_FONT_SIZE);
	}
	/*$pdf->MultiCell(170,10,"
	arrLineSituation[0]=$arrLineSituation[0]
	arrLineSituation[1]=$arrLineSituation[1]
	linesB4Break=$linesB4Break
	linesAfterBreak=$linesAfterBreak
	",0);*/
}

function displayTaggedText($text, $alterTag, $alterTagTo, $splitFrom, $font, $fontSize, $left, $top, $width, $wordWrap = 0, $lineHeight = DEFAULT_LINE_HIGHT ) {
	$text=alterTags($text,$alterTag,$alterTagTo);
	$arrTreffer=splitTextFromTag($text,$splitFrom);
	displaySplittedText($arrTreffer, $font, $fontSize, $left, $top, $width, $wordWrap, $lineHeight);
}

function displaySplittedText  ($arrSplittedText, $font, $fontSize, $left, $top, $width, $wordWrap, $lineHeight) {
	global $pdf;
	foreach ($arrSplittedText as $key => $value) {
		//print_r($value);
		if ($value[1] != "") {
			if ($value[0] == "strong") {
				$pdf->SetFont($font, "B", $fontSize);
			} else {
				$pdf->SetFont($font, "", $fontSize);
			}
			if ($countCells == 0) {
				$pdf->SetXY($left, $top);
			} else {
				$pdf->SetXY($left,$pdf->GetY());
			}
			$countCells++;
			
			$value[1] = str_replace("<br />", "\n", $value[1]);
			$value[1] = str_replace("<br>", "\n", $value[1]); 
			
			if ($wordWrap == 1) {
				$pdf->WordWrap($value[1], $width);
			}
			//echo $lineHeight;
			$pdf->MultiCell($width,$lineHeight,$value[1],0); 
		}
	}
}

function alterTags($subject, $from, $to) {
	$subject=str_replace("<$from>","<$to>",$subject);
	$subject=str_replace("</$from>","</$to>",$subject);
	return $subject;
}

function splitTextFromTag ($text, $tag) {
	$text = nl2br($text);
	$text=str_replace("\n\r","",$text);
	$text=str_replace("
","",$text);
	$text=str_replace("\n","",$text);
	$text=str_replace("\r","",$text);
	$arrTreffer=array();
	while (preg_match('/(.*)<'.$tag.'>(.+)<\/'.$tag.'>(.*)/im',$text,$treffer)) {
		$text=$treffer[1];
		$arrTemp[0]="";
		$arrTemp[1]=str_replace("<br />","\n\r",$treffer[3]);
		array_push($arrTreffer, $arrTemp);
		$arrTemp[0]="strong";
		$arrTemp[1]=str_replace("<br />","\n\r",$treffer[2]);
		array_push($arrTreffer, $arrTemp);
	} 
	//Korrektur, erster Teil des Strings wird noch eingelesen
	$arrTemp[0]="";
	$arrTemp[1]=$text;
	array_push($arrTreffer, $arrTemp);
	$arrTreffer=array_reverse($arrTreffer);
	return $arrTreffer;
}

?>