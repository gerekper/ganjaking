<?php
/**
 * weLaunch Welcome Class
 *
 * @class weLaunch_Core
 * @version 4.0.0
 * @package weLaunch Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'weLaunch_Welcome', false ) ) {

	/**
	 * Class weLaunch_Welcome
	 */
	class weLaunch_Welcome {

		/**
		 * Min capacity.
		 *
		 * @var string The capability users should have to view the page
		 */
		public $minimum_capability = 'manage_options';

		/**
		 * Display version.
		 *
		 * @var string
		 */
		public $display_version = '';

		/**
		 * Is loaded.
		 *
		 * @var bool
		 */
		public $welaunch_loaded = false;

		/**
		 * Get things started
		 *
		 * @since 1.4
		 */
		public function __construct() {
			// Load the welcome page even if a weLaunch panel isn't running.
			add_action( 'init', array( $this, 'init' ), 999 );
		}

		/**
		 * Class init.
		 */
		public function init() {
			if ( $this->welaunch_loaded ) {
				return;
			}

			if(isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'welaunch_add_license') {
				if(isset($_POST['license']) && !empty($_POST['license'])) {
					$license = $_POST['license'];

					$url = 'https://www.welaunch.io/updates/account/validate.php?license=' . $license;

					$ch = curl_init( $url );
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
					$result = json_decode( curl_exec($ch) );
					curl_close($ch);
					
					if(empty($result)) {
						die('Empty result');
					}

					if(!$result->status) {
						wp_die($result->msg);
					}

					if($result->status && !empty($result->data)) {
						
						if(is_multisite()) {

							$existingLicenses = get_network_option(0, 'welaunch_licenses');

							if(!$existingLicenses) {
								$toSave = array(
									$result->data->item_id => $result->data->license
								);
							} else {
								$toSave = $existingLicenses;
								$toSave[$result->data->item_id] = $result->data->license;
							}

							update_network_option(0, 'welaunch_licenses', $toSave);
						} else {
							$existingLicenses = get_option('welaunch_licenses');

							if(!$existingLicenses) {
								$toSave = array(
									$result->data->item_id => $result->data->license
								);
							} else {
								$toSave = $existingLicenses;
								$toSave[$result->data->item_id] = $result->data->license;
							}
												
							update_option('welaunch_licenses', $toSave);
						}
					}
				}
			}

			$this->welaunch_loaded = true;
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );

			if ( isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification

				if ( 'welaunch-' === substr( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 0, 9 ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$version               = explode( '.', weLaunch_Core::$version );
					$this->display_version = $version[0] . '.' . $version[1];
					add_filter( 'admin_footer_text', array( $this, 'change_wp_footer' ) );
					add_action( 'admin_head', array( $this, 'admin_head' ) );
				}
			}
		}

		/**
		 * Do Redirect.
		 */
		public function do_redirect() {
			if ( ! defined( 'WP_CLI' ) ) {
				wp_safe_redirect( esc_url( admin_url( add_query_arg( array( 'page' => 'welaunch-framework' ), 'tools.php' ) ) ) );
				exit();
			}
		}

		/**
		 * Change Footer.
		 */
		public function change_wp_footer() {
			echo esc_html__( 'If you like', 'welaunch-framework' ) . ' <strong>weLaunch</strong> ' . esc_html__( 'please leave us a', 'welaunch-framework' ) . ' <a href="https://wordpress.org/support/view/plugin-reviews/welaunch-framework?filter=5#postform" target="_blank" class="welaunch-rating-link" data-rated="Thanks :)">&#9733;&#9733;&#9733;&#9733;&#9733;</a> ' . esc_html__( 'rating. A huge thank you in advance!', 'welaunch-framework' );
		}



		/**
		 * Register the Dashboard Pages which are later hidden but these pages
		 * are used to render the What's weLaunch pages.
		 *
		 * @access public
		 * @since  1.4
		 * @return void
		 */
		public function admin_menus() {
			$page = 'add_management_page';

			// About Page.
			$page( esc_html__( 'What is weLaunch Framework?', 'welaunch-framework' ), esc_html__( 'weLaunch', 'welaunch-framework' ), $this->minimum_capability, 'welaunch-framework', array( $this, 'about_screen' ) );

			// Support Page.


			remove_submenu_page( 'tools.php', 'welaunch-status' );

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			do_action( 'welaunch/pro/welcome/admin/menu', $page, $this );
		}

		/**
		 * Hide Individual Dashboard Pages
		 *
		 * @access public
		 * @since  1.4
		 * @return void
		 */
		public function admin_head() {
			?>

			<script
				id="welaunch-qtip-js"
				src='<?php echo esc_url( weLaunch_Core::$url ); ?>assets/js/vendor/qtip/qtip.js'>
			</script>

			<script
				id="welaunch-welcome-admin-js"
				src='<?php echo esc_url( weLaunch_Core::$url ); ?>inc/welcome/js/welaunch-welcome-admin.js'>
			</script>

			<link
				rel='stylesheet' id='welaunch-qtip-css' <?php // phpcs:ignore WordPress.WP.EnqueuedResources ?>
				href='<?php echo esc_url( weLaunch_Core::$url ); ?>assets/css/vendor/qtip.css'
				type='text/css' media='all'/>

			<link
				rel='stylesheet' id='elusive-icons' <?php // phpcs:ignore WordPress.WP.EnqueuedResources ?>
				href='<?php echo esc_url( weLaunch_Core::$url ); ?>assets/css/vendor/elusive-icons.css'
				type='text/css' media='all'/>

			<link
				rel='stylesheet' id='welaunch-welcome-css' <?php // phpcs:ignore WordPress.WP.EnqueuedResources ?>
				href='<?php echo esc_url( weLaunch_Core::$url ); ?>inc/welcome/css/welaunch-welcome.css'
				type='text/css' media='all'/>

			<style type="text/css">
				.welaunch-badge:before {
				<?php echo is_rtl() ? 'right' : 'left'; ?>: 0;
				}

				.about-wrap .welaunch-badge {
				<?php echo is_rtl() ? 'left' : 'right'; ?>: 0;
				}

				.about-wrap .feature-rest div {
					padding- <?php echo is_rtl() ? 'left' : 'right'; ?>: 100px;
				}

				.about-wrap .feature-rest div.last-feature {
					padding- <?php echo is_rtl() ? 'right' : 'left'; ?>: 100px;
					padding- <?php echo is_rtl() ? 'left' : 'right'; ?>: 0;
				}

				.about-wrap .feature-rest div.icon:before {
					margin: <?php echo is_rtl() ? '0 -100px 0 0' : '0 0 0 -100px'; ?>;
				}
			</style>
			<?php
		}

		/**
		 * Navigation tabs
		 *
		 * @access public
		 * @since  1.9
		 * @return void
		 */
		public function tabs() {
			$selected = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : 'welaunch-framework'; // phpcs:ignore WordPress.Security.NonceVerification
			$nonce    = wp_create_nonce( 'welaunch-support-hash' );

			?>
			<input type="hidden" id="welaunch_support_nonce" value="<?php echo esc_attr( $nonce ); ?>"/>
			<h2 class="nav-tab-wrapper">
				<a
					class="nav-tab <?php echo( 'welaunch-framework' === $selected ? 'nav-tab-active' : '' ); ?>"
					href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'welaunch-framework' ), 'tools.php' ) ) ); ?>">
					<?php esc_attr_e( 'What is weLaunch?', 'welaunch-framework' ); ?>
				</a>


				<?php // phpcs:ignore WordPress.NamingConventions.ValidHookName ?>
				<?php do_action( 'welaunch/pro/welcome/admin/tab', $selected ); ?>

			</h2>
			<?php
		}

		/**
		 * Render About Screen
		 *
		 * @access public
		 * @since  1.4
		 * @return void
		 */
		public function about_screen() {
			// Stupid hack for WordPress alerts and warnings.
			echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

			require_once 'views/about.php';
		}

		/**
		 * Render Get Support Screen
		 *
		 * @access public
		 * @since  1.9
		 * @return void
		 */
		public function get_support() {
			// Stupid hack for WordPress alerts and warnings.
			echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

			require_once 'views/support.php';
		}

		/**
		 * Action.
		 */
		public function actions() {
			?>
			<p class="welaunch-actions">
				<a href="http://docs.welaunch.io/" class="docs button button-primary">Docs</a>
				<a
					href="https://wordpress.org/support/view/plugin-reviews/welaunch-framework?filter=5#postform"
					class="review-us button button-primary"
					target="_blank">Review Us</a>
				<a
					href="https://www.paypal.me/welaunchframework"
					class="review-us button button-primary" target="_blank">Donate</a>
				<a
					href="https://twitter.com/share"
					class="twitter-share-button"
					data-url="https://welaunch.io"
					data-text="Supercharge your WordPress experience with weLaunch.io, the world's most powerful and widely used WordPress interface builder."
					data-via="weLaunchFramework" data-size="large" data-hashtags="weLaunch">Tweet</a>
				<?php
				$options = weLaunch_Helpers::get_plugin_options();
				$nonce   = wp_create_nonce( 'welaunch_framework_demo' );

				$query_args = array(
					'page'                   => 'welaunch-framework',
					'welaunch-framework-plugin' => 'demo',
					'nonce'                  => $nonce,
				);

				if ( $options['demo'] ) {
					?>
					<a
						href="<?php echo esc_url( admin_url( add_query_arg( $query_args, 'tools.php' ) ) ); ?>"
						class=" button-text button-demo"><?php echo esc_html__( 'Disable Panel Demo', 'welaunch-framework' ); ?></a>
					<?php
				} else {
					?>
					<a
						href="<?php echo esc_url( admin_url( add_query_arg( $query_args, 'tools.php' ) ) ); ?>"
						class=" button-text button-demo active"><?php echo esc_html__( 'Enable Panel Demo', 'welaunch-framework' ); ?></a>
					<?php
				}

				?>
				<script>
					!function( d, s, id ) {
						var js, fjs = d.getElementsByTagName( s )[0],
							p = /^http:/.test( d.location ) ? 'http' : 'https';
						if ( !d.getElementById( id ) ) {
							js = d.createElement( s );
							js.id = id;
							js.src = p + '://platform.twitter.com/widgets.js';
							fjs.parentNode.insertBefore( js, fjs );
						}
					}( document, 'script', 'twitter-wjs' );
				</script>
			</p>
			<?php
		}
	}
}
