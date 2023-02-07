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




/*
ENQUEUE NV MODULES
available modules:
    lightbox - lightbox gallery
    datepicker - for reservation functionality
*/

$NV_MODULES = array();

function nv_use_modules ( $modules ) {
    global $NV_MODULES;
    $NV_MODULES = $modules;
}

function navalachy_modules()
{
    $templ_dir = get_template_directory_uri();
    global $NV_MODULES;

    include_once get_template_directory() . "/UI/cover-image.php";

    //wp_enqueue_style( 'navalachy', $templ_dir."/assets/style.css" );
    wp_enqueue_style( 'navalachy-style', $templ_dir."/assets/style.css" );
    wp_enqueue_style( "navalachy-style-legacy", $templ_dir."/assets/legacy.css" );
    wp_enqueue_style( "navalachy-icons", $templ_dir. "/assets/icons/style.css" );

    wp_enqueue_script( "domster", $templ_dir. "/assets/domster.js" );
    
    if ( !empty( $NV_MODULES ) )
    {
        foreach ( $NV_MODULES as $M )
        {
            include_once get_template_directory() . "/$M.php";
        }
    }

    do_action( "nv_load_modules" );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'navalachy_modules' );



/*
PASS VARIABLES TO JAVASCRIPT 

USAGE:

in template file, before wp_head(), assign variables to global $nv_vars
*/


function nv_register_vars ( ) {
    global $nv_vars;
    if ( empty( $nv_vars ) ) return;
    wp_register_script( "nv_vars", "" );
    wp_enqueue_script( "nv_vars" );
    wp_add_inline_script( 'nv_vars', 'var nv_vars = ' . json_encode($nv_vars) , 'before' );
}
add_action( 'wp_enqueue_scripts', 'nv_register_vars' );







//RESPONSIVE IMG FUNCTION

function nv_responsive_img ( $attachment_id, $sizes = "(max-width: 600px) 100vw, 25vw", $alt = "") {
    $src = wp_get_attachment_image_url( $attachment_id, "medium");
    $srcfull = wp_get_attachment_image_url( $attachment_id, "full");
    $srcset = wp_get_attachment_image_srcset( $attachment_id, "large" );
    $attalt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true);
    if ($attalt != "") $alt = $attalt;

    return '<img src="'.esc_attr( $src ).'"
            srcset="'.esc_attr( $srcset ).'"
            sizes="'.esc_attr( $sizes ).'"
            alt="'.esc_attr( $alt ).'"
            loading="lazy"/>';
}
