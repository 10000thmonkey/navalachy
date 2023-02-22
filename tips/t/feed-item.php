<?php
nv_new_t ( "tips/t/feed-item", function() {
	ob_start();
	?>
	<template>
		<article class="feed-item" href="{$link}">
			<a href="{$link}" class="image">{!img}</a>
			<header class="padding-lg">
				<a href="{$link}"><h2>{$title}</h2></a>
				<div class="secondary-text">{$location}</div>
			</header>
			<footer class="padding-lg">
				<nv-repeat class="iconlist iconlist-hg" nv-fill="slug" nv-items="{$tags}">
					<template>
						<nv-icon class="icon nvicon-{$slug}"></nv-icon>
					</template>
				</nv-repeat>
			</footer>
		</article>
	</template>
	<?php
	return ob_get_clean();
});