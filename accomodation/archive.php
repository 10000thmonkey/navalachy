<?php /* Template Name: NV/Accomodation/Archive */
wp_enqueue_script("nv-booking", "/wp-content/themes/navalachy/accomodation/a/booking.js");
wp_enqueue_script( "nv-datepicker", "/wp-content/themes/navalachy/accomodation/a/hello-week.min.js" );
$meta_fields = get_post_meta( get_the_ID() );

$nv_vars = [$meta_fields];


$range = ( !empty($_GET["begin"]) && !empty($_GET["end"]) ) ?
	array( "begin" => $_GET["begin"], "end" => $_GET["end"] ) : [];


get_header();
?>
<main id="primary">

	<?php
	echo nv_c( "UI/cover-image", [
		"attachment_id" => 1441,
		"heading" => "Ubytování v Bílých Karpatech",
		"subheading" => "",
		"content" => nv_c( "accomodation/c/form", [
			"isa" => true
		] )
	] );
	?>

	<?php
	$feed = nv_c( "accomodation/c/feed", [ "range" => $range, "apartments" => [] ] );
	?>
	<nv-feed id="accomodation-feed" nv-ajax-get="accomodation/feed" nv-ajax-params="begin,end" class="contentwrap space-around-hg padding-lg">
		<div class="padding-hg feed-filters">
			<a class="button button-plain" style="width:fit-content;" onclick="document.q('#accomodation-feed')[0].removeClass('feed-filtered').feedFetch();">Zobrazit vše</a>
		</div>
		<nv-items class="gap-hg">
			<?php echo nv_t("accomodation/t/feed-item"); ?>
		</nv-items>
		<nv-messagebox></nv-messagebox>
	</nv-feed>

</main>
<style type="text/css">
	#accomodation-feed .feed-filters {display: none}
	#accomodation-feed.feed-filtered .feed-filters {display: block}
</style>
<?php
get_footer();