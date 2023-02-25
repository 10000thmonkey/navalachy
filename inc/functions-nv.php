<?php
global $_VAR;
$templ_dir = get_template_directory();






global $nv_controllers;
$nv_controllers = array();

function nv_new_c ( $path, $callable )
{
    global $nv_controllers;
    $nv_controllers[ $path ] = $callable;
}

function nv_c ( $path, $VAR = array(), $print = false )
{
    global $nv_controllers;
    global $templ_dir;

    if ( file_exists( "$templ_dir/$path.php" ) )
    {
        include_once "$templ_dir/$path.php";
        return $nv_controllers[ $path ]( $VAR );
    }
    else {
        trigger_error( "Component does not exist!", E_USER_WARNING);
        return false;
    }
}

function nv_c_attr ( $attr )
{
    return esc_attr( json_encode( $attr ) );
}


global $nv_emails;
$nv_emails = array();

function nv_new_e ( $path, $callable )
{
    global $nv_emails;
    $nv_emails[ $path ] = $callable;
}

function nv_e ( $path, $VAR = array(), $print = false )
{
    global $nv_emails;
    global $templ_dir;

    if ( file_exists( "$templ_dir/$path.php" ) )
    {
        include_once "$templ_dir/$path.php";
        return $nv_emails[ $path ]( $VAR );
    }
    else {
        trigger_error( "Email does not exist!", E_USER_WARNING);
        return false;
    }
}




global $nv_templates;

function nv_new_t ( $path, $callable )
{
    global $nv_templates;
    $nv_templates[ $path ] = $callable; 
}
function nv_t ( $path )
{
    global $templ_dir;
    global $nv_templates;

    if ( file_exists( "$templ_dir/$path.html" ) )
    {
        return file_get_contents( "$templ_dir/$path.html" );
        //return $nv_templates[ $path ]();
    }
    else {
        trigger_error( "Template does not exist!", E_USER_WARNING);
        return false;
    }
}



function nv_ajax ( $endpoint, $callback )
{
    $endpoint = str_replace( "/", "_", $endpoint );

    $passing = function () use ( $callback ) {
        //echo var_dump( $callback );
        if(WP_DEBUG) @ini_set( 'display_errors', 1 );
        echo json_encode( call_user_func( $callback ) );
        die();
    };
    add_action( "wp_ajax_nv_$endpoint", $passing );
    add_action( "wp_ajax_nopriv_nv_$endpoint", $passing );
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


    wp_enqueue_style( 'navalachy-style', $templ_dir."/assets/style.css?v=0.1" );
    wp_enqueue_style( "navalachy-style-legacy", $templ_dir."/assets/legacy.css?v=0.1" );
    wp_enqueue_style( "navalachy-icons", $templ_dir. "/assets/icons/style.css?v=0.1" );

    wp_enqueue_script( "nv-domster", $templ_dir. "/assets/domster.js?v=0.1" );
    wp_enqueue_script( "nv-framework", $templ_dir. "/assets/framework.js?v=0.1" );

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

add_action(
    'wp_enqueue_scripts',
    function () {
        global $nv_vars;
        if ( empty( $nv_vars ) ) return;
        wp_register_script( "nv_vars", "" );
        wp_enqueue_script( "nv_vars" );
        wp_add_inline_script( 'nv_vars', 'var nv_vars = ' . json_encode($nv_vars) , 'before' );
    }
);