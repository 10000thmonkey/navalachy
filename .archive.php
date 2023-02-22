<?php

switch ( get_post_type() )
{
	case "accomodation":
		include "accomodation/archive/template.php";
		break;
	case "tipy":
		include "experiences/archive/template.php";
		break;
}
echo get_post_type();