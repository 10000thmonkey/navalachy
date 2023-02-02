<?php

switch ( get_post_type() )
{
	case "accomodation":
		include "Accomodation/archive.php";
		break;
	case "tipy":
		include "Experiences/archive.php";
		break;
}
echo get_post_type();