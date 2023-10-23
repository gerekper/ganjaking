<?php
/**
 * Custom Template header
 *
 * @package YITH\ReviewReminder
 * @var $email_heading
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$assets_url = untrailingslashit( YWRR_ASSETS_URL );

// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?></title>
	<link href="<?php echo esc_url( YWRR_ASSETS_URL ); ?>/fonts/raleway/style.css" rel="stylesheet">
	<style type="text/css">
		@media only screen and (max-width: 599px) {
			#header {
				height: 197px !important;
				line-height: 26px !important;
				font-size: 18px !important;
			}

			#header img {
				margin: 20px auto 30px auto !important;
				height: 40px !important;
			}

			.items {
				height: auto !important;
				text-align: center !important;
			}

			.items > img {
				float: none !important;
				margin: 0 auto 20px auto !important;
			}
		}
	</style>
</head>

<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<!--[if (gte mso 9)|(IE)]>
			<table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td><![endif]-->
			<table id="content_table" align="center" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td id="overheader">
					</td>
				</tr>
				<tr>
					<td id="header" valign="top">
						<img src="<?php echo esc_url( $assets_url ); ?>/images/stars-icon.png" alt="" />
						<?php echo esc_html( $email_heading ); ?>
					</td>
				</tr>
				<tr>
					<td id="mailbody">
