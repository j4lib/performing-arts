<?php

$csvIn = $argv[1];
$res = [];

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
      $res = addQuick($res,$row,"P2348",[$row["Spielzeit"]]);
    }
    if ($row["Datum"]) {
      $res = addQuick($res,$row,"P1191",[$row["Datum"]]);
    }
    if ($row["Stück"]) {
      $res = addQuick($res,$row,"Lde",[$row["Stück"]]);
    }
    if ($row["Vorlage"]) {
      $res = addQuick($res,$row,"P144",[$row["Vorlage"]]);
    }
    /*if ($row["Autor1"] || $row["Autor2"] || $row["Autor3"]) {
      $res = addQuick($res,$row,"P",[$row["Autor1"],$row["Autor2"],$row["Autor3"]]);
    }*/
    if ($row["Regie1"] || $row["Regie2"]) {
      $res = addQuick($res,$row,"P57",[$row["Regie1"],$row["Regie2"]]);
    }
    if ($row["Musik1"] || $row["Musik2"]) {
      $res = addQuick($res,$row,"P86",[$row["Musik1"],$row["Musik2"]]);
      $res = addQuick($res,$row,"P136",["Q39894018"]);
    } else {
      $res = addQuick($res,$row,"P136",["Q39892385"]);
    }
    if ($row["Bühnenbild1"] || $row["Bühnenbild2"]) {
      $res = addQuick($res,$row,"P4608",[$row["Bühnenbild1"],$row["Bühnenbild2"]]);
    }
    if ($row["Kostüme1"] || $row["Kostüme2"]) {
      $res = addQuick($res,$row,"P2515",[$row["Kostüme1"],$row["Kostüme2"]]);
    }
    if ($row["Choreographie"]) {
      $res = addQuick($res,$row,"P1809",[$row["Choreographie"]]);
    }
    if ($row["Ortsvermerk"]) {
      if ($row["Typ"] == 'Q40249767') {
        $prop = "P4647";
      } else {
        $prop = "P276";
      }
      if ($row["OrtQualifier1"] && $row["OrtQualifier2"]) {
        $res = addQuickQualifier($res,$row,$prop,$row["Ortsvermerk"],$row["OrtQualifier1"],
               $row["OrtQualifier2"]);
      } else {
        $res = addQuick($res,$row,$prop,[$row["Ortsvermerk"]]);
      }
    }
    if ($row["Person"] && $row["Rolle"]) {
        $res = addQuickQualifier($res,$row,"P161",$row["Person"],"P4633",$row["Rolle"]);
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

function addQuick($res,$row,$property,$items) {
  foreach ($items as $item) {
    if ($item) {
    $item = addQuotes($item);
    $res[$row["Stück"].$row["Datum"]] =
      addIfNotExists($res[$row["Stück"].$row["Datum"]],
                     "LAST\t" . $property . "\t" . $item . "\n");
  }
}
  return $res;
}

function addQuickQualifier($res,$row,$property,$item,$qualifier,$item2) {
    if ($item) {
      $item = addQuotes($item);
      $item2 = addQuotes($item2);
      $res[$row["Stück"].$row["Datum"]] =
        addIfNotExists($res[$row["Stück"].$row["Datum"]],
                     "LAST\t" . $property . "\t" . $item . "\t" .
                     $qualifier . "\t". $item2 . "\n");
  }
  return $res;
}

?>
