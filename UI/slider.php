<?php
function nv_ui_slider () {
	$templ_dir = get_template_directory_uri();
	wp_enqueue_script( "lightbox", $templ_dir . "/assets/slider.js" );
}
add_action( "nv_load_modules", "nv_ui_slider" );