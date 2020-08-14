<?php
/**
 * Add new field for contact customize panel.
 *
 * Page for adding new field to contact module.
 *
 * @package    Wordpress
 * @subpackage Kassyopea
 * @since      1.1
 */

if ( !defined( 'IFRAME_REQUEST' ) ) {
    define( 'IFRAME_REQUEST', true );
}

$wp_load = dirname( dirname( __FILE__ ) );


global $YWC_Product_Slider;


for ( $i = 0; $i < 10; $i ++ ) {
    if ( file_exists( $wp_load . '/wp-load.php' ) ) {
        
        require_once "$wp_load/wp-load.php";
        break;
    }
    else {
        $wp_load = dirname( $wp_load );
    }

}

@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );

?>
<html <?php if ( yit_ie_version() < 9 && yit_ie_version() > 0 ) {
    echo 'class="ie8"';
} ?>xmlns="http://www.w3.org/1999/xhtml" <?php do_action( 'admin_xml_ns' ); ?> <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
    <title><?php _e( "Add shortcode", 'yit' ) ?></title>
    <?php if ( isset( $sitepress ) ) : ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
    <?php endif; ?>
    <?php
    wp_admin_css( 'wp-admin', true );

    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    wp_print_scripts( 'jquery' );
    remove_action('admin_print_styles', array( 'WC_Name_Your_Price_Admin', 'add_help_tab' ), 20 );
    $slider = $YWC_Product_Slider->get_productslider();

    do_action( 'admin_print_styles' );
    do_action( 'admin_print_scripts' );
    do_action( 'admin_head' );
    ?>
    <style type="text/css">
        html, body {
            background: #fff;
        }

        .button {
            background: #00a0d2;
            border-color: #0073aa;
            -webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, .5), 0 1px 0 rgba(0, 0, 0, .15);
            box-shadow: inset 0 1px 0 rgba(120, 200, 230, .5), 0 1px 0 rgba(0, 0, 0, .15);
            color: #fff;
            text-decoration: none;
            display: inline-block;
            font-size: 13px;
            line-height: 26px;
            height: 28px;
            margin: 0;
            padding: 0 10px 1px;
            cursor: pointer;
            border-width: 1px;
            border-style: solid;
            -webkit-appearance: none;
            -webkit-border-radius: 3px;
            border-radius: 3px;
            white-space: nowrap;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            font-family: inherit;
            font-weight: inherit;
        }

        .button:focus {
            border-color: #0e3950;
            -webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6), 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, .8);
            box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6), 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, .8);
        }

        .button:hover {
            background: #0091cd;
            border-color: #0073aa;
            -webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6);
            box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6);
            color: #fff;
        }

        #ywcps_lightbox_content{
            margin: 20px;
        }

    </style>
</head>
<body>

<div id="ywcps_lightbox_content">

    <p class="ywcps_select_field">
        <label for="ywcps_slider"><?php _e( 'Select a Slider', 'yith-woocommerce-product-slider-carousel');?></label>
        <select id="ywcps_slider" class="widefat">
            <option value="" selected><?php _e( 'Select a Slider', 'yith-woocommerce-product-slider-carousel' );?></option>
            <?php
                foreach( $slider as $key=>$value ){

                    echo '<option value="'.esc_attr( $value['value'] ).'" >'.esc_attr( $value['text'] ).'</option>';
                }
            ?>
        </select>
    </p>
    <p class="ywcps_z_index_content">
        <label for="ywcps_z_index"><?php _e( 'z-index', 'yith-woocommerce-product-slider-carousel' );?></label>
        <input type="text" id="ywcps_z_index">
    </p>
</div>
<div class="widget-control-actions">
    <div class="alignright" style="margin-right: 10px;">
        <input type="submit" name="ywcps_shortcode_insert" id="ywcps_shortcode_insert" class="button" value="<?php _e( 'Insert', 'yith-woocommerce-product-slider-carousel' ); ?>">
    </div>
    <br class="clear">
</div>
<script type="text/javascript">

    jQuery(document).on('click', '#ywcps_shortcode_insert', function () {

        var how_show            =   jQuery('#ywcps_slider').val(),
            index               =   jQuery('#ywcps_z_index').val();

        if(how_show=='')
        {
            alert("Select a slider");
            return;
        }

        var    str = '[yith_wc_productslider id="'+how_show+'" z_index="'+index+'"]',
            win = window.dialogArguments || opener || parent || top;

        win.send_to_editor(str);
        var ed;
        if (typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden()) {
            ed.setContent(ed.getContent());
        }

    });
</script>
</body>
</html>