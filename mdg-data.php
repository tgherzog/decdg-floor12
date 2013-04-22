<?php

/* Quick script to transform csv input data into json output. Run from command line only
*/

if( php_sapi_name() != 'cli' ) {
  print "must be run from the command line.\n";
  exit;
}

ini_set('display_errors', true);

$file = pathinfo($argv[0], PATHINFO_FILENAME) . '.csv';

$key = null;
$data = array();
if( $f = fopen($file, 'r') ) {
  while( ! feof($f) ) {
    $row = rtrim(fgets($f));
	if( ! $row ) continue;

	if( preg_match('/^#\s*(\w+):/', $row, $vars) ) {
	  $key = $vars[1];
	  $data[$key] = array();
	  $rowcount = -1;
	  continue;
	}

	if( ! $key ) continue;  // can't match this data to a region

	$row = str_getcsv($row);
	$rowcount++;
	if( $rowcount == 0 ) {
	  $hdr = array_filter($row);
	  array_shift($hdr);
	  continue;
	}

	$series = array();
	$i = 1;
	foreach($hdr as $x) {
	  if( is_numeric($row[$i]) ) $series[] = array((integer)$x,(float) $row[$i]);

	  $i++;
    }

	$i = strtolower($row[0]);
	$data[$key][$i] = $series;
  }

  fclose($f);

  print json_encode($data);
}
