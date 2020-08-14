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
    do_action( 'admin_print_styles' );
    do_action( 'admin_print_scripts' );
    do_action( 'admin_head' );
    ?>
    <style type="text/css">
        html, body {
            background: #fff;
            margin: 0;
            padding: 0;
        }
        /* Menu Styles */

        .second-level-menu
        {
            position: absolute;
            top: 0;
            left: 150px;
            width: 150px;
            list-style: none;
            padding: 0;
            margin: 0;
            display: none;
        }

        .top-level-menu
        {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .top-level-menu > li,  .second-level-menu > li
        {
            position: relative;
            height: 49px;
            width: 150px;
            padding: ;
            background: #f1f1f1;
        }
        .top-level-menu > li:hover,  .second-level-menu > li:hover  { background: #999999; }

        .top-level-menu li:hover > ul
        {
            /* On hover, display the next level's menu */
            display: inline;
        }


        /* Menu Link Styles */

        .top-level-menu a /* Apply to all links inside the multi-level menu */
        {
            font: bold 14px Arial, Helvetica, sans-serif;
            color: #5e5e5e;
            text-decoration: none;
            padding: 0 0 0 10px;

            /* Make the link cover the entire list item-container */
            display: block;
            line-height: 49px;
        }
        .top-level-menu a:hover { color: #fff; }
    </style>
</head>
<body>
<?php
$pending_surveys = YITH_Pending_Order_Survey_Type()->get_pending_survey();
$placeholders = array(
'firstname'      => __( 'User\' first Name', 'yith-woocommerce-pending-order-survey' ),
'lastname'       => __( 'User\' last Name', 'yith-woocommerce-pending-order-survey' ),
'fullname'       => __( 'Full name', 'yith-woocommerce-pending-order-survey' ),
'useremail'      => __( 'User \'s email address', 'yith-woocommerce-pending-order-survey' ),
'cartcontent'    => __( 'Cart content', 'yith-woocommerce-pending-order-survey' ),
'cartlink'       => __( 'Cart link', 'yith-woocommerce-pending-order-survey' ),
'coupon'         => __( 'Coupon', 'yith-woocommerce-pending-order-survey' ),
);?>
<div id="yith_pending_survey_lightbox_content">

    <ul class="top-level-menu">
        <?php foreach( $placeholders as $key=>$placeholder ): ?>
            <li class="element"><input type="hidden" value="<?php esc_attr_e( $key );?>"><a href="#"><?php echo( $placeholder );?></a></li>
         <?php endforeach;?>
        <li class="pending_surveys">
            <a href="#"><?php _e( 'Pending Survey', 'yith-woocommerce-pending-order-survey' );?></a>
            <ul class="second-level-menu">
                <?php foreach( $pending_surveys as $survey ):
                        $title = get_the_title( $survey );?>
                <li class="element"><input type="hidden" value="<?php esc_attr_e( $survey );?>"><a href="#"><?php echo $title;?></a></li>
                <?php endforeach;?>
            </ul>
        </li>
    </ul>
</div>
<script type="text/javascript">
    jQuery(document).on('click', '.top-level-menu > li.element', function (e) {
        e.stopImmediatePropagation();

        var how_show            =   jQuery( this),
            hidden = how_show.find('input[type="hidden"]:eq(0)');

       if( how_show.hasClass('pending_surveys') )
        {
            alert("Select a survey");
            return;
        }

        var    str = '{ywcpos_'+hidden.val()+'}',
                win = window.dialogArguments || opener || parent || top;

        win.send_to_editor(str);
        var ed;
        if (typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden()) {
            ed.setContent(ed.getContent());
        }


    });
    jQuery(document).on('click', '.top-level-menu li.pending_surveys ul li.element', function (e) {

        e.stopImmediatePropagation();
        var how_show            =   jQuery( this),
            hidden = how_show.find('input[type="hidden"]:eq(0)');

        if( how_show.hasClass('pending_surveys') )
        {
            alert("Select a survey");
            return;
        }

        var    str = '{ywcpos_pendingsurveys id="'+hidden.val()+'"}',
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