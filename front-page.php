<?php
nv_use_modules(["booking"]);

global $nvbk;

$zazitky_tags_terms = get_terms( array(
	"taxonomy" => "zazitky_tag", 
	"hide_empty" => false
));
$zazitky_tags_asssoc = array();
foreach($zazitky_tags_terms as $term) {
	$zazitky_tags_asssoc[$term->slug] = $term;
	$zazitky_tags_asssoc[$term->slug]->meta_fields = get_term_meta( $term->term_id,  );
}

get_header();
?>
<? echo "<h1>PIČOOOOOOOOOOO</h1>" ?>
<main id="primary" class="site-main">

	<?php
	echo nv_template_cover_image( array(
		"attachment" => get_post_thumbnail_id( get_the_id() ),
		"content" => '<img decoding="async" src="'.get_site_url().'/wp-content/uploads/uvodni-titulek.svg" alt="" width="500" height="350">'
	) );
	?>
	
	<div class="section_zazitky">

		<div class="section-block">
			<div class="contentwrap">
				<div class="block">
					<div class="block-header">
						<h2>Do přírody</h2>
						<p>Objevujte Valašskou krajinu a místní zajímavosti!</p>
					</div>
					<div class="hovercards">
					<?php
					$tags = array( "pesky",	"koupani", "nakole", "naskaly", "sdetmi");
					foreach ( $tags as $tag ) :
						echo '
						<a class="hovercard" href="/zazitky?tags='.$tag.'">
							'.@nv_responsive_img( (int) $zazitky_tags_asssoc[ $tag ]->meta_fields["image"][0] ).'
							<div class="label">
								<div class="nvicon nvicon-md nvicon-'.$tag.'"></div>'. $zazitky_tags_asssoc[$tag]->name .'
							</div>
						</a>';
					endforeach;
					?>
					</div>
					<a class="button button-icon" href="/zazitky">Objevujte<div class="nvicon nvicon-arrow-right"></div></a>
				</div>
			</div>
		</div>

		<div class="section-highlight">
			<div class="contentwrap">
				<div class="highlight">
					<div class="post-highlight">
						<?php
						$args = array(
							'post_type' => 'zazitky',
							'posts_per_page' => 1,
							'orderby' => 'date',
							'tax_query' => array(
								array(
									'taxonomy' => 'zazitky_tag',
									'field' => 'slug',
									'terms' => 'featured_1'
								)
							)
						);
						$query = new WP_Query($args);
						if($query->have_posts()) {
							while ($query->have_posts()) {
								$query->the_post();
								?>
								<a href="<?= get_permalink();?>">
									<?= nv_responsive_img( get_post_thumbnail_id( get_the_ID() ) ); ?>
								</a>
								<div class="info">
									<h3>Doporučujeme</h3>
									<a href="<?= get_permalink();?>"><h2><?=the_title();?></h2></a>
									<div class="content"><?=force_balance_tags(get_the_excerpt());?></div>
									<a href="<?= get_permalink();?>" class="button button-icon button-secondary-transparent">Více<div class="nvicon nvicon-arrow-right"></div></a>
								</div>
								<?php
							}
							wp_reset_postdata();
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="section_zazitky2">
		<div class="section-block">
			<div class="contentwrap">
				<div class="block">
					<div class="block-header">
						<h2>Za tradicí</h2>
						<p>Poznejte osobitou kulturu Valach všemi smysly!</p>
					</div>
					<div class="hovercards">
					<?php
					$tags = array( "kultura", "remesla", "tradice" );
					foreach ( $tags as $tag ) :
						echo '
						<a class="hovercard" href="/zazitky?tags='.$tag.'">
							'.@nv_responsive_img( (int) $zazitky_tags_asssoc[ $tag ]->meta_fields["image"][0] ).'
							<div class="label">
								<div class="nvicon nvicon-'.$tag.'"></div>'. $zazitky_tags_asssoc[$tag]->name .'
							</div>
						</a>';
					endforeach;
					?>
					</div>
					<a class="button button-icon" href="/zazitky">Objevujte<div class="nvicon nvicon-arrow-right"></div></a>
				</div>
			</div>
		</div>

		<div class="section-highlight">
			<div class="contentwrap">
				<div class="highlight">
					<div class="post-highlight">
						<?php
						$args = array(
							'post_type' => 'zazitky',
							'posts_per_page' => 1,
							'page' => 2,
							'orderby' => 'date',
							'tax_query' => array(
								array(
									'taxonomy' => 'zazitky_tag',
									'field' => 'slug',
									'terms' => 'featured_2'
								)
							)
						);
						$query = new WP_Query($args);
						if($query->have_posts()) {
							while ($query->have_posts()) {
								$query->the_post();
								?>
								<a href="<?= get_permalink();?>">
									<?= nv_responsive_img( get_post_thumbnail_id( get_the_ID() ) ); ?>
								</a>
								<div class="info">
									<h3>Doporučujeme</h3>
									<a href="<?= get_permalink();?>"><h2><?=the_title();?></h2></a>
									<div class="content"><?=force_balance_tags(get_the_excerpt());?></div>
									<a href="<?= get_permalink();?>" class="button button-icon button-secondary-transparent">Více<div class="nvicon nvicon-md nvicon-arrow-right"></div></a>
								</div>
								<?php
							}
							wp_reset_postdata();
						} else {
							echo "zatím žádné příspěvky";
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="section-block section_ubytovani">
		<div class="contentwrap">
			<div class="block">
				<div class="block-header">
					<h2>Ubytování</h2>
				</div>

				<?php
				echo nv_template_booking_form(array("iss" => false));
				?>
				
				<div class="hovercards">
					<?php
					$query = new WP_Query( array(
						'post_type' => 'ubytovani',
					) );

					$reviewAssoc = array();
					$reviews = pods ( "ubytovani-reviews" );
					$reviews->find( array(
						"select" => "*",
						"join" => "LEFT JOIN `wp_podsrel` ON `t`.`id` = `wp_podsrel`.`item_id`"
					) );

					while( $reviews->fetch() ) {
						$reviewAssoc[$reviews->display("related_item_id")][] = $reviews->row();
					}
					
					while( $query->have_posts() ): $query->the_post();

						$feat_image = get_the_post_thumbnail_url();
						$post_link = get_post_permalink($query->post->ID);
						$meta_fields = get_post_meta($query->post->ID);
						echo '
						<a class="hovercard" href="'.$post_link.'" style="background-image:url(\''.$feat_image.'\')">
							<!--div class="icon nvicon"></div-->
							<div class="label">
								<div>'.$query->post->post_title.'</div>
								<div class="secondary-text">'. (isset($meta_fields["address"][0]) ? $meta_fields["address"][0] : "") . '</div>
							</div>
						</a>';
						if ( isset($reviewsAssoc[$query->post->ID]) ) :
							$pod = $reviewsAssoc[$query->post->ID];
							echo '
							<div class="review">
								<div class="review-head">
									<div class="review-avatar">'.nv_responsive_img(get_post_thumbnail_id()).'</div>
									<div>
										<div class="review-name">'.display("name").'</div>
										<div class="review-source">'.$review->display("zdroj").'</div>
									</div>
								</div>
								<div class="review-body">
									<div class="review-text">'.$pod->display("recenze").'</div>
								</div>
							</div>';
						endif;
					endwhile;
					wp_reset_postdata();
					?>
				</div>
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
jQuery(()=> {
	let bodyEl = jQuery(document.body);
	document.addEventListener("scroll", ()=>{
		(window.scrollY * 2 ) <= window.innerHeight ? bodyEl.removeClass("scrolled") : bodyEl.addClass("scrolled");
	});
window.scrollY == 0 ? bodyEl.removeClass("scrolled") : bodyEl.addClass("scrolled");
});
</script>
<?php
//get_sidebar();
get_footer();
