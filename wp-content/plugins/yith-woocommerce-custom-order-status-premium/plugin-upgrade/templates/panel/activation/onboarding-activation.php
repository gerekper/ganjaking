<?php
/**
 * This file belongs to the YIT Framework.
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @author YITH
 * @package YITH License & Upgrade Framework
 * @var string $assets_url Base plugin path.
 * @var array $product Base plugin path.
 * @var string $plugin Current processing plugin.
 * @var string $return_url Return url.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<!-- Required meta tags-->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Title Page-->
	<title><?php esc_html_e( 'YITH License Activation', 'yith-plugin-upgrade-fw' ); ?></title>
	<?php wp_print_head_scripts(); ?>
	<?php wp_print_styles( 'yith-plugin-fw-icon-font' ); ?>
	<?php wp_print_styles( 'yith-plugin-ui' ); ?>
	<?php wp_print_styles( 'yith-licence-onboarding-css' ); ?>
</head>
<body class="yith-plugin-licence-onboarding">
<header>
	<div class="logo"><img src="<?php echo esc_url( $assets_url . '/images/logo-yith.svg' ); ?>" width="127" height="82" alt="YITH"></div>
	<h1>
		<span><?php esc_html_e( 'Thank you for choosing to install', 'yith-plugin-upgrade-fw' ); ?></span>
		<b><?php echo esc_html( $product['Name'] ); ?></b>
	</h1>
</header>
<div id="wpwrap">
	<div id="content">
		<h2><?php esc_html_e( 'Activate your license now to', 'yith-plugin-upgrade-fw' ); ?>:</h2>
		<ul>
			<li>
				<?php echo wp_kses_post( __( 'Unblock <b>advanced features</b> available only with a valid license', 'yith-plugin-upgrade-fw' ) ); ?>
			</li>
			<li>
				<?php echo wp_kses_post( __( 'Get <b>automatic and regular updates</b> to remain compatible with the latest WordPress and WooCommerce versions, and to keep you shop safe', 'yith-plugin-upgrade-fw' ) ); ?>
			</li>
			<li>
				<?php echo wp_kses_post( __( 'Get <b>dedicated expert help</b> in our support desk', 'yith-plugin-upgrade-fw' ) ); ?>
			</li>
		</ul>
		<form id="activation-form" method="POST" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
			<p class="form-row">
				<label for="email"><?php echo esc_html_x( 'Email associated with order', 'Link on activation license panel', 'yith-plugin-upgrade-fw' ); ?>:</label>
				<span>
				<input type="text" id="email" name="email" value="">
			</span>
			</p>
			<p class="form-row">
				<label for="licence_key">
					<?php echo esc_html_x( 'License key', 'Link on activation license panel', 'yith-plugin-upgrade-fw' ); ?>:
					<a href="#" id="yith-license-where-find-these" class="small-link" tabindex="-1"><?php echo esc_html_x( 'Where to find your license key?', 'Link on activation license panel', 'yith-plugin-upgrade-fw' ); ?></a>
				</label>
				<span>
				<input type="text" id="licence_key" name="licence_key" value="">
			</span>
			</p>
			<p class="form-row submit">
				<input type="submit" value="<?php esc_attr_e( 'Activate license', 'yith-plugin-upgrade-fw' ); ?>">
			</p>
			<input type="hidden" name="action" value="yith_activate-plugin">
			<input type="hidden" name="product_init" value="<?php echo esc_attr( $plugin ); ?>"/>
		</form>
		<a href="<?php echo esc_url( $return_url ); ?>" class="small-link" tabindex="-1"><?php esc_html_e( 'Return to the dashboard and activate the license later', 'yith-plugin-upgrade-fw' ); ?></a>
		<div class="loading-icon"><div></div><div></div><div></div><div></div></div>
	</div>
</div>
<?php wp_print_scripts( 'wp-backbone' ); ?>
<?php wp_print_scripts( 'yith-ui' ); ?>
<?php wp_print_scripts( 'yit-license-utils' ); ?>
<?php wp_print_scripts( 'yith-licence-onboarding-js' ); ?>
<script type="text/template" id="tmpl-success-message">
	<div id="activation-success">
		<img src="<?php echo esc_url( $assets_url . '/images/alldone.svg' ); ?>" width="60" height="60" alt="">
		<p><?php esc_html_e( 'All done!', 'yith-plugin-upgrade-fw' ); ?></p>
		<p><?php esc_html_e( 'Thank you for activating the plugin license.', 'yith-plugin-upgrade-fw' ); ?></p>
		<a href="<?php echo esc_url( $return_url ); ?>" class="button"><?php esc_html_e( 'Go to plugin dashboard', 'yith-plugin-upgrade-fw' ); ?></a>
	</div>
</script>
</body>
</html>
