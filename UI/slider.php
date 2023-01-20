<?php
function nv_ui_slider () {
	wp_enqueue_script( "lightbox", $templ_dir . "/assets/slider.js" );
}
add_action( "nv_load_modules", "nv_ui_slider" );