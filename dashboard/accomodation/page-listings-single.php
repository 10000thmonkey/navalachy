
<?php

$sections = [
	"basic" => ["Základní"],
	"advanced" => ["Pokročilé"],
	"pricing" => ["Ceny"],
	"rooms" => ["Pokoje"],
	"reviews" => ["Reviews"],
	"related" => ["Doporučení"],
	"sync" => ["Synchronizace"]
];
$section = empty($_GET["section"]) ? array_keys( $sections )[0] : $_GET['section'];


$apartments = new WP_Query([
	"post_type" => "accomodation",
	"post__in" => [(int)$_GET['listing']],
	"meta_query" => [
		"key" => "host",
		"value" => $current_user->data->ID
	]
]);

if ($apartments->have_posts())
{
	while ($apartments->have_posts())
	{
		$apartments->the_post();

		$img = nv_responsive_img( get_post_thumbnail_id($apartments->post->ID) );

		$meta = get_post_meta( $apartments->post->ID );

		$pod = pods("accomodation", (int)$_GET['listing']);
		?>

			<header class="cols-flex space-betweeen padding-md" style="">
				<h2><?=$apartments->post->post_title?></h2>
				<?=$img?>
			</header>

			<div class="cols-flex gap-lg">

				<div class="dashboard-menu-left" style="position: relative;">
					<div style="position: sticky;top:0">
					<?php
					foreach ( $sections as $s => $d )
					{
						$cls = ($section == $s) ? " selected" : "";
						echo <<<HTML
						<a class="button button-icon button-plain$cls" href="/admin-accomodation?show=listings&listing={$apartments->post->ID}&section=$s"><i class="nvicon"></i>{$d[0]}</a>
						HTML;
					}
					?>
					</div>
				</div>

				<form class="dashboard-form rows gap-lg" style="flex:1">
					<?php if ($section == "basic"):	?>
						<div class="dashboard-fieldset">

							<?= $pod->form([
								"post_title" => ["label" => "Název"],
								"slug" => ["label" => "URL zkratka"],
								"capacity",
								"bedroom",
								"desc_short",
								"gps",
								"address"
							]); ?>

						</div>
					<?php elseif ($section == "advanced"): ?>
						<div class="dashboard-fieldset">

							<?= $pod->form([
								""
							]); ?>

						</div>
					<?php elseif ($section == "pricing"):	?>

						<div class="dashboard-fieldset rows gap-md">
							<label>
								<h3>Cena za noc</h3>
								<input type="number" name="price" value="<?=$meta["price"][0]?>">
							</label>
							<label>
								<h3>Sleva na týden</h3>
								<input type="number" name="discount_week" value="<?=$meta["discount_week"][0]?>">
							</label>
							<label>
								<h3>Sleva na měsíc</h3>
								<input type="number" name="discount_month" value="<?=$meta["discount_month"][0]?>">
							</label>
						</div>
						<div class="dashboard-fieldset ">
							<h3>Náklady</h3>
							<template id="template-fields-costs">
								<?php
								$options = ["once", "daily"];
								$options_html = "";
								foreach ($options as $option)
									$options_html .= '<option value="'.$option.'">'.$option.'</option>';

								$template = <<<HTML
								<li class="cols-flex space-between">
									<div><input data-cost value=""></div>
									<div><input data-cost value="" type="number"></div>
									<div><select>{$options_html}</select></div>
									<div><a onclick="this.closestParent('li').remove()" style="font-size: 200%;color: red;">×</a></div>
								</li>
								HTML;
								echo $template;
								?>
							</template>
							<ul class="fields-costs">
								<?php 

								if ( empty($meta["costs"][0]) ):
									echo $template;
								else:
									$costs = json_decode($meta["costs"][0]);
									foreach ( $costs as $cost ):
										$options = ["once", "daily"];
										$options_html = "";
										foreach ($options as $option)
											$options_html .= '<option value="'.$option.'" '.($option == $cost[2] ? "selected" : "").'>'.$option.'</option>';

										echo <<<HTML
											<li class="cols-flex space-between">
												<div><input data-cost value="{$cost[0]}" required></div>
												<div><input data-cost type="number" value="{$cost[1]}" required></div>
												<div><select>{$options_html}</select></div>
												<div><a onclick="this.closestParent('li').remove()" style="font-size: 200%;color: red;">×</a></div>
											</li>
										HTML;
									endforeach;
								endif;
								?>
							</ul>
							<a onclick="this.parentElement.q('.fields-costs')[0].insert(this.parentElement.q('#template-fields-costs')[0].content.cloneNode(true))" class="button button-plain button-sm">Přidat</a>
							<input name="costs" type="hidden">
							<script>
							function setCostsField () {
								var fields = q(".fields-costs input, .fields-costs select");
								var input = q("input[name=costs]");
								var res = [];

								for (let i = 0; i < (fields.length / 3); i++) {
									if (fields[i*3].value || fields[i*3].value || fields[i*3].value) {
										res.push( [ fields[i * 3].value, fields[i * 3 + 1].value, fields[i * 3 + 2].value ] );
									}
								}
								input.attr("value", JSON.stringify(res));
							}	

							</script>
						</div>

					<?php elseif ($section == "rooms"):	?>

					<?php elseif ($section == "reviews"):	?>
					<?php elseif ($section == "related"):	?>
					<?php elseif ($section == "sync"):	?>
					<?php endif;?>

					<input type="submit">
				</form>
				<script type="text/javascript">
					q(() => {
						q("form.dashboard-form").on("submit", (e)=>{
							e.preventDefault();
							setCostsField ();

							jax.post("/wp-admin/admin-ajax.php", {action: "nv_dashboard_accomodation_settings",data: JSON.stringify(q("form.dashboard-form")[0].serialize())});
						});
					});
				</script>

			</div>

		<?php
	}
}