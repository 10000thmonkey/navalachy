<?php

nv_new_t( "accomodation/t/feed-item", function() {
	ob_start();
	?>

	<template>
		<article class="feed-item">
			<a href="{$link}" class="image">
				{!img}

				<nv-repeat nv-items="{$details}" class="padding-lg iconlist">
					<template>
						<div class="icon" title="{$name}">
							<nvicon class="nvicon-{$key}">
						</div>
					</template>
				</nv-repeat>

			</a>
			<header class="padding-lg">
				<a href="{$link}"><h2 class="nomargin">{$title}</h2></a>
				<div class="secondary-text">{$address}</div>
			</header>
			<section class="padding-lg iconset iconset-hg cols cols-2">
				<div class="col icon">
					<i class="nvicon nvicon-group"></i>
					<span><b>{$capacity}</b> lidí</span>
				</div>
				<div class="col icon">
					<i class="nvicon nvicon-beds"></i>
					<span><b>{$bedroom}</b> ložnice</span>
				</div>
			</section>
			<footer class="padding-lg">
				<div class="price">
					od<span style="font-size: var(--font-hg);color:var(--primary);">&nbsp;{$price_final},-</span>/&nbsp; noc
				</div>
				<a class="button nomargin" href="{$link_reserv}">Rezervovat</a>
			</footer>
		</article>
	</template>
	
	<?php
	return ob_get_clean();
});