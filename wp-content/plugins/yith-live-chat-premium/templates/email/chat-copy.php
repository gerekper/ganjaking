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

$body_css            = 'margin: 0; padding: 0; min-width: 100%!important; font-family: \'Open Sans\', sans-serif; color: #4d4d4d;';
$content_css         = 'width: 100%; max-width: 600px; -webkit-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1); -moz-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1); box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1);';
$overheader_css      = 'height: 20px';
$header_css          = 'padding: 10px; height: 30px; border-radius: 5px 5px 0 0; line-height: 30px; font-size: 16px; text-align: center; color: #ffffff;';
$mailbody_css        = 'padding: 50px 40px; font-size: 14px; color: #656565; line-height: 25px; border-width: 0 1px; border-color: #dfdfdf; border-style: solid;';
$mailbody_p_css      = 'line-height: normal; margin: 7px 0; background: #e5f6fd; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding: 5px 10px; ';
$mailbody_p_op_css   = 'line-height: normal; margin: 7px 0; background: #f2f2f2; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding: 5px 10px; ';
$mailbody_p_date_css = 'display:block; font-size: 11px; font-style: italic;';
$footer_css          = 'padding: 10px; height: 30px; border-radius: 0 0 5px 5px; line-height: 30px; font-size: 13px; text-align: center; border-width: 0 1px 1px 1px; border-color: #dfdfdf; border-style: solid;';
$subfooter_css       = 'height: 20px';

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
						<?php echo $mail_body; ?>
						<br />
						<br />
						<?php if ( $chat_data != array() ) : ?>
							<strong>
								<?php esc_html_e( 'User Name', 'yith-live-chat' ) ?>:
							</strong>
							<?php echo $chat_data['user_name']; ?>
							<br />
							<strong>
								<?php esc_html_e( 'User e-mail', 'yith-live-chat' ) ?>:
							</strong>
							<?php echo $chat_data['user_email']; ?>
							<br />
							<strong>
								<?php esc_html_e( 'IP Address', 'yith-live-chat' ) ?>:
							</strong>
							<?php echo $chat_data['user_ip']; ?>
							<br />
							<br />
							<strong>
								<?php esc_html_e( 'Operator Name', 'yith-live-chat' ) ?>:
							</strong>
							<?php echo $chat_data['operator']; ?>
							<br />
							<strong>
								<?php esc_html_e( 'Chat Duration', 'yith-live-chat' ) ?>:
							</strong>
							<?php echo $chat_data['duration']; ?>
							<br />
							<strong>
								<?php esc_html_e( 'Chat closed by', 'yith-live-chat' ) ?>:
							</strong>
							<?php echo $chat_data['closed_by']; ?>
							<br />
							<strong>
								<?php esc_html_e( 'Chat Evaluation', 'yith-live-chat' ) ?>:
							</strong>
							<?php echo $chat_data['evaluation']; ?>
							<br />
							<br />
						<?php endif; ?>
						<?php $chat_logs = ylc_get_chat_conversation( $cnv_id ); ?>
						<?php foreach ( $chat_logs as $log ): ?>
							<p style="<?php echo( ( $log['user_type'] == 'operator' ) ? $mailbody_p_op_css : $mailbody_p_css ); ?>">
                                <span style="<?php echo $mailbody_p_date_css; ?>">
                                    <?php echo ylc_convert_timestamp( $log['msg_time'] ); ?>
                                </span>
                                <span>
                                    <strong><?php echo $log['user_name']; ?>: </strong>
	                                <?php echo stripslashes( $log['msg'] ); ?>
                                </span>
							</p>
						<?php endforeach; ?>
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
