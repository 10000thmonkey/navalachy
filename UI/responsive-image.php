<?php

nv_new_c (
    "UI/responsive-image",
    function ( $_VAR )
    {
        $VAR = array_merge( [
            "sizes" => "(max-width: 600px) 100vw, 25vw",
            "alt" => "",
            "attachment_id" => 1
        ], $_VAR );

        $src = wp_get_attachment_image_url( $VAR["attachment_id"], "medium");
        $srcfull = wp_get_attachment_image_url( $VAR["attachment_id"], "full");
        $srcset = wp_get_attachment_image_srcset( $VAR["attachment_id"], "large" );
        $attalt = get_post_meta( $VAR["attachment_id"], '_wp_attachment_image_alt', true);
        if ($attalt != "") $VAR['alt'] = $attalt;

        return '<img src="'.esc_attr( $src ).'"
                srcset="'.esc_attr( $srcset ).'"
                sizes="'.esc_attr( $VAR["sizes"] ).'"
                alt="'.esc_attr( $VAR['alt'] ).'"
                loading="lazy"/>';
    }
);