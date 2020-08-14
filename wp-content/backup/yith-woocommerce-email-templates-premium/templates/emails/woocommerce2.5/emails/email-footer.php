<?php
/**
 * Email Footer
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-footer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see           http://docs.woothemes.com/document/template-structure/
 * @author        WooThemes
 * @package       WooCommerce/Templates/Emails
 * @version       3.7.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/**
 * @var WC_Email $current_email
 */
global $current_email;
$template = yith_wcet_get_email_template( $current_email );
$meta = yith_wcet_get_template_meta( $template );

$socials_on_footer = ( isset( $meta[ 'socials_on_footer' ] ) ) ? $meta[ 'socials_on_footer' ] : 0;

$socials_color = ( isset( $meta[ 'socials_color' ] ) ) ? '-' . $meta[ 'socials_color' ] : '-black';

$footer_logo_url = ( isset( $meta[ 'footer_logo_url' ] ) ) ? $meta[ 'footer_logo_url' ] : '';
$footer_logo_url = apply_filters( 'yith_wcet_footer_logo_url', $footer_logo_url, $current_email );

$footer_text = isset( $meta[ 'footer_text' ] ) ? $meta[ 'footer_text' ] : '';
$footer_text = apply_filters( 'yith_wcet_footer_text', $footer_text, $current_email );

$use_mini_social_icons = get_option( 'yith-wcet-use-mini-social-icons', 'no' ) == 'yes';
$social_icon_path      = YITH_WCET_ASSETS_URL . '/images/socials-icons';
$social_icon_path      .= $use_mini_social_icons ? '-mini/' : '/';
$social_icon_path      = apply_filters( 'yith_wcet_social_icon_path', $social_icon_path, $use_mini_social_icons, $current_email, 'footer' );

$social_icons            = yith_wcet_get_socials();
$at_least_one_social_set = !!array_filter( $social_icons );

$premium_mail_style = ( !empty( $meta[ 'premium_mail_style' ] ) ) ? $meta[ 'premium_mail_style' ] : 0;

$footer_social_centered = apply_filters( 'yith_wcet_footer_social_centered', $premium_mail_style != 2, $premium_mail_style, $template );
?>
</div>
</td>
</tr>
</table>
<!-- End Content -->
</td>
</tr>
</table>
<!-- End Body -->
</td>
</tr>
<tr>
    <td align="center" valign="top">
        <!-- Footer -->
        <table class="yith-wcet-max-width-mobile" border="0" cellpadding="10" cellspacing="0" id="template_footer">
            <tr>
                <td valign="top">
                    <table class="yith-wcet-max-width-mobile" border="0" cellpadding="10" cellspacing="0" width="100%">
                        <?php if ( strlen( $footer_logo_url ) > 0 || strlen( $footer_text ) > 0 ) : ?>
                            <tr>
                                <?php if ( strlen( $footer_logo_url ) > 0 ) : ?>
                                    <td>
                                        <img height="70px" src=" <?php echo esc_url( $footer_logo_url ) ?>" alt=" <?php echo get_bloginfo( 'name', 'display' ) ?>"/>
                                    </td>
                                <?php endif; ?>
                                <td colspan="2" valign="middle" id="template_footer_text">
                                    <?php
                                    echo call_user_func( '__', $footer_text, 'yith-woocommerce-email-templates' );
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if ( !empty( $args ) ) : ?>
                            <tr>
                                <td colspan="3" valign="middle" id="template_footer_extra_text">
                                    <?php
                                    foreach ( $args as $arg ) {

                                        echo $arg;

                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </td>
            </tr>
        </table>
        <!-- End Footer -->
    </td>
</tr>
<?php if ( $socials_on_footer && $at_least_one_social_set ) : ?>
    <tr>
        <td id="template_footer_social" align="center" valign="middle">

            <table class="yith-wcet-max-width-mobile" border="0" cellpadding="0" cellspacing="5px" width="100%">
                <tr>
                    <?php if ( $footer_social_centered ) : ?>
                        <td width="50%"></td>
                    <?php else: ?>
                        <td width="100%"></td>
                    <?php endif ?>
                    <?php foreach ( $social_icons as $social_name => $social_link ) : ?>
                        <?php if ( strlen( $social_link ) > 0 ) {
                            $social_url = $social_icon_path . $social_name . $socials_color . '.png';
                            if ( $custom_social_url = get_option( 'yith-wcet-' . $social_name . '-icon', '' ) ) {
                                $social_url = $custom_social_url;
                            }
                            ?>
                            <td width="32px" class="yith-wcet-socials-icons" style="text-align:center; width:32px !important">
                                <a href="<?php echo $social_link ?>"><img width="30" height="30" src="<?php echo $social_url ?>" alt="<?php echo esc_attr( $social_name ); ?>"></a>
                            </td>
                        <?php } ?>
                    <?php endforeach; ?>
                    <?php if ( $footer_social_centered ) : ?>
                        <td width="50%"></td>
                    <?php endif ?>
                </tr>
            </table>
        </td>
    </tr>
<?php endif; ?>
<tr>
    <td class="yith-wcet-max-width-mobile" id="template_footer_wc_credits" align="center" valign="middle">
        <?php echo wpautop( wp_kses_post( wptexturize( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ) ); ?>
    </td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>
