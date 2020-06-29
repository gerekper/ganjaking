<?php

$position_css = "";

$position_data      = array( 'top' => $pos_top, 'bottom' => $pos_bottom, 'left' => $pos_left, 'right' => $pos_right );
$position_data_json = htmlspecialchars( wp_json_encode( $position_data ) );
$position_data_html = " data-position='$position_data_json'";

$pos_top    = ( is_numeric( $pos_top ) ) ? ( $pos_top . "px" ) : $pos_top;
$pos_bottom = ( is_numeric( $pos_bottom ) ) ? ( $pos_bottom . "px" ) : $pos_bottom;
$pos_left   = ( is_numeric( $pos_left ) ) ? ( $pos_left . "px" ) : $pos_left;
$pos_right  = ( is_numeric( $pos_right ) ) ? ( $pos_right . "px" ) : $pos_right;

$position_css .= "top: " . $pos_top . ";";
$position_css .= "bottom: " . $pos_bottom . ";";
$position_css .= "left: " . $pos_left . ";";
$position_css .= "right: " . $pos_right . ";";

//--wpml-------------
$text     = yith_wcbm_wpml_string_translate( 'yith-woocommerce-badges-management', sanitize_title( $text ), $text );
$css_text = yith_wcbm_wpml_string_translate( 'yith-woocommerce-badges-management', sanitize_title( $css_text ), $css_text );
//-------------------

$text     = apply_filters( 'yith_wcbm_text_badge_text', $text, $args );
$css_text = apply_filters( 'yith_wcbm_css_badge_text', $css_text, $args );

$badge_classes = "yith-wcbm-badge yith-wcbm-badge-{$id_badge} yith-wcbm-badge--on-product-{$product_id} yith-wcbm-badge--anchor-point-{$position}";

switch ( $type ) {
    case 'text':
    case 'custom':
        ?>
        <div class='<?php echo $badge_classes ?> yith-wcbm-badge-custom' <?php echo $position_data_html ?>>
            <div class='yith-wcbm-badge__wrap'>
                <div class="yith-wcbm-badge-text"><?php echo $text ?></div>
            </div><!--yith-wcbm-badge__wrap-->
        </div><!--yith-wcbm-badge-->
        <?php
        break;

    case 'image':
        //if the badge was created by free version
        if ( strlen( $image_url ) < 6 ) {
            $image_url = YITH_WCBM_ASSETS_URL . '/images/image-badge/' . $image_url;
        }
        $image_url  = str_replace( 'http://', '//', $image_url );
        $image_url  = str_replace( 'https://', '//', $image_url );
        $image_url  = apply_filters( 'yith_wcbm_image_badge_url', $image_url, $args );
        $image_html = '<img src="' . $image_url . '" alt="" />';
        ?>
        <div class='<?php echo $badge_classes ?> yith-wcbm-badge-image' <?php echo $position_data_html ?>>
            <div class='yith-wcbm-badge__wrap'>
                <?php echo $image_html ?>
            </div><!--yith-wcbm-badge__wrap-->
        </div><!--yith-wcbm-badge-->
        <?php
        break;

    case 'css':
        $css_badge = isset( $css_badge ) ? $css_badge : 'css';
        ?>
        <div class="<?php echo $badge_classes ?> yith-wcbm-badge-css yith-wcbm-css-badge-<?php echo $id_badge ?>" <?php echo $position_data_html ?>>
            <div class='yith-wcbm-badge__wrap'>
                <div class="yith-wcbm-css-s1"></div>
                <div class="yith-wcbm-css-s2"></div>
                <div class="yith-wcbm-css-text">
                    <div class="yith-wcbm-badge-text"><?php echo $css_text ?></div>
                </div>
            </div><!--yith-wcbm-badge__wrap-->
        </div>
        <?php
        break;
    case 'advanced':
        $product            = wc_get_product( $product_id );
        $product_is_on_sale = yith_wcbm_product_is_on_sale( $product );

        if ( ( $product && $product_is_on_sale ) || 'preview' === $product_id ) {
            $id_advanced_badge = $id_badge;
            include( YITH_WCBM_TEMPLATE_PATH . '/advanced_sale_badges.php' );
        }
        break;
}


?>


