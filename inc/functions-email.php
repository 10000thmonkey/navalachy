<?php
function nv_send_mail ($args = []) {
	if (!isset($args) || $args == [] ) return "fuckoff. dej mi kurva aspon jeden argument";
	$body = isset($args->body) ? $args->body : "";

	return wp_mail( $args["to"], $args["subject"], $args["body"], $args["headers"] );
}


function debug_wpmail( $result = false ) {

	if ( $result )
		return;

	global $ts_mail_errors, $phpmailer;

	if ( ! isset($ts_mail_errors) )
		$ts_mail_errors = array();

	if ( isset($phpmailer) )
		$ts_mail_errors[] = $phpmailer->ErrorInfo;

	print_r($ts_mail_errors);
}

