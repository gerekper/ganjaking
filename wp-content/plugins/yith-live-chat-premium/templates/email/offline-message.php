<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$body_css       = 'margin: 0; padding: 0; min-width: 100%!important; font-family: \'Open Sans\', sans-serif; color: #4d4d4d; ';
$content_css    = 'width: 100%; max-width: 600px; -webkit-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1); -moz-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1); box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1);';
$overheader_css = 'height: 20px';
$header_css     = 'padding: 10px; height: 30px; border-radius: 5px 5px 0 0; line-height: 30px; font-size: 16px; text-align: center; color: #ffffff;';
$mailbody_css   = 'padding: 50px 40px; font-size: 14px; color: #656565; line-height: 25px; border-width: 0 1px; border-color: #dfdfdf; border-style: solid;';
$user_info_css  = 'font-size:11px; font-style: italic;';
$footer_css     = 'padding: 10px; height: 30px; border-radius: 0 0 5px 5px; line-height: 30px; font-size: 13px; text-align: center; border-width: 0 1px 1px 1px; border-color: #dfdfdf; border-style: solid;';
$subfooter_css  = 'height: 20px';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo get_option( 'blogname' ); ?></title>
    <style type="text/css">
        @import url(http://fonts.googleapis.com/css?family=Open+Sans:400,700);
    </style>
</head>
<body style="<?php echo $body_css; ?>" bgcolor="#ffffff">
<table width="100%" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="<?php echo $overheader_css; ?>"></td>
    </tr>
    <tr>
        <td>
            <!--[if (gte mso 9)|(IE)]>
            <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td><![endif]-->
            <table style="<?php echo $content_css; ?>" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td bgcolor="#009edb" style="<?php echo $header_css; ?>">
                        [<?php echo get_option( 'blogname' ) ?>] <?php echo $subject; ?>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#ffffff" style="<?php echo $mailbody_css; ?>">
						<?php echo wpautop( $mail_body ); ?>
                        <br />
                        <strong>
							<?php esc_html_e( 'Name', 'yith-live-chat' ) ?>:
                        </strong>
						<?php echo $name; ?>
                        <br />
                        <strong>
							<?php esc_html_e( 'E-mail', 'yith-live-chat' ) ?>:
                        </strong>
                        <a href="mailto:<?php echo $email; ?>">
							<?php echo $email; ?>
                        </a>
                        <br />
                        <strong>
							<?php esc_html_e( 'Message', 'yith-live-chat' ) ?>:
                        </strong>
                        <br />
						<?php echo str_replace( "\n", '<br />', htmlspecialchars( stripslashes( $message ) ) ); ?>
                        <br />
                        <br />
                        <span style="<?php echo $user_info_css ?>">
                            <?php esc_html_e( 'User information', 'yith-live-chat' ) ?>: <?php echo $ip_address . ' - ' . $os . ', ' . $browser . ' ' . $version ?>
                            <br />
							<?php echo $page ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#ffffff" style="<?php echo $footer_css; ?>">
						<?php echo apply_filters( 'ylc_email_footer_text', date( 'Y' ) . ' YITH Live Chat' ) ?>
                    </td>
                </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
        </td>
    </tr>
    <tr>
        <td style="<?php echo $subfooter_css; ?>"></td>
    </tr>
</table>
</body>
</html>