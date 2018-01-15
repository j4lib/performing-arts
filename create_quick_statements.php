<?php

$csvIn = $argv[1];
$res = [];
// Repertoire Schauspielhaus Zürich 1938-1968
$source = "Q39907533";
$existing = ["Der Hauptmann von Köpenick10/18/1947","Eine kleine Stadt03/09/1939",
    "L'école des femmes (Die Schule der Frauen)02/11/1941","Frau Warrens Gewerbe09/03/1938"];

$csv = array_map('str_getcsv', file($csvIn));
$csv_head = $csv[0];
unset($csv[0]);

foreach($csv as $row){
  if (sizeof($row) == 47) {
    $row = array_combine($csv_head, $row);
    $id = $row["Stück"].$row["Datum"];
    if (!in_array($id,$existing)) {
    if (!array_key_exists($id,$res)) {
      if ($row["Typ"] == 'Q40249767') { // if Gastspiel
        $res[$id][] = "CREATE\nLAST\tP31\tQ43100730\tS248\tQ39907533\n";
      } else {
        $res[$id][] = "CREATE\nLAST\tP31\tQ7777570\tS248\tQ39907533\n";
      }

    }
    if ($row["Spielzeit"]) {
      $res = addQuick($res,$row,[[["P2348",$row["Spielzeit"]]]],$source);
    }
    if ($row["Datum"]) {
      $res = addQuick($res,$row,[[["P1191",$row["Datum"]]]],$source);
    }
    if ($row["Stück"]) {
      $res = addQuick($res,$row,[[["Lde",$row["Stück"]]]],"");
      $res = addQuick($res,$row,[[["P1705",$row["Stück"]]]],$source);
      if ($row["Kommentar"]) {
        $res = addQuick($res,$row,[[["Dde","Theaterproduktion des Schauspielhauses Zürich in der Spielzeit 1938-1939. " . $row["Kommentar"]]]],"");
      } else {
        $res = addQuick($res,$row,[[["Dde","Theaterproduktion des Schauspielhauses Zürich in der Spielzeit 1938-1939"]]],"");
      }
      $res = addQuick($res,$row,[[["Den","theatrical production of Schauspielhaus Zurich during the season 1938-1939"]]],"");
    }
    if ($row["SpracheQ"]) {
      $res = addQuick($res,$row,[[["P407",$row["SpracheQ"]]]],"");
    }
    if ($row["ProduzentQ"]) {
      $res = addQuick($res,$row,[[["P272",$row["ProduzentQ"]]]],$source);
    }
    if ($row["Vorlage"]) {
      $res = addQuick($res,$row,[[["P144",$row["Vorlage"]]]],$source);
    }
    /*if ($row["Autor1"] || $row["Autor2"] || $row["Autor3"]) {
      $res = addQuick($res,$row,"P",[$row["Autor1"],$row["Autor2"],$row["Autor3"]]);
    }*/
    if ($row["Regie1"] && $row["Regie1String"] || $row["Regie2"] && $row["Regie2String"]) {
      $res = addQuick($res,$row,[[["P57",$row["Regie1"]],["P1810",$row["Regie1String"]]],[["P57",$row["Regie2"]],["P1810",$row["Regie2String"]]]],$source);
    }
    if ($row["Musik1"] && $row["Musik1String"] || $row["Musik2"] && $row["Musik2String"] || $row["Musik3"] && $row["Musik3String"] || $row["Musik4"] && $row["Musik4String"]) {
      $res = addQuick($res,$row,[[["P86",$row["Musik1"]],["P1810",$row["Musik1String"]]],[["P86",$row["Musik2"]],["P1810",$row["Musik2String"]]]
             ,[["P86",$row["Musik3"]],["P1810",$row["Musik3String"]]],[["P86",$row["Musik4"]],["P1810",$row["Musik4String"]]]],$source);
      $res = addQuick($res,$row,[[["P136","Q39894018"]]],"");
    } else {
      $res = addQuick($res,$row,[[["P136","Q39892385"]]],"");
    }
    if ($row["Bühnenbild1"] && $row["Bühnenbild1String"] || $row["Bühnenbild2"] && $row["Bühnenbild2String"]) {
      $res = addQuick($res,$row,[[["P4608",$row["Bühnenbild1"]],["P1810",$row["Bühnenbild1String"]]],[["P4608",$row["Bühnenbild2"]],["P1810",$row["Bühnenbild2String"]]]],$source);
    }
    if ($row["Kostüme1"] && $row["Kostüme1String"]|| $row["Kostüme2"]&& $row["Kostüme2String"]) {
      $res = addQuick($res,$row,[[["P2515",$row["Kostüme1"]],["P1810",$row["Kostüme1String"]]],[["P2515",$row["Kostüme2"]],["P1810",$row["Kostüme2String"]]]],$source);
    }
    if ($row["Choreographie"] && $row["ChreographieString"]) {
      $res = addQuick($res,$row,[[["P1809",$row["Choreographie"]],["P1810",$row["ChreographieString"]]]],$source);
    }
    if ($row["Ortsvermerk"]) {
      if ($row["Typ"] == 'Q40249767') {
        $prop = "P4647";
      } else {
        $prop = "P276";
      }
      if ($row["OrtQualifier1"] && $row["OrtQualifier2"]) {
        $res = addQuick($res,$row,[[[$prop,$row["Ortsvermerk"]],[$row["OrtQualifier1"],$row["OrtQualifier2"]]]],$source);
      } else {
        $res = addQuick($res,$row,[[[$prop,$row["Ortsvermerk"]]]],$source);
      }
    }
    if ($row["Typ"]) {
      $res = addQuick($res,$row,[[["P4634",$row["Typ"]]]],$source);
    }
    if ($row["Person"] && $row["Rolle"] && $row["PersonString"]) {
        $res = addQuick($res,$row,[[["P161",$row["Person"]],["P4633",$row["Rolle"]],["P1810",$row["PersonString"]]]],$source);
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
  if (!preg_match("/(^Q[0-9]+)|(^P[0-9]+)|(^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$)|(^[0-9]{4}-[0-9]{2}-[0-9]{2})$/",$item)) {
    $item = "\"" . $item . "\"";
  }
  if (preg_match("/(^[0-9]{4}-[0-9]{2}-[0-9]{2}$)/",$item)) {
    $item = "+" . $item . "T00:00:00Z/11";
    $item = "+" . substr($item,0,4) . "-" . substr($item,7,2) . "-" . substr($item,5,2) . "T00:00:00Z/11";
  }
  if (preg_match("/(^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$)/",$item)) {
    $item = "+" . substr($item,6,4) . "-" . substr($item,0,2) . "-" . substr($item,3,2) . "T00:00:00Z/11";
  }
  return $item;
}

function addQuick($res,$row,$properties,$source) {
  if ($source) {
    $source = "\tS248\t" . $source;
  }
  foreach ($properties as $props) {
    $propStr = "";
    foreach ($props as list($prop,$item)) {
      if ($item) {
        $item = addQuotes($item);
        if ($prop == "P1705") {
          $propStr = $propStr . "\t" . $prop . "\tde:" . $item;
        } else {
          $propStr = $propStr . "\t" . $prop . "\t" . $item;
        }
      }
    }
    $res[$row["Stück"].$row["Datum"]] =
      addIfNotExists($res[$row["Stück"].$row["Datum"]],"LAST" . $propStr . $source . "\n");
    }
    return $res;
}


?>
