<?php
if ( empty( $_GET["listing"] ) )
{
	$apartments = new WP_Query([
		"post_type" => "accomodation",
		"meta_query" => [
			[
				"key" => "host",
				"value" => 2//$current_user->data->ID
			]
		]
	]);

	if ($apartments->have_posts())
	{
		while ($apartments->have_posts()) {
			$apartments->the_post();

			//print_r($apartments->post);

			$img = nv_responsive_img( get_post_thumbnail_id($apartments->post->ID) );

			echo <<<HTML
				<a class="box cols-flex space-around-md" href="/admin-accomodation?show=listings&listing={$apartments->post->ID}">
					$img
					<div class="padding-md">
						<h2>{$apartments->post->post_title}</h2>
					</div>
				</a>
			HTML;
		}
	}
	else {
		echo "Zatím nespravujete žádné objekty";
	}

}
else
{
	include "page-listings-single.php";
}
?>