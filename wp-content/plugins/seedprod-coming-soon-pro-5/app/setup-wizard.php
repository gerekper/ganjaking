<?php

/**
 * Complete Setup Wizard
 */
function seedprod_pro_complete_setup_wizard() {
    if ( check_ajax_referer( 'seedprod_pro_complete_setup_wizard' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$wizard_id = isset( $_POST['wizard_id'] ) ? wp_unslash( $_POST['wizard_id'] ) : null;

		// get the wizard data with id and token
		$site_token = get_option( 'seedprod_token' );

		$data = array(
			'wizard_id'       => $wizard_id,
			'site_token'      => $site_token,
		);

		$headers = array();

		// Build the headers of the request.
		$headers = wp_parse_args(
			$headers,
			array(
				'Accept' => 'application/json',
			)
		);

		$url      = SEEDPROD_PRO_API_URL . 'get-wizard-data';
		$response = wp_remote_post(
			$url,
			array(
				'body'    => $data,
				'headers' => $headers,
			)
		);

		$status_code = wp_remote_retrieve_response_code( $response );

		// manually install code if error
		if ( is_wp_error( $response ) ) {
			$response = array(
				'status' => 'false',
				'ip'     => seedprod_pro_get_ip(),
				'msg'    => $response->get_error_message(),
			);
			wp_send_json( $response );
		}

		if ( 200 !== $status_code ) {
			$response = array(
				'status' => 'false',
				'ip'     => seedprod_pro_get_ip(),
				'msg'    => $response['response']['message'],
			);
			wp_send_json( $response );
		}

		$body = wp_remote_retrieve_body( $response );

		if ( ! empty( $body ) ) {
			$body = json_decode( $body );
		}

		// store the wizard id and data locally
		$onboarding = $body->onboarding;

		// store the wizard verify plugins
		update_option('seedprod_verify_wizard_options',$onboarding->options);

		// set tracking if they have opted in
		if(!empty($onboarding->allow_usagetracking)){
			update_option( 'seedprod_allow_usage_tracking', true );
		}

		// free templates
		if(!empty($onboarding->email)){
			update_option( 'seedprod_free_templates_subscribed', true );
		}
		

		// get template type that was setup in the onboarding
		$type = 'lp';
		if ( !empty( $onboarding->sp_type ) ) {
			$type = $onboarding->sp_type;
		}

		// create a landoing page
		if($type == 'lp' || $type == 'cs' || $type == 'mm' || $type == 'p404' || $type == 'loginp' ){

            // install themplate
            $cpt = 'page';
            // seedprod ctp types
            $cpt_types = array(
            'cs',
            'mm',
            'p404',
            'header',
            'footer',
            'part',
            'page');

            if (in_array($type, $cpt_types)) {
                $cpt = 'seedprod';
            }
    

			// base page settings
			require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
			$basic_settings            = json_decode( $seedprod_basic_lpage , true );
			$basic_settings['is_new']    = true;
			$basic_settings['page_type'] = $type;

			// slug
			if ('cs' == $type) {
				$slug                       = 'sp-cs';
				$lpage_name                 = $slug;
				$basic_settings['no_conflict_mode'] = true;
			}
			if ('mm' == $type) {
				$slug                       = 'sp-mm';
				$lpage_name                 = $slug;
				$basic_settings['no_conflict_mode'] = true;
			}
			if ('p404' == $type) {
				$slug                       = 'sp-p404';
				$lpage_name                 = $slug;
				$basic_settings['no_conflict_mode'] = true;
			}
			if ('loginp' == $type) {
				$slug                       = 'sp-login';
				$lpage_name                 = $slug;
				$basic_settings['no_conflict_mode'] = true;
			}

			// insert page code
			$code = '';
			if(!empty($onboarding->code)){
				$code = base64_decode($onboarding->code);
			}

			$code = json_decode( $code , true );

			// merge in code
			if(!empty($slug)){
                $basic_settings['post_title'] = $slug;
                $basic_settings['post_name'] = $slug;
            }
			$basic_settings['template_id'] = intval($onboarding->template_id);
			if ( 99999 != $onboarding->template_id ) {
				unset( $basic_settings['document'] );
				if ( is_array( $code ) ) {
					$new_settings = $basic_settings + $code;
				}
			}

            $id = wp_insert_post(
            array(
                'comment_status'        => 'closed',
                'ping_status'           => 'closed',
                'post_content'          => '',
                'post_status'           => 'draft',
                'post_title'            => 'seedprod',
                'post_type'             => $cpt,
                'post_name'             => $slug,
                'post_content_filtered' => wp_json_encode($new_settings),
                'meta_input'            => array(
                    '_seedprod_page'               => true,
                    '_seedprod_page_uuid'          => wp_generate_uuid4(),
                    '_seedprod_page_template_type' => $type,
                ),
            ),
            true
        );

			// update pointer
			// record coming soon page_id
			if ( 'cs' == $type ) {
				update_option( 'seedprod_coming_soon_page_id', $id );
			}
			if ( 'mm' == $type ) {
				update_option( 'seedprod_maintenance_mode_page_id', $id );
			}
			if ( 'p404' == $type ) {
				update_option( 'seedprod_404_page_id', $id );
			}
			if ( 'loginp' == $type ) {
				update_option( 'seedprod_login_page_id', $id );
			}

			// If landing page set a temp name

			if ( 'lp' == $type ) {
				if ( is_numeric( $id ) ) {
					$lpage_name = esc_html__( 'New Page', 'seedprod-pro' ) . " (ID #$id)";
				} else {
					$lpage_name = esc_html__( 'New Page', 'seedprod-pro' );
				}
			}

			wp_update_post(
				array(
					'ID'         => $id,
					'post_title' => $lpage_name,
				)
			);

        }

		// install theme if theme is the type
        if ($type == 'websitebuilder' || $type == 'woocommerce') {
			$template_id = $onboarding->template_id;			
			seedprod_pro_theme_import( $template_id );
        }



		// install plugins


		$reponse = array(
			'status' => 'true',
			'type'   => $type,
			'id'     => $id,
			'options'=> $onboarding->options,
		);



        wp_send_json_success($reponse);
	}
    
}



// Install Plugins Request During Setup
// function seedprod_pro_complete_setup_wizard_plugins(){
// 	if( !empty( $_GET['site_token'] ) ){
// 		$site_token = get_option( 'seedprod_token' );
// 		if( $_GET['site_token'] == $site_token ){
//             if ( !empty( $_GET['seedprod_verify'] ) ) {
// 				$seedprod_verify = json_decode( wp_unslash( urldecode( $_GET['seedprod_verify'] ) ) );
// 				// if we get here see what plugins the user wants to install
// 				$paths_map = array(
// 					'rafflepress' => 'rafflepress/rafflepress.php',
// 					'allinoneseo' => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
// 					'ga'          => 'google-analytics-for-wordpress/googleanalytics.php',
// 					'wpforms'     => 'wpforms-lite/wpforms.php',
// 					'optinmonster' => 'optinmonster/optin-monster-wp-api.php',
// 				);
// 				$options = get_option('seedprod_verify_wizard_options');
// 				if ( ! empty( $options ) ) {
// 					$options = json_decode( $options );
// 					foreach($options as $p){
// 						if(in_array($p,$seedprod_verify)){
// 							if(!empty($paths_map[$p])){
// 								$plugin = $paths_map[$p];
// 								error_log($plugin);
// 							}
							
// 						}
// 					}
// 				}
// 				exit();
//             }
//         }
// 	}
// }
// add_action( 'init', 'seedprod_pro_complete_setup_wizard_plugins' );

function seedprod_pro_install_addon_setup(){
	// Run a security check.
	check_ajax_referer( 'seedprod_pro_install_addon_setup', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error();
	}

	// if we get here see what plugins the user wants to install
	$paths_map = array(
		'rafflepress' => array('slug'=>'rafflepress/rafflepress.php','url'=>'https://downloads.wordpress.org/plugin/rafflepress.zip'),
		'allinoneseo' => array('slug'=>'all-in-one-seo-pack/all_in_one_seo_pack.php','url'=>'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip'),
		'ga'          => array('slug'=>'google-analytics-for-wordpress/googleanalytics.php','url'=>'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip'),
		'wpforms'     => array('slug'=>'wpforms-lite/wpforms.php','url'=>'https://downloads.wordpress.org/plugin/wpforms-lite.zip'),
		'optinmonster' => array('slug'=>'optinmonster/optin-monster-wp-api.php','url'=>'https://downloads.wordpress.org/plugin/optinmonster.zip'),
	);
	$options = get_option('seedprod_verify_wizard_options');
	$options = json_decode( $options );
	// this allows us to do one at a time
    if (isset($_POST['plugin'])) {
		$plugin = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
		$options = array($plugin);
    }
	$install_plugins = array();

	$all_plugins = get_plugins();

	// purge options to make sure we don't install plugin with conflicts
	if(in_array('allinoneseo',$options)){
		if(
			isset($all_plugins['all-in-one-seo-pack/all_in_one_seo_pack.php']) ||
			isset($all_plugins['all-in-one-seo-pack-pro/all_in_one_seo_pack.php']) ||
			isset($all_plugins['seo-by-rank-math/rank-math.php']) ||
			isset($all_plugins['wordpress-seo/wp-seo.php']) ||
			isset($all_plugins['wordpress-seo-premium/wp-seo-premium.php']) ||
			isset($all_plugins['autodescription/autodescription.php'])
		){
			if (($key = array_search('allinoneseo', $options)) !== false) {
				unset($options[$key]);
			}
		}
	}
	if(in_array('rafflepress',$options)){
		if(
			isset($all_plugins['rafflepress/rafflepress.php']) ||
			isset($all_plugins['rafflepress-pro/rafflepress-pro.php'])
		){
			if (($key = array_search('rafflepress', $options)) !== false) {
				unset($options[$key]);
			}
		}
	}
	if(in_array('wpforms',$options)){
		if(
			isset($all_plugins['wpforms-lite/wpforms.php']) ||
			isset($all_plugins['wpforms/wpforms.php'])
		){
			if (($key = array_search('wpforms', $options)) !== false) {
				unset($options[$key]);
			}
		}
	}
	if(in_array('monsterinsights',$options)){
		if(
			isset($all_plugins['google-analytics-for-wordpress/googleanalytics.php']) ||
			isset($all_plugins['google-analytics-premium/googleanalytics-premium.php'])
		){
			if (($key = array_search('monsterinsights', $options)) !== false) {
				unset($options[$key]);
			}
		}
	}

	


	// install plugins
	if ( ! empty( $options ) ) {
		foreach($options as $p){
				if(!empty($paths_map[$p])){
					$plugin = $paths_map[$p]['slug'];
					$download_url = $paths_map[$p]['url'];

					global $hook_suffix;

					// Set the current screen to avoid undefined notices.
					set_current_screen();
			
					// Prepare variables.
					$method = '';
					$url    = add_query_arg(
						array(
							'page' => 'seedprod_pro',
						),
						admin_url( 'admin.php' )
					);
					$url    = esc_url( $url );
			
					// Start output bufferring to catch the filesystem form if credentials are needed.
					$creds = request_filesystem_credentials( $url, $method, false, false, null );
					if ( false === $creds ) {
						wp_send_json_error();
					}
			
					// If we are not authenticated, make it happen now.
					if ( ! WP_Filesystem( $creds ) ) {
						request_filesystem_credentials( $url, $method, true, false, null );
						$form = ob_get_clean();
						return;
					}
			
					// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
					require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
					global $wp_version;
					if ( version_compare( $wp_version, '5.3.0' ) >= 0 ) {
						require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/includes/skin53.php';
					} else {
						require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/includes/skin.php';
					}
			
					// Create the plugin upgrader with our custom skin.
					ob_start();
					$installer = new Plugin_Upgrader( new SeedProd_Skin() );
					$installer->install( $download_url );
					$output = ob_get_clean();

			
					// Flush the cache and return the newly installed plugin basename.
					wp_cache_flush();
					if ( $installer->plugin_info() ) {
						$plugin_basename = $installer->plugin_info();
						$install_plugins[] = $plugin_basename;
					}

				}
		}
	}
	// activate plugins
	foreach($install_plugins as $ip){
		activate_plugin($ip, '', false, true);
	}
	wp_send_json_success($install_plugins);
            
        
	
}
