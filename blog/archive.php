<?php

if (!$NV_DEV):

echo nv_c( "UI/cover-image", [
	"attachment_id" => 1568,
	"heading" => "Coming soon..."
]);

else:

echo nv_c( "UI/cover-image", [
	"attachment_id" => 442,
	"heading" => "Blog"
]);
?>

<main class="contentwrap padding-lg">
	<nv-feed nv-ajax-get="blog/feed">
		<div class="padding-hg feed-filters">
			<!-- <a class="button button-plain" style="width:fit-content;" onclick="document.q('#accomodation-feed')[0].removeClass('feed-filtered').feedFetch();">Zobrazit vše</a> -->
		</div>
		<nv-items class="gap-hg rows gap-hg">
			<?php echo nv_t("blog/t/feed-item"); ?>
		</nv-items>
	</nv-feed>
</main>

<?php

endif;