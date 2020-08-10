<?php
/*
	Copyright (C) 2015-20 CERBER TECH INC., https://cerber.tech
	Copyright (C) 2015-20 CERBER TECH INC., https://wpcerber.com

    Licenced under the GNU GPL.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/*

*========================================================================*
|                                                                        |
|	       ATTENTION!  Do not change or edit this file!                  |
|                                                                        |
*========================================================================*

*/



// If this file is called directly, abort executing.
if ( ! defined( 'WPINC' ) ) { exit; }

// Processed by WP Settings API
define( 'CERBER_OPT', 'cerber-main' );
define( 'CERBER_OPT_H', 'cerber-hardening' );
define( 'CERBER_OPT_U', 'cerber-users' );
define( 'CERBER_OPT_A', 'cerber-antispam' );
define( 'CERBER_OPT_C', 'cerber-recaptcha' );
define( 'CERBER_OPT_N', 'cerber-notifications' );
define( 'CERBER_OPT_T', 'cerber-traffic' );
define( 'CERBER_OPT_S', 'cerber-scanner' );
define( 'CERBER_OPT_E', 'cerber-schedule' );
define( 'CERBER_OPT_P', 'cerber-policies' );
define( 'CERBER_OPT_US', 'cerber-user_shield' );
define( 'CERBER_OPT_OS', 'cerber-opt_shield' );
define( 'CERBER_OPT_SL', 'cerber-nexus-slave' );
define( 'CERBER_OPT_MA', 'cerber-nexus_master' );

// Processed by Cerber
define( 'CERBER_SETTINGS', 'cerber_settings' );
define( 'CERBER_GEO_RULES', 'geo_rule_set' );
// A new, united settings entry
// @since 8.5.9.1
define( 'CERBER_CONFIG', 'cerber_configuration' );

/**
 * A set of Cerber settings (WP options)
 *
 * @param bool $all
 * @return array
 */
function cerber_get_setting_list( $all = false ) {
	$ret = array( CERBER_SETTINGS, CERBER_OPT, CERBER_OPT_H, CERBER_OPT_U, CERBER_OPT_A, CERBER_OPT_C, CERBER_OPT_N, CERBER_OPT_T, CERBER_OPT_S, CERBER_OPT_E, CERBER_OPT_P, CERBER_OPT_SL, CERBER_OPT_MA, CERBER_OPT_US, CERBER_OPT_OS );

	if ( $all ) {
		$ret = array_merge( $ret, array( CERBER_GEO_RULES, CERBER_CONFIG ) );
	}

	return $ret;
}

function cerber_settings_config( $args = array() ) {
	if ( $args && ! is_array( $args ) ) {
		return false;
	}

	// WP setting is: 'cerber-'.$screen_id
	$screens = array(
		'main'          => array( 'boot', 'liloa', 'stspec', 'proactive', 'custom', 'citadel', 'activity', 'prefs' ),
		'users'         => array( 'us', 'pdata' ),
		'hardening'     => array( 'hwp', 'rapi' ),
		'notifications' => array( 'notify', 'pushit', 'reports' ),
		'traffic'       => array( 'tmain', 'tierrs', 'tlog' ),
		'scanner'       => array( 'smain' ),
		'schedule'      => array( 's1', 's2' ),
		'policies'      => array( 'scanpls', 'scanrecover', 'scanexcl' ),
		'antispam'      => array( 'antibot', 'antibot_more', 'commproc' ),
		'recaptcha'     => array( 'recap' ),
		'user_shield'   => array( 'acc_protect', 'role_protect' ),
		'opt_shield'    => array( 'opt_protect' ),
		'nexus-slave'   => array( 'slave_settings' ),
		'nexus_master'  => array( 'master_settings' ),
	);

	$add = crb_addon_settings_config( $args );

	if ( ! empty( $add['screens'] ) ) {
		$screens = array_merge( $screens, $add['screens'] );
	}

	// Pushbullet devices
	$pb_set = array();
	if ( cerber_is_admin_page( false, array( 'tab' => 'notifications' ) ) ) {
		$pb_set = cerber_pb_get_devices();
		if ( is_array( $pb_set ) ) {
			if ( ! empty( $pb_set ) ) {
				$pb_set = array( 'all' => __( 'All connected devices', 'wp-cerber' ) ) + $pb_set;
			}
			else {
				$pb_set = array( 'N' => __( 'No devices found', 'wp-cerber' ) );
			}
		}
		else {
			$pb_set = array( 'N' => __( 'Not available', 'wp-cerber' ) );
		}
	}

	// Descriptions
	if ( ! cerber_is_permalink_enabled() ) {
		$custom = '<span style="color:#DF0000;">' . __( 'Please enable Permalinks to use this feature. Set Permalink Settings to something other than Default.', 'wp-cerber' ) . '</span>';
	}
	else {
		$custom = __( 'Be careful about enabling these options.', 'wp-cerber' ) . ' ' . __( 'If you forget your Custom login URL, you will be unable to log in.', 'wp-cerber' );
	}

	$no_wcl = __( 'These restrictions do not apply to IP addresses in the White IP Access List', 'wp-cerber' );

	$sections = array(
		'boot'      => array(
			'name'   => __( 'Plugin initialization', 'wp-cerber' ),
			'fields' => array(
				'boot-mode' => array(
					'title' => __( 'Load security engine', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array(
						__( 'Legacy mode', 'wp-cerber' ),
						__( 'Standard mode', 'wp-cerber' )
					)
				),
			),
		),
		'liloa'     => array(
			'name'   => __( 'Limit login attempts', 'wp-cerber' ),
			'fields' => array(
				'attempts'   => array(
					'title' => __( 'Limit', 'wp-cerber' ),
					'type'  => 'attempts',
				),
				'lockout' => array(
					'type'  => 'digits',
					'title' => __( 'Lockout duration', 'wp-cerber' ),
					'label' => __( 'minutes', 'wp-cerber' ),
				),
				'aggressive' => array(
					'title' => __( 'Aggressive lockout', 'wp-cerber' ),
					'type'  => 'aggressive',
				),
				'limitwhite' => array(
					'title' => __( 'Use White IP Access List', 'wp-cerber' ),
					'label' => __( 'Apply limit login rules to IP addresses in the White IP Access List', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
			),
		),
		'proactive' => array(
			'name' => __( 'Proactive security rules', 'wp-cerber' ),
			'desc' => __( 'Make your protection smarter!', 'wp-cerber' ),
			'fields' => array(
				'subnet'     => array(
					'title' => __( 'Block subnet', 'wp-cerber' ),
					'label' => __( 'Always block entire subnet Class C of intruders IP', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'nonusers'   => array(
					'title' => __( 'Non-existing users', 'wp-cerber' ),
					'label' => __( 'Immediately block IP when attempting to log in with a non-existing username', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'noredirect' => array(
					'title' => __( 'Disable dashboard redirection', 'wp-cerber' ),
					'label' => __( 'Disable automatic redirection to the login page when /wp-admin/ is requested by an unauthorized request', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'wplogin'    => array(
					'title' => __( 'Request wp-login.php', 'wp-cerber' ),
					'label' => __( 'Immediately block IP after any request to wp-login.php', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'page404'    => array(
					'title' => __( 'Display 404 page', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array(
						__( 'Use 404 template from the active theme', 'wp-cerber' ),
						__( 'Display simple 404 page', 'wp-cerber' )
					)
				),

			),
		),
		'custom'    => array(
			'name'   => __( 'Custom login page', 'wp-cerber' ),
			'desc' => $custom,
			'fields' => array(
				'loginpath' => array(
					'title'     => __( 'Custom login URL', 'wp-cerber' ),
					'label'     => __( 'must not overlap with the existing pages or posts slug', 'wp-cerber' ),
					'label_pos' => 'below',
					'attr'      => array( 'title' => __( 'Custom login URL may contain Latin alphanumeric characters, dashes and underscores only', 'wp-cerber' ) ),
					'size'      => 30,
					'pattern'   => '[a-zA-Z0-9\-_]{1,100}',
				),
				'loginnowp' => array(
					'title' => __( 'Disable wp-login.php', 'wp-cerber' ),
					'label' => __( 'Block direct access to wp-login.php and return HTTP 404 Not Found Error', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
			),
		),
		'stspec'    => array(
			'name'   => __( 'Site-specific settings', 'wp-cerber' ),
			'fields' => array(
				'proxy'      => array(
					'title' => __( 'Site connection', 'wp-cerber' ),
					'label' => __( 'My site is behind a reverse proxy', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'cookiepref' => array(
					'title'       => __( 'Prefix for plugin cookies', 'wp-cerber' ),
					'attr'        => array( 'title' => __( 'Prefix may contain only Latin alphanumeric characters and underscores', 'wp-cerber' ) ),
					'placeholder' => 'Latin alphanumeric characters or underscores',
					'size'        => 24,
					'pattern'     => '[a-zA-Z0-9_]{1,24}',
				),
			),
		),
		'citadel'   => array(
			'name' => __( 'Citadel mode', 'wp-cerber' ),
			'desc' => __( 'In the Citadel mode nobody is able to log in except IPs from the White IP Access List. Active user sessions will not be affected.', 'wp-cerber' ),
			'fields' => array(
				'citadel_on' => array(
					'title'   => __( 'Enable authentication log monitoring', 'wp-cerber' ),
					//'doclink' => 'https://wpcerber.com/only-logged-in-wordpress-users/',
					'type'    => 'checkbox',
					'default' => 0,
				),
				'citadel'    => array(
					'title'   => __( 'Threshold', 'wp-cerber' ),
					'type'    => 'citadel',
					'enabler' => array( 'citadel_on' ),
				),
				'ciduration' => array(
					'title'   => __( 'Duration', 'wp-cerber' ),
					'label'   => __( 'minutes', 'wp-cerber' ),
					'type'    => 'digits',
					'enabler' => array( 'citadel_on' ),
				),
				'cinotify'   => array(
					'title' => __( 'Notifications', 'wp-cerber' ),
					'type'  => 'checkbox',
					'label' => __( 'Send notification to admin email', 'wp-cerber' ) .
					           ' <span class="crb-no-wrap">[ <a href="' . cerber_admin_link_add( array(
							'cerber_admin_do' => 'testnotify',
							'type'            => 'citadel',
						) ) . '">' . __( 'Click to send test', 'wp-cerber' ) . '</a> ]</span>',
					'enabler' => array( 'citadel_on' ),
				),
			),
		),
		'activity'  => array(
			'name'   => __( 'Activity', 'wp-cerber' ),
			'fields' => array(
				'keeplog'     => array(
					'title' => __( 'Keep log records of not logged in visitors for', 'wp-cerber' ),
					'label' => __( 'days', 'wp-cerber' ),
					//'label'  => __( 'days, not logged in visitors', 'wp-cerber' ),
					'type'  => 'digits'
				),
				'keeplog_auth'     => array(
					'title' => __( 'Keep log records of logged in users for', 'wp-cerber' ),
					'label' => __( 'days', 'wp-cerber' ),
					//'label'  => __( 'days, logged in users', 'wp-cerber' ),
					'type'  => 'digits'
				),
				'cerberlab' => array(
					'title'   => __( 'Cerber Lab connection', 'wp-cerber' ),
					'label'   => __( 'Send malicious IP addresses to the Cerber Lab', 'wp-cerber' ),
					'type'    => 'checkbox',
					'doclink' => 'https://wpcerber.com/cerber-laboratory/'
				),
				'cerberproto' => array(
					'title' => __( 'Cerber Lab protocol', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array(
						'HTTP',
						'HTTPS'
					)
				),
				'usefile'     => array(
					'title' => __( 'Use file', 'wp-cerber' ),
					'label' => __( 'Write failed login attempts to the file', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
			),
		),
		'prefs'     => array(
			'name'   => __( 'Preferences', 'wp-cerber' ),
			'fields' => array(
				'ip_extra'   => array(
					'title' => __( 'Drill down IP', 'wp-cerber' ),
					'label' => __( 'Retrieve extra WHOIS information for IP', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'dateformat' => array(
					'title'     => __( 'Date format', 'wp-cerber' ),
					'label'     => sprintf( __( 'if empty, the default format %s will be used', 'wp-cerber' ), '<b>' . date( crb_get_default_dt_format(), time() ) . '</b>' ),
					'doclink'   => 'https://wpcerber.com/date-format-setting/',
					'label_pos' => 'below',
					'size'      => 16,
				),
				'plain_date' => array(
					'title' => __( 'Date format for CSV export', 'wp-cerber' ),
					'label' => __( 'Use ISO 8601 date format for CSV export files', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'admin_lang' => array(
					'title' => __( 'Use English', 'wp-cerber' ),
					'label' => __( 'Use English for admin interface', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'no_white_my_ip' => array(
					'title' => __( 'My IP address', 'wp-cerber' ),
					'label' => __( 'Do not add my IP address to the White IP Access List upon plugin activation', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				/*'log_errors' => array(
					'title' => __( 'Log critical errors', 'wp-cerber' ),
					'type'  => 'checkbox',
				),*/
			),
		),

		'hwp'  => array(
			'name'   => __( 'Hardening WordPress', 'wp-cerber' ),
			'desc'   => $no_wcl,
			'fields' => array(
				'stopenum' => array(
					'title' => __( 'Stop user enumeration', 'wp-cerber' ),
					'label' => __( 'Block access to user pages like /?author=n', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'adminphp' => array(
					'title' => __( 'Protect admin scripts', 'wp-cerber' ),
					'label' => __( 'Block unauthorized access to load-scripts.php and load-styles.php', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'phpnoupl' => array(
					'title' => __( 'Disable PHP in uploads', 'wp-cerber' ),
					'label' => __( 'Block execution of PHP scripts in the WordPress media folder', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'nophperr' => array(
					'title' => __( 'Disable PHP error displaying', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'xmlrpc'   => array(
					'title' => __( 'Disable XML-RPC', 'wp-cerber' ),
					'label' => __( 'Block access to the XML-RPC server (including Pingbacks and Trackbacks)', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'nofeeds'  => array(
					'title' => __( 'Disable feeds', 'wp-cerber' ),
					'label' => __( 'Block access to the RSS, Atom and RDF feeds', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
			),
		),
		'rapi' => array(
			'name'    => __( 'Access to WordPress REST API', 'wp-cerber' ),
			'desc'    => __( 'Restrict or completely block access to the WordPress REST API according to your needs', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/restrict-access-to-wordpress-rest-api/',
			'fields'  => array(
				'norestuser' => array(
					'title' => __( 'Stop user enumeration', 'wp-cerber' ),
					'label' => __( "Block access to users' data via REST API", 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'norest'     => array(
					'title' => __( 'Disable REST API', 'wp-cerber' ),
					'label' => __( 'Block access to WordPress REST API except any of the following', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'restauth'   => array(
					'title'   => __( 'Logged in users', 'wp-cerber' ),
					'label'   => __( 'Allow REST API for logged in users', 'wp-cerber' ),
					'type'    => 'checkbox',
					'enabler' => array( 'norest' ),
				),
				'restroles'  => array(
					'title'   => __( 'Allow REST API for these roles', 'wp-cerber' ),
					'type'    => 'role_select',
					'enabler' => array( 'norest' ),
				),
				'restwhite'  => array(
					'title'     => __( 'Allow these namespaces', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => "\n",
					'list'      => true,
					'label'     => __( 'Specify REST API namespaces to be allowed if REST API is disabled. One string per line.', 'wp-cerber' ),
					'doclink'   => 'https://wpcerber.com/restrict-access-to-wordpress-rest-api/',
					'enabler'   => array( 'norest' ),
				),
			),
		),

		'acc_protect' => array(
			'name'   => __( 'Protect user accounts', 'wp-cerber' ),
			//'desc'   => 'These policies prevent site takeover (admin dashboard hijacking) by creating accounts with administrator privileges',
			'desc'   => 'These security measures prevent site takeover by preventing bad actors from creating additional administrator accounts or user privilege escalation',
			'fields' => array(
				'ds_4acc'     => array(
					'label'   => __( 'Restrict user account creation and user management with the following policies', 'wp-cerber' ),
					//'doclink' => 'https://wpcerber.com/only-logged-in-wordpress-users/',
					'type'    => 'checkbox',
					//'pro'     => 1
				),
				'ds_regs_roles' => array(
					'label'   => __( 'User registrations are limited to these roles', 'wp-cerber' ),
					//'title'   => __( 'Roles restricted to new user registrations', 'wp-cerber' ),
					'type'    => 'role_select',
					'enabler' => array( 'ds_4acc' ),
				),
				'ds_add_acc'  => array(
					'label'   => __( 'Users with these roles are permitted to create new accounts', 'wp-cerber' ),
					'type'    => 'role_select',
					'enabler' => array( 'ds_4acc' ),
				),
				'ds_edit_acc'    => array(
					'label'   => __( 'Users with these roles are permitted to change sensitive user data', 'wp-cerber' ),
					'type'    => 'role_select',
					'enabler' => array( 'ds_4acc' ),
				),
				'ds_4acc_acl' => array(
					'label'   => __( 'Do not apply these policies to the IP addresses in the White IP Access List', 'wp-cerber' ),
					'type'    => 'checkbox',
					'default' => 0,
					'enabler' => array( 'ds_4acc' ),
				),
			),
		),
		'role_protect' => array(
			'name'   => __( 'Protect user roles', 'wp-cerber' ),
			'desc'   => 'These security measures prevent site takeover by preventing bad actors from creating new roles or role capabilities escalation',
			'fields' => array(
				'ds_4roles'       => array(
					'label'   => __( "Restrict roles and capabilities management with the following policies", 'wp-cerber' ),
					//'doclink' => 'https://wpcerber.com/only-logged-in-wordpress-users/',
					'type'    => 'checkbox',
					'default' => 0,
				),
				'ds_add_role'    => array(
					'label'   => __( 'Users with these roles are permitted to add new roles', 'wp-cerber' ),
					'type'    => 'role_select',
					'enabler' => array( 'ds_4roles' ),
				),
				'ds_edit_role' => array(
					'label'   => __( "Users with these roles are permitted to change role capabilities", 'wp-cerber' ),
					'type'    => 'role_select',
					'enabler' => array( 'ds_4roles' ),
				),
				'ds_4roles_acl'   => array(
					'label'   => __( 'Do not apply these policies to the IP addresses in the White IP Access List', 'wp-cerber' ),
					'type'    => 'checkbox',
					'default' => 0,
					'enabler' => array( 'ds_4roles' ),
				),
			),
		),
		'opt_protect' => array(
			'name'   => __( 'Protect site settings', 'wp-cerber' ),
			'desc'   => 'These security measures prevent malware injection by preventing bad actors from altering vital site settings',
			'fields' => array(
				'ds_4opts'       => array(
					'label'   => __( "Restrict updating site settings with the following policies", 'wp-cerber' ),
					//'doclink' => 'https://wpcerber.com/only-logged-in-wordpress-users/',
					'type'    => 'checkbox',
					'default' => 0,
				),
				'ds_4opts_roles'    => array(
					'label'   => __( 'Users with these roles are permitted to change protected settings', 'wp-cerber' ),
					'type'    => 'role_select',
					'enabler' => array( 'ds_4opts' ),
				),
				'ds_4opts_list' => array(
					'label'   => __( 'Protected settings', 'wp-cerber' ),
					'type'    => 'checkbox_set',
					'set'     => CRB_DS::get_settings_list(),
					'enabler' => array( 'ds_4opts' ),
				),
				'ds_4opts_acl'   => array(
					'label'   => __( 'Do not apply these policies to the IP addresses in the White IP Access List', 'wp-cerber' ),
					'type'    => 'checkbox',
					'default' => 0,
					'enabler' => array( 'ds_4opts' ),
				),
			),
		),


		'us' => array(
			//'name'   => __( 'User related settings', 'wp-cerber' ),
			//'info'   => __( 'User related settings', 'wp-cerber' ),
			'fields' => array(
				'authonly'      => array(
					'title'   => __( 'Authorized users only', 'wp-cerber' ),
					'label'   => __( 'Only registered and logged in website users have access to the website', 'wp-cerber' ),
					'doclink' => 'https://wpcerber.com/only-logged-in-wordpress-users/',
					'type'    => 'checkbox',
					'default' => 0,
				),
				'authonlyacl'   => array(
					'title'   => __( 'Use White IP Access List', 'wp-cerber' ),
					'label'   => __( 'Do not apply these policy to the IP addresses in the White IP Access List', 'wp-cerber' ),
					'type'    => 'checkbox',
					'default' => 0,
					'enabler' => array( 'authonly' ),
				),
				'authonlymsg'   => array(
					'title'       => __( 'User Message', 'wp-cerber' ),
					'placeholder' => 'An optional login form message',
					'type'        => 'textarea',
					//'filter'      => 'strip_tags',
					'default'     => __( 'Only registered and logged in users are allowed to view this website', 'wp-cerber' ),
					'enabler'     => array( 'authonly' ),
					'class'       => ''
				),
				'authonlyredir' => array(
					'title'       => __( 'Redirect to URL', 'wp-cerber' ),
					//'label'       => __( 'if empty, visitors are redirected to the login page', 'wp-cerber' )
					'placeholder' => 'http://',
					'type'        => 'url',
					'default'     => '',
					'maxlength'   => 1000,
					'enabler'     => array( 'authonly' ),
				),
				'reglimit'      => array(
					'title'   => __( 'Registration limit', 'wp-cerber' ),
					'type'    => 'reglimit',
					'default' => array( 3, 60 ),
					'pro'     => 1
				),
				'emrule' => array(
					'title' => __( 'Restrict email addresses', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array(
						__( 'No restrictions', 'wp-cerber' ),
						__( 'Deny all email addresses that match the following', 'wp-cerber' ),
						__( 'Permit only email addresses that match the following', 'wp-cerber' ),
					)
				),
				'emlist'    => array(
					'title' => '',
					'label'     => __( 'Specify email addresses, wildcards or REGEX patterns. Use comma to separate items.', 'wp-cerber' ). ' ' . __( 'To specify a REGEX pattern wrap a pattern in two forward slashes.', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => ',',
					'list'      => true,
					'default'   => array(),
				),
				'prohibited'    => array(
					'title'     => __( 'Prohibited usernames', 'wp-cerber' ),
					'label'     => __( 'Usernames from this list are not allowed to log in or register. Any IP address, have tried to use any of these usernames, will be immediately blocked. Use comma to separate logins.', 'wp-cerber' ) . ' ' . __( 'To specify a REGEX pattern wrap a pattern in two forward slashes.', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => ',',
					'list'      => true,
					'default'   => array(),
				),
				'auth_expire' => array(
					'title'   => __( 'User session expiration time', 'wp-cerber' ),
					'label'   => __( 'minutes (leave empty to use the default WordPress value)', 'wp-cerber' ),
					'default' => '',
					'size'    => 6,
					'type'    => 'digits',
				),
				'usersort'      => array(
					'title'   => __( 'Sort users in dashboard', 'wp-cerber' ),
					'label'   => __( 'by date of registration', 'wp-cerber' ),
					'default' => '',
					'type'    => 'checkbox',
				),
			)
		),

		'pdata' => array(
			'name'    => __( 'Personal Data', 'wp-cerber' ),
			//'desc'   => __( 'These features help your organization to be in compliance with data privacy laws', 'wp-cerber' ),
			'desc'    => __( 'These features help your organization to be in compliance with personal data protection laws', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/wordpress/gdpr/',
			'fields'  => array(
				'pdata_erase' => array(
					'title'   => __( 'Enable data erase', 'wp-cerber' ),
					//'label'   => __( 'Only registered and logged in website users have access to the website', 'wp-cerber' ),
					//'doclink' => 'https://wpcerber.com/only-logged-in-wordpress-users/',
					'type'    => 'checkbox',
					'default' => 0,
				),
				'pdata_sessions' => array(
					'title'   => __( 'Terminate user sessions', 'wp-cerber' ),
					'label'   => __( 'Delete user sessions data when user data is erased', 'wp-cerber' ),
					'type'    => 'checkbox',
					'default' => 0,
					'enabler' => array( 'pdata_erase' ),
				),
				'pdata_export' => array(
					'title'   => __( 'Enable data export', 'wp-cerber' ),
					//'label'   => __( 'Only registered and logged in website users have access to the website', 'wp-cerber' ),
					//'doclink' => 'https://wpcerber.com/only-logged-in-wordpress-users/',
					'type'    => 'checkbox',
					'default' => 0,
				),
				'pdata_act'    => array(
					'title'   => __( 'Include activity log events', 'wp-cerber' ),
					'type'    => 'checkbox',
					'default' => 0,
					'enabler' => array( 'pdata_export' ),
				),
				'pdata_trf'    => array(
					'title'   => __( 'Include traffic log entries', 'wp-cerber' ),
					'type'    => 'checkbox_set',
					'set'     => array(
						1 => __( 'Request URL', 'wp-cerber' ),
						2 => __( 'Form fields data', 'wp-cerber' ),
						3 => __( 'Cookies', 'wp-cerber' )
					),
					'enabler' => array( 'pdata_export' ),
				),
			),
		),

		'notify' => array(
			'name'    => __( 'Email notifications', 'wp-cerber' ),
			'desc'    => 'Configure email notifications, reports, and alerts, your way',
			'doclink' => 'https://wpcerber.com/wordpress-notifications-made-easy/',
			'fields'  => array(
				'notify' => array(
					'title' => __( 'Lockout notifications', 'wp-cerber' ),
					'type'  => 'notify',
				),
				'email'          => array(
					'title'       => __( 'Email Address', 'wp-cerber' ),
					'placeholder' => __( 'Use comma to specify multiple values', 'wp-cerber' ),
					'delimiter'   => ',',
					'list'        => true,
					'maxlength'   => 1000,
					'label'       => sprintf( __( 'if empty, the website administrator email %s will be used', 'wp-cerber' ), '<b>' . get_site_option( 'admin_email' ) . '</b>' )
				),
				'emailrate'      => array(
					'title'   => __( 'Notification limit', 'wp-cerber' ),
					'label'   => __( 'notifications are allowed per hour (0 means unlimited)', 'wp-cerber' ),
					'type'    => 'digits',
				),
				'notify-new-ver' => array(
					'title' => __( 'New version is available', 'wp-cerber' ),
					'type'  => 'checkbox'
				),
			),
		),
		'pushit' => array(
			'name'    => __( 'Push notifications', 'wp-cerber' ),
			'desc'    => __( 'Get notified instantly with mobile and desktop notifications', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/wordpress-mobile-and-browser-notifications-pushbullet/',
			'fields'  => array(
				'pbtoken' => array(
					'title' => __( 'Pushbullet access token', 'wp-cerber' ),
				),
				'pbdevice' => array(
					'title' => __( 'Pushbullet device', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => $pb_set
				),
			),
		),
		'reports' => array(
			'name'   => __( 'Weekly reports', 'wp-cerber' ),
			'desc'   => __( 'Weekly report is a summary of all activities and suspicious events occurred during the last seven days', 'wp-cerber' ),
			'fields' => array(
				'enable-report' => array(
					'title' => __( 'Enable reporting', 'wp-cerber' ),
					'type'  => 'checkbox'
				),
				'wreports'      => array(
					'title' => __( 'Send reports on', 'wp-cerber' ),
					'type'  => 'reptime',
					'enabler' => array( 'enable-report' ),
				),
				'email-report'  => array(
					'title'       => __( 'Email Address', 'wp-cerber' ),
					'label'       => __( 'if empty, the email addresses from the notification settings will be used', 'wp-cerber' ),
					'placeholder' => __( 'Use comma to specify multiple values', 'wp-cerber' ),
					'delimiter'   => ',',
					'list'        => true,
					'maxlength'   => 1000,
					'enabler' => array( 'enable-report' ),
				),
			),
		),

		'tmain' => array(
			'name'    => __( 'Traffic Inspection', 'wp-cerber' ),
			'desc'    => __( 'Traffic Inspector is a context-aware web application firewall (WAF) that protects your website by recognizing and denying malicious HTTP requests', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/traffic-inspector-in-a-nutshell/',
			'fields'  => array(
				'tienabled' => array(
					'title' => __( 'Enable traffic inspection', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array(
						__( 'Disabled', 'wp-cerber' ),
						__( 'Maximum compatibility', 'wp-cerber' ),
						__( 'Maximum security', 'wp-cerber' )
					),
				),
				'tiipwhite' => array(
					'title' => __( 'Use White IP Access List', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'tiwhite'   => array(
					'title'     => __( 'Request whitelist', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => "\n",
					'list'      => true,
					'label'     => __( 'Enter a request URI to exclude the request from inspection. One item per line.', 'wp-cerber' ) . ' ' . __( 'To specify a REGEX pattern, enclose a whole line in two braces.', 'wp-cerber' ),
					'doclink'   => 'https://wpcerber.com/wordpress-probing-for-vulnerable-php-code/',
				),
			),
		),
		'tierrs' => array(
			'name'   => __( 'Erroneous Request Shielding', 'wp-cerber' ),
			//'desc'   => 'Block IP addresses that generate excessive HTTP 404 requests.',
			'desc'   => __( 'Block IP addresses that send excessive requests for non-existing pages or scan website for security breaches', 'wp-cerber' ),
			'fields' => array(
				'tierrmon' => array(
					'title' => __( 'Enable error shielding', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array(
						__( 'Disabled', 'wp-cerber' ),
						__( 'Maximum compatibility', 'wp-cerber' ),
						__( 'Maximum security', 'wp-cerber' )
					)
				),
				'tierrnoauth' => array(
					'title' => __( 'Ignore logged in users', 'wp-cerber' ),
					'type'  => 'checkbox',
					//'enabler' => array( 'tierrmon', 1 ),
				),
			),
		),
		'tlog'   => array(
			'name'    => __( 'Traffic Logging', 'wp-cerber' ),
			'desc'    => __( 'Enable optional traffic logging if you need to monitor suspicious and malicious activity or solve security issues', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/wordpress-traffic-logging/',
			'fields'  => array(
				'timode'         => array(
					'title' => __( 'Logging mode', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array(
						0 => __( 'Logging disabled', 'wp-cerber' ),
						3 => __( 'Minimal', 'wp-cerber' ),
						1 => __( 'Smart', 'wp-cerber' ),
						2 => __( 'All traffic', 'wp-cerber' )
					),
				),
				'tinocrabs'      => array(
					'title' => __( 'Do not log known crawlers', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'tinolocs' => array(
					'title'     => __( 'Do not log these locations', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => "\n",
					'list'      => true,
					'label'     => __( 'Specify URL paths to exclude requests from logging. One item per line.', 'wp-cerber' ) . ' ' . __( 'To specify a REGEX pattern, enclose a whole line in two braces.', 'wp-cerber' ),
				),
				'tinoua'   => array(
					'title'     => __( 'Do not log these User-Agents', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => "\n",
					'list'      => true,
					'label'     => __( 'Specify User-Agents to exclude requests from logging. One item per line.', 'wp-cerber' ),
				),
				'tifields'       => array(
					'title' => __( 'Save request fields', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'timask'         => array(
					'title'       => __( 'Mask these form fields', 'wp-cerber' ),
					'maxlength'   => 1000,
					'placeholder' => __( 'Use comma to specify multiple values', 'wp-cerber' ),
					'delimiter'   => ',',
					'list'        => true,
				),
				'tihdrs'         => array(
					'title' => __( 'Save request headers', 'wp-cerber' ),
					'label' => __( '', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'tisenv'         => array(
					'title' => __( 'Save $_SERVER', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'ticandy'        => array(
					'title' => __( 'Save request cookies', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'tiphperr'       => array(
					'title' => __( 'Save software errors', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'tithreshold'    => array(
					'title' => __( 'Page generation time threshold', 'wp-cerber' ),
					'label' => __( 'milliseconds', 'wp-cerber' ),
					'type'  => 'digits',
					'size'  => 4,
				),
				'tikeeprec'      => array(
					'title' => __( 'Keep log records of not logged in visitors for', 'wp-cerber' ),
					'label' => __( 'days', 'wp-cerber' ),
					'type'  => 'digits',
					'size'  => 4,
				),
				'tikeeprec_auth' => array(
					'title' => __( 'Keep log records of logged in users for', 'wp-cerber' ),
					'label' => __( 'days', 'wp-cerber' ),
					'type'  => 'digits',
					'size'  => 4,
				),
			),
		),

		'smain' => array(
			'name'    => __( 'Scanner settings', 'wp-cerber' ),
			'desc'    => __( 'The scanner monitors file changes, verifies the integrity of WordPress, plugins, and themes, and detects malware', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/wordpress-security-scanner/',
			'fields'  => array(
				'scan_cpt'      => array(
					'title'     => __( 'Custom signatures', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => "\n",
					'list'      => true,
					'label'     => __( 'Specify custom PHP code signatures. One item per line. To specify a REGEX pattern, enclose a whole line in two braces.', 'wp-cerber' ) . ' <a target="_blank" href="https://wpcerber.com/malware-scanner-settings/">Read more</a>'
				),
				'scan_uext'     => array(
					'title'     => __( 'Unwanted file extensions', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => ",",
					'list'      => true,
					'label'     => __( 'Specify file extensions to search for. Full scan only. Use comma to separate items.', 'wp-cerber' )
				),
				'scan_exclude'  => array(
					'title'     => __( 'Directories to exclude', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => "\n",
					'list'      => true,
					'label'     => __( 'Specify directories to exclude from scanning. One directory per line.', 'wp-cerber' )
				),
				'scan_inew'     => array(
					'title' => __( 'Monitor new files', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array(
						0 => __( 'Disabled', 'wp-cerber' ),
						1 => __( 'Executable files', 'wp-cerber' ),
						2 => __( 'All files', 'wp-cerber' ),
					)
				),
				'scan_imod'     => array(
					'title' => __( 'Monitor modified files', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array(
						0 => __( 'Disabled', 'wp-cerber' ),
						1 => __( 'Executable files', 'wp-cerber' ),
						2 => __( 'All files', 'wp-cerber' ),
					)
				),
				'scan_chmod'    => array(
					'title' => __( 'Change file permissions when necessary', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'scan_tmp'      => array(
					'title' => __( 'Scan temporary directory', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'scan_sess'     => array(
					'title' => __( 'Scan session directory', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'scan_debug'    => array(
					'title' => __( 'Enable diagnostic logging', 'wp-cerber' ),
					'label' => 'Once enabled, the log is available here: <a target="_blank" href="' . cerber_admin_link( 'diag-log' ) . '">Diagnostic Log</a>',
					'type'  => 'checkbox',
				),
				'scan_qcleanup' => array(
					'title' => __( 'Delete quarantined files after', 'wp-cerber' ),
					'type'  => 'digits',
					'label' => __( 'days', 'wp-cerber' ),
				),

			),
		),

		's1' => array(
			'name'    => __( 'Automated recurring scan schedule', 'wp-cerber' ),
			'desc'    => __( 'The scanner automatically scans the website, removes malware and sends email reports with the results of a scan', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/automated-recurring-malware-scans/',
			'fields'  => array(
				'scan_aquick' => array(
					'title' => __( 'Launch Quick Scan', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => cerber_get_qs(),
				),
				'scan_afull'  => array(
					'title'   => __( 'Launch Full Scan', 'wp-cerber' ),
					'type'    => 'timepicker',
					'enabled' => 'once a day at',
				),
			),
		),
		's2' => array(
			'name'    => __( 'Scan results reporting', 'wp-cerber' ),
			'desc'    => __( 'Configure what issues to include in the email report and the condition for sending reports', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/automated-recurring-malware-scans/',
			'fields'  => array(
				'scan_reinc'   => array(
					'title' => __( 'Report an issue if any of the following is true', 'wp-cerber' ),
					'type'  => 'checkbox_set',
					'set'   => array(
						           1 => __( 'Low severity', 'wp-cerber' ),
						           2 => __( 'Medium severity', 'wp-cerber' ),
						           3 => __( 'High severity', 'wp-cerber' )
					           ) + cerber_get_issue_label( array( CERBER_IMD, CERBER_UXT, 50, 51, CERBER_VULN ) ),
				),
				'scan_relimit' => array(
					'title' => __( 'Send email report', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array(
						1 => __( 'After every scan', 'wp-cerber' ),
						3 => __( 'If any changes in scan results occurred', 'wp-cerber' ),
						5 => __( 'If new issues found', 'wp-cerber' ),
					)
				),
				'scan_isize'   => array(
					'title' => __( 'Include file sizes', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'scan_ierrors' => array(
					'title' => __( 'Include scan errors', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'email-scan'   => array(
					'title'       => __( 'Email Address', 'wp-cerber' ),
					'label'       => __( 'if empty, the email addresses from the notification settings will be used', 'wp-cerber' ),
					'placeholder' => __( 'Use comma to specify multiple values', 'wp-cerber' ),
					'delimiter'   => ',',
					'list'        => true,
					'maxlength'   => 1000,
				),
			),
		),

		'scanpls'     => array(
			'name'    => __( 'Automatic cleanup of malware and suspicious files', 'wp-cerber' ),
			'desc'    => __( 'These policies are automatically enforced at the end of every scheduled scan based on its results. All affected files are moved to the quarantine', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/automatic-malware-removal-wordpress/',
			'fields'  => array(
				'scan_delunatt'  => array(
					'title' => __( 'Delete unattended files', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'scan_delupl'    => array(
					'title' => __( 'Files in the uploads folder', 'wp-cerber' ),
					'type'  => 'checkbox_set',
					'set'   => array(
						1 => __( 'Low severity', 'wp-cerber' ),
						2 => __( 'Medium severity', 'wp-cerber' ),
						3 => __( 'High severity', 'wp-cerber' )
					),
				),
				'scan_delunwant' => array(
					'title' => __( 'Files with unwanted extensions', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
			),
		),
		'scanrecover' => array(
			'name'   => __( 'Automatic recovery of modified and infected files', 'wp-cerber' ),
			'fields' => array(
				'scan_recover_wp' => array(
					'title' => __( 'Recover WordPress files', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'scan_recover_pl' => array(
					'title' => __( 'Recover plugins files', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
			),
		),
		'scanexcl'    => array(
			'name'   => __( 'Exclusions', 'wp-cerber' ),
			'desc'   => __( 'These files will never be deleted during automatic cleanup.', 'wp-cerber' ),
			'fields' => array(
				'scan_nodeltemp' => array(
					'title' => __( 'Files in the temporary directory', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'scan_nodelsess' => array(
					'title' => __( 'Files in the sessions directory', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'scan_delexdir'  => array(
					'title'     => __( 'Files in these directories', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => "\n",
					'list'      => true,
					'label'     => __( 'Use absolute paths. One item per line.', 'wp-cerber' )
				),
				'scan_delexext'  => array(
					'title'     => __( 'Files with these extensions', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => ",",
					'list'      => true,
					'label'     => __( 'Use comma to separate items.', 'wp-cerber' )
				),
			),
		),


		'antibot'      => array(
			'name'    => __( 'Cerber anti-spam engine', 'wp-cerber' ),
			'desc'    => __( 'Spam protection for comment, registration and contact forms on a website', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/antispam-for-wordpress-contact-forms/',
			'fields'  => array(
				'botscomm' => array(
					'title' => __( 'Comment form', 'wp-cerber' ),
					'label' => __( 'Protect comment form with bot detection engine', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'botsreg'  => array(
					'title' => __( 'Registration form', 'wp-cerber' ),
					'label' => __( 'Protect registration form with bot detection engine', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'botsany'  => array(
					'title' => __( 'Other forms', 'wp-cerber' ),
					'label' => __( 'Protect all forms on the website with bot detection engine', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
			)
		),
		'antibot_more' => array(
			'name'   => __( 'Adjust anti-spam engine', 'wp-cerber' ),
			'desc'   => __( 'These settings enable you to fine-tune the behavior of anti-spam algorithms and avoid false positives', 'wp-cerber' ),
			'fields' => array(
				'botssafe' => array(
					'title' => __( 'Safe mode', 'wp-cerber' ),
					'label' => __( 'Use less restrictive policies (allow AJAX)', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'botsnoauth' => array(
					'title' => __( 'Logged in users', 'wp-cerber' ),
					'label' => __( 'Disable bot detection engine for logged in users', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'botswhite'  => array(
					'title'     => __( 'Query whitelist', 'wp-cerber' ),
					'label'     => __( 'Enter a part of query string or query path to exclude a request from inspection by the engine. One item per line.', 'wp-cerber' ),
					'type'      => 'textarea',
					'delimiter' => "\n",
					'list'      => true,
					'doclink' => 'https://wpcerber.com/antispam-exception-for-specific-http-request/',
				),
			)
		),
		'commproc'     => array(
			'name'   => __( 'Comment processing', 'wp-cerber' ),
			'desc'   => __( 'How the plugin processes comments submitted through the standard comment form', 'wp-cerber' ),
			'fields' => array(
				'spamcomm' => array(
					'title' => __( 'If a spam comment detected', 'wp-cerber' ),
					'type'  => 'select',
					'set'   => array( __( 'Deny it completely', 'wp-cerber' ), __( 'Mark it as spam', 'wp-cerber' ) )
				),
				'trashafter' => array(
					'title'   => __( 'Trash spam comments', 'wp-cerber' ),
					'type'    => 'digits',
					'enabled' => __( 'Move spam comments to trash after' ),
					'label'   => __( 'days', 'wp-cerber' ),
				),
			)
		),

		'recap' => array(
			'name' => __( 'reCAPTCHA settings', 'wp-cerber' ),
			'desc' => __( 'Before you can start using reCAPTCHA, you have to obtain Site key and Secret key on the Google website', 'wp-cerber' ),
			'doclink' => 'https://wpcerber.com/how-to-setup-recaptcha/',
			'fields' => array(
				'sitekey'       => array(
					'title' => __( 'Site key', 'wp-cerber' ),
					'type'  => 'text',
				),
				'secretkey'     => array(
					'title' => __( 'Secret key', 'wp-cerber' ),
					'type'  => 'text',
				),
				'invirecap'     => array(
					'title' => __( 'Invisible reCAPTCHA', 'wp-cerber' ),
					'label' => __( 'Enable invisible reCAPTCHA', 'wp-cerber' ) . ' ' . __( '(do not enable it unless you get and enter the Site and Secret keys for the invisible version)', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'recapreg'      => array(
					'title' => __( 'Registration form', 'wp-cerber' ),
					'label' => __( 'Enable reCAPTCHA for WordPress registration form', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'recapwooreg'   => array(
					'title' => '',
					'label' => __( 'Enable reCAPTCHA for WooCommerce registration form', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'recaplost'     => array(
					'title' => __( 'Lost password form', 'wp-cerber' ),
					'label' => __( 'Enable reCAPTCHA for WordPress lost password form', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'recapwoolost'  => array(
					'title' => '',
					'label' => __( 'Enable reCAPTCHA for WooCommerce lost password form', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'recaplogin'    => array(
					'title' => __( 'Login form', 'wp-cerber' ),
					'label' => __( 'Enable reCAPTCHA for WordPress login form', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'recapwoologin' => array(
					'title' => '',
					'label' => __( 'Enable reCAPTCHA for WooCommerce login form', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'recapcom'      => array(
					'title' => __( 'Anti-spam', 'wp-cerber' ),
					'label' => __( 'Enable reCAPTCHA for WordPress comment form', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'recapcomauth'  => array(
					'title' => '',
					'label' => __( 'Disable reCAPTCHA for logged in users', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'recaplimit'    => array(
					'title' => __( 'Limit attempts', 'wp-cerber' ),
					'label' => __( 'Lock out IP address for %s minutes after %s failed attempts within %s minutes', 'wp-cerber' ),
					'type'  => 'limitz',
				),
			)
		),

		'master_settings' => array(
			'name'   => __( 'Master settings', 'wp-cerber' ),
			//'info'   => __( 'Master settings', 'wp-cerber' ),
			'fields' => array(
				/*('master_cache'    => array(
					'title' => __( 'Cache Time', 'wp-cerber' ),
					'type'  => 'text',
				),*/
				'master_tolist'  => array(
					'title' => __( 'Return to the website list', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'master_swshow'  => array(
					'title' => __( 'Show "Switched to" notification', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'master_at_site' => array(
					'title' => __( 'Add @ site to the page title', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'master_locale'  => array(
					'title' => __( 'Use master language', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				/*
				'master_dt'      => array(
					'title' => __( 'Use master datetime format', 'wp-cerber' ),
					'type'  => 'checkbox',
				),
				'master_tz'      => array(
					'title' => __( 'Use master timezone', 'wp-cerber' ),
					'type'  => 'checkbox',
				),*/
				'master_diag'    => array(
					'title' => __( 'Enable diagnostic logging', 'wp-cerber' ),
					'label' => 'Once enabled, the log is available here: <a target="_blank" href="' . cerber_admin_link( 'diag-log' ) . '">Diagnostic Log</a>',
					'type'  => 'checkbox',
				),
			)
		),
		'slave_settings'  => array(
			'name'   => '',
			//'info'   => __( 'User related settings', 'wp-cerber' ),
			'fields' => array(
				'slave_ips'    => array(
					'title' => __( 'Limit access by IP address', 'wp-cerber' ),
					//'placeholder' => 'The IP address of the master',
					'type'  => 'text',
					//'pro'   => 1
				),
				'slave_access' => array(
					'title'     => __( 'Access to this website', 'wp-cerber' ),
					'type'      => 'select',
					'set'       => array(
						2 => __( 'Full access mode', 'wp-cerber' ),
						4 => __( 'Read-only mode', 'wp-cerber' ),
						8 => __( 'Disabled', 'wp-cerber' )
					),
					'label_pos' => 'below',
					'default'   => 2,
				),
				'slave_diag'   => array(
					'title'   => __( 'Enable diagnostic logging', 'wp-cerber' ),
					'label' => 'Once enabled, the log is available here: <a target="_blank" href="' . cerber_admin_link( 'diag-log' ) . '">Diagnostic Log</a>',
					'default' => 0,
					'type'    => 'checkbox',
				),
			)
		)
	);

	if ( ! empty( $add['sections'] ) ) {
		$sections = array_merge( $sections, $add['sections'] );
	}

	if ( ! lab_lab() ) {
		$sections['slave_settings']['fields']['slave_access']['label'] = '<a href="https://wpcerber.com/pro/" target="_blank">' . __( 'The full access mode requires the PRO version of WP Cerber', 'wp-cerber' ) . '</a>';
	}

	if ( $screen_id = crb_array_get( $args, 'screen_id' ) ) {
		if ( empty( $screens[ $screen_id ] ) ) {
			return false;
		}

		return array_intersect_key( $sections, array_flip( $screens[ $screen_id ] ) );
	}

	if ( $setting = crb_array_get( $args, 'setting' ) ) {
		foreach ( $sections as $s ) {
			if ( isset( $s['fields'][ $setting ] ) ) {
				return $s['fields'][ $setting ];
			}
		}

		return false;

	}

	return $sections;
}

/**
 * @param $name string HTML input name
 * @param $list array   List of elements
 * @param null $selected Index of selected element
 * @param string $class HTML class
 * @param string $id HTML ID
 * @param string $multiple
 *
 * @return string
 */
function cerber_select( $name, $list, $selected = null, $class = '', $id = '', $multiple = '', $placeholder = '', $data = array(), $atts = '' ) {
	$options = array();
	foreach ( $list as $key => $value ) {
		$s         = ( $selected == (string) $key ) ? 'selected' : '';
		$options[] = '<option value="' . $key . '" ' . $s . '>' . htmlspecialchars( $value ) . '</option>';
	}
	$p      = ( $placeholder ) ? ' data-placeholder="' . $placeholder . '" placeholder="' . $placeholder . '" ' : '';
	$m      = ( $multiple ) ? ' multiple="multiple" ' : '';
	$the_id = ( $id ) ? ' id="' . $id . '" ' : '';
	$d      = '';
	if ( $data ) {
		foreach ( $data as $att => $val ) {
			$d .= ' data-' . $att . '="' . $val . '"';
		}
	}

	return ' <select name="' . $name . '" ' . $the_id . ' class="crb-select ' . $class . '" ' . $m . $p . $d . ' ' . $atts . '>' . implode( "\n", $options ) . '</select>';
}

function crb_get_activity_dd( $first = '' ) {
	$labels = cerber_get_labels( 'activity' );

	if ( ! class_exists( 'BP_Core' ) ) {
		unset( $labels[200] );
	}

	if ( ! nexus_is_slave() ) {
		unset( $labels[300] );
	}

	asort( $labels );

	if ( ! $first ) {
		$first = __( 'Any activity', 'wp-cerber' );
	}

	$labels = array( 0 => __( $first, 'wp-cerber' ) ) + $labels;

	$selected = crb_get_query_params( 'filter_activity', '\d+' );
	if ( ! $selected || is_array( $selected ) ) {
		$selected = 0;
	}

	return cerber_select( 'filter_activity', $labels, $selected, 'crb-filter-act' );
}

/**
 * Fill missed settings (array keys) with empty values
 * @since 5.8.2
 *
 * @param $values
 * @param $group
 *
 * @return array
 */
function cerber_normalize( $values, $group ) {
	$def = cerber_get_defaults();
	if ( isset( $def[ $group ] ) ) {
		$keys  = array_keys( $def[ $group ] );
		$empty = array_fill_keys( $keys, '' );
		$values   = array_merge( $empty, $values );
	}

	return $values;
}

/**
 * Convert an array to text string by using a given delimiter
 *
 * @param array $array
 * @param string $delimiter
 *
 * @return array|string
 */
function cerber_array2text( $array = array(), $delimiter = '') {
	if ( empty( $array ) ) {
		return '';
	}

	if ( is_array( $array ) ) {
	    if ($delimiter == ',') $delimiter .= ' ';
		$ret = implode( $delimiter , $array );
	}
	else {
		$ret = $array;
    }

    return $ret;
}

/**
 * Convert text to array by using a given element delimiter, remove empty and duplicate elements
 * Optionally a callback function may be applied to resulting array elements.
 *
 * @param string $text
 * @param string $delimiter
 * @param string $callback
 *
 * @return array|string
 */
function cerber_text2array( $text = '', $delimiter = '', $callback = '') {

	if ( empty( $text ) ) {
		return array();
	}

	if ( ! is_array( $text ) ) {
		$list = explode( $delimiter, $text );
	}
	else {
		$list = $text;
	}
	$list = array_map( 'trim', $list );

	if ( $callback ) {
		$list = array_map( $callback, $list );
	}

	$list = array_filter( $list );
	$list = array_unique( $list );

	return $list;
}

/*
 * 	Default settings.
 *  Each setting field must have a default value!
 *
 */
function cerber_get_defaults( $setting = null ) {
	$all_defaults = array(
		CERBER_OPT   => array(
			'boot-mode'   => 0,
			'attempts'   => 5,
			'period'     => 30,
			'lockout'    => 60,
			'agperiod'   => 24,
			'aglocks'    => 2,
			'aglast'     => 4,
			'limitwhite' => 0,

			'proxy' => 0,
			'cookiepref' => '',

			'subnet'     => 0,
			'nonusers'   => 0,
			'wplogin'    => 0,
			'noredirect' => 0,
			'page404'    => 1,

			'loginpath' => '',
			'loginnowp' => 0,

			'citadel_on' => '1',
			'cilimit'    => 200,
			'ciperiod'   => 15,
			'ciduration' => 60,
			'cinotify'   => 1,

			'keeplog'      => 30,
			'keeplog_auth' => 30,
			'ip_extra'     => 1,
			'cerberlab'    => 0,
			'cerberproto'  => 0,
			'usefile'      => 0,
			'dateformat'   => '',
			'plain_date'   => 0,
			'admin_lang'   => 0,
			'no_white_my_ip'   => 0,
			//'log_errors'   => 1

		),
		CERBER_OPT_H => array(
			'stopenum'   => 1,
			'adminphp'   => 0,
			'phpnoupl'   => 0,
			'nophperr'   => 1,
			'xmlrpc'     => 0,
			'nofeeds'    => 0,
			'norestuser' => 1,
			'norest'     => 0,
			'restauth'   => 1,
			'restroles'  => array('administrator'),
			'restwhite'  => 'oembed',
			'hashauthor' => 0,
			'cleanhead'  => 1,
		),
		CERBER_OPT_U => array(
			'authonly'       => 0,
			'authonlyacl'    => 0,
			'authonlymsg'    => __( 'Only registered and logged in users are allowed to view this website', 'wp-cerber' ),
			'authonlyredir'  => '',
			'reglimit_num'   => 3,
			'reglimit_min'   => 60,
			'emrule'         => 0,
			'emlist'         => array(),
			'prohibited'     => array(),
			'auth_expire'    => '',
			'usersort'       => '',
			'pdata_erase'    => 0,
			'pdata_sessions' => 0,
			'pdata_export'   => 0,
			'pdata_act'      => 0,
			'pdata_trf'      => array(),
		),
		CERBER_OPT_A => array(
			'botscomm'   => 1,
			'botsreg'    => 0,
			'botsany'    => 0,
			'botssafe'   => 0,
			'botsnoauth' => 1,
			'botswhite'  => '',

			'spamcomm'           => 0,
			'trashafter'         => 7,
			'trashafter-enabled' => 0,
		),
		CERBER_OPT_C => array(
			'sitekey' => '',
			'secretkey' => '',
			'invirecap'  => 0,
			'recaplogin' => 0,
			'recaplost' => 0,
			'recapreg' => 0,
			'recapwoologin' => 0,
			'recapwoolost' => 0,
			'recapwooreg' => 0,
			'recapcom' => 0,
			'recapcomauth' => 0,
            'recaptcha-period' => 60,
			'recaptcha-number' => 3,
			'recaptcha-within' => 30,
		),
		CERBER_OPT_N => array(
			'notify'         => 1,
			'above'          => 3,
			'email'          => '',
			'emailrate'      => 12,
			'notify-new-ver' => '1',
			'pbtoken'        => '',
			'pbdevice'       => '',
			'wreports-day'   => '1', // workaround, see cerber_upgrade_settings()
			'wreports-time'  => 9,
			'email-report'   => '',
			'enable-report'  => '1',  // workaround, see cerber_upgrade_settings()
		),
		CERBER_OPT_T => array(
			'tienabled'      => '1',
			'tiipwhite'      => 0,
			'tiwhite'        => '',
			'tierrmon'       => '1',
			'tierrnoauth'    => 0,
			'timode'         => '3',
			'tinocrabs'      => '1',
			'tinolocs'       => array(),
			'tinoua'         => array(),
			'tifields'       => 0,
			'timask'         => '',
			'tihdrs'         => 0,
			'tisenv'         => 0,
			'ticandy'        => 0,
			'tiphperr'       => 0,
			'tithreshold'    => '',
			'tikeeprec'      => 30,
			'tikeeprec_auth' => 30,
		),
		CERBER_OPT_US => array(
			'ds_4acc'      => 0,
			'ds_regs_roles'  => array(),
			'ds_add_acc'   => array( 'administrator' ),
			'ds_edit_acc'    => array( 'administrator' ),
			'ds_4acc_acl'  => 0,
			'ds_4roles'       => 0,
			'ds_add_role'    => array( 'administrator' ),
			'ds_edit_role' => array( 'administrator' ),
			'ds_4roles_acl'   => 0,
		),
		CERBER_OPT_OS => array(
			'ds_4opts'       => 0,
			'ds_4opts_roles' => array( 'administrator' ),
			'ds_4opts_list'  => CRB_DS::get_settings_list( false ),
			'ds_4opts_acl'   => 0,
		),
		CERBER_OPT_S => array(
			'scan_cpt'      => array(),
			'scan_uext'     => array(),
			'scan_exclude'  => array(),
			'scan_inew'     => '1',
			'scan_imod'     => '1',
			'scan_chmod'    => 0,
			'scan_tmp'      => 0,
			'scan_sess'     => 0,
			'scan_debug'    => 0,
			'scan_qcleanup' => '30',
		),
		CERBER_OPT_E => array(
			'scan_aquick'        => 0,
			'scan_afull'         => '0' . rand( 1, 5 ) . ':00',
			'scan_afull-enabled' => 0,
			'scan_reinc'         => array( 3 => 1, CERBER_VULN => 1, CERBER_IMD => 1, 50 => 1, 51 => 1 ),
			'scan_relimit'       => 3,
			'scan_isize'         => 0,
			'scan_ierrors'       => 0,
			'email-scan'         => ''
		),
		CERBER_OPT_P => array(
			'scan_delunatt'   => 0,
			'scan_delupl'     => array(),
			'scan_delunwant'  => 0,
			'scan_recover_wp' => 0,
			'scan_recover_pl' => 0,

			'scan_nodeltemp' => 0,
			'scan_nodelsess' => 0,
			'scan_delexdir'  => array(),
			'scan_delexext'  => array(),
		),
		CERBER_OPT_MA => array(
			'master_tolist'  => 1,
			'master_swshow'  => 1,
			'master_at_site' => 1,
			'master_locale'  => 0,
			'master_dt'      => 0,
			'master_tz'      => 0,
			'master_diag'    => 0,
		),
		CERBER_OPT_SL => array(
			'slave_ips'    => '',
			'slave_access' => 2,
			'slave_diag'   => 0,
		),
	);

	if ( $setting ) {
		foreach ( $all_defaults as $section ) {
			if ( isset( $section[ $setting ] ) ) {
				return $section[ $setting ];
			}
		}

		return null;
	}

	return $all_defaults;
}

/**
 * Upgrade plugin options
 *
 */
function cerber_upgrade_settings() {
	// @since 4.4, move fields to a new option
	if ( $main = get_site_option( CERBER_OPT ) ) {
		if ( ! empty( $main['email'] ) || ! empty( $main['emailrate'] ) ) {
			$new              = get_site_option( CERBER_OPT_N, array() );
			$new['email']     = $main['email'];
			$new['emailrate'] = $main['emailrate'];
			update_site_option( CERBER_OPT_N, $new );
			unset( $main['email'] );
			unset( $main['emailrate'] );
			update_site_option( CERBER_OPT, $main );
		}
	}
	// @since 7.5.4, move some fields CERBER_OPT_С => CERBER_OPT_A
	crb_move_fields( CERBER_OPT_C, CERBER_OPT_A, array(
		'botscomm',
		'botsreg',
		'botsany',
		'botssafe',
		'botsnoauth',
		'botswhite',
		'spamcomm',
		'trashafter'
	) );
	// @since 8.2
	crb_move_fields( CERBER_OPT, CERBER_OPT_N, array(
		'notify',
		'above',
	) );
	// @since 5.7
    // Upgrade plugin settings
	foreach ( cerber_get_defaults() as $option_name => $def_fields ) {
		$values = get_site_option( $option_name );
		if ( ! $values ) {
			$values = array();
		}
		// Add new settings (fields) with their default values
		foreach ( $def_fields as $field_name => $default ) {
			if ( ! isset( $values[ $field_name ] ) && $default !== 1) { // @since 5.7.2 TODO refactor $default !== 1 to more obvious
				$values[ $field_name ] = $default;
			}
		}

		// Remove non-existing/outdated fields, @since 7.5.7
		$values = array_intersect_key( $values, $def_fields );

		// Must be after all operations above
		$values = cerber_normalize($values, $option_name); // @since 5.8.2

		update_site_option( $option_name, $values );
	}
	// @since 7.9.4 Stop user enumeration for REST API
	if ( $h = get_site_option( CERBER_OPT_H ) ) {
		if ( $h['stopenum'] && ! isset( $h['norestuser'] ) ) {
			$h['norestuser'] = 1;
			update_site_option( CERBER_OPT_H, $h );
		}
	}

	if ( ! $key = get_site_option( '_cerberkey_' ) ) {
		$key = cerber_get_site_option( '_cerberkey_' );
	}
	if ( $key ) {
		if ( cerber_update_set( '_cerberkey_', $key ) ) {
			delete_site_option( '_cerberkey_' );
		}
	}
}

/**
 * @param string $from
 * @param string $to
 * @param array $fields
 *
 * @return bool
 */
function crb_move_fields( $from, $to, $fields ) {
	if ( ! $old = get_site_option( $from ) ) {
		return false;
	}
	$new = get_site_option( $to );
	if ( ! $new || ! is_array( $new ) ) {
		$new = array();
	}
	foreach ( $fields as $key ) {
		if ( isset( $old[ $key ] )
		     && ! isset( $new[ $key ] ) ) {
			$new[ $key ] = $old[ $key ]; // move old values
			unset( $old[ $key ] ); // clean up old values
		}
	}
	update_site_option( $from, $old );
	update_site_option( $to, $new );

	return true;
}

/*
 *
 * Right way to save Cerber settings outside of wp-admin settings page
 * @since 2.0
 *
 */
function cerber_save_settings( $options ) {

    foreach ( cerber_get_defaults() as $option_name => $fields ) {
		$filtered = array();
		foreach ( $fields as $field_name => $def ) {
			if ( isset( $options[ $field_name ] ) ) {
				$filtered[ $field_name ] = $options[ $field_name ];
			}
		}
		if ( ! empty( $filtered ) ) {
			update_site_option( $option_name, $filtered );
		}
	}

	crb_purge_settings_cache();
}

/**
 *
 * @deprecated since 4.0 Use crb_get_settings() instead.
 *
 * @param string $option
 *
 * @return array|bool|mixed
 */
function cerber_get_options( $option = '' ) {
	$options = cerber_get_setting_list();
	$united  = array();
	foreach ( $options as $opt ) {
		$o = get_site_option( $opt );
		if ( ! is_array( $o ) ) {
			continue;
		}
		$united = array_merge( $united, $o );
	}
	$options = $united;
	if ( ! empty( $option ) ) {
		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}
		else {
			return false;
		}
	}

	return $options;
}

/**
 * @since 8.5.9.1
 *
 * @param string $option
 * @param bool $purge_cache purge static cache
 *
 * @return array|bool|mixed
 */
function cerber_get_settings( $option = '', $purge_cache = false ) {
	return crb_get_settings( $option, $purge_cache );
}

/**
 * The replacement for cerber_get_options()
 *
 * @param string $option
 * @param bool $purge_cache purge static cache
 *
 * @return array|bool|mixed
 */
function crb_get_settings( $option = '', $purge_cache = false ) {
	global $wpdb;
	static $united;

	/**
	 * For some hosting environments it might be faster, e.g. Redis enabled
	 */
	if ( defined( 'CERBER_WP_OPTIONS' ) && CERBER_WP_OPTIONS ) {
		return cerber_get_options( $option );
	}

	if ( ! $option && $purge_cache ) {
		$united = null;

		return false; // @since 8.5.9.1
	}

	if ( ! isset( $united ) || $purge_cache ) {

		$options = cerber_get_setting_list();
		$in      = '("' . implode( '","', $options ) . '")';
		$united  = array();

	    if ( is_multisite() ) {
		    $sql = 'SELECT meta_value FROM ' . $wpdb->sitemeta . ' WHERE meta_key IN ' . $in;
		    $sql_new = 'SELECT meta_value FROM ' . $wpdb->sitemeta . ' WHERE meta_key = "' . CERBER_CONFIG . '"';
	    }
	    else {
		    $sql = 'SELECT option_value FROM ' . $wpdb->options . ' WHERE option_name IN ' . $in;
		    $sql_new = 'SELECT option_value FROM ' . $wpdb->options . ' WHERE option_name = "' . CERBER_CONFIG . '"';
	    }

		$set = cerber_db_get_col( $sql );

		if ( ! $set || ! is_array( $set ) ) {
			return false;
		}

		$set_new = cerber_db_get_var( $sql_new );

		if ( $set_new ) {
			array_unshift( $set, $set_new );
		}

	    foreach ( $set as $item ) {
		    if ( empty( $item ) ) {
			    continue;
		    }

		    $value = unserialize( $item );

		    if ( ! $value || ! is_array( $value ) ) {
			    continue;
		    }

		    $united = array_merge( $united, $value );
	    }

    }

	if ( ! empty( $option ) ) {
		if ( isset( $united[ $option ] ) ) {
			return $united[ $option ];
		}
		else {
			return false;
		}
	}

	return $united;
}

function crb_purge_settings_cache() {
	crb_get_settings( null, true );
}

/**
 * @param string $option Name of site option
 * @param boolean $unserialize If true the value of the option must be unserialized
 *
 * @return null|array|string
 * @since 5.8.7
 */
function cerber_get_site_option($option = '', $unserialize = true){
    global $wpdb;
	static $values = array();

	if ( ! $option ) {
		return null;
	}

	/**
	 * For some hosting environments it might be faster, e.g. Redis enabled
	 */
	if ( defined( 'CERBER_WP_OPTIONS' ) && CERBER_WP_OPTIONS ) {
		return get_site_option( $option, null );
	}

	if ( isset( $values[ $option ] ) ) {
		return $values[ $option ];
	}

    if ( is_multisite() ) {
	    // @since 7.1
		//$value = $wpdb->get_var( 'SELECT meta_value FROM ' . $wpdb->sitemeta . ' WHERE meta_key = "' . $option . '"' );
	    $value = cerber_db_get_var( 'SELECT meta_value FROM ' . $wpdb->sitemeta . ' WHERE meta_key = "' . $option . '"' );
	}
	else {
		// @since 7.1
		//$value = $wpdb->get_var( 'SELECT option_value FROM ' . $wpdb->options . ' WHERE option_name = "' . $option . '"' );
		$value = cerber_db_get_var( 'SELECT option_value FROM ' . $wpdb->options . ' WHERE option_name = "' . $option . '"' );
	}

	if ( $value ) {
		if ( $unserialize ) {
			$value = @unserialize( $value );
			if ( ! is_array( $value ) ) {
				$value = null;
			}
		}
	}
	else {
		$value = null;
	}

	$values[$option] = $value;
	return $value;
}

/*
	Load default settings, except Custom Login URL
*/
function cerber_load_defaults() {

	$save = array();
	foreach ( cerber_get_defaults() as $option_name => $fields ) {
		foreach ( $fields as $field_name => $def ) {
			$save[ $field_name ] = $def;
		}
	}

	if ( $path = crb_get_settings( 'loginpath' ) ) {
		$save['loginpath'] = $path;
	}

	foreach ( cerber_get_setting_list( true ) as $opt ) {
		delete_site_option( $opt ); // @since 8.6.3.4
	}

	cerber_save_settings( $save );
}

/**
 * @param string $type Type of notification email
 * @param bool $array  Return as an array
 *
 * @return array|string Email address(es) for notifications
 */
function cerber_get_email( $type = '', $array = false ) {
	$email = '';

	if ( in_array( $type, array( 'report', 'scan' ) ) ) {
		$email = crb_get_settings( 'email-' . $type );
	}

	if ( ! $email ) {
		$email = crb_get_settings( 'email' );
	}

	if ( ! $array && is_array( $email ) ) {
		$email = implode( ', ', $email );
	}

	if ( empty( $email ) ) {
		$email = get_site_option( 'admin_email' );
		if ( $array ) {
			$email = array( $email );
		}
	}

	return $email;
}

/**
 * Sync a set of scanner/uptime bots settings with the cloud
 *
 * @param $data
 *
 * @return bool
 */
function cerber_cloud_sync( $data = array() ) {
	if ( ! lab_lab() ) {
		return false;
	}

	if ( ! $data ) {
		$data = crb_get_settings();
	}

	$full  = ( empty( $data['scan_afull-enabled'] ) ) ? 0 : 1;
	$quick = absint( $data['scan_aquick'] );

	if ( $quick || $full ) {
		$set             = array(
			$quick,
			$full,
			cerber_sec_from_time( $data['scan_afull'] ),
			cerber_get_email( 'scan', true )
		);
		$scan_scheduling = array( // Is used for scheduled scans
			'client'     => $set,
			'site_url'   => cerber_get_home_url(),
			'gmt_offset' => (int) get_option( 'gmt_offset' ),
			'dtf'        => cerber_get_dt_format(),
		);
	}
	else {
		$scan_scheduling = array();
	}

	if ( lab_api_send_request( array(
		'scan_scheduling' => $scan_scheduling
	) ) ) {
		return true;
	}

	return false;
}

/**
 * Is a cloud based service enabled by the site owner
 *
 * @return bool False if nothing cloud related is enabled
 */
function cerber_is_cloud_enabled( $what = '' ) {
	$data = crb_get_settings();

	$s = array( 'quick' => 'scan_aquick', 'full' => 'scan_afull-enabled' );

	if ( $what ) {
		if ( ! empty( $data[ $s[ $what ] ] ) ) {
			return true;
		}

		return false;
	}

	foreach ( $s as $item ) {
		if ( ! empty( $data[ $item ] ) ) {
			return true;
		}
	}

	return false;
}

function cerber_get_role_policies( $role ) {
	if ( $conf = crb_get_settings( 'crb_role_policies' ) ) {
		return crb_array_get( $conf, $role );
	}

	return true;
}

/**
 * @param $policy string
 * @param $user integer | WP_User
 *
 * @return bool|string
 */
function cerber_get_user_policy( $policy, $user = null ) {

	if ( ! ( $user instanceof WP_User ) ) {
		if ( is_numeric( $user ) ) {
			$user = get_user_by( 'id', $user );
		}
		else {
			$user = wp_get_current_user();
		}
	}

	if ( ! $user ) {
		return false;
	}

	$ret = false;

	foreach ( $user->roles as $role ) {
		$policies = cerber_get_role_policies( $role );
		if ( ! empty( $policies[ $policy ] ) ) {
			$ret = $policies[ $policy ];
		}
	}

	return $ret;
}
