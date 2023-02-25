<?php
require_once( "../../../../wp-load.php" );
require_once( "../accomodation/i/lib.php" );

global $wpdb;
//$wpdb->show_errors();

$nvbk = new NVBK();
$nvbk->sync();

//$wpdb->print_error();

$logg = date("Y-m-d H:i:s") . " - sync done!\n";

$log = file_get_contents("log.txt");
file_put_contents("log.txt", $log . $logg);
?>