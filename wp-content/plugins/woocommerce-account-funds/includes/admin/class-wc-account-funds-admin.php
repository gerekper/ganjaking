<?php
/**
 * WooCommerce Account Funds Admin
 *
 * @package WC_Account_Funds/Admin
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Account_Funds_Admin
 */
class WC_Account_Funds_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_filter( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );

		// Plugin action links.
		add_filter( 'plugin_action_links_' . WC_ACCOUNT_FUNDS_BASENAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		// Users.
		add_filter( 'manage_users_columns', array( $this, 'manage_users_columns' ) );
		add_action( 'manage_users_custom_column', array( $this, 'manage_users_custom_column' ), 10, 3 );
		add_action( 'show_user_profile', array( $this, 'user_meta_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'user_meta_fields' ) );
		add_action( 'personal_options_update', array( $this, 'save_user_meta_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_meta_fields' ) );
	}

	/**
	 * Include any classes we need within admin.
	 *
	 * @since 2.3.7
	 */
	public function includes() {
		include_once 'wc-account-funds-admin-functions.php';
		include_once 'class-wc-account-funds-admin-notices.php';
		include_once 'class-wc-account-funds-admin-system-status.php';
	}

	/**
	 * Enqueues admin scripts.
	 *
	 * @since 2.6.0
	 */
	public function enqueue_scripts() {
		if ( ! wc_account_funds_is_settings_page() ) {
			return;
		}

		$suffix = wc_account_funds_get_scripts_suffix();

		wp_enqueue_script( 'wc-account-funds-settings', WC_ACCOUNT_FUNDS_URL . "assets/js/admin/settings{$suffix}.js", array( 'jquery' ), WC_ACCOUNT_FUNDS_VERSION, true );
	}

	/**
	 * Adds the settings page.
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings The settings pages.
	 * @return array An array with the settings pages.
	 */
	public function add_settings_page( $settings ) {
		$settings[] = include 'class-wc-account-funds-admin-settings.php';

		return $settings;
	}

	/**
	 * Adds custom links to the plugins page.
	 *
	 * @since 2.2.0
	 *
	 * @param array $links The plugin links.
	 * @return array
	 */
	public function action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( admin_url( 'admin.php?page=wc-settings&tab=account_funds' ) ),
			_x( 'View WooCommerce Account Funds settings', 'aria-label: settings link', 'woocommerce-account-funds' ),
			_x( 'Settings', 'plugin action link', 'woocommerce-account-funds' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Adds custom links to this plugin on the plugins screen.
	 *
	 * @since 2.2.0
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( WC_ACCOUNT_FUNDS_BASENAME === $file ) {
			$row_meta = array(
				'docs'      => sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( 'https://docs.woocommerce.com/document/account-funds/' ),
					esc_attr_x( 'View WooCommerce Account Funds documentation', 'aria-label: documentation link', 'woocommerce-account-funds' ),
					esc_html_x( 'Docs', 'plugin row link', 'woocommerce-account-funds' )
				),
				'changelog' => sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( 'https://woocommerce.com/changelogs/woocommerce-account-funds/changelog.txt' ),
					esc_attr_x( 'View WooCommerce Account Funds changelog', 'aria-label: changelog link', 'woocommerce-account-funds' ),
					esc_html_x( 'Changelog', 'plugin row link', 'woocommerce-account-funds' )
				),
				'support'   => sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( 'https://support.woocommerce.com/' ),
					esc_attr_x( 'View WooCommerce Account Funds support', 'aria-label: support link', 'woocommerce-account-funds' ),
					esc_html_x( 'Support', 'plugin row link', 'woocommerce-account-funds' )
				),
			);

			$links = array_merge( $links, $row_meta );
		}

		return $links;
	}

	/**
	 * Add column
	 * @param  array $columns
	 * @return array
	 */
	public function manage_users_columns( $columns ) {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$columns['account_funds'] = __( 'Account Funds', 'woocommerce-account-funds' );
		}
		return $columns;
	}

	/**
	 * Column value
	 * @param  string $value
	 * @param  string $column_name
	 * @param  int $user_id
	 * @return string
	 */
	public function manage_users_custom_column( $value, $column_name, $user_id ) {
		if ( $column_name === 'account_funds' ) {
        	$funds = get_user_meta( $user_id, 'account_funds', true );
        	$funds = $funds ? $funds : 0;
        	$value = wc_price( $funds );
   		}
    	return $value;
	}

	/**
	 * Show Meta Fields
	 * @param  object $user
	 */
	public function user_meta_fields( $user ) {
		if ( current_user_can( 'manage_woocommerce' ) ) {
		    $funds = get_user_meta( $user->ID, 'account_funds', true );
		    $funds = $funds ? $funds : 0;
		    ?>
			<h3><?php _e( 'Account Funds', 'woocommerce-account-funds' ); ?></h3>
			<table class="form-table">
				<tr>
	                <th><label for="account_funds"><?php _e( 'Account Funds Amount', 'woocommerce-account-funds' ); ?></label></th>
	                <td>
	                    <input type="text" name="account_funds" id="account_funds" value="<?php echo esc_attr( $funds ); ?>" class="small-text" /><br/>
	                    <span class="description"><?php _e( 'Funds this user can use to purchase items', 'woocommerce-account-funds' ); ?></span>
	                </td>
	            </tr>
			</table>
			<?php
		}
	}

	/**
	 * Save meta fields.
	 *
	 * @version 2.1.6
	 *
	 * @param int $user_id User ID.
	 */
	public function save_user_meta_fields( $user_id ) {
		if ( isset( $_POST['account_funds'] ) && current_user_can( 'manage_woocommerce' ) ) {
			$current_funds = floatval( get_user_meta( $user_id, 'account_funds', true ) );
			$new_funds     = floatval( wc_clean( $_POST['account_funds'] ) );
			if ( update_user_meta( $user_id, 'account_funds', $new_funds ) ) {
				if ( $current_funds < $new_funds ) {
					// Send email to customer.
					$wc_emails = WC_Emails::instance();
					$email = $wc_emails->emails['WC_Account_Funds_Email_Account_Funds_Increase'];
					$email->trigger( $user_id, $current_funds, $new_funds );
				}
			}
		}
	}

	/**
	 * Returns settings array.
	 *
	 * @deprecated 2.6.0
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = array();

		if ( has_filter( 'woocommerce_account_funds_get_settings' ) ) {
			/**
			 * The plugin settings.
			 *
			 * @deprecated 2.6.0
			 *
			 * @param array $settings An array with the settings.
			 */
			$settings = apply_filters( 'woocommerce_account_funds_get_settings', $settings );
		}

		return $settings;
	}

	/**
	 * Add settings tab to woocommerce
	 *
	 * @deprecated 2.6.0
	 *
	 * @param array $settings_tabs An array with the settings tabs.
	 * @return array
	 */
	public function add_woocommerce_settings_tab( $settings_tabs ) {
		wc_deprecated_function( __FUNCTION__, '2.6.0' );

		return $settings_tabs;
	}

	/**
	 * Do this when viewing our custom settings tab(s). One function for all tabs.
	 *
	 * @deprecated 2.6.0
	 */
	public function woocommerce_settings_tab_action() {
		wc_deprecated_function( __FUNCTION__, '2.6.0' );
	}

	/**
	 * Save settings in a single field in the database for each tab's fields (one field per tab).
	 *
	 * @deprecated 2.6.0
	 */
	public function woocommerce_settings_save() {
		wc_deprecated_function( __FUNCTION__, '2.6.0' );
	}
}

new WC_Account_Funds_Admin();
