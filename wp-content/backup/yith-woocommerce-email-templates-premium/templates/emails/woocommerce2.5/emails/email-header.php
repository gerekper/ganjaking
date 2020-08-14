<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-header.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see        http://docs.woothemes.com/document/template-structure/
 * @author     WooThemes
 * @package    WooCommerce/Templates/Emails
 * @version    4.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/**
 * @var WC_Email $current_email
 */
global $current_email;
$template = yith_wcet_get_email_template( $current_email );
$meta     = yith_wcet_get_template_meta( $template );

$custom_links_array = ( !empty( $meta[ 'custom_links' ] ) ) ? $meta[ 'custom_links' ] : array();
$socials_on_header  = ( isset( $meta[ 'socials_on_header' ] ) ) ? $meta[ 'socials_on_header' ] : 0;

$socials_color = ( isset( $meta[ 'socials_color' ] ) ) ? '-' . $meta[ 'socials_color' ] : '-black';

$page_width = ( isset( $meta[ 'page_width' ] ) ) ? $meta[ 'page_width' ] . 'px' : '800px';

$logo_url    = ( isset( $meta[ 'logo_url' ] ) ) ? $meta[ 'logo_url' ] : '';
$logo_url    = apply_filters( 'yith_wcet_header_logo_url', $logo_url, $current_email );
$logo_height = ( isset( $meta[ 'logo_height' ] ) ) ? $meta[ 'logo_height' ] : '100'; // height is set without 'px' to prevent issues on Outlook

$use_mini_social_icons = get_option( 'yith-wcet-use-mini-social-icons', 'no' ) == 'yes';
$social_icon_path      = YITH_WCET_ASSETS_URL . '/images/socials-icons';
$social_icon_path      .= $use_mini_social_icons ? '-mini/' : '/';

$social_icon_path = apply_filters( 'yith_wcet_social_icon_path', $social_icon_path, $use_mini_social_icons, $current_email, 'header' );

$custom_logo = apply_filters( 'yith_wcet_header_custom_logo', false, $template );


$social_icons            = yith_wcet_get_socials();
$at_least_one_social_set = !!array_filter( $social_icons );

$outlook_style = "<!--[if gte mso 9]>";
$outlook_style .= "<yith-wccet-style type='text/css'>";
$outlook_style .= "#template_container {width: {$page_width} !important}";
$outlook_style .= "</yith-wccet-style>";
$outlook_style .= "<![endif]-->";
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo get_bloginfo( 'name', 'display' ); ?></title>

    <?php do_action( 'yith_wcet_header_after_head', $current_email, $template ); ?>

    <yith-wccet-style type="text/css">
        @media (min-width:500px){
        #template_container {
        width : 100%;
        }
        }

        .pre-header {
        visibility : hidden !important;
        opacity : 0 !important;
        font-size : 0 !important;
        color : transparent !important;
        height : 0 !important;
        width : 0 !important;
        }

        <?php do_action( 'yith_wcet_header_after_style', $current_email, $template ); ?>

    </yith-wccet-style>

    <?php echo $outlook_style; ?>

</head>
<?php do_action( 'yith_wcet_header_before_body', $current_email, $template ); ?>

<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="
0">
<?php do_action( 'yith_wcet_pre_header', $current_email, $template ); ?>

<div id="wrapper" class="yith-wcet-woocommerce-email-wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr' ?>">
    <table id="table_wrapper" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top">
                <div id="wc_template_header_image">
                    <?php
                    if ( $img = get_option( 'woocommerce_email_header_image' ) ) {
                        echo '<p style="margin-top:0;"><img src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name', 'display' ) . '" /></p>';
                    }
                    ?>
                </div>
                <table border="0" cellpadding="0" cellspacing="0" id="template_container" class="yith-wcet-max-width-mobile">
                    <?php
                    if ( false !== $custom_logo ): ?>
                        <tr>
                            <td align="center" valign="top">
                                <!-- Header -->
                                <table id="template_header_image" class="yith-wcet-max-width-mobile" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <?php echo $custom_logo; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php elseif ( strlen( $logo_url ) > 0 ) : ?>
                        <tr>
                            <td align="center" valign="top">
                                <!-- Header -->
                                <table id="template_header_image" class="yith-wcet-max-width-mobile" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <?php echo '<img src="' . esc_url( $logo_url ) . '" height="' . $logo_height . '" alt="' . get_bloginfo( 'name', 'display' ) . '" />'; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Header -->
                            <table class="yith-wcet-max-width-mobile" order="0" cellpadding="0" cellspacing="0" id="template_header">
                                <tr>
                                    <td class="yith-wcet-max-width-mobile" id="header_wrapper">
                                        <h1><?php
                                            /**
                                             * @var string $email_heading
                                             */
                                            echo $email_heading; ?>
                                        </h1>
                                    </td>
                                </tr>
                            </table>
                            <!-- End Header -->
                        </td>
                    </tr>
                    <?php if ( count( $custom_links_array ) > 0 || ( $socials_on_header && $at_least_one_social_set ) ) { ?>

                        <tr>
                            <td align="center" valign="top">
                                <!-- Custom Links -->
                                <table class="yith-wcet-max-width-mobile" border="0" cellpadding="0" cellspacing="0" id="template_custom_links">
                                    <tr>
                                        <td>
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding:4px 0">
                                                <tr>
                                                    <td width="100%">
                                                        <table border="0" cellpadding="0" cellspacing="0" id="custom_links">
                                                            <tr>
                                                                <?php foreach ( $custom_links_array as $cl ) {
                                                                    echo '<td style="padding:5px 5px 5px 0"><a href="' . $cl[ 'url' ] . '">' . $cl[ 'text' ] . '</a></td>';
                                                                }
                                                                ?>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <?php if ( $socials_on_header ) { ?>
                                                        <?php foreach ( $social_icons as $social_name => $social_link ) : ?>
                                                            <?php if ( strlen( $social_link ) > 0 ) {
                                                                $social_url = $social_icon_path . $social_name . $socials_color . '.png';
                                                                if ( $custom_social_url = get_option( 'yith-wcet-' . $social_name . '-icon', '' ) ) {
                                                                    $social_url = $custom_social_url;
                                                                }
                                                                ?>
                                                                <td width="32px" style="padding:1px" class="yith-wcet-socials-icons" style="text-align:center;">
                                                                    <a href="<?php echo $social_link ?>"><img width="30" height="30" src="<?php echo $social_url ?>" alt="<?php echo esc_attr( $social_name ); ?>"></a>
                                                                </td>
                                                            <?php } ?>
                                                        <?php endforeach; ?>
                                                    <?php } ?>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!-- End Custom Links -->
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Body -->
                            <table class="yith-wcet-max-width-mobile" border="0" cellpadding="0" cellspacing="0" id="template_body">
                                <tr>
                                    <td valign="top" id="body_content">
                                        <!-- Content -->
                                        <table class="yith-wcet-max-width-mobile" border="0" cellpadding="20" cellspacing="0" width="100%">
                                            <tr>
                                                <td valign="top">
                                                    <div id="body_content_inner">
