<?php

nv_new_t( "accomodation/t/hovercard", function() {
	ob_start();
	?>

	<template>
		<a class="hovercard" href="{$link}">
			{!img}
			<div class="label">
				<div>{$title}</div>
				<div class="secondary-text">{$address}</div>
			</div>
		</a>
	</template>
	
	<?php
	return ob_get_clean();
});