<?php

$csvIn = $argv[1];
$res = [];
// Repertoire Schauspielhaus Zürich 1938-1968
$source = "Q39907533";

$csv = array_map('str_getcsv', file($csvIn));
$csv_head = $csv[0];
unset($csv[0]);

foreach($csv as $row){
  if (sizeof($row) != 1) {
    $row = array_combine($csv_head, $row);
    $id = $row["Stück"].$row["Datum"];
    if ($id != "Der Hauptmann von Köpenick1947-10-18") {
    if (!array_key_exists($id,$res)) {
      if ($row["Typ"] == 'Q40249767') { // if Gastspiel
        $res[$id][] = "CREATE\nLAST\tP31\tQ43100730\n";
      } else {
        $res[$id][] = "CREATE\nLAST\tP31\tQ7777570\n";

      }
    }
    if ($row["Spielzeit"]) {
      $res = addQuick($res,$row,[["P2348",$row["Spielzeit"]]],$source);
    }
    if ($row["Datum"]) {
      $res = addQuick($res,$row,[["P1191",$row["Datum"]]],$source);
    }
    if ($row["Stück"]) {
      $res = addQuick($res,$row,[["Lde",$row["Stück"]]],"");
      $res = addQuick($res,$row,[["P1705",$row["Stück"]]],$source);
      if ($row["Kommentar"]) {
        $res = addQuick($res,$row,[["Dde","Theaterproduktion des Schauspielhauses Zürich in der Spielzeit 1938-1939. " . $row["Kommentar"]]],"");
      } else {
        $res = addQuick($res,$row,[["Dde","Theaterproduktion des Schauspielhauses Zürich in der Spielzeit 1938-1939"]],"");
      }
      $res = addQuick($res,$row,[["Den","theatrical production of Schauspielhaus Zurich during the season 1938-1939"]],"");
    }
    if ($row["Vorlage"]) {
      $res = addQuick($res,$row,[["P144",$row["Vorlage"]]],$source);
    }
    /*if ($row["Autor1"] || $row["Autor2"] || $row["Autor3"]) {
      $res = addQuick($res,$row,"P",[$row["Autor1"],$row["Autor2"],$row["Autor3"]]);
    }*/
    if ($row["Regie1"] || $row["Regie2"]) {
      $res = addQuick($res,$row,[["P57",$row["Regie1"]],["P57",$row["Regie2"]]],$source);
    }
    if ($row["Musik1"] || $row["Musik2"]) {
      $res = addQuick($res,$row,[["P86",$row["Musik1"]],["P86",$row["Musik2"]]],$source);
      $res = addQuick($res,$row,[["P136","Q39894018"]],"");
    } else {
      $res = addQuick($res,$row,[["P136","Q39892385"]],"");
    }
    if ($row["Bühnenbild1"] || $row["Bühnenbild2"]) {
      $res = addQuick($res,$row,[["P4608",$row["Bühnenbild1"]],["P4608",$row["Bühnenbild2"]]],$source);
    }
    if ($row["Kostüme1"] || $row["Kostüme2"]) {
      $res = addQuick($res,$row,[["P2515",$row["Kostüme1"]],["P2515",$row["Kostüme2"]]],$source);
    }
    if ($row["Choreographie"]) {
      $res = addQuick($res,$row,[["P1809",$row["Choreographie"]]],$source);
    }
    if ($row["Ortsvermerk"]) {
      if ($row["Typ"] == 'Q40249767') {
        $prop = "P4647";
      } else {
        $prop = "P276";
      }
      if ($row["OrtQualifier1"] && $row["OrtQualifier2"]) {
        $res = addQuick($res,$row,[[$prop,$row["Ortsvermerk"]],[$prop,$row["OrtQualifier1"]],
               [$prop,$row["OrtQualifier2"]]],$source);
      } else {
        $res = addQuick($res,$row,[[$prop,$row["Ortsvermerk"]]],$source);
      }
    }
    if ($row["Typ"]) {
      $res = addQuick($res,$row,[["P4634",$row["Typ"]]],$source);
    }
    if ($row["Person"] && $row["Rolle"]) {
        $res = addQuick($res,$row,[["P161",$row["Person"]],["P4633",$row["Rolle"]]],$source);
    }
  }
  }
}

foreach ($res as $k => $prods) {
  $res[$k] = implode($prods,'');
}

foreach ($res as $str) {
  echo $str . "\n";
}

function addIfNotExists($resAtKey,$propWithItem) {

  if (!in_array($propWithItem,$resAtKey)) {
    $resAtKey[] = $propWithItem;
  }
  return $resAtKey;
}

function addQuotes($item) {
  if (preg_match("/(^[0-9]{4}-[0-9]{2}-[0-9]{2}$)/",$item)) {
    $item = "+" . $item . "T00:00:00Z/11";
  }
  if (!preg_match("/(^Q[0-9]+)|(^\+[0-9]{4}-[0-9]{2}-[0-9]{2}.+)/",$item)) {
    $item = "\"" . $item . "\"";
  }
  return $item;
}

function addQuick($res,$row,$properties,$source) {
  $propStr = "";
  foreach ($properties as list($prop,$item)) {
    if ($item) {
      $item = addQuotes($item);
      if ($prop == "P1705") {
        $propStr = $propStr . "\t" . $prop . "\tde:" . $item ;
      } else {
        $propStr = $propStr . "\t" . $prop . "\t" . $item ;
      }
    }
  }
  if ($source) {
    $source = "\tS248\t" . $source;
  }
  $res[$row["Stück"].$row["Datum"]] =
    addIfNotExists($res[$row["Stück"].$row["Datum"]],"LAST" . $propStr . $source . "\n");
  return $res;
}


?>
