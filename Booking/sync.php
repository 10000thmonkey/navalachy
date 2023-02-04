<?php
require_once("../../../../wp-load.php");
require_once("lib.php");

$nvbk = new NVBK();
$nvbk->sync();

echo "done";
?>