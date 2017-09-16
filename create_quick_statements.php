<?php

$csvIn = $argv[1];
$res = [];

$csv = array_map('str_getcsv', file($csvIn));
$csv_head = $csv[0];
unset($csv[0]);

foreach($csv as $key => $row){
  if (sizeof($row) != 1) {
    $row = array_combine($csv_head, $row);
    if (!array_key_exists($row["Stück"],$res)) {
      $res[$row["Stück"]][] = "CREATE\nLAST\tP31\tQ7777570\n";
    }
    if ($row["Datum"]) {
      $res = addQuick($res,$row,"P1191",[$row["Datum"]]);
    }
    if ($row["Stück"]) {
      $res = addQuick($res,$row,"Lde",[$row["Stück"]]);
      //$res = addQuick($res,$row,"P144",[$row["Stück"]]);
    }
    if ($row["Regie 1"] || $row["Regie 2"]) {
      $res = addQuick($res,$row,"P57",[$row["Regie 1"],$row["Regie 2"]]);
    }
    if ($row["Musik1"] || $row["Musik2"]) {
      $res = addQuick($res,$row,"P86",[$row["Musik1"],$row["Musik2"]]);
    }
    if ($row["Bühnenbild1"] || $row["Bühnenbild2"]) {
      //$res = addQuick($res,$row,"P?",[$row["Bühnenbild1"],$row["Bühnenbild2"]]);
    }
    if ($row["Kostüme1"] || $row["Kostüme2"]) {
      $res = addQuick($res,$row,"P2515",[$row["Kostüme1"],$row["Kostüme2"]]);
    }
    if ($row["Choreographie"]) {
      $res = addQuick($res,$row,"P1809",[$row["Choreographie"]]);
    }
    if ($row["Ortsvermerk"]) {
      if ($row["OrtQualifier1"] && $row["OrtQualifier2"]) {
        $res = addQuickQualifier($res,$row,"P?",$row["Ortsvermerk"],$row["OrtQualifier1"],
               $row["OrtQualifier2"]);
      } else {
        $res = addQuick($res,$row,"P?",[$row["Ortsvermerk"]]);
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
  if (preg_match("/(^[0-9]+.+Z$)/",$item)) {
    $item = "+" . $item . "/11";
  }
  if (!preg_match("/(^Q[0-9]+)|(^\+[0-9]+.+Z\/11$)/",$item)) {
    $item = "\"" . $item . "\"";
  }
  return $item;
}

function addQuick($res,$row,$property,$items) {
  foreach ($items as $item) {
    if ($item) {
    $item = addQuotes($item);
    $res[$row["Stück"]] =
      addIfNotExists($res[$row["Stück"]],
                     "LAST\t" . $property . "\t" . $item . "\n");
  }
}
  return $res;
}

function addQuickQualifier($res,$row,$property,$item,$qualifier,$item2) {
    if ($item) {
    $res[$row["Stück"]] =
      addIfNotExists($res[$row["Stück"]],
                     "LAST\t" . $property . "\t" . $item . "\t" .
                     $qualifier . "\t". $item2 . "\n");
  }
  return $res;
}

?>
