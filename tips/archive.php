<?php /* Template Name: NV/Experiences/Archive */

get_header();
?>
<main id="primary">

	<?php
	echo nv_c( "UI/cover-image", [
		"attachment_id" => 1298,
		"heading" => "Naše tipy na výlet",
		"subheading" => ""
	] );
	?>
	
	<div class="contentwrap padding-lg">
		
		<nv-modal class="notmodal fullwidth" id="tips-modal-filter">
			<div>
				<article style="padding:5px">

					<form action="<?php echo site_url(); ?>/wp-admin/admin-ajax.php" method="POST" id="tips-form-filter">

						<?php $nv_tags = get_terms( [ 'taxonomy'   => 'tips_tags', 'hide_empty' => true ] ); ?>
						<nv-repeat class="filter-tags" nv-fill="slug,name" nv-items="<?=esc_attr(json_encode( $nv_tags ))?>">
							<template>
								<input type="checkbox" name="tags" value="{$slug}" id="nv_tips_tag_toggle-{$slug}" onchange="tipsFilter()">
								<label class="tag button button-icon button-plain button-icon-right" for="nv_tips_tag_toggle-{$slug}">
									<nv-icon class="nvicon-{$slug}"></nv-icon>
									<div>{$name}</div>
								</label>
							</template>
						</nv-repeat>

					</form>

				</article>
				<footer>
					<a class="button" nv-modal-close onclick="nv.modal['tips-modal-filter'].addClass('notmodal'); tipsFilter(true)">OK</a>
				</footer>
			</div>
		</div>

		
		<div class="tips-feed-wrapper" style="position: relative;">

			<div class="filter-mobilebar">
				<a class="button button-transparent-secondary button-icon" nv-modal-open="tips-modal-filter" onclick="nv.modal['tips-modal-filter'].removeClass('notmodal');">Filtrovat<nv-icon class="nvicon-filter"></nv-icon></a>
			</div>

			<nv-feed id="tips-feed" class="contentwrap" nv-ajax-get="tips/feed" nv-ajax-params="tags">
				<nv-items class="gap-lg padding-lg">
					<?= nv_t ( "tips/t/feed-item" ); ?>
				</nv-items>
				<div class="cols-flex center">
					<a id="button-loadmore" onclick="loadMore()" class="button button-primary nodisplay">Další</a>
				</div>
			</nv-feed>

		</div>
	</div>
</main>



<script>
	let nv_urlparams = new URLSearchParams(location.search);
	let nv_filter_tags = [];

	let filterform = q('#tips-form-filter')[0];
	let feed = q('#tips-feed')[0];
	let loadMoreBtn = q("#button-loadmore");
	let counter = 1;

	function loadMore ()
	{
		counter += 1;
		feed.spinnerShow();
		
		jax.post( filterform.attr('action'), 
			{
				action: "tips/feed",
				paged: counter,
				tags: nv_filter_tags
			},
			(response) =>
			{
				let data = JSON.parse(response);

				if ( parseInt( data.status ) === 0 )
				{						
					feed.addItems( data.items ); // insert data

					if ( ! parseInt( data.more ) )
						loadMoreBtn.noDisplay();
					else
						loadMoreBtn.display();
				}
				feed.spinnerHide();
			}
		);
	}

	function tipsFilter (isFromModal = false)
	{
		if (isMobile() && !isFromModal) return false;

		updateFilter(false, filterform);

		feed.spinnerShow();

		jax.post( filterform.attr('action'),
		{
			action: "tips/feed",
			paged: counter,
			tags: nv_filter_tags
		},
		(response) =>
		{
			let data = JSON.parse(response);
			counter = 1;

			feed.cleanItems();
			feed.addItems( data.items ); // insert data

			feed.spinnerHide();

			if ( ! parseInt(data.more) ) {
				loadMoreBtn.noDisplay();
			}
			else {
				loadMoreBtn.display();
			}
		});
	}
	function updateFilter ( fromURL, form )
	{
		if (fromURL)
		{ //fetch query params and change filter form values	
			if ( nv_urlparams.get("tags") == null ) {
				nv_filter_tags = [];
			} else {
				
				nv_filter_tags = 
					( nv_urlparams.get("tags").indexOf(",") == -1 ) ?
					[nv_urlparams.get("tags")] :
					nv_urlparams.get("tags").split(",");

				for(let tag of nv_filter_tags)
					filterform.q(".filter-tags input[type=checkbox][value="+tag+"]").attr("checked", "checked");
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