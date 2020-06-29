<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

if( ! class_exists( 'TGM_Plugin_Activation' ) ){

	/**
	 * Plugin installation and activation for WordPress themes.
	 */
	class Mfn_TGMPA extends Mfn_API {

		protected $code = '';

		public $plugins = array(

			array(
				'name'     	=> 'Contact Form 7',
				'slug'     	=> 'contact-form-7',
				'required' 	=> false,
			),

			array(
				'name'			=> 'Duplicate Post',
				'slug'     	=> 'duplicate-post',
				'required' 	=> false,
			),

			array(
				'name'			=> 'Elementor',
				'slug'     	=> 'elementor',
				'required' 	=> false,
			),

			array(
				'name'     	=> 'Force Regenerate Thumbnails',
				'slug'     	=> 'force-regenerate-thumbnails',
				'required' 	=> false,
			),

		);

		/**
		 * Constructor
		 */
		public function __construct(){

			if( class_exists( 'TGM_Plugin_Activation' ) ){
				return false;
			}

			include_once 'class-tgm-plugin-activation.php';

			// TGMPA registraton and configuration
			add_action( 'tgmpa_register', array( $this, 'tgmpa_register' ) );

			$this->code = mfn_get_purchase_code();

			$this->plugins = $this->get_plugins_list();
		}

		/**
		 * TGMPA register action
		 */
		public function tgmpa_register(){

			$config = array(

				'id'           	=> 'be-tgmpa',        		// Unique ID for hashing notices for multiple instances of TGMPA.
				'menu'         	=> 'be-plugins', 					// Menu slug.
				'parent_slug'  	=> 'betheme',							// Parent menu slug.
				'capability'   	=> 'edit_theme_options',  // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
				'has_notices'  	=> true,                  // Show admin notices or not.
				'dismissable'  	=> true,                  // If false, a user cannot dismiss the nag message.
				'is_automatic'	=> false,									// Automatically activate plugins after installation or not.
				'message' 			=> '<div class="notice notice-warning"><p><strong>Important:</strong> before updating, please <a href="https://codex.wordpress.org/WordPress_Backups">back up your database and files</a>.</p></div><div class="notice notice-info"><p><strong>Server limits:</strong> if you are not sure about server`s settings and limits, please activate necessary plugins only.</p></div>',
				'strings'      	=> array(
					'page_title'                      	=> __( 'Install Plugins', 'tgmpa' ),
					'menu_title'                     	=> __( 'Install Plugins', 'tgmpa' ),
					'installing'                      	=> __( 'Installing Plugin: %s', 'tgmpa' ), // %s = plugin name.
					'oops'                            	=> __( 'Something went wrong with the plugin API.', 'tgmpa' ),
					'notice_can_install_required'     	=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'tgmpa' ),
					'notice_can_install_recommended'  	=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'tgmpa' ),
					'notice_cannot_install'           	=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'tgmpa' ),
					'notice_can_activate_required'    	=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'tgmpa' ),
					'notice_can_activate_recommended' 	=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'tgmpa' ),
					'notice_cannot_activate'          	=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'tgmpa' ),
					'notice_ask_to_update'            	=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'tgmpa' ),
					'notice_cannot_update'            	=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'tgmpa' ),
					'install_link'                    	=> _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'tgmpa' ),
					'activate_link'                   	=> _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'tgmpa' ),
					'return'                          	=> __( 'Return to Required Plugins Installer', 'tgmpa' ),
					'plugin_activated'                	=> __( 'Plugin activated successfully.', 'tgmpa' ),
					'complete'                        	=> __( 'All plugins installed and activated successfully. %s', 'tgmpa' ), // %s = dashboard link.
					'nag_type'                        	=> 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
				),

			);

			tgmpa( $this->plugins, $config );

		}

		public function get_plugins_list(){

			// get transient
			$plugins = get_site_transient( 'betheme_plugins' );
			if( ! $plugins ){
				$plugins = $this->update_plugins_list();
			}

			if( ! $plugins ){
				return $this->plugins;
			}

			return array_merge( $this->plugins, $plugins );
		}

		/**
		 * Get premium plugins list and download links
		 */
		public function update_plugins_list(){

			// if there was a check in last 1 hour, skip this check
			if( get_site_transient( 'betheme_update_plugins' ) ){
				return false;
			}

			// set transient
			set_site_transient( 'betheme_update_plugins', 1, HOUR_IN_SECONDS );
			// end: if there was a check in last 1 hour, skip this check

			$plugins = $this->remote_get( 'plugins_version' );

			if( is_wp_error( $plugins ) || ! $plugins ){
				return false;
			}

			$args = array(
				'code' => $this->code,
			);

			if( mfn_is_hosted() ){
				$args[ 'ish' ] = mfn_get_ish();
			}

			foreach( $plugins as $key => $plugin ){

				$args[ 'plugin' ] = $plugin[ 'slug' ];
				$plugins[ $key ]['source'] = add_query_arg( $args, $this->get_url( 'plugins_download' ) );

			}

			// set transient
			set_site_transient( 'betheme_plugins', $plugins, HOUR_IN_SECONDS );

			return $plugins;
		}

	}

	$Mfn_TGMPA = new Mfn_TGMPA();

}
