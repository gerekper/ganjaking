<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooThemes Updater Admin Class
 *
 * Admin class for the WooThemes Updater.
 *
 * @package WordPress
 * @subpackage WooThemes Updater
 * @category Core
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * private $token
 * private $api
 * private $name
 * private $menu_label
 * private $page_slug
 * private $plugin_path
 * private $screens_path
 * private $classes_path
 *
 * private $installed_products
 * private $pending_products
 *
 * - __construct()
 * - register_settings_screen()
 * - settings_screen()
 * - get_activated_products()
 * - get_product_reference_list()
 * - get_detected_products()
 * - get_pending_products()
 * - activate_products()
 * - deactivate_product()
 * - load_updater_instances()
 */
class WooThemes_Updater_Admin {
	private $token;
	private $api;
	private $name;
	private $menu_label;
	private $page_slug;
	private $plugin_path;
	private $plugin_url;
	private $screens_path;
	private $classes_path;
	private $assets_url;
	private $hook;

	private $installed_products;
	private $pending_products;

	private $my_account_url;
	private $my_subscriptions_url;

	/**
	 * Constructor.
	 *
	 * @access  public
	 * @since    1.0.0
	 * @return    void
	 */
	public function __construct ( $file ) {
		$this->token = 'woothemes-updater'; // Don't ever change this, as it will mess with the data stored of which products are activated, etc.

		// Load in the class to use for the admin screens.
		require_once( 'class-woothemes-updater-screen.php' );

		// Load the API.
		require_once( 'class-woothemes-updater-api.php' );
		$this->api = new WooThemes_Updater_API();

		$this->name = __( 'WooCommerce Helper', 'woothemes-updater' );
		$this->menu_label = __( 'WooCommerce Helper', 'woothemes-updater' );
		$this->page_slug = 'woothemes-helper';
		$this->plugin_path = trailingslashit( plugin_dir_path( $file ) );
		$this->plugin_url = trailingslashit( plugin_dir_url( $file ) );
		$this->screens_path = trailingslashit( $this->plugin_path . 'screens' );
		$this->classes_path = trailingslashit( $this->plugin_path . 'classes' );
		$this->assets_url = trailingslashit( $this->plugin_url . 'assets' );

		$this->installed_products = array();
		$this->pending_products = array();

		// Setup URLs to go to the Woo.com account management screens.
		$utm_tags = array(
			'utm_source' => 'helper',
			'utm_medium' => 'product',
			'utm_content' => 'subscriptiontab',
		);
		$this->my_account_url = add_query_arg( $utm_tags, 'https://woocommerce.com/my-account/' );
		$this->my_subscriptions_url = add_query_arg( $utm_tags, 'https://woocommerce.com/my-account/my-subscriptions/' );

		// Load the updaters.
		add_action( 'admin_init', array( $this, 'load_updater_instances' ) );

		$menu_hook = is_multisite() ? 'network_admin_menu' : 'admin_menu';
		add_action( $menu_hook, array( $this, 'register_settings_screen' ) );

		// Display an admin notice, if there are Woo products, eligible for licenses, that are not activated.
		add_action( 'network_admin_notices', array( $this, 'maybe_display_activation_notice' ) );
		add_action( 'admin_notices', array( $this, 'maybe_display_activation_notice' ) );
		if( is_multisite() && ! is_network_admin() ) remove_action( 'admin_notices', array( $this, 'maybe_display_activation_notice' ) );

		add_action( 'admin_footer', array( $this, 'theme_upgrade_form_adjustments' ) );

		add_action( 'admin_init', array( $this, 'handle_master_key' ), 5 );
		add_action( 'woothemes_updater_license_screen_before', array( $this, 'ensure_keys_are_actually_active' ) );

		add_action( 'wp_ajax_woothemes_activate_license_keys', array( $this, 'ajax_process_request' ) );
		add_action( 'wp_ajax_woothemes_helper_dismiss_renew', array( $this, 'ajax_process_dismiss_renew' ) );
		add_action( 'wp_ajax_woothemes_helper_dismiss_activation', array( $this, 'ajax_process_dismiss_activation' ) );

		// Be sure to update our local autorenew flag (key 4) when we refresh the transient.
		add_action( 'setted_transient', array( $this, 'update_autorenew_status' ), 10, 2 );

		// delete transient for master key info when plugin activated (in case new woo one)
		add_action( 'activated_plugin', array( $this, 'delete_master_key_info_transient') );
	} // End __construct()

	/**
	 * Display an admin notice, if there are licenses that are not yet activated.
	 * @access  public
	 * @since   1.2.1
	 * @return  void
	 */
	public function maybe_display_activation_notice () {
		if ( isset( $_GET['page'] ) && 'woothemes-helper' == $_GET['page'] ) return;
		if ( ! current_user_can( 'manage_options' ) ) return; // Don't show the message if the user isn't an administrator.
		if ( is_multisite() && ! is_super_admin() ) return; // Don't show the message if on a multisite and the user isn't a super user.
		$this->maybe_display_renewal_notice();

		if ( true == get_site_option( 'woothemes_helper_dismiss_activation_notice', false ) ) return; // Don't show the message if the user dismissed it.

		$products = $this->get_detected_products();

		$has_inactive_products = false;
		if ( 0 < count( $products ) ) {
			foreach ( $products as $k => $v ) {
				if ( isset( $v['product_status'] ) && 'inactive' == $v['product_status'] ) {
					$has_inactive_products = true; // We know we have inactive product licenses, so break out of the loop.
					break;
				}
			}

			if ( $has_inactive_products ) {
				$url = add_query_arg( 'page', 'woothemes-helper', network_admin_url( 'index.php' ) );
				echo '<div id="woothemes-helper-product-activation-message" class="updated fade notice is-dismissible"><p>' . sprintf( __( '%sYour WooCommerce products are almost ready.%s To get started, %sactivate your product subscriptions%s.', 'woothemes-updater' ), '<strong>', '</strong>', '<a href="' . esc_url( $url ) . '">', '</a>' ) . '</p></div>' . "\n";
			}
		}
	} // End maybe_display_activation_notice()

	/**
	 * Update auto-renew statuses every time we reset the updates transient.
	 * @param  string  $transient The name of the transient we're working on.
	 * @param  object  $value The current transient value.
	 */
	public function update_autorenew_status( $transient, $value ) {
		// Deal only with our own transient.
		if ( 'woothemes_helper_updates' != $transient ) {
			return;
		}

		$local_data = get_option( 'woothemes-updater-activated' );
		$transient_data = $value;

		if ( is_array( $local_data ) && 0 < count( $local_data ) ) {
			foreach ( $local_data as $k => $v ) {
				$has_autorenew = $this->is_autorenew_enabled( $k );
				$local_data[$k][4] = (bool)$has_autorenew;
			}
		}

		update_option( 'woothemes-updater-activated', $local_data );
	}

	/**
	 * Check if auto-renew is enabled for a product.
	 * @param  string  $file The plugin filename we're looking at.
	 * @return boolean       Whether or not auto-renew is enabled.
	 */
	public function is_autorenew_enabled( $file ) {
		$response = 0;
		$local_data = get_option( 'woothemes-updater-activated' );

		if ( isset( $local_data[$file][4] ) && 1 == $local_data[$file][4] ) {
			$response = 1;
		} else {
			$data = get_transient( 'woothemes_helper_updates' );
			$maybe_theme_name = str_replace( '/style.css', '', $file );

			if ( is_object( $data ) ) {
				if ( isset( $data->plugins->$file->autorenew ) ) {
					$response = (bool)$data->plugins->$file->autorenew;
				} elseif ( isset( $data->themes->{$maybe_theme_name}->autorenew ) ) {
					$response = (bool) $data->themes->{$maybe_theme_name}->autorenew;
				} elseif ( isset( $data->themes->{$file}->autorenew ) ) {
					$response = (bool) $data->themes->{$file}->autorenew;
				}
			}
		}
		return $response;
	}

	/**
	 * Display an admin notice if a product subscription is about to expire.
	 * @return [type] [description]
	 */
	public function maybe_display_renewal_notice() {
		$products = $this->get_detected_products();
		$notices = array();
		$renew_link = add_query_arg( array( 'utm_source' => 'helper', 'utm_medium' => 'product', 'utm_content' => 'subscriptiontab', 'utm_campaign' => 'licenserenewal' ), $this->my_subscriptions_url );
		// Create dismissal URL for the renew notice.
		$dismiss_url = add_query_arg( 'action', 'woothemes-helper-dismiss-renew', add_query_arg( 'nonce', wp_create_nonce( 'woothemes-helper-dismiss-renew' ) ) );

		foreach ( $products as $file => $product ) {
			$has_autorenew = $this->is_autorenew_enabled( $file );

			// Skip this product if autorenew is enabled, and the Helper knows about it.
			if ( 1 == $has_autorenew ) {
				continue;
			}

			if ( isset( $product['license_expiry'] ) && strtotime( $product['license_expiry'] ) ) {
				try {
					$date = new DateTime( $product['license_expiry'] );
				} catch ( Exception $e ) {
					continue;
				}

				if ( current_time( 'timestamp' ) > strtotime( '-60 days', $date->format( 'U' ) ) && current_time( 'timestamp' ) < strtotime( '+4 days', $date->format( 'U' ) ) ) {
					$notices[] = sprintf( __( '<strong>%s</strong> expires on %s - %senable auto-renew%s.', 'woothemes-updater' ), $product['product_name'], $date->format( get_option( 'date_format' ) ), '<a href="' . esc_url( $renew_link ) . '">', '</a>' );
				} elseif ( current_time( 'timestamp' ) > $date->format( 'U' ) ) {
					$notices[] = sprintf( __( '<strong>%s</strong> has expired. Please %senable auto-renew%s to be eligible for future updates and support.', 'woothemes-updater' ), $product['product_name'], '<a href="' . esc_url( $renew_link ) . '">', '</a>' );
				}
			}
		}

		if ( is_array( $notices ) && 0 < count( $notices ) && false == get_site_transient( 'woo_hide_renewal_notices' ) && apply_filters( 'woothemes_helper_display_renewals_notices', true ) ) {
			$subscription_text = _n( 'a subscription is about to expire', 'the product subscriptions below are about to expire', intval( count( $notices ) ) , 'woothemes-updater' );
			echo '<div id="woothemes-helper-subscription-message" class="notice is-dismissible error" style="display: block;"><p><strong>' . __( 'WooCommerce Helper warning:', 'woothemes-updater' ) . ' ' . $subscription_text . '.</strong></p><ul><li>' . implode( '</li><li>', $notices ) . '</li></ul><div class="clear"></div></div>' . "\n";
		}
	}

	/**
	 * Run a small snippet of JavaScript to highlight the "you will lose all your changes" text on the theme updates screen.
	 * Be sure to add a confirmation dialog box to the "Update Themes" button as well.
	 *
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function theme_upgrade_form_adjustments () {
		global $pagenow;
		if ( 'update-core.php' != $pagenow ) return;
?>
<script type="text/javascript">
/* <![CDATA[ */
if ( jQuery( 'form[name="upgrade-themes"]' ).length ) {
	jQuery( 'form[name=upgrade-themes]' ).prev( 'p' ).wrap( '<div class="error fade"></div>' );

	jQuery( 'form[name=upgrade-themes]' ).find( 'input.button[name=upgrade]' ).click( function ( e ) {
		var response = confirm( '<?php _e( 'Any customizations you have made to theme files will be lost. Are you sure you would like to update?', 'woothemes-updater' ); ?>' );
		if ( false == response ) return false;
	});
}
/*]]>*/
</script>
<?php
	} // End theme_upgrade_form_adjustments()

	/**
	 * Register the admin screen.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function register_settings_screen () {
		$this->hook = add_dashboard_page( $this->name, $this->menu_label, 'manage_options', $this->page_slug, array( $this, 'settings_screen' ) );

		add_action( 'load-' . $this->hook, array( $this, 'process_request' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_print_scripts', array( $this, 'enqueue_scripts' ) );
	} // End register_settings_screen()

	/**
	 * Load the main management screen.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function settings_screen () {
		?>
		<div id="welcome-panel" class="wrap about-wrap woothemes-updater-wrap">
			<h1><?php _e( 'Welcome to WooCommerce Helper', 'woothemes-updater' ); ?></h1>

			<div class="about-text woothemes-helper-about-text">
				<?php
					_e( 'This is your one-stop-spot for connecting your subscriptions.', 'woothemes-updater' );
				?>
			</div>
			<div class="connect-wrapper">
				<?php
				$return = add_query_arg( 'page', 'woothemes-helper', network_admin_url( 'index.php' ) );
				$master_key_info = $this->api->get_master_key_info();

				if ( $master_key_info ) {
					$disconnect_url = admin_url( 'index.php?page=woothemes-helper&delete_wth_token=true' );
					$refresh_url = wp_nonce_url( admin_url( 'index.php?page=woothemes-helper&refresh=true' ), 'wth_refresh_token' );
					?>
					<div class="connected">
						<?php echo get_avatar( $master_key_info->user_email, 80 ); ?>
						<p>
							Connected as<br />
							<strong><?php echo $master_key_info->user_email; ?></strong>
							<span class="buttons">
								<a href="<?php echo $disconnect_url; ?>" class="button button-secondary disconnect" title="Disconnect this WooCommerce.com account">Disconnect</a>
								<a href="<?php echo $refresh_url; ?>" class="button button-secondary refresh" title="Refresh the connection to this WooCommerce.com account">Refresh</a>
							</span>
						</p>
					</div>
				<?php } else {
					$connect_url = wp_nonce_url( $this->api->connect_url . '?site=' . urlencode( site_url() ) . '&return=' . urlencode( $return ), 'wth_connect' );
					?>
					<a href="<?php echo $connect_url ?>" class="button button-primary connect-button"><span class="dashicons dashicons-admin-plugins"></span> Connect your WooCommerce.com Account</a>
				<?php } ?>

				<?php if ( ! $master_key_info ) { ?>
					<p class="short-description woothemes-helper-short-description">
						<?php echo sprintf( __( 'When you connect your account, your subscription keys will automatically be added to your site. You can disconnect the account or an individual key at anytime. Once you\'re connected, you\'ll receive the updates and support that come with your subscription. If you prefer, you can also manually copy the key from your account and paste it below.', 'woothemes-updater' ) ); ?>
					</p><!--/.short-description-->
				<?php }	?>
			</div>
		</div><!--/#welcome-panel .welcome-panel-->
		<?php

		Woothemes_Updater_Screen::get_header();

		$screen = Woothemes_Updater_Screen::get_current_screen();

		switch ( $screen ) {
			// Help screen.
			case 'help':
				do_action( 'woothemes_updater_help_screen_before' );
				$this->load_help_screen_boxes();
				require_once( $this->screens_path . 'screen-help.php' );
				do_action( 'woothemes_updater_help_screen_after' );
			break;

			// Licenses screen.
			case 'license':
			default:
				if ( $this->api->ping() ) {
					$this->installed_products = $this->get_detected_products();
					$this->pending_products = $this->get_pending_products();

					do_action( 'woothemes_updater_license_screen_before' );
					require_once( $this->screens_path . 'screen-manage.php' );
					do_action( 'woothemes_updater_license_screen_after' );
				} else {
					do_action( 'woothemes_updater_api_unreachable_screen_before' );
					require_once( $this->screens_path . 'woothemes-api-unreachable.php' );
					do_action( 'woothemes_updater_api_unreachable_screen_after' );
				}
			break;
		}

		Woothemes_Updater_Screen::get_footer();
	} // End settings_screen()

	/**
	 * Load the boxes for the "Help" screen.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function load_help_screen_boxes () {
		add_action( 'woothemes_helper_column_left', array( $this, 'display_general_links' ) );
		add_action( 'woothemes_helper_column_left', array( $this, 'display_sensei_links' ) );
		add_action( 'woothemes_helper_column_middle', array( $this, 'display_woocommerce_links' ) );
		add_action( 'woothemes_helper_column_middle', array( $this, 'display_themes_links' ) );
	} // End load_help_screen_boxes()

	/**
	 * Display rendered HTML markup containing general support links.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function display_general_links () {
		$links = array(
			'http://docs.woocommerce.com/' => __( 'Documentation Portal', 'woothemes-updater' ),
			'http://www.woocommerce.com/my-account/tickets' => __( 'Get Help', 'woothemes-updater' ),
			'http://www.woocommerce.com/support-faq/' => __( 'FAQ', 'woothemes-updater' ),
			'http://www.woocommerce.com/blog/' => __( 'Blog', 'woothemes-updater' )
			);
		echo '<img src="' . esc_url( $this->assets_url . 'images/getting-started.png' ) . '" alt="' . __( 'Getting Started', 'woothemes-updater' ) . '" />' . "\n";
		echo '<h4>' . __( 'Getting Started', 'woothemes-updater' ) . '</h4>' . "\n";
		echo '<ul>' . $this->_generate_link_list( $links ) . "\n";
		echo '<li><em><a href="' . esc_url( 'https://twitter.com/WooCommerce/' ) . '" title="' . esc_attr__( 'Follow WooCommerce on Twitter', 'woothemes-updater' ) . '">' . __( 'Follow WooCommerce on Twitter', 'woothemes-updater' ) . '</a></em></li>' . "\n";
		echo '</ul>' . "\n";
	} // End display_general_links()

	/**
	 * Display rendered HTML markup containing WooCommerce support links.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function display_woocommerce_links () {
		$links = array(
			'http://docs.woocommerce.com/documentation/plugins/woocommerce/' => __( 'Getting Started', 'woothemes-updater' ),
			'http://docs.woocommerce.com/document/third-party-custom-theme-compatibility/' => __( 'Theme Compatibility', 'woothemes-updater' ),
			'http://docs.woocommerce.com/document/product-variations/' => __( 'Product Variations', 'woothemes-updater' ),
			'http://docs.woocommerce.com/document/woocommerce-self-service-guide/' => __( 'WooCommerce Self Service Guide', 'woothemes-updater' )
			);
		echo '<img src="' . esc_url( $this->assets_url . 'images/woocommerce.png' ) . '" alt="' . __( 'WooCommerce', 'woothemes-updater' ) . '" />' . "\n";
		echo '<h4>' . __( 'WooCommerce', 'woothemes-updater' ) . '</h4>' . "\n";
		echo '<ul>' . $this->_generate_link_list( $links ) . "\n";
		echo '<li><em><a href="' . esc_url( 'http://woocommerce.com/?utm_source=helper&utm_medium=product&utm_content=helptab' ) . '" title="' . esc_attr__( 'Find out more about WooCommerce', 'woothemes-updater' ) . '">' . __( 'Find out more about WooCommerce', 'woothemes-updater' ) . '</a></em></li>' . "\n";
		echo '</ul>' . "\n";
	} // End display_woocommerce_links()

	/**
	 * Display rendered HTML markup containing Sensei support links.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function display_sensei_links () {
		$links = array(
			'http://docs.woocommerce.com/document/sensei/' => __( 'Getting Started', 'woothemes-updater' ),
			'http://docs.woocommerce.com/document/sensei-theming/' => __( 'Theming Sensei', 'woothemes-updater' ),
			'http://docs.woocommerce.com/document/importing-sensei-dummy-data/' => __( 'Import Sensei Dummy Data', 'woothemes-updater' )
			);
		echo '<img src="' . esc_url( $this->assets_url . 'images/sensei.png' ) . '" alt="' . __( 'Sensei', 'woothemes-updater' ) . '" />' . "\n";
		echo '<h4>' . __( 'Sensei', 'woothemes-updater' ) . '</h4>' . "\n";
		echo '<ul>' . $this->_generate_link_list( $links ) . "\n";
		echo '<li><em><a href="' . esc_url( 'http://woocommerce.com/products/sensei/?utm_source=helper&utm_medium=product&utm_content=helptab' ) . '" title="' . esc_attr__( 'Find out more about Sensei', 'woothemes-updater' ) . '">' . __( 'Find out more about Sensei', 'woothemes-updater' ) . '</a></em></li>' . "\n";
		echo '</ul>' . "\n";
	} // End display_sensei_links()

	/**
	 * Display rendered HTML markup containing Themes support links.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function display_themes_links () {
		$links = array(
			'http://docs.woocommerce.com/documentation/themes/' => __( 'Getting Started', 'woothemes-updater' ),
			'http://docs.woocommerce.com/document/canvas/' => __( 'Setting up Canvas', 'woothemes-updater' ),
			'http://docs.woocommerce.com/documentation/woocodex/' => __( 'WooCodex', 'woothemes-updater' )
			);
		echo '<img src="' . esc_url( $this->assets_url . 'images/themes.png' ) . '" alt="' . __( 'Themes', 'woothemes-updater' ) . '" />' . "\n";
		echo '<h4>' . __( 'Themes', 'woothemes-updater' ) . '</h4>' . "\n";
		echo '<ul>' . $this->_generate_link_list( $links ) . "\n";
		echo '<li><em><a href="' . esc_url( 'http://woocommerce.com/product-category/themes/?utm_source=helper&utm_medium=product&utm_content=helptab' ) . '" title="' . esc_attr__( 'Find out more about our Themes', 'woothemes-updater' ) . '">' . __( 'Find out more about our Themes', 'woothemes-updater' ) . '</a></em></li>' . "\n";
		echo '</ul>' . "\n";
	} // End display_themes_links()

	/**
	 * Generate the HTML for a given array of links.
	 * @access  private
	 * @since   1.2.0
	 * @param   array  $links Links with the key as the URL and the value as the title.
	 * @return  string Rendered HTML for the links.
	 */
	private function _generate_link_list ( $links = array() ) {
		if ( 0 >= count( $links ) ) return;
		$html = '';
		foreach ( $links as $k => $v ) {
			$html .= '<li><a href="' . esc_url( trailingslashit( $k ) . '?utm_source=helper&utm_medium=product&utm_content=helptab' ) . '" title="' . esc_attr( $v ) . '">' . esc_html( $v ) . '</a></li>' . "\n";
		}

		return $html;
	} // End _generate_link_list()

	/**
	 * Returns the action value to use.
	 * @access private
	 * @since 1.0.0
	 * @return string|bool Contains the string given in $_POST['action'] or $_GET['action'], or false if none provided
	 */
	private function get_post_or_get_action( $supported_actions ) {
		if ( isset( $_POST['action'] ) && in_array( $_POST['action'], $supported_actions ) )
			return $_POST['action'];

		if ( isset( $_GET['action'] ) && in_array( $_GET['action'], $supported_actions ) )
			return $_GET['action'];

		return false;
	} // End get_post_or_get_action()

	/**
	 * Enqueue admin styles.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function enqueue_styles( $hook ) {
		if ( ! in_array( $hook, array( 'plugins.php', 'update-core.php', $this->hook ) ) ) {
			return;
		}
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( 'woothemes-updater-admin', esc_url( $this->assets_url . 'css/admin.css' ), array(), '1.0.0', 'all' );
	} // End enqueue_styles()

	/**
	 * Enqueue admin scripts.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		$screen = get_current_screen();
		wp_register_script( 'woothemes-updater-admin', $this->assets_url . 'js/admin.js', array( 'jquery', 'post' ) );
		wp_register_script( 'woothemes-updater-admin-notice-hider', $this->assets_url . 'js/admin-notice-hider.js?version=1.6.0', array( 'jquery' ) );

		// Only load script and localization on helper admin page.
		if ( in_array( $screen->id, array( 'dashboard_page_woothemes-helper' ) ) ) {
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'woothemes-updater-admin' );
			$localization = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'activate_license_nonce' => wp_create_nonce( 'activate-license-keys' )
			);
			wp_localize_script( 'woothemes-updater-admin', 'WTHelper', $localization );
		}

		// Load the admin notice hider script.
		wp_enqueue_script( 'woothemes-updater-admin-notice-hider' );
		$localization = array(
			'dismiss_renew_nonce' => wp_create_nonce( 'woothemes_helper_dismiss_renew_nonce' ),
			'dismiss_activation_nonce' => wp_create_nonce( 'woothemes_helper_dismiss_activation_nonce' )
		);
		wp_localize_script( 'woothemes-updater-admin-notice-hider', 'woothemes_helper', $localization );
	} // End enqueue_scripts()

	/**
	 * Process the action for the admin screen.
	 * @since  1.0.0
	 * @return  void
	 */
	public function process_request () {
		$notices_hook = is_multisite() ? 'network_admin_notices' : 'admin_notices';
		add_action( $notices_hook, array( $this, 'admin_notices' ) );

		$supported_actions = array( 'activate-products', 'deactivate-product' );

		$action = $this->get_post_or_get_action( $supported_actions );

		if ( $action && in_array( $action, $supported_actions ) ) {

			// should we be here?
			if ( $action == 'activate-products' ) {
				check_admin_referer( 'wt-helper-activate-license', 'wt-helper-nonce' );
			} else {
				check_admin_referer( 'bulk-licenses' );
			}

			$response = false;
			$status = 'false';
			$type = $action;

			switch ( $type ) {
				case 'activate-products':
					$license_keys = array();
					if ( isset( $_POST['license_keys'] ) && 0 < count( $_POST['license_keys'] ) ) {
						foreach ( $_POST['license_keys'] as $k => $v ) {
							if ( '' != $v ) {
								$license_keys[$k] = array(
									'key' => $v,
									'method' => 'manual',
								);
							}
						}
					}

					if ( isset( $_POST['license_methods'] ) && 0 < count( $_POST['license_methods'] ) ) {
						foreach( $_POST['license_methods'] as $k => $v ) {
							if ( isset( $license_keys[$k] ) ) {
								$license_keys[$k]['method'] = $v;
							}
						}
					}

					if ( 0 < count( $license_keys ) ) {
						$response = $this->activate_products( $license_keys );
					} else {
						$response = false;
						$type = 'no-license-keys';
					}
				break;

				case 'deactivate-product':
					if ( isset( $_GET['filepath'] ) && ( '' != $_GET['filepath'] ) ) {
						$response = $this->deactivate_product( $_GET['filepath'] );
					}
				break;

				default:
				break;
			}

			if ( $response == true ) {
				$status = 'true';
			}

			wp_safe_redirect( add_query_arg( 'type', urlencode( $type ), add_query_arg( 'status', urlencode( $status ), add_query_arg( 'page', urlencode( $this->page_slug ),  network_admin_url( 'index.php' ) ) ) ) );
			exit;
		}
	} // End process_request()

	/**
	 * Process Ajax license activation requests
	 * @since 1.3.1
	 * @return void
	 */
	public function ajax_process_request() {
		if ( isset( $_POST['security'] ) && wp_verify_nonce( $_POST['security'], 'activate-license-keys' ) && isset( $_POST['license_data'] ) && ! empty( $_POST['license_data'] ) ) {
			$license_keys = array();
			foreach ( $_POST['license_data'] as $license_data ) {
				if ( '' != $license_data['key'] ) {
					$license_keys[ $license_data['name'] ] = array(
						'key' => $license_data['key'],
						'method' => $license_data['method'],
					);
				}
			}

			if ( 0 < count( $license_keys ) ) {
				$response = $this->activate_products( $license_keys );
			}
			if ( $response == true ) {
				$request_errors = $this->api->get_error_log();
				if ( 0 >= count( $request_errors ) ) {
					$return = '<div class="updated true fade notice is-dismissible">' . "\n";
					$return .= wpautop( __( 'Products activated successfully.', 'woothemes-updater' ) . ' ' . __( 'Refreshing the products list...', 'woothemes-updater' ) );
					$return .= '</div>' . "\n";
					$return_json = array( 'success' => 'true', 'message' => $return, 'url' => add_query_arg( array( 'page' => 'woothemes-helper', 'status' => 'true', 'type' => 'activate-products' ), admin_url( 'index.php' ) ) );
				} else {
					$return = '<div class="error fade notice is-dismissible">' . "\n";
					$return .= wpautop( __( 'There was an error and not all products were activated.', 'woothemes-updater' ) );
					$return .= '</div>' . "\n";

					$message = '';
					foreach ( $request_errors as $k => $v ) {
						$message .= wpautop( $v );
					}

					$return .= '<div class="error fade notice is-dismissible">' . "\n";
					$return .= make_clickable( $message );
					$return .= '</div>' . "\n";

					$return_json = array( 'success' => 'false', 'message' => $return );

					// Clear the error log.
					$this->api->clear_error_log();
				}
			} else {
				$return = '<div class="error fade notice is-dismissible">' . "\n";
				$return .= wpautop( __( 'No license keys were specified for activation.', 'woothemes-updater' ) );
				$return .= '</div>' . "\n";
				$return_json = array( 'success' => 'false', 'message' => $return );
			}
			echo json_encode( $return_json );
		}
		die();
	}

	/**
	 * Process the dismiss link on our renewal admin notice.
	 * @access public
	 * @since 1.6.0
	 */
	public function ajax_process_dismiss_renew () {
		if ( isset( $_POST['action'] ) && 'woothemes_helper_dismiss_renew' == $_POST['action'] ) {

			// Add nonce security to the request
			if ( ! isset( $_POST['woothemes_helper_dismiss_renew_nonce'] ) || ! wp_verify_nonce( $_POST['woothemes_helper_dismiss_renew_nonce'], 'woothemes_helper_dismiss_renew_nonce' ) ) {
		        die();
		    }
		}

		$set_transient = set_site_transient( 'woo_hide_renewal_notices', 'yes', 60 * DAY_IN_SECONDS );

		echo json_encode( array( 'status' => (bool)$set_transient ) );
		die();
	}

	/**
	 * Process the dismiss link on our activation admin notice.
	 * @access public
	 * @since 1.6.0
	 */
	public function ajax_process_dismiss_activation () {
		if ( isset( $_POST['action'] ) && 'woothemes_helper_dismiss_activation' == $_POST['action'] ) {

			// Add nonce security to the request
			if ( ! isset( $_POST['woothemes_helper_dismiss_activation_nonce'] ) || ! wp_verify_nonce( $_POST['woothemes_helper_dismiss_activation_nonce'], 'woothemes_helper_dismiss_activation_nonce' ) ) {
		        die();
		    }
		}

		$set_status = update_site_option( 'woothemes_helper_dismiss_activation_notice', true );

		echo json_encode( array( 'status' => (bool)$set_status ) );
		die();
	}

	/**
	 * Display admin notices.
	 * @since  1.0.0
	 * @return  void
	 */
	public function admin_notices () {
		$message = '';
		$response = '';

		if ( isset( $_GET['status'] ) && in_array( $_GET['status'], array( 'true', 'false' ) ) && isset( $_GET['type'] ) ) {
			$classes = array( 'true' => 'updated', 'false' => 'error' );

			$request_errors = $this->api->get_error_log();

			switch ( $_GET['type'] ) {
				case 'no-license-keys':
					$message = __( 'No license keys were specified for activation.', 'woothemes-updater' );
				break;

				case 'deactivate-product':
					if ( 'true' == $_GET['status'] && ( 0 >= count( $request_errors ) ) ) {
						$message = __( 'Product deactivated successfully.', 'woothemes-updater' );
					} else {
						$message = __( 'There was an error while deactivating the product.', 'woothemes-updater' );
					}
				break;

				default:

					if ( 'true' == $_GET['status'] && ( 0 >= count( $request_errors ) ) ) {
						$message = __( 'Products activated successfully.', 'woothemes-updater' );
					} else {
						$message = __( 'There was an error and not all products were activated.', 'woothemes-updater' );
					}
				break;
			}

			$response = '<div class="' . esc_attr( $classes[$_GET['status']] ) . ' fade">' . "\n";
			$response .= wpautop( $message );
			$response .= '</div>' . "\n";

			// Cater for API request error logs.
			if ( is_array( $request_errors ) && ( 0 < count( $request_errors ) ) ) {
				$message = '';

				foreach ( $request_errors as $k => $v ) {
					$message .= wpautop( $v );
				}

				$response .= '<div class="error fade">' . "\n";
				$response .= make_clickable( $message );
				$response .= '</div>' . "\n";

				// Clear the error log.
				$this->api->clear_error_log();
			}

			if ( '' != $response ) {
				echo $response;
			}
		}
	} // End admin_notices()

	/**
	 * Detect which products have been activated.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   array
	 */
	protected function get_activated_products () {
		$response = array();

		$response = get_option( $this->token . '-activated', array() );

		if ( ! is_array( $response ) ) $response = array();

		return $response;
	} // End get_activated_products()

	/**
	 * Get a list of products from WooCommerce.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   array
	 */
	protected function get_product_reference_list () {
		global $woothemes_updater;
		$response = array();
		$response = $woothemes_updater->get_products();
		return $response;
	} // End get_product_reference_list()

	/**
	 * Get a list of WooCommerce products found on this installation.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   array
	 */
	protected function get_detected_products () {
		$response = array();
		$products = get_plugins();
		$themes = wp_get_themes();

		if ( 0 < count( $themes ) ) {
			foreach ( $themes as $k => $v ) {
				$filepath = basename( $v->__get( 'stylesheet_dir' ) ) . '/style.css';
				$products[$filepath] = array( 'Name' => $v->__get( 'name' ), 'Version' => $v->__get( 'version' ) );
			}
		}

		if ( is_array( $products ) && ( 0 < count( $products ) ) ) {
			$reference_list = $this->get_product_reference_list();
			$activated_products = $this->get_activated_products();

			if ( is_array( $reference_list ) && ( 0 < count( $reference_list ) ) ) {
				foreach ( $products as $k => $v ) {
					if ( isset( $reference_list[$k] ) ) {
						$status = 'inactive';
						$license_expiry = __( 'Please connect', 'woothemes-updater' );
						if ( in_array( $k, array_keys( $activated_products ) ) ) {
							$status = 'active';
							if ( isset( $activated_products[$k][3] ) ) {
								$license_expiry = $activated_products[$k][3];
							} else {
								$license_expiry = '-';
							}
						}

						// Retrieve the latest actual version, from the hosted changelog file.
						$latest_version = $this->get_version_from_changelog( dirname( $k ) );

						$response[$k] = array( 'product_name' => $v['Name'], 'product_version' => $v['Version'], 'file_id' => $reference_list[$k]['file_id'], 'product_id' => $reference_list[$k]['product_id'], 'product_status' => $status, 'product_file_path' => $k, 'license_expiry' => $license_expiry, 'latest_version' => $latest_version );
					}
				}
			}
		}

		return $response;
	} // End get_detected_products()

	/**
	 * Return the latest version number for a given product, based on the hosted changelog.txt file.
	 *
	 * @access  protected
	 * @since   1.6.0
	 * @return  string Product version number.
	 */
	protected function get_version_from_changelog ( $slug ) {
		if ( false === $version = get_transient( 'wth_' . esc_attr( $slug ) . '_latest_version' ) ) {
			$product_changelog = wp_safe_remote_get( esc_url( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . esc_attr( $slug ) . '/changelog.txt' ) );

			$cl_lines  = explode( "\n", wp_remote_retrieve_body( $product_changelog ) );
			if ( ! empty( $cl_lines ) ) {
				foreach ( $cl_lines as $line_num => $cl_line ) {
					if ( preg_match( '/^[0-9]/', $cl_line ) ) {
						$version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
						// Cache for 1 hour.
						set_transient( 'wth_' . esc_attr( $slug ) . '_latest_version', $version, HOUR_IN_SECONDS );
						break;
					}
				}
			}
		}
		return $version;
	} // End get_version_from_changelog()

	/**
	 * Get an array of products that haven't yet been activated.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return  array Products awaiting activation.
	 */
	protected function get_pending_products () {
		$response = array();

		$products = $this->installed_products;

		if ( is_array( $products ) && ( 0 < count( $products ) ) ) {
			$activated_products = $this->get_activated_products();

			if ( is_array( $activated_products ) && ( 0 <= count( $activated_products ) ) ) {
				foreach ( $products as $k => $v ) {
					if ( isset( $v['product_key']) && ! in_array( $v['product_key'], $activated_products ) ) {
						$response[$k] = array( 'product_name' => $v['product_name'] );
					}
				}
			}
		}

		return $response;
	} // End get_pending_products()

	/**
	 * Activate a given array of products.
	 *
	 * @since    1.0.0
	 * @param    array   $products  Array of products ( filepath => key )
	 * @return boolean
	 */
	protected function activate_products ( $products ) {
		$response = false;
		if ( ! is_array( $products ) || ( 0 >= count( $products ) ) ) { return false; } // Get out if we have incorrect data.

		$key = $this->token . '-activated';
		$has_update = false;
		$already_active = $this->get_activated_products();
		$product_keys = $this->get_product_reference_list();

		foreach ( $products as $k => $v ) {
			if ( ! in_array( $v['key'], $product_keys ) ) {

				// Perform API "activation" request.
				$activate = $this->api->activate( $v['key'], $product_keys[$k]['product_id'], $k, $v['method'] );

				if ( ! ( ! $activate ) ) {
					// key: base file, 0: product id, 1: file_id, 2: hashed license, 3: expiry date, 4: autorenew status
					$already_active[$k] = array( $product_keys[$k]['product_id'], $product_keys[$k]['file_id'], md5( $v['key'] ), $activate->expiry_date, (bool)$activate->autorenew );
					$has_update = true;
				}
			}
		}

		// Store the error log.
		$this->api->store_error_log();

		if ( $has_update ) {
			$response = update_option( $key, $already_active );
		} else {
			$response = true; // We got through successfully, and the supplied keys are already active.
		}

		// Lets Clear the updates transient
		delete_transient( 'woothemes_helper_updates' );

		return $response;
	} // End activate_products()

	/**
	 * Deactivate a given product key.
	 * @since    1.0.0
	 * @param    string $filename File name of the to deactivate plugin licence
	 * @param    bool $local_only Deactivate the product locally without pinging WooCommerce.com.
	 * @return   boolean          Whether or not the deactivation was successful.
	 */
	public function deactivate_product ( $filename, $local_only = false ) {
		$response = false;
		$already_active = $this->get_activated_products();

		if ( 0 < count( $already_active ) ) {
			$deactivated = true;

			if ( isset( $already_active[ $filename ][0] ) ) {
				$key = $already_active[ $filename ][2];

				if ( false == $local_only ) {
					$deactivated = $this->api->deactivate( $key );
				}
			}

			if ( $deactivated ) {
				unset( $already_active[ $filename ] );
				$response = update_option( $this->token . '-activated', $already_active );
			} else {
				$this->api->store_error_log();
			}
		}

		return $response;
	} // End deactivate_product()

	/**
	 * Handle saving, deleting and refreshing of the master key & info in the admin.
	 * Also handles tracking optin.
	 */
	public function handle_master_key() {
		// If we're not where we want to be (the 'woothemes-helper' page), then get out quick.
		if ( ! isset( $_GET['page'] ) || 'woothemes-helper' != $_GET['page'] ) {
			return;
		}

		// Token
		$token = isset( $_GET['key'] ) ? sanitize_text_field( $_GET['key'] ) : false;
		if ( $token ) {
			check_admin_referer( 'wth_connect' );
			update_option( 'woothemes_helper_master_key', $token );

			if ( class_exists( 'WC_Tracker' ) ) {
				update_option( 'woocommerce_allow_tracking', 'yes' );
				WC_Tracker::send_tracking_data( true );
			}
		}

		// Refresh - delete local transients for master key info and helper updates
		if ( isset( $_GET['refresh'] ) && $_GET['refresh'] == true ) {
			check_admin_referer( 'wth_refresh_token' );
			delete_transient( 'wth_master_key_info' );
			delete_transient( 'woothemes_helper_updates' );
		}

		// Token deleting
		if ( isset( $_GET['delete_wth_token'] ) && $_GET['delete_wth_token'] == true ) {
			// delete remotely
			$this->api->delete_master_key_request( get_option( 'woothemes_helper_master_key' ) );

			// delete locally
			delete_option( 'woothemes_helper_master_key' );
			delete_transient( 'wth_master_key_info' );
		}
	}

	/**
	 * Delete the master key info transient.
	 * This runs when a plugin is activated on the site.
	 */
	public function delete_master_key_info_transient() {
		delete_transient( 'wth_master_key_info' );
	}

	/**
	 * Load an instance of the updater class for each activated WooCommerce Product.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_updater_instances () {
		$products = $this->get_detected_products();
		$activated_products = $this->get_activated_products();
		$themes = array();
		$plugins = array();
		if ( 0 < count( $products ) ) {
			foreach ( $products as $k => $v ) {
				if ( isset( $v['product_id'] ) && isset( $v['file_id'] ) ) {
					$license_hash = isset( $activated_products[ $k ][2] ) ? $activated_products[ $k ][2] : '';

					// If it's a theme, add it to the themes list, otherwise add it to the plugins list
					if ( strpos( $k, 'style.css' ) ) {
						$themes[] = array( $k, $v['product_id'], $v['file_id'], $license_hash, $v['product_version'] );
					} else {
						$plugins[] = array( $k, $v['product_id'], $v['file_id'], $license_hash, $v['product_version'] );
					}
				}
			}
			require_once( 'class-woothemes-updater-update-checker.php' );
			new WooThemes_Updater_Update_Checker( $plugins, $themes );
		}
	} // End load_updater_instances()

	/**
	 * Run checks against the API to ensure the product keys are actually active on WooCommerce.com. If not, deactivate them locally as well.
	 * @access public
	 * @since  1.3.0
	 * @return void
	 */
	public function ensure_keys_are_actually_active () {
		$products = (array)$this->get_activated_products();
		$call_data = array();

		if ( 0 < count( $products ) ) {
			foreach ( $products as $k => $v ) {
				if ( 3 <= count( $v ) ) { // Prevents undefined offset notices
					$call_data[$k] = array( $v[0], $v[1], $v[2], esc_url( home_url( '/' ) ) );
				}
			}
		}

		if ( empty( $call_data ) ) {
			return;
		}

		$statuses = $this->api->product_active_statuses_check( $call_data );
		if ( ! $statuses ) {
			return;
		}

		foreach ( $statuses as $k => $v ) {
			if ( isset( $v->deactivate ) ) {
				$this->deactivate_product( $k, true );
			}
		}
	} // End ensure_keys_are_actually_active()
} // End Class
?>
