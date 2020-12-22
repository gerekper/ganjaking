<?php
/**
 * Ajax plugin configuration
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 2017 Webraftic Ltd
 * @version       1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * This action allows you to process Ajax requests to activate external components Clearfy
 */
function wfactory_439_install_components($plugin_instance)
{
	check_ajax_referer('updates');

	$slug = $plugin_instance->request->post('plugin', null, true);
	$action = $plugin_instance->request->post('plugin_action', null, true);
	$storage = $plugin_instance->request->post('storage', null, true);

	if( !current_user_can('update_plugins') ) {
		wp_die(__('You don\'t have enough capability to edit this information.', 'wbcr_factory_439'), __('Something went wrong.'), 403);
	}

	if( empty($slug) || empty($action) ) {
		wp_send_json_error(['error_message' => __('Required attributes are not passed or empty.', 'wbcr_factory_439')]);
	}
	$success = false;
	$send_data = [];

	if( $storage == 'internal' ) {
		if( $action == 'activate' ) {
			if( $plugin_instance->activateComponent($slug) ) {
				$success = true;
			}
		} else if( $action == 'deactivate' ) {

			if( $plugin_instance->deactivateComponent($slug) ) {
				$success = true;
			}
		} else {
			wp_send_json_error(['error_message' => __('You are trying to perform an invalid action.', 'wbcr_factory_439')]);
		}
	} else if( $storage == 'wordpress' || $storage == 'creativemotion' ) {
		if( !empty($slug) ) {
			$network_wide = $plugin_instance->isNetworkActive();

			if( $action == 'activate' ) {
				$result = activate_plugin($slug, '', $network_wide);

				if( is_wp_error($result) ) {
					wp_send_json_error(['error_message' => $result->get_error_message()]);
				}
			} else if( $action == 'deactivate' ) {
				deactivate_plugins($slug, false, $network_wide);
			}

			$success = true;
		}
	}

	if( $action == 'install' || $action == 'deactivate' ) {
		try {
			// Delete button
			$delete_button = $plugin_instance->get_delete_component_button($storage, $slug);
			$send_data['delete_button'] = $delete_button->get_button();
		} catch( Exception $e ) {
			wp_send_json_error(['error_message' => $e->getMessage()]);
		}
	}

	// Если требуется обновить постоянные ссылки, даем сигнал, что пользователю, нужно показать
	// всплывающее уведомление.
	// todo: сделать более красивое решение с передачей текстовых сообщений
	/*if ( $action == 'deactivate' ) {
		$is_need_rewrite_rules = $plugin_instance->getPopulateOption( 'need_rewrite_rules' );
		if ( $is_need_rewrite_rules ) {
			$send_data['need_rewrite_rules'] = sprintf( '<span class="wbcr-clr-need-rewrite-rules-message">' . __( 'When you deactivate some components, permanent links may work incorrectly. If this happens, please, <a href="%s">update the permalinks</a>, so you could complete the deactivation.', 'wbcr_factory_439' ), admin_url( 'options-permalink.php' ) . '</span>' );
		}
	}*/

	if( $success ) {
		// todo: для совместимости с плагином Clearfy
		if( "wbcr_clearfy" === $plugin_instance->getPluginName() ) {
			do_action('wbcr_clearfy_update_component', $slug, $action, $storage);
		}
		do_action("wfactory/updated_{$plugin_instance->getPluginName()}_component", $slug, $action, $storage);

		wp_send_json_success($send_data);
	}

	wp_send_json_error(['error_message' => __('An unknown error occurred during the activation of the component.', 'wbcr_factory_439')]);
}

/**
 * Ajax event that calls the wbcr/clearfy/activated_component action,
 * to get the component to work. Usually this is a call to the installation functions,
 * but in some cases, overwriting permanent references or compatibility checks.
 */
function wfactory_439_prepare_component($plugin_instance)
{
	check_ajax_referer('updates');

	$component_name = $plugin_instance->request->post('plugin', null, true);

	if( !current_user_can('update_plugins') ) {
		wp_send_json_error(['error_message' => __('You don\'t have enough capability to edit this information.', 'wbcr_factory_439')], 403);
	}

	if( empty($component_name) ) {
		wp_send_json_error(['error_message' => __('Required attribute [component_name] is empty.', 'wbcr_factory_439')]);
	}
	// todo: для совместимости с плагином Clearfy
	if( "wbcr_clearfy" === $plugin_instance->getPluginName() ) {
		do_action('wbcr/clearfy/activated_component', $component_name);
	}
	do_action("wfactory/activated_{$plugin_instance->getPluginName()}_component", $component_name);

	wp_send_json_success();
}

/**
 * Ajax handler for installing a plugin.
 *
 * @since 4.6.0
 *
 * @see Plugin_Upgrader
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 */
function wfactory_439_creativemotion_install_plugin($plugin_instance)
{
	check_ajax_referer('updates');

	if( empty($_POST['slug']) ) {
		wp_send_json_error(array(
			'slug' => '',
			'errorCode' => 'no_plugin_specified',
			'errorMessage' => __('No plugin specified.'),
		));
	}

	$status = array(
		'install' => 'plugin',
		'slug' => sanitize_key(wp_unslash($_POST['slug'])),
	);

	if( !current_user_can('install_plugins') ) {
		$status['errorMessage'] = __('Sorry, you are not allowed to install plugins on this site.');
		wp_send_json_error($status);
	}

	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

	$api = plugins_api('plugin_information', array(
		'slug' => sanitize_key(wp_unslash($_POST['slug'])),
		'fields' => array(
			'sections' => false,
		),
	));

	if( is_wp_error($api) ) {
		$status['errorMessage'] = $api->get_error_message();
		wp_send_json_error($status);
	}

	$status['pluginName'] = $api->name;

	$skin = new WP_Ajax_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader($skin);
	//$result = $upgrader->install($api->download_link);
	$result = $upgrader->install('https://clearfy.pro/components/download.php');

	if( defined('WP_DEBUG') && WP_DEBUG ) {
		$status['debug'] = $skin->get_upgrade_messages();
	}

	if( is_wp_error($result) ) {
		$status['errorCode'] = $result->get_error_code();
		$status['errorMessage'] = $result->get_error_message();
		wp_send_json_error($status);
	} elseif( is_wp_error($skin->result) ) {
		$status['errorCode'] = $skin->result->get_error_code();
		$status['errorMessage'] = $skin->result->get_error_message();
		wp_send_json_error($status);
	} elseif( $skin->get_errors()->has_errors() ) {
		$status['errorMessage'] = $skin->get_error_messages();
		wp_send_json_error($status);
	} elseif( is_null($result) ) {
		global $wp_filesystem;

		$status['errorCode'] = 'unable_to_connect_to_filesystem';
		$status['errorMessage'] = __('Unable to connect to the filesystem. Please confirm your credentials.');

		// Pass through the error from WP_Filesystem if one was raised.
		if( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->has_errors() ) {
			$status['errorMessage'] = esc_html($wp_filesystem->errors->get_error_message());
		}

		wp_send_json_error($status);
	}

	$install_status = install_plugin_install_status($api);
	$pagenow = isset($_POST['pagenow']) ? sanitize_key($_POST['pagenow']) : '';

	// If installation request is coming from import page, do not return network activation link.
	$plugins_url = ('import' === $pagenow) ? admin_url('plugins.php') : network_admin_url('plugins.php');

	if( current_user_can('activate_plugin', $install_status['file']) && is_plugin_inactive($install_status['file']) ) {
		$status['activateUrl'] = add_query_arg(array(
			'_wpnonce' => wp_create_nonce('activate-plugin_' . $install_status['file']),
			'action' => 'activate',
			'plugin' => $install_status['file'],
		), $plugins_url);
	}

	if( is_multisite() && current_user_can('manage_network_plugins') && 'import' !== $pagenow ) {
		$status['activateUrl'] = add_query_arg(array('networkwide' => 1), $status['activateUrl']);
	}

	wp_send_json_success($status);
}