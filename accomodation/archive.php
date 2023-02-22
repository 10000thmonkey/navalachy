<?php /* Template Name: NV/Accomodation/Archive */
wp_enqueue_script("nv-booking", "/wp-content/themes/navalachy/accomodation/a/booking.js");
wp_enqueue_script( "nv-datepicker", "/wp-content/themes/navalachy/accomodation/a/hello-week.min.js" );
wp_enqueue_style( "nv-datepicker", "/wp-content/themes/navalachy/accomodation/a/hello-week.min.css" );

$meta_fields = get_post_meta( get_the_ID() );

$nv_vars = [$meta_fields];


$range = ( !empty($_GET["begin"]) && !empty($_GET["end"]) ) ?
	array( "begin" => $_GET["begin"], "end" => $_GET["end"] ) : [];


get_header();
?>
<main id="primary">

	<?php
	echo nv_c( "UI/cover-image", [
		"attachment_id" => get_post_thumbnail_id( get_the_id() ),
		"heading" => "Ubytování v Bílých Karpatek",
		"subheading" => "",
		"content" => nv_c( "accomodation/c/form", [
			"isa" => true
		] )
	] );
	?>

	<?php
	$feed = nv_c( "accomodation/c/feed", [ "range" => $range, "apartments" => [] ] );
	?>
	<nv-feed id="accomodation-feed" nv-items="<?= esc_attr( json_encode( $feed["items"] ) );?>" id="accomodation-feed" class="contentwrap space-around-lg">
		<?php echo nv_t("accomodation/t/feed-item"); ?>
	</nv-feed>

</main>
<?php
get_footer();