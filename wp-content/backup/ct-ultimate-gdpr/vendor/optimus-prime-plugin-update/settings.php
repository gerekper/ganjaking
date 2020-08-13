<?php

/** TGM Plugin Activation settings
 * @see http://tgmpluginactivation.com/configuration/
 */

// init tgmpa settings
add_action( 'tgmpa_register', 'ct_ultimate_gdpr_register_required_plugins', 5 );
function ct_ultimate_gdpr_register_required_plugins() {

	$config = array(
		'id'           => 'ct-ultimate-gdpr',
		// Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',
		// Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins',
		// Menu slug.
		'parent_slug'  => 'themes.php',
		// Parent menu slug.
		'capability'   => 'manage_options',
		// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,
		// Show admin notices or not.The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme
		'dismissable'  => false,
		// If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',
		// If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,
		// Automatically activate plugins after installation or not.
		'message'      => '',
		// Message to output right before the plugins table.
	);

	$plugins = array(

		array(
			'name'               => 'Ultimate GDPR',
			// The plugin name.
			'slug'               => 'ct-ultimate-gdpr',
			// The plugin slug (typically the folder name).
			'source'             => apply_filters( 'ct_ultimate_gdpr_op_update_url', 'http://update.optimus-prime.createit.pl/updater?theme=ct-ultimate-gdpr' ),
			// The plugin source.
			'required'           => false,
			// If false, the plugin is only 'recommended' instead of required.
			'version'            => apply_filters( 'ct_plugin_op_version', '1.2', 'ct-ultimate-gdpr' ),
			// E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false,
			// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false,
			// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '',
			// If set, overrides default API URL and points to an external URL.
			'is_callable'        => 'ct_ultimate_gdpr_url',
			// If set, this callable will be be checked for availability to determine if a plugin is active.
		),

	);

	tgmpa( $plugins, $config );

}

// add a get parameter to make this plugin update request distinct
add_filter( 'plugin_action_links_ct-ultimate-gdpr/ct-ultimate-gdpr.php', 'ct_ultimate_gpdr_plugin_action_links_filter', 30 );
function ct_ultimate_gpdr_plugin_action_links_filter( $actions ) {

	if ( ! isset( $actions['update'] ) ) {
		return $actions;
	}

	$actions['update'] = sprintf(
		'<a href="%1$s" title="%2$s" class="edit">%3$s</a>',
		esc_url( add_query_arg(
				array(
					'plugin_status' => urlencode( 'update' ),
					'ct-tgmpa'      => 1,
				),
				TGM_Plugin_Activation::get_instance()->get_tgmpa_url() )
		),
		esc_attr__( 'This plugin needs to be updated to be compatible with your theme.', 'ct-ultimate-gdpr' ),
		esc_html__( 'Update Required', 'ct-ultimate-gdpr' )
	);


	return $actions;
}

// if a request for this plugin update, bypass avada tgmpa
if ( ct_ultimate_gdpr_get_value( 'ct-tgmpa', $_GET ) || ct_ultimate_gdpr_get_value( 'plugin', $_GET ) == 'ct-ultimate-gdpr' ) {

	class Avada_TGM_Plugin_Activation {};
	function avada_tgmpa() {};

}

// declare a plugin to update
add_filter( 'ct_optimus_prime_plugin_update_plugins', 'ct_ultimate_gpdr_add_plugin_update' );
function ct_ultimate_gpdr_add_plugin_update( $plugins ) {

	$plugins['ct-ultimate-gdpr'] = array(
		'file'    => dirname( dirname( __DIR__ ) ) . '/ct-ultimate-gdpr.php',
		'slug'    => 'ct-ultimate-gdpr',
		'version' => ct_ultimate_gdpr_get_plugin_version(),
	);

	return $plugins;
}