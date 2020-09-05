<?php

// uncomment this line for testing
//set_site_transient( 'update_plugins', null );

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Allows plugins to use their own update API.
 *
 * @author Pippin Williamson mods by John Turner for SeedProd/SellWP
 * @version 2.0.0
 */
class SeedProd_Updater_cspv5 {
	private $api_url   = '';
	private $api_data  = array();
	private $name      = '';
	private $slug      = '';
	private $version   = '';

	/**
	 * Class constructor.
	 *
	 * @uses plugin_basename()
	 * @uses hook()
	 *
	 * @param string  $_api_url     The URL pointing to the custom API endpoint.
	 * @param string  $_plugin_file Path to the plugin file.
	 * @param array   $_api_data    Optional data to send with API calls.
	 */
	function __construct( $_api_url, $_plugin_file, $_api_data = null ) {

		$this->api_url  =  $_api_url ;
		$this->api_data = $_api_data;
		$this->name     = plugin_basename( $_plugin_file );
		$this->slug     = basename( $_plugin_file, '.php' );
		$this->version  = SEED_CSPV5_VERSION;

		// Set up hooks.
		$this->init();

	}

	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @uses add_filter()
	 *
	 * @return void
	 */
	public function init() {

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		remove_action( 'after_plugin_row_' . $this->name, 'wp_plugin_update_row', 10, 2 );
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );


	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @uses api_request()
	 *
	 * @param array   $_transient_data Update array build by WordPress.
	 * @return array Modified update array with custom plugin data.
	 */
	function check_update( $_transient_data ) {
		return;

		global $pagenow;

		if( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass;
		}

		if( 'plugins.php' == $pagenow && is_multisite() ) {
			return $_transient_data;
		}

		if ( empty( $_transient_data->response ) || empty( $_transient_data->response[ $this->name ] ) ) {
			$version_info = $this->api_request( 'plugin_latest_version', array( 'slug' => $this->slug ) );


			if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {
				$version_info->package = $version_info->download_link;
				unset($version_info->download_link);

				if( version_compare( $this->version, $version_info->new_version, '<' ) ) {

                    $obj                                     = new stdClass();
                    $obj->slug                               = $this->slug;
                    $obj->new_version                        = $version_info->new_version;
                    $obj->package                            = $version_info->package;
                    $obj->upgrade_notice                     = $version_info->upgrade_notice;

					$_transient_data->response[ $this->name ] = $obj;


				}else {
					// No update is available.
					$item = (object) array(
						'id'            => 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php',
						'slug'          => 'seedprod-coming-soon-pro-5',
						'plugin'        => 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php',
						'new_version'   => SEED_CSPV5_VERSION,
						'url'           => '',
						'package'       => '',
						'icons'         => array(),
						'banners'       => array(),
						'banners_rtl'   => array(),
						'tested'        => '',
						'requires_php'  => '',
						'compatibility' => new stdClass(),
					);
					// Adding the "mock" item to the `no_update` property is required
					// for the enable/disable auto-updates links to correctly appear in UI.
					$_transient_data->no_update['seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php'] = $item;
				}

				$_transient_data->last_checked = time();
				$_transient_data->checked[ $this->name ] = $this->version;

			}

		}

		return $_transient_data;
	}

	/**
	 * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
	 *
	 * @param string  $file
	 * @param array   $plugin
	 */
	public function show_update_notification( $file, $plugin ) {

		if( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if( ! is_multisite() ) {
			return;
		}

		if ( $this->name != $file ) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );

		$update_cache = get_site_transient( 'update_plugins' );
		
		$update_cache = is_object( $update_cache ) ? $update_cache : new stdClass();

		if ( empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {

			$cache_key    = md5( 'edd_plugin_' . sanitize_key( $this->name ) . '_version_info' );
			$version_info = get_transient( $cache_key );

			if( false === $version_info ) {

				$version_info =  $this->api_request( 'plugin_latest_version', array( 'slug' => $this->slug ) );

				set_transient( $cache_key, $version_info, 3600 );
			}

			if( ! is_object( $version_info ) ) {
				return;
			}

			if( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$update_cache->response[ $this->name ] = $version_info;

			}

			$update_cache->last_checked = time();
			$update_cache->checked[ $this->name ] = $this->version;

			set_site_transient( 'update_plugins', $update_cache );

		} else {

			$version_info = $update_cache->response[ $this->name ];

		}

		// Restore our filter
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		if ( ! empty( $update_cache->response[ $this->name ] ) && version_compare( $this->version, $version_info->new_version, '<' ) ) {

			// build a plugin list row, with update notification
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message">';

			$changelog_link = self_admin_url( 'index.php?edd_sl_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911' );

			if ( empty( $version_info->download_link ) ) {
				printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a>.', 'easy-digital-downloads' ),
					esc_html( $version_info->name ),
					esc_url( $changelog_link ),
					esc_html( $version_info->new_version )
				);
			} else {
				printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a> or <a href="%4$s">update now</a>.', 'easy-digital-downloads' ),
					esc_html( $version_info->name ),
					esc_url( $changelog_link ),
					esc_html( $version_info->new_version ),
					esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) )
				);
			}

			do_action( "in_plugin_update_message-{$file}", $plugin, $version_info );

			echo '</div></td></tr>';
		}
	}


	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @uses api_request()
	 *
	 * @param mixed   $_data
	 * @param string  $_action
	 * @param object  $_args
	 * @return object $_data
	 */
	function plugins_api_filter( $_data, $_action = '', $_args = null ) {
		$slug = $this->slug;
		if ( !empty( $_args->slug ) ) {
	        if ( !empty( $slug ) ) {
	            if ( $_args->slug === $slug ) {
					$information =   $this->api_request( 'plugin_latest_version', array( 'slug' => $this->slug ) );
					if(!empty($information->sections)){
						$information->sections = (array) $information->sections;
					}
	                

	                return $information;
	            }
	        }
        }
        
        return $_data;
	}



	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 */
	private function api_request( $_action, $_data ) {

		global $wp_version;

		$data = array_merge( $this->api_data, $_data );

		if ( $data['slug'] != $this->slug ) {
			return;
		}

		if( $this->api_url == home_url() ) {
			return false; // Don't allow a plugin to ping itself
		}


		$api_params = array(
			'action' => 'info',
			'license_key'    => ! empty( $data['license'] ) ? $data['license'] : '',
			'slug'       => SEED_CSPV5_SLUG,
			'domain'        => home_url(),
			'token'      => get_option('seed_cspv5_token'),
			'installed_version' => SEED_CSPV5_VERSION,
			'data' => $data['data']

		);
		
		$request = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		//var_dump($request);


		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
			$arequest = $request;
                $nag = $arequest->message;
                
                update_option('seed_cspv5_api_message',$nag);
                if($arequest->status == '200'){
                    update_option('seed_cspv5_api_nag','');
                    update_option('seed_cspv5_a',true);
                    update_option('seed_cspv5_per',$arequest->per);
                }elseif($arequest->status == '401'){
                    update_option('seed_cspv5_api_nag',$nag);
                    update_option('seed_cspv5_a',false);
                    update_option('seed_cspv5_per','');
                }elseif($arequest->status == '402'){
                    update_option('seed_cspv5_api_nag',$nag);
                    update_option('seed_cspv5_a',false);
                    update_option('seed_cspv5_per',$arequest->per);

                }     
		}else{
			$request = false;
		}
		

		return $request;
	}

	

}
