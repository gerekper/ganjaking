<?php
/**
 * Woocommerce Compare page
 *
 * @author Your Inspiration Themes
 * @package YITH Woocommerce Compare
 * @version 1.1.4
 */

// remove the style of woocommerce
if( defined('WOOCOMMERCE_USE_CSS') && WOOCOMMERCE_USE_CSS ) wp_dequeue_style('woocommerce_frontend_styles');

// removes scripts for massive-dynamic theme
remove_action('wp_enqueue_scripts', 'pixflow_theme_scripts');
    
wp_enqueue_script( 'jquery-fixedheadertable', YITH_WOOCOMPARE_ASSETS_URL . '/js/jquery.dataTables.min.js', array('jquery'), '1.10.18', true );
wp_enqueue_script( 'jquery-fixedcolumns', YITH_WOOCOMPARE_ASSETS_URL . '/js/FixedColumns.min.js', array('jquery', 'jquery-fixedheadertable' ), '3.2.6', true );
wp_enqueue_script( 'yith_woocompare_owl', YITH_WOOCOMPARE_ASSETS_URL . '/js/owl.carousel.min.js', array( 'jquery' ), '2.0.0', true );
wp_enqueue_script( 'jquery-imagesloaded', YITH_WOOCOMPARE_ASSETS_URL . '/js/imagesloaded.pkgd.min.js', array('jquery'), '3.1.8', true );

/** FIX WOO 2.1 */
$wc_get_template = function_exists('wc_get_template') ? 'wc_get_template' : 'woocommerce_get_template';

$table_text = get_option( 'yith_woocompare_table_text', __( 'Compare products', 'yith-woocommerce-compare' ) );

?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" class="ie"<?php language_attributes() ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" class="ie"<?php language_attributes() ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" class="ie"<?php language_attributes() ?>>
<![endif]-->
<!--[if IE 9]>
<html id="ie9" class="ie"<?php language_attributes() ?>>
<![endif]-->
<!--[if gt IE 9]>
<html class="ie"<?php language_attributes() ?>>
<![endif]-->
<!--[if !IE]>
<html <?php language_attributes() ?>>
<![endif]-->

<!-- START HEAD -->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width" />
    <title><?php esc_html_e( 'Product Comparison', 'yith-woocommerce-compare' ) ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11" />

    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" />

    <?php wp_head() ?>

    <?php do_action( 'yith_woocompare_popup_head' ) ?>

    <link rel="stylesheet" href="<?php echo YITH_WOOCOMPARE_ASSETS_URL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>/css/colorbox.css"/>
    <link rel="stylesheet" href="<?php echo YITH_WOOCOMPARE_ASSETS_URL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>/css/jquery.dataTables.css"/>
    <link rel="stylesheet" href="<?php echo YITH_WOOCOMPARE_ASSETS_URL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>/css/owl.carousel.css"/>
    <link rel="stylesheet" href="<?php echo $this->stylesheet_url(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" type="text/css" />

    <style type="text/css">
        body.loading {
            background: url("<?php echo YITH_WOOCOMPARE_URL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>assets/images/colorbox/loading.gif") no-repeat scroll center center transparent;
        }
    </style>
</head>
<!-- END HEAD -->

<?php global $product; ?>

<!-- START BODY -->
<body <?php body_class('woocommerce yith-woocompare-popup') ?>>

<h1>
    <?php echo wp_kses_post( $table_text ) ?>
</h1>

<?php $wc_get_template( 'yith-compare-table.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH . '/' ); ?>

<?php do_action( 'yith_woocompare_popup_footer' ) ?>

<?php do_action( 'wp_print_footer_scripts' ); ?>

<script type="text/javascript">

    jQuery(document).ready(function($){

        $('a').attr('target', '_parent');

	    var body = $('body'),
            redirect_to_cart = false;

        // close colorbox if redirect to cart is active after add to cart
        body.on( 'adding_to_cart', function ( $thisbutton, data ) {
            if( wc_add_to_cart_params.cart_redirect_after_add == 'yes' ) {
                wc_add_to_cart_params.cart_redirect_after_add = 'no';
                redirect_to_cart = true;
            }
        });

        // remove add to cart button after added
	    body.on('added_to_cart', function( ev, fragments, cart_hash, button ){

            if( redirect_to_cart == true ) {
                // redirect
                parent.window.location = wc_add_to_cart_params.cart_url;
                return;
            }

            $('a').attr('target', '_parent');

            // Replace fragments
            if ( fragments ) {
                $.each(fragments, function(key, value) {
                    $(key, window.parent.document).replaceWith(value);
                });
            }
        });
    });

</script>

</body>
</html>