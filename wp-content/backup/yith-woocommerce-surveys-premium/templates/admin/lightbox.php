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
    remove_action( 'admin_print_scripts', 'ultimate_tabs_admin',999 );

    $surveys = YITH_Surveys_Type()->get_other_surveys();

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

    </style>
</head>
<body>

<div id="yith_survey_lightbox_content">

    <p class="yith_survey_select_field">
        <label for="yith_survey_slider"><?php _e( 'Select a Survey', 'yith-woocommerce-surveys');?></label>
        <select id="yith_survey_slider" class="widefat">
            <option value="" selected><?php _e( 'Select a Survey', 'yith-woocommerce-surveys' );?></option>
            <?php
                foreach( $surveys as $survey ){

                    $title = get_the_title( $survey );
                    echo '<option value="'.esc_attr( $survey ).'" >'.esc_attr( $title ).'</option>';
                }
            ?>
        </select>
    </p>
</div>
<div class="widget-control-actions">
    <div class="alignright" style="margin-right: 10px;">
        <input type="submit" name="survey_shortcode_insert" id="survey_shortcode_insert" class="button" value="<?php _e( 'Insert', 'yith-woocommerce-surveys' ); ?>">
    </div>
    <br class="clear">
</div>
<script type="text/javascript">
    jQuery(document).on('click', '#survey_shortcode_insert', function () {

        var how_show            =   jQuery('#yith_survey_slider').val();


        if(how_show=='')
        {
            alert("Select a survey");
            return;
        }

        var    str = '[yith_wc_surveys survey_id="'+how_show+'"]',
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