<?php 
get_header();

?>
<main id="primary" class="site-main">

	<div class="section-cover-image">
		<div class="cover-image"></div>
		<div class="cover-content">
			<h1>Udělejte radost svým blízkým</h1>
			<h5></h5>
		</div>
	</div>

	<article class="contentwrap">
		
		<form class="entry-content" name="voucher-selection" id="voucher-selection" action="https://navalachy.cz/wp-admin/admin-ajax.php" method="POST">
			<div class="voucher-type">
				<h2>Nabídka voucherů</h2>
				<input type="radio" id="voucher_type_1" name="voucher_type" value="1">
				<label for="voucher_type_1">
					<img src="https://navalachy.cz/wp-content/uploads/voucher1.png">
					<h2>Víkend pá-ne</h2>
					<h3>10000 Kč</h3>
					<span type="button" class="button"></span>
				</label>
				<input type="radio" id="voucher_type_2" name="voucher_type" value="2">
				<label for="voucher_type_2">
					<img src="https://navalachy.cz/wp-content/uploads/voucher2.png">
					<h2>Mimovíkend ne-čt</h2>
					<h3>5000 Kč</h3>
					<span type="button" class="button"></span>
				</label>
			</div>

			<div class="voucher-form">
				<h2>Osobní údaje</h2>
				<label><input type="checkbox" name="voucher_given_email_send">Zaslat obdarovanému na email</label>
				<input name="voucher_given_email" placeholder="Email" type="email">
				<label><input type="checkbox" name="voucher_given_name_send">Vytisknout jméno obdarovaného</label>
				<input name="voucher_given_name" placeholder="Jméno">
				<div class="voucher-submit">
					<a class="button button-primary" onclick="submitVoucherForm()">Přejít k úhradě</a>
				</div>
				<input type="hidden" name="action" value="nv_voucher_precheckout">
			</div>

		</form>

	</article>
</main>
<?php
get_footer();