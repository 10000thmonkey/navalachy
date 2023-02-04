<?php
require_once("../../../../wp-load.php");
require_once("lib.php");

$nvbk = new NVBK();
$nvbk->sync();

$logg = date("Y-m-d H:i:s") . " - sync done!\n";

$log = file_get_contents("log.txt");
file_put_contents("log.txt", $log . $logg);
?>