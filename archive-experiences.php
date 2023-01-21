<?php /* Template Name: NV/Experiences/Archive */
$NV_MODULES = [
	"Experiences/feed",
	"Experiences/tags"
];

$meta_fields = get_post_meta( get_the_ID() );

get_header();
?>
<main id="primary">

	<?php
	echo nv_template_cover_image( array(
		"attachment" => get_post_thumbnail_id( ),
		"heading" => ( !empty($meta_fields["heading"]) ? $meta_fields["heading"][0] : "" ),
		"subheading" => ( !empty($meta_fields["subheading"]) ? $meta_fields["subheading"][0] : "" )
	) );
	?>
	
	<div class="contentwrap padding-lg">
		
		<div class="opened" id="filter-experiences-modale" data-modale="filter-mobile">
			<div class="modal-dialog">
				<div class="modal-body" style="padding:5px">
					<form action="<?php echo site_url(); ?>/wp-admin/admin-ajax.php" method="POST" id="experiences-filter">
						<div class="filter-tags">
							<?php
							$nv_tags = get_terms(
								array(
									'taxonomy'   => 'experiences_tags',
									'hide_empty' => false,
								)
							);
							foreach ( $nv_tags as $nv_tag ) {
								echo <<<HTML
								<input type="checkbox" name="tagfilter[]" id="nv_experiences_tag_toggle-{$nv_tag->slug}" value="{$nv_tag->slug}">
								<label class="tag button button-plain button-icon button-icon-right" for="nv_experiences_tag_toggle-{$nv_tag->slug
									}">
									<i class="nvicon nvicon-{$nv_tag->slug}"></i>
									<div>{$nv_tag->name}</div>
								</label>
								HTML;
							}
							?>
						</div>

						<input type="hidden" name="action" value="nv_filter_experiences">
						<input type="hidden" name="paged" value="1">
					</form>
				</div>
				<div class="modal-footer">
					<a class="button closemodale" onclick="jQuery('#filter-experiences-modale').removeClass('modale'); experiencesFilter(true)">OK</a>
				</div>
			</div>
		</div>

		
		<div class="experiences-feed-wrapper" style="position: relative;">

			<div class="filter-mobilebar">
				<a class="button button-transparent-secondary button-icon" data-modale="filter-mobile" onclick="jQuery('#filter-experiences-modale').addClass('modale');">Filtrovat<div class="nvicon nvicon-filter"></div></a>
			</div>

			<div id="experiences-feed" class="show">
				<?php
				$args = array(
					"tagfilter" => false,
					"categoryfilter" => false,
					"orderby" => "date",
					"paged" => false
				);
				if (isset( $_GET["tags"] )){
					if (strpos( $_GET["tags"], ","))
						$args["tagfilter"] = explode($_GET["tags"], ",");
					else
						$args["tagfilter"] = array($_GET["tags"]);
				}
				if (isset( $_GET["categories"] )){
					if (strpos( $_GET["categories"], ","))
						$args["categoryfilter"] = explode($_GET["categories"], ",");
					else
						$args["categoryfilter"] = array($_GET["categories"]);
				}
				if (isset( $_GET["orderby"] )){
					$args["orderby"] = $_GET["orderby"];
				}
				
				echo nv_template_experiences_feed ( $args ) ["data"];
				?>
			</div>
			<div style="display:flex;justify-content: center;">
				<a id="button-loadmore" onclick="loadMore()"  class="button button-primary<?php if($query["page"] == 1) echo ' hidden';?>">Další</a>
			</div>

			<div class="spinner-wrapper hidden"><div class="spinner"></div></div>
		</div>
	</div>
</main>



<script>
	let nv_urlparams = new URLSearchParams(location.search);
	let nv_filter_tags = [];

	let filterform = q('#experiences-filter')[0];
	let feed = q('#experiences-feed')[0];
	let spinner = q('.spinner-wrapper');
	//let counter = q("#experiences-filter input[name=paged");
	let loadMoreBtn = q("#button-loadmore");
	let counter = 1;

	function loadMore ()
	{
		counter += 1;
		
		jax.post( filterform.attr('action'), 
			{
				action: "nv_filter_experiences",
				paged: counter,
				tagfilter: nv_filter_tags
			},
			(data) =>
			{
				data = JSON.parse(data);
				feed.content( feed.content() + data["data"] ); // insert data

				if (counter == parseInt(data["page"]) )
					loadMoreBtn.addClass("hidden");
			}
		);
	}

	function experiencesFilter (isFromModal = false)
	{
		if (isMobile && !isFromModal) return false;

		updateFilter(false, filterform);

		feed.removeClass("show");
		spinner.addClass("show");
		console.log(filterform.serialize());

		jax.post( filterform.attr('action'),
		{
			action: "nv_filter_experiences",
			paged: counter,
			tagfilter: nv_filter_tags
		},
		(data) =>
		{
			data = JSON.parse(data);
			counter = 1;

			feed.content( data["data"] ); // insert data
			feed.addClass("show");

			if (parseInt(data["page"]) == 1) {
				loadMoreBtn.addClass("hidden");
			}
			else {
				loadMoreBtn.removeClass("hidden");
				spinner.removeClass("show");
			}
		});
	}
	function updateFilter ( fromURL, form )
	{
		if (fromURL) { //fetch query params and change filter form values
			
			if ( nv_urlparams.get("tags") == null ) {
				nv_filter_tags = [];
			} else {
				
				nv_filter_tags = 
					( nv_urlparams.get("tags").indexOf(",") == -1 ) ?
					[nv_urlparams.get("tags")] :
					nv_urlparams.get("tags").split(",");

				for(let tag of nv_filter_tags)
					q(".filter-tags input[type=checkbox][value="+tag+"]").checked = true;
			}
			
		} else { //form changed, write to URL
			
			nv_filter_tags = [];

			let nv_filter_tags_checkboxes = form.q(".filter-tags input[type=checkbox]");
			
			for ( let checkbox of nv_filter_tags_checkboxes)
				{ if (checkbox.checked) nv_filter_tags.push(checkbox.value); }
			
			if ( nv_filter_tags.length !== 0 )
				nv_urlparams.set("tags", nv_filter_tags.join(","));
			else
				nv_urlparams.delete("tags");
			
			history.pushState(null,null,"?"+unescape(nv_urlparams.toString()));
		}
	}
	q(function(){
		updateFilter(true);
		q('#experiences-filter input[type=checkbox]').on("change", () => { experiencesFilter(); });
	});
</script>


<?php
get_footer();
?>