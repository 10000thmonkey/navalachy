<?php
if (!$user) wp_redirect(home_url());

$NV_MODULES = [
	"Booking/lib"
];
get_header();


$pages = [
	"listings" => ["Ubytování", "beds"],
	"calendar" => ["Kalendář", "calendar"],
	"reservations" => ["Rezervace", "menu"]
];

$page = empty($_GET["show"]) ? "listings" : $_GET['show'];
?>


<header class="dashboard-header">
	<div class="contentwrap cols-flex gap-md padding-md">
		<?php
		foreach ( $pages as $p => $d )
		{
			$cls = ($page == $p) ? " selected" : "";
			echo '<a class="button button-icon button-plain'.$cls.'" href="/admin-accomodation?show='.$p.'"><i class="nvicon nvicon-'.$d[1].'"></i> '.$d[0].'</a>';
		}
		?>
	</div>
</header>

<div class="contentwrap">

	
	<?php
	$page_file = get_template_directory() . "/dashboard/accomodation/page-$page.php";
	if ( file_exists( $page_file ) )
	{
		include $page_file;
	}
	else 
	{
		echo "wrong page";
		echo $page_file;
	}

	?>

</div>

<?php
get_footer();
?>