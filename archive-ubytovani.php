<?php /* Template Name: NV/Accomodation/Archive */
$NV_MODULES = [
	"booking/lib",
	"booking/form",
	"accomodation/feed"
];


$meta_fields = get_post_meta( get_the_ID() );

$nv_vars = [$meta_fields];


$range = ( !empty($_GET["begin"]) && !empty($_GET["end"]) ) ?
	array( "begin" => $_GET["begin"], "end" => $_GET["end"] ) : [];


get_header();
?>
<main id="primary">

	<?php
	echo nv_template_cover_image( array(
		"attachment" => get_post_thumbnail_id( get_the_id() ),
		"heading" => $meta_fields["heading"][0],
		"subheading" => $meta_fields["subheading"][0],
		"content" => nv_template_booking_form( array(
			"iss" => false
		))
	) );
	?>

	<div class="ubytovani-feed contentwrap space-around-lg padding-lg gap-hg">
		
		<?php
		echo nv_template_accomodation_feed( array(
			"range" => $range,
			"apartments" => $nvbk->get_apartments_array()
		) );
		?>

	</div>
</main>
<?php
get_footer();