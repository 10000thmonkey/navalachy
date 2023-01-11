<?php
global $_VAR;

function nv_template ($filePath, $variables = array(), $print = false)
{
    $output = NULL;
    if(file_exists($filePath)){

        $_VAR = $variables;

        ob_start();

        include get_template_directory() . "/templates/" . $filePath . ".php" ;

        $output = ob_get_clean();
    }
    if ($print) {
        print $output;
    }
    return $output;
}
?>