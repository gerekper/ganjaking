<?php
/**
 * Admin View: Page - About
 *
 * @package weLaunch Framework
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="wrap about-wrap">
	<div class="error hide">
		<p>weLaunch.io is running from within one of your products. To keep your site safe, please install the weLaunch
			Framework
			plugin from WordPress.org.</p>
	</div>
	<h1><?php printf( esc_html__( 'Welcome to', 'welaunch-framework' ) . ' weLaunch Framework', esc_html( $this->display_version ) ); ?></h1>


	<div class="about-text">
		<?php esc_html_e( "This framworks adds the possiblity to manage your licenses & adds the option to create admin panels.", 'welaunch-framework' ); ?>
		<p>Enter your license / purchase code from CodeCanyon here to receive auto updates. <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Where is my purchase code?</a></p>
	</div>

	<form action="<?php echo esc_url($_SERVER['REQUEST_URI']) ?>" method="POST" style="margin-bottom: 50px;">
		<input type="hidden" name="action" value="welaunch_add_license">
		<input type="text" name="license" placeholder="Enter your license here ..."><input type="submit" value="Add License" class="btn button">
	</form>

	<?php
		if(is_multisite()) {
			$weLaunchLicenses = get_network_option(0, 'welaunch_licenses');
		} else {
			$weLaunchLicenses = get_option('welaunch_licenses');
		}
		if(empty($weLaunchLicenses)) {
			echo 'No licenses activated yet';
		} else {
			foreach ($weLaunchLicenses as $itemName => $license) {
				?>
				<div class="welaunch-product">
					<h2 class="name"><?php echo ucwords( str_replace('-', ' ', $itemName) ) ?></h2>
					<p class="author">By <a href="https://welaunch.io" target="_blank">weLaunch</a>
						<span class="type plugin">Active</span>
					</p>
					<hr style="margin: 0 0 15px 0;padding:0;">
					<p class="author">
						<small>
							License: <?php echo $license ?>
						</small>
					</p>
				</div>

				<?php
			}
		}
	?>
</div>
