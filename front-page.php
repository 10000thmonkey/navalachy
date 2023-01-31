<?php
nv_use_modules([
	"Booking/lib",
	"Booking/form",
	"Accomodation/feed",
	"UI/slider",
]);

global $nvbk;


$nv_vars = [
	
];

get_header();
?>
<main id="primary" class="site-main">

	<?php
	echo nv_template_cover_image( array(
		"attachment" => get_post_thumbnail_id( get_the_id() ),
		"content" => '<img decoding="async" src="/wp-content/uploads/uvodni-titulek.svg" alt="" width="500" height="350">'
	) );
	?>
	
	<div class="section_testimonials space-around-xl">
		<nv-slider controls class="space-around-hg slider-wrapper center" style="height:auto">

				<?php
				$testimonials = pods("reviews")->find( ["where" => "d.homepage = 1"] );

				while( $testimonials->fetch() )
				{
					$img = @nv_responsive_img( get_post_thumbnail_id( $testimonials->display( "ID" ) ) );

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

		<div class="section-block">
			<div class="contentwrap box padding-xl rows gap-lg">
				<div class="cols-flex gap-lg block-header" style="align-items: baseline;">
					<a href="experiences"><h2>Do přírody</h2></a>
					<p class="text-primary">Objevujte Valašskou krajinu a místní zajímavosti!</p>
				</div>

				<nv-slider class="hovercards">
					<?php
					$tags_query = get_terms( "experiences_tags", array(
						"include" => (array) get_option("homepage_settings_tags_1"),  
						"orderby" => "include",
						"hide_empty" => false
					) );
					foreach ( $tags_query as $tag )
					{
						$i = @nv_responsive_img( (int) get_term_meta( $tag->term_id )["image"][0] );
						echo <<<HTML

						<a class="hovercard" href="/tipy?tags={$tag->slug}">
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

				<a class="self-end button button-icon" href="/experiences">Objevujte <i class="nvicon nvicon-arrow-right"></i></a>
			</div>
		</div>

		<div class="section-highlight">
			<div class="contentwrap highlight padding-xl">
				<div class="post-highlight cols cols-sm-2 gap-lg space-around-lg">

				<?php
				$post_1 = get_post( get_option("homepage_settings_featured_1")[0] );
				$post_1_link = get_post_permalink( $post_1 );
				?>

					<a href="<?= get_post_permalink( $post_1 );?>">
						<?= nv_responsive_img( get_post_thumbnail_id( $post_1->ID ) ); ?>
					</a>
					<div class="info rows gap-sm space-around-lg">
						<h3 class="secondary-text">Doporučujeme</h3>
						<a href="<?= $post_1_link ?>"><h2><?= $post_1->post_title;?></h2></a>
						<div class="content">
							<?= force_balance_tags( explode( ". ", $post_1->post_content )[0] );?>
						</div>
						<a href="<?= get_permalink();?>" class="button button-icon button-secondary-transparent self-end">
							Více<i class="nvicon nvicon-arrow-right"></i>
						</a>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="section_experiences2">

		<div class="section-block">
			<div class="contentwrap box padding-xl rows gap-lg">
				<div class="cols-flex gap-lg block-header" style="align-items: baseline;">
					<a href="/experiences"><h2>Za zážitky</h2></a>
					<p class="text-primary">Poznejte osobitou kulturu Valach všemi smysly</p>
				</div>

				<nv-slider class="hovercards">
					<?php
					$tags_query = get_terms( "experiences_tags", array(
						"include" => (array) get_option("homepage_settings_tags_2"),
						"orderby" => "include",
						"hide_empty" => false
					) );
					foreach ( $tags_query as $tag )
					{
						$i = @nv_responsive_img( (int) get_term_meta( $tag->term_id )["image"][0] );
						echo <<<HTML
						<a class="hovercard" href="/tipy?tags={$tag->slug}">
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
				<a class="button button-icon self-end" href="/experiences">Objevujte <i class="nvicon nvicon-arrow-right"></i></a>
			</div>
		</div>

		<div class="section-highlight">
			<div class="contentwrap highlight padding-xl">
				<div class="post-highlight cols cols-sm-2 gap-lg space-around-lg">

					<?php
					$post_1 = get_post( get_option("homepage_settings_featured_2")[0] );
					$post_1_link = get_post_permalink( $post_1 );
					?>
					<a href="<?= get_post_permalink( $post_1 );?>">
						<?= nv_responsive_img( get_post_thumbnail_id( $post_1->ID ) ); ?>
					</a>
					<div class="info rows gap-sm space-around-lg">
						<h3 class="secondary-text">Doporučujeme</h3>
						<a href="<?= $post_1_link ?>"><h2><?= $post_1->post_title;?></h2></a>
						<div class="content">
							<?= force_balance_tags( explode( ". ", $post_1->post_content )[0] );?>
						</div>
				
						<a href="<?= get_permalink();?>" class="button button-icon button-secondary-transparent self-end">Více<i class="nvicon nvicon-arrow-right"></i></a>

					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="section-block section_accomodation">
		<div class="contentwrap box rows padding-xl gap-xl">
			<div class="block-header">
				<a href="/accomodation"><h2>Ubytování</h2></a>
			</div>

			<?php
			echo nv_template_booking_form(array("iss" => false));
			?>
			
			<div class="hovercards">
				<?php
				echo nv_template_accomodation_feed( array(
					"hovercards" => true
				) );
				?>
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
