<?php
wp_enqueue_script("nv-booking", "/wp-content/themes/navalachy/accomodation/a/booking.js");
wp_enqueue_script( "nv-datepicker", "/wp-content/themes/navalachy/accomodation/a/hello-week.min.js" );

get_header();
?>
<main id="primary" class="site-main">

	<?= nv_c( "UI/cover-image", [
		"attachment_id" => 1193,
		"attachment_id_portrait" => 1678,
		"content" => '<img decoding="async" src="/wp-content/uploads/logo-navalachy.svg" alt="Logo NaValachy.cz - Záruka zážitku a tradic" width="500" height="350">'
	] );
	?>
	<svg class="clip-path-template">
		<clipPath id="section-cover-image-clip" clipPathUnits="objectBoundingBox"><path d="M1,0.929 C0.55,1,0.25,0.929,0,1 L0,0 L1,0"></path></clipPath>
	</svg>
	
	<div class="section_testimonials space-around-xl">
		<nv-slider controls class="space-around-hg slider-wrapper center" style="height:auto">

		<?php
		$testimonials = pods("reviews")->find( ["where" => "d.homepage = 1"] );

		while( $testimonials->fetch() )
		{
			$img = nv_c( "UI/responsive-image", [ "attachment_id" => get_post_thumbnail_id( $testimonials->display( "ID" ) ) ] );

			echo <<<HTML
			
			<article class="card padding-xl rows center gap-md space-around-md"> 
				<div class="avatar avatar-xxxl"> $img </div>
				<h2 style="font-size: var(--font-hg)">{$testimonials->display( "title" )}</h2>
				<p>{$testimonials->display( "text" )}</p>
			</article>

			HTML;
		}
		?>

		</nv-slider>
	</div>




	<div class="section_experiences">

		<div class="section-block padding-lg">
			<div class="contentwrap box padding-xxl rows gap-hg">
				<div class="cols-flex gap-lg block-header">
					<a href="/tipy"><h2>Kam na výlet?</h2></a>
					<p class="text-primary"> Kam na výlet s dětmi, na kola nebo na romantickou procházku?</p>
				</div>

				<div>
					<nv-slider class="hovercards">
					<?php
					$tags_query = get_terms( "tips_tags", array(
						"include" => (array) get_option("homepage_settings_tags_1"),  
						"orderby" => "include",
						"hide_empty" => false
					) );
					foreach ( $tags_query as $tag )
					{
						$i = nv_c( "UI/responsive-image", [
							"attachment_id" => (int) get_term_meta( $tag->term_id )["image"][0],
							"alt" => $tag->description,
							"sizes" => "(min-width: 1px) 200px, 200px",
						] );
						echo <<<HTML

						<a class="hovercard" href="/tipy/?tags={$tag->slug}">
							$i
							<div class="iconset">
								<div class="icon">
									<i class="nvicon nvicon-md nvicon-{$tag->slug}"></i>
									{$tag->name}
								</div>
							</div>
						</a>

						HTML;
					}
					?>
					</nv-slider>
				</div>

				<a class="self-end button button-icon" href="/tipy">Objevujte <nv-icon class="nvicon-arrow-right"></nv-icon></a>
			</div>
		</div>

		<div class="section-highlight">
			<svg class="clip-path-template">
				<clipPath id="section-experiences-clip" clipPathUnits="objectBoundingBox"><path d="M1,1 C0.65,0.947,0.55,0.895,0,0.895 L0,0.105 C0.35,0.053,0.8,0.053,1,0"></path></clipPath>
			</svg>
			<div class="padding-xl">
				<div class="contentwrap highlight cols cols-sm-2 gap-lg padding-xxl">

					<?php
					$post_1 = get_post( get_option("homepage_settings_featured_1")[0] );
					$post_1_link = get_post_permalink( $post_1 );
					?>

					<a href="<?= $post_1_link;?>" class="img-golden">
						<?= nv_c( "UI/responsive-image", ["attachment_id" => get_post_thumbnail_id( $post_1->ID ), "sizes" => "(max-width: 440px) 300px, 500px" ] ); ?>
					</a>

					<div class="info rows gap-sm">
						<h3 class="secondary-text">Doporučujeme</h3>
						<a href="<?= $post_1_link ?>"><h2><?= $post_1->post_title;?></h2></a>
						<div class="content">
							<?php
							$match = [];
							preg_match( "/.*[a-z]\. /U", $post_1->post_content, $match );
							echo force_balance_tags( $match[0] );
							?>
						</div>
						<a href="<?= $post_1_link;?>" class="button button-icon space-around-md button-secondary-transparent self-end">
							Více<i class="nvicon nvicon-arrow-right"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>




	<div class="section_experiences2">

		<div class="section-block padding-lg">
			<div class="contentwrap box padding-xxl rows gap-hg">
				<div class="cols-flex gap-lg block-header">
					<a href="/tipy"><h2>Zážitky</h2></a>
					<p class="text-primary">Poznejte osobitou tradici Valašska, folklor a místní zvyky</p>
				</div>

				<div>
					<nv-slider class="hovercards">
						<?php
						$tags_query = get_terms( "tips_tags", array(
							"include" => (array) get_option("homepage_settings_tags_2"),
							"orderby" => "include",
							"hide_empty" => false
						) );
						foreach ( $tags_query as $tag )
						{
							$i = nv_c( "UI/responsive-image", [
								"attachment_id" => (int) get_term_meta( $tag->term_id )["image"][0],
								"alt" => $tag->description,
								"sizes" => "(min-width: 1px) 200px, 200px",
							] );
							echo <<<HTML
							<a class="hovercard" href="/tipy/?tags={$tag->slug}">
								$i
								<div class="iconset">
									<div class="icon">
										<i class="nvicon nvicon-md nvicon-{$tag->slug}"></i>
										{$tag->name}
									</div>
								</div>
							</a>
							HTML;
						}
						?>
					</nv-slider>
				</div>

				<a class="button button-icon self-end" href="/tipy/">Objevujte <nv-icon class="nvicon-arrow-right"></nv-icon></a>
			</div>
		</div>

		<div class="section-highlight">
			<svg class="clip-path-template">
				<clipPath id="section-experiences2-clip" clipPathUnits="objectBoundingBox"><path d="M1,0.895 C0.55,1,0.4,0.842,0,1 L0,0 C0.4,0.105,0.7,0,1,0.105"></path></clipPath>
			</svg>
			<div class="padding-xl">
				<div class="contentwrap highlight cols cols-sm-2 gap-lg padding-xxl">

					<?php
					$post_1 = get_post( get_option("homepage_settings_featured_2")[0] );
					$post_1_link = get_post_permalink( $post_1 );
					?>
					<a href="<?= get_post_permalink( $post_1 );?>" class="img-golden">
						<?= nv_c( "UI/responsive-image", [ "attachment_id" => get_post_thumbnail_id( $post_1->ID ), "sizes" => "(max-width: 440px) 300px, 500px" ] ); ?>
					</a>

					<div class="info rows gap-sm">
						<h3 class="secondary-text">Doporučujeme</h3>
						<a href="<?= $post_1_link ?>"><h2><?= $post_1->post_title;?></h2></a>
						<div class="content">
							<?php
							$match = [];
							preg_match( "/.*[a-z]\. /U", $post_1->post_content, $match );
							echo force_balance_tags( $match[0] );
							?>
						</div>
				
						<a href="<?= $post_1_link;?>" class="button button-icon space-around-md button-secondary-transparent self-end">Více<i class="nvicon nvicon-arrow-right"></i></a>

					</div>
				</div>
			</div>
		</div>
	</div>







	<div class="section_accomodation">

		<div class="section-block padding-lg">
			<div class="contentwrap box rows padding-xxl gap-hg">
				<div class="block-header rows gap-md">
					<a href="/ubytovani"><h2>Ubytování</h2></a>

					<?= nv_c( "accomodation/c/form" ); ?>
				</div>
				
				<?php $feed = nv_c( "accomodation/c/feed", [ "limit" => 3, "sizes" => "(max-width: 800px) 300px, 500px" ] ); ?>

				<nv-repeat nv-items="<?= esc_attr( json_encode( $feed["items"] ) ); ?>" class="hovercards">
					<nv-items class="gap-md">
						<?= nv_t( "accomodation/t/hovercard" ); ?>
					</nv-items>
				</nv-repeat>
			</div>
		</div>
	</div>
<?php /*
	<div class="section-spaced section_voucher">
		<h2>Darujte zážitek</h2>
		<a href="/vouchery" class="voucher-banner">
			<img src="/wp-content/uploads/Screenshot-at-2022-11-05-13-54-36.png">
		</a>
		<a href="/vouchery" class="button button-icon">Výběr voucherů<div class="nvicon nvicon-arrow_right"></div></a>
	</div>
*/ ?> 
</main>

<svg width="0" height="0">
	<defs>
		<clipPath id="clip-frontpage-top" clipPathUnits="objectBoundingBox">
			<path d="M 0 0.0625 Q 0.25 0.125 0.5625 0.0625 Q 0.8125 0 1 0.0625 L 1 1 L 0 1 Q 0 0.375 0 0.0625 Z"></path>
		</clipPath>
	</defs>
</svg>

<script type="text/javascript">
q(()=> {
	document.addEventListener("scroll", ()=>{
		(window.scrollY * 2 ) <= window.innerHeight ? document.body.removeClass("scrolled") : document.body.addClass("scrolled");
	});
window.scrollY == 0 ? document.body.removeClass("scrolled") : document.body.addClass("scrolled");
});
</script>
<?php
//get_sidebar();
get_footer();
