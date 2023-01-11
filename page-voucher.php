<?php


session_start();

// THIS WILL CHANGE
$vouchers = json_decode(file_get_contents(get_template_directory()."/vouchers.json"));


//directly from checkout
if ( isset($_GET["payment"]) && isset($_GET["order"]) && $_GET["payment"] == "successful" && isset($_SESSION['voucher_code']) ) {

	foreach($vouchers as $key => $voucher) {

		if (isset($voucher->code) && $voucher->code == $_SESSION["voucher_code"]) {

			$voucher_code = $_SESSION["voucher_code"];
			$vouchers[$key]->order = $_GET["order"];
			file_put_contents(get_template_directory()."/vouchers.json", json_encode($vouchers));
		}
	}
	$voucher_first_time = true;
	$voucher_success_message = true;
	unset( $_SESSION["voucher_code"]);
}


//identify voucher by either code or order id
if ( isset($_GET["order"]) ) {

	foreach ($vouchers as $key => $value) {

		if ($value->order == $_GET["order"]) {
			$voucher_code = $value->code;
			$voucher_meta = $vouchers[$key];
			break;
		}
	}
	$voucher_order = $_GET["order"];
}
elseif ( isset($_GET["code"]) && !isset($voucher_code) ) {

	foreach ($vouchers as $key => $value) {

		if ($value->code == $_GET["code"]) {
			$voucher_order = $value->order;
			$voucher_meta = $vouchers[$key];
			break;
		}
	}
	$voucher_code = $_GET["code"];
}

//if voucher is not identifyable, redirect
if ( !( isset($voucher_order) && isset($voucher_code) ) ) {
	header("Location: /vouchery/");
}




get_header();

$voucher = wc_get_order( $voucher_order )->get_data();
//echo var_dump($voucher_order);
?>

<main id="primary" class="site-main contentwrap">

	<article>
		<header>
			<?php if(isset($voucher_success_message) && $voucher_success_message) echo "Děkujeme";?>
			<h1>Váš voucher</h1>
		</header>
		<div class="content">
			<div class="voucher-preview">
				<canvas id="voucher-canvas" width="1190" height="560"></canvas>
			</div>
			<h2>Kod: <?=$voucher_meta->code;?></h2>
			<p>Hodnota: <?= $voucher["total"];?></p>
			<p>Jméno: <?= $voucher["billing"]["first_name"]. " " .$voucher["billing"]["last_name"];?></p>
		</div>
	</article>

</main><!-- #main -->


<?php
$voucher_first_time = true; //comment out later;

if($voucher_first_time) {
include "inc/lib-convertsvgtopng.php";
?>
<script>
$(()=>{
var canvas = document.getElementById('voucher-canvas');
var svg = document.getElementById("voucher-svg");
var ctx = canvas.getContext('2d');
var data = (new XMLSerializer()).serializeToString(svg);
var DOMURL = window.URL || window.webkitURL || window;

var img = new Image();
var svgBlob = new Blob([data], {type: 'image/svg+xml;charset=utf-8'});
var url = DOMURL.createObjectURL(svgBlob);

img.onload = function () {
    ctx.drawImage(img, 0, 0);
    DOMURL.revokeObjectURL(url);

    var imgURI = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream');

    console.log(imgURI);

    $.ajax ({
    	url: "<?= site_url(); ?>/wp-admin/admin-ajax.php",
    	method: "POST",
    	data: {
    		imgURI: imgURI,
    		action: "nv_gen_voucher",
    		order_id: <?=$voucher_order;?>,
    		type: <?=$voucher_meta->type;?>

    	},
    	success: function (result) {
    		console.log(result);
    	}
    });
};
img.src = url;
});
</script>
<?php
} // end of first time (voucher gen.) condition

get_footer();