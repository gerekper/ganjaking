<?php
/**
 * PA Setup Functions.
 */

namespace PremiumAddonsPro\Admin\Includes;

// PAPRO Classes.
use PremiumAddonsPro\Admin\Includes\Admin_Notices;
use PremiumAddonsPro\Includes\PAPRO_Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PA_Installer.
 */
class PA_Installer {

	/**
	 * Class instance
	 *
	 * @var instance
	 */
	public static $instance = null;

	/**
	 * Class Constructor
	 */
	public function __construct() {

		if ( is_admin() && current_user_can( 'install_plugins' ) ) {
			add_action( 'admin_action_install_pa_version', array( $this, 'pa_install' ) );
		}
	}

	/**
	 * Install and activates pa.
	 *
	 * @since 2.5.3
	 * @access public
	 */
	public function pa_install() {

		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$plugin = 'premium-addons-for-elementor';

		self::print_inline_style();

		?>
			<div class="pa-wrap">
				<h1 class="pa-heading"><?php echo esc_html__( 'Premium Addons for Elementor Installation', 'premium-addons-pro' ); ?></h1>
				<div class="pa-action-wrap">
					<?php
						$api = plugins_api(
							'plugin_information',
							array(
								'slug'   => $plugin,
								'fields' => array(
									'short_description' => false,
									'sections'          => false,
									'requires'          => false,
									'rating'            => false,
									'ratings'           => false,
									'downloaded'        => false,
									'last_updated'      => false,
									'added'             => false,
									'tags'              => false,
									'compatibility'     => false,
									'homepage'          => false,
									'donate_link'       => false,
								),
							)
						);

						$upgrader = new \Plugin_Upgrader();

						wp_cache_flush();

						$installed = $upgrader->install( $api->download_link );

						$plugin_page_url = ( is_multisite() && is_network_admin() ) ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );

					if ( ! is_wp_error( $installed ) ) {

						$network_activate = ( is_multisite() && is_network_admin() ) ? true : false;

						?>
							<p><?php echo esc_html__( 'Activating Premium Addons For Elementor.....', 'premium-addons-pro' ); ?></p>

							<?php activate_plugin( 'premium-addons-for-elementor/premium-addons-for-elementor.php', '', $network_activate, true ); ?>

							<p><?php echo esc_html__( 'Premium Addons For Elementor activated.', 'premium-addons-pro' ); ?></p>

						<?php } else { ?>
							<p><?php echo esc_html__( 'It seems there was an error installing Premium Addons For Elementor, Please try again in a moment.', 'premium-addons-pro' ); ?></p>
						<?php } ?>
						<a href="<?php echo esc_url( $plugin_page_url ); ?>"><?php echo esc_html__( 'Go To Plugins Page', 'premium-addons-pro' ); ?></a>
				</div>
			</div>
		<?php
	}

	/**
	 * Print Inline Style
	 *
	 * Used to print inline style on rollback page
	 *
	 * @since 0.0.1
	 * @access private
	 */
	private function print_inline_style() {
		?>

		<style>
			.pa-wrap {
				width: 50%;
				min-height: 100vh;
				margin: auto;
				text-align: center;
				border: 2px solid #6ec1e4;
				padding-bottom: 15px;
			}

			.pa-heading {
				background: #6ec1e4;
				text-align: center;
				color: #fff !important;
				padding: 70px !important;
				text-transform: uppercase;
				letter-spacing: 1px;
				margin-top: 0px;
			}

			h1 img {
				max-width: 300px;
				display: block;
				margin: auto auto 50px;
			}
		</style>

		<?php
	}

	/**
	 * Creates and returns an instance of the class
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();

		}

		return self::$instance;
	}



}

?>
