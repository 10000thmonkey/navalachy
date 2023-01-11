<?php
//provide array of Attachment IDs, component will handle rest

function nv_template_cover_image( $_VAR ) {
$output = NULL;
ob_start();
?>

	<div class="section-cover-image">
		<div class="cover-image"><?php echo nv_responsive_img( $_VAR["attachment"], "(min-width: 1px) 100vw, 100vw"); ?></div>
		<div class="cover-content">
			<?= ! empty($_VAR["heading"]) ? "<h1>".$_VAR["heading"]."</h1>" : ""; ?>
			<?= ! empty($_VAR["subheading"]) ? "<h5>".$_VAR["subheading"]."</h5>" : ""; ?>
			<?= ! empty($_VAR["content"]) ? $_VAR["content"] : ""; ?>
		</div>
	</div>

<?php
return ob_get_clean();
}
?>