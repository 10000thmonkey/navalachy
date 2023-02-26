<?php

nv_ajax(
	"blog/feed",
	function ()
	{
		return nv_c( "blog/c/feed" );
	}
);