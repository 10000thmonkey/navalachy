<?php /* Template Name: NV/Experiences/Archive */
$NV_MODULES = [
	"experiences/feed"
];

$meta_fields = get_post_meta( get_the_ID() );

get_header();
?>
<main id="primary">

	<?php
	echo nv_template_cover_image( array(
		"attachment" => get_post_thumbnail_id( ),
		"heading" => ( !empty($meta_fields["heading"]) ? $meta_fields["heading"] : "" ),
		"subheading" => ( !empty($meta_fields["subheading"]) ? $meta_fields["heading"] : "" )
	) );
	?>
	
	<div class="contentwrap padding-lg">
		
		<div class="opened" id="filter-tipy-modale" data-modale="filter-mobile">
			<div class="modal-dialog">
				<div class="modal-body" style="padding:5px">
					<form action="<?php echo site_url(); ?>/wp-admin/admin-ajax.php" method="POST" id="tipy-filter">
						<div class="filter-tags">
							<?php
							$nv_tags = get_terms(
								array(
									'taxonomy'   => 'zazitky_tag',
									'hide_empty' => false,
								)
							);
							$output = "";

							foreach ( $nv_tags as $nv_tag ) :
								$output .= '
								<input type="checkbox" name="tagfilter[]" id="nv_tipy_tag_toggle-'.$nv_tag->slug.'" value="'.$nv_tag->slug.'">
								<label class="filter-tag button button-plain button-icon-right" for="nv_tipy_tag_toggle-'.$nv_tag->slug.'">
									<div class="filter-tag-icon nvicon nvicon-'.$nv_tag->slug.'"></div>
									<div class="filter-tag-name">'.$nv_tag->name.'</div>
								</label>';
								
							endforeach;

							echo $output;
							?>
						</div>

						<input type="hidden" name="action" value="nv_filter_tipy">
						<input type="hidden" name="paged" value="1">
					</form>
				</div>
				<div class="modal-footer">
					<a class="button closemodale" onclick="jQuery('#filter-tipy-modale').removeClass('modale'); tipyFilter(true)">OK</a>
				</div>
			</div>
		</div>

		
		<div class="tipy-feed-wrapper" style="position: relative;">

			<div class="filter-mobilebar">
				<a class="button button-transparent-secondary button-icon" data-modale="filter-mobile" onclick="jQuery('#filter-tipy-modale').addClass('modale');">Filtrovat<div class="nvicon nvicon-filter"></div></a>
			</div>

			<div id="tipy-feed" class="show">
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
	let nv_filter_categories = [];

	let filterform = jQuery('#tipy-filter');
	let feed = jQuery('#tipy-feed');
	let spinner = jQuery('.spinner-wrapper');
	let counter = jQuery("#tipy-filter input[name=paged");
	let loadMoreBtn = jQuery("#button-loadmore");

	function loadMore ()
	{
		counter.attr("value", parseInt(counter.attr("value"))+1);
		
		jQuery.ajax({
			url: filterform.attr('action'),
			data: filterform.serialize(), // form data
			type: filterform.attr('method'), // POST
			success:function(data)
			{
				data = JSON.parse(data);
				feed.html(feed.html() + data["data"]); // insert data

				if (counter.attr("value") == data["page"])
					loadMoreBtn.addClass("hidden");
			}
		});
	}

	function tipyFilter (isFromModal = false)
	{
		if (isMobile && !isFromModal) return false;

		jQuery.ajax({
			url: filterform.attr('action'),
			data: filterform.serialize(), // form data
			type: filterform.attr('method'), // POST
			beforeSend: function(xhr){
				feed.removeClass("show");
				spinner.addClass("show");
			},
			success: function(data){
				data = JSON.parse(data);
				counter.attr("value", 1);
				updateFilter(false, filterform);
				feed.html(data["data"]); // insert data
				feed.addClass("show");
				if (parseInt(data["page"]) == 1)
					loadMoreBtn.addClass("hidden");
				else 
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
					document.querySelector(".filter-tags input[type=checkbox][value="+tag+"]").checked = true;
			}
			
			if ( nv_urlparams.get("categories") == null ) {
				nv_filter_categories = [];
			} else {
				nv_filter_categories = 
					( nv_urlparams.get("categories").indexOf(",") == -1 ) ?
					[nv_urlparams.get("categories")] :
					nv_urlparams.get("categories").split(",");
				
				for(let category of nv_filter_categories)
					document.querySelector(".filter-categories input[type=checkbox][value="+category+"]").checked = true;
			}
			
		} else { //form changed, write to URL
			
			nv_filter_tags = [];
			nv_filter_categories = [];

			let nv_filter_tags_checkboxes = form.find(".filter-tags input[type=checkbox]");
			let nv_filter_categories_checkboxes = form.find(".filter-categories input[type=checkbox]");
			
			for ( let checkbox of nv_filter_tags_checkboxes)
				{ if (checkbox.checked) nv_filter_tags.push(checkbox.value); }
			for ( let checkbox of nv_filter_categories_checkboxes)
				{ if (checkbox.checked) nv_filter_categories.push(checkbox.value); }
			
			if (!!nv_filter_tags[0])
				nv_urlparams.set("tags", nv_filter_tags.join(","));
			else
				nv_urlparams.delete("tags");

			if (!!nv_filter_categories[0])
				nv_urlparams.set("categories", nv_filter_categories.join(","));
			else
				nv_urlparams.delete("categories");
			
			history.pushState(null,null,"?"+unescape(nv_urlparams.toString()));
		}
	}
	jQuery(function(){
		updateFilter(true);
		jQuery('#tipy-filter input[type=checkbox]').on("change", () => { tipyFilter(); });
	});
</script>


<?php
get_footer();
?>