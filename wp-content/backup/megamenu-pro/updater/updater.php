<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists('Mega_Menu_Updater') ) :

/**
 *
 */
class Mega_Menu_Updater {


	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		define( 'EDD_MMM_STORE_URL', 'https://www.megamenu.com' );
		define( 'EDD_MMM_ITEM_NAME', 'Max Mega Menu Pro' );

        add_filter( 'megamenu_menu_tabs', array( $this, 'add_license_tab' ), 999 );
        add_action( 'megamenu_page_license', array( $this, 'license_page' ) );

        add_action( 'admin_post_megamenu_update_license', array( $this, 'update_license') );

		add_action( 'admin_init', array( $this, 'edd_mmm_plugin_updater'), 0 );

	}


	/**
	 * Check for new updates
	 */
	public function edd_mmm_plugin_updater() {

		if ( ! class_exists( 'EDD_MMM_Plugin_Updater' ) ) {
			include( dirname( __FILE__ ) . '/EDD_MMM_Plugin_Updater.php' );
		}

		// retrieve our license key from the DB
		$license_key = trim( get_option( 'edd_mmm_license_key' ) );

		// setup the updater
		$edd_updater = new EDD_MMM_Plugin_Updater( EDD_MMM_STORE_URL, MEGAMENU_PRO_PLUGIN_FILE, array(
				'version' 	=> MEGAMENU_PRO_VERSION,  // current version number
				'license' 	=> $license_key, // license key (used get_option above to retrieve from DB)
				'item_name' => EDD_MMM_ITEM_NAME, // name of this plugin
				'author' 	=> 'Tom Hemsley',  // author of this plugin
				'url'       => home_url(),
        		'beta'      => false // set to true if you wish customers to receive update notifications of beta releases
			)
		);

	}


    /**
     * Process license changes
     *
     * @since 1.0
     */
    public function update_license() {

        check_admin_referer( 'megamenu_update_license' );

        if ( isset( $_POST['edd_mmm_license_key'] ) ) {
        	update_option('edd_mmm_license_key', sanitize_text_field($_POST['edd_mmm_license_key']));
        }

        if( isset( $_POST['edd_mmm_license_activate'] ) ) {
			$this->edd_mmm_activate_license();
		}

		if( isset( $_POST['edd_mmm_license_deactivate'] ) ) {
			$this->edd_mmm_deactivate_license();
		}

    }


	/**
	 * Activate a license
	 */
	public function edd_mmm_activate_license() {


		// retrieve the license from the database
		$license = trim( get_option( 'edd_mmm_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( EDD_MMM_ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		/*$response = wp_remote_get(
			add_query_arg( $api_params, EDD_MMM_STORE_URL ),
			array(
				'timeout' => 5,
				'sslverify' => false,
				'body' => ''
			)
		);*/

		$response = wp_remote_post( EDD_MMM_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			echo $response->get_error_message(); die();
        	wp_redirect( admin_url( "admin.php?page=maxmegamenu&tab=license&activated=error" ) );
        	exit;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "valid" or "invalid"
		update_option( 'edd_mmm_license_status', $license_data->license );

		update_option( 'edd_mmm_licence_domain', home_url() );

		$cache_key = 'edd_sl_' . md5( serialize( basename( MEGAMENU_PRO_PLUGIN_FILE, '.php' ) . $license . false  ) );

		delete_option( $cache_key );

		delete_site_transient('update_plugins');

		$args = array( 
	        'page' => 'maxmegamenu',
	        'tab'  => 'license',
	        'activated' => $license_data->license
	    );

		if ( false === $license_data->success ) {
			$args['error_code'] = $license_data->error;
		}

        wp_redirect( add_query_arg( $args, admin_url() ) );

        exit;
	}


	/**
	 * Deactivate the license
	 */
	public function edd_mmm_deactivate_license() {

		// retrieve the license from the database
		$license = trim( get_option( 'edd_mmm_license_key' ) );

		delete_option( 'edd_mmm_license_status' );
		delete_option( 'edd_mmm_licence_domain' );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( EDD_MMM_ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		/*$response = wp_remote_get(
			add_query_arg( $api_params, EDD_MMM_STORE_URL ),
			array(
				'timeout' => 5,
				'sslverify' => false,
				'body' => ''
			)
		);*/

		$response = wp_remote_post( EDD_MMM_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			echo $response->get_error_message(); die();
	        wp_redirect( admin_url( "admin.php?page=maxmegamenu&tab=license&deactivated=error" ) );
	        exit;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

        wp_redirect( admin_url( "admin.php?page=maxmegamenu&tab=license&deactivated={$license_data->license}" ) );
        exit;

	}


	/**
	 * Add the License tab to our available tabs
	 *
	 * @param array $tabs
	 * @since 1.0
	 */
	public function add_license_tab($tabs) {

		$tabs['license'] = __("License", "megamenupro");

		return $tabs;

	}


	/**
	 * Show the license page
	 *
	 * @param array $saved_settings
	 * @since 1.0
	 */
	public function license_page( $saved_settings ) {

		$license = get_option( 'edd_mmm_license_key' );
		$status = get_option( 'edd_mmm_license_status' );

		?>

		<div class='menu_settings'>

			<?php

			if ( isset( $_GET['error_code'] ) ) {
				switch( $_GET['error_code'] ) {
					case 'expired' :
						$message = __("This licence key has expired. Please log in to your client area to renew your licence key.", "megamenupro" );
						break;
					case 'revoked' :
						$message =  __( 'Your license key has been disabled.', "megamenupro" );
						break;
					case 'missing' :
						$message =  __( 'This licence key is not valid.', "megamenupro" );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message =  __( 'Your license is not active for this URL.', "megamenupro" );
						break;
					case 'item_name_mismatch' :
						$message =  __( 'This appears to be an invalid license key.', "megamenupro" );
						break;
					case 'no_activations_left':
						$message =  __( 'Your license key has reached its activation limit. Log into your client area to upgrade your licence or deactivate unused sites.', "megamenupro" );
						break;
				}

				echo "<div class='warning'>" . $message . "</div>";
			}

			?>



            <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                <input type="hidden" name="action" value="megamenu_update_license" />
                <?php wp_nonce_field( 'megamenu_update_license' ); ?>

				<h3 class='first'><?php _e('Max Mega Menu Pro License', "megamenupro"); ?></h3>

				<table>
					<tbody>
						<tr>
							<td class='mega-name'>
								<?php _e('License Key'); ?>
								<div class='mega-description'>
									<?php _e('A license key must be entered and activated to enable automatic plugin updates', "megamenupro"); ?>
								</div>
							</td>
							<td class='mega-value'>

								<input style='width: 25em;' name="edd_mmm_license_key" type="<?php echo apply_filters("megamenu_licence_key_field_type", "text"); ?>" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />

								<?php if( $status !== false && $status == 'valid' ) { ?>
									<input type="submit" class="button-secondary" name="edd_mmm_license_deactivate" value="<?php _e('Deactivate License', "megamenupro"); ?>"/>
								<?php } else { ?>
									<input style='width: auto;' type="submit" class="button-secondary" name="edd_mmm_license_activate" value="<?php _e('Activate License', "megamenupro"); ?>"/>
								<?php } ?>

								<div class='licence_info'>
									<p><b><?php _e('Did you know?', "megamenupro"); ?></b></p>
									<p><?php _e('You can also manage your active licenses and download updates in your <a href="https://www.maxmegamenu.com/client-area/">Client Area</a>.', "megamenupro"); ?></p>
								</div>

								<?php

								$activated_domain = get_option('edd_mmm_licence_domain');
								$current_domain = home_url();

								if ( $activated_domain && $activated_domain != $current_domain ) {
									echo "<div class='licence_warning'>";
									echo "<p><b>Have you moved your site?</b></p>";
									echo "<p>It looks like this licence was originally activated for <code>{$activated_domain}</code>, but the current domain is <code>{$current_domain}</code></p>";
									echo "<p>Please try deactivating and reactivating the licence.</p>";
									echo "<p>If you have a single site licence (or have otherwise hit your licence activation limit) you will need to ensure the licence is deactivated against the old domain before reactivating the licence on this domain. To do this, log into the old domain and deactivate the licence. Alternatively you can increase your site activation limit by upgrading your licence in your <a href='https://www.megamenu.com/client-area/'>Client Area</a>.</p>";
									echo "<p>You can also manage your active domains within your <a href='https://www.megamenu.com/client-area/'>Client Area</a> (your username is your email address, if you have not set a password use the Lost Password option).</p>";
									echo "<p>An active licence is required to enable automatic updates.</p>";
									echo "<p>If you are using a development site you can ignore this message.</p>";
									echo "</div>";
								}

								?>
							</td>
						</tr>
					</tbody>
				</table>


			</form>
		</div>
		<?php
	}
}

endif;