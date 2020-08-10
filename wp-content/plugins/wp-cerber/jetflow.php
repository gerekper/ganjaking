<?php
/*

 Integration with the jetFlow.io automation and customization plugin, https://jetflow.io
 Actions and triggers definitions.

 Copyright (C) 2015-20 CERBER TECH INC., https://cerber.tech
 Copyright (C) 2015-20 CERBER TECH INC., https://wpcerber.com

 Licenced under the GNU GPL.

*/

/*

*========================================================================*
|                                                                        |
|	       ATTENTION!  Do not change or edit this file!                  |
|                                                                        |
*========================================================================*

*/


// If this file is called directly, abort executing.
if ( ! defined( 'WPINC' ) ) {
	exit;
}

if ( function_exists( 'wof_register' ) ) {
	cerber_jetflow();
}
else {
	add_action( 'jetflow_register', function () {
		cerber_jetflow();
	} );
}

function cerber_jetflow() {

	class TR_IP_Locked extends WF_Action {
		static $trigger = true;
		static $wp_hook = 'cerber_ip_locked';
		static $name = 'IP address locked out';
		static $description = 'Start after IP has been blocked by WP Cerber';
		static $form_help = '
	Start the workflow after an IP address has been locked out by the WP Cerber plugin and conditions match the specified criteria. 
	<p>The variable <code>{TRIGGER.IP}</code> contains the blocked IP address, the variable <code>{TRIGGER.reason}</code> contains the reason why.
	';
		public static $fields = array(
			'filter' => array(
				'type'   => 'group',
				'fields' => array(
					'locks'  => array(
						'type'         => 'text',
						'label'        => 'Start if an IP address has been locked out more than times',
						'default'      => '0',
						'autocomplete' => 0
					),
					'period' => array(
						'type'         => 'text',
						'label'        => 'in the last minutes',
						'default'      => '60',
						'autocomplete' => 0
					),
				)
			),
			'limit'  => array(
				'type'         => 'text',
				'label'        => 'Start if the number of currently locked out IP addresses is greater than',
				'default'      => '0',
				'required'     => 1,
				'autocomplete' => 0
			),
		);

		function execute( $fields ) {
			global $wpdb;
			list ( $fields, $previous, $env, $wp_arguments ) = func_get_args();
			if ( cerber_blocked_num() <= absint( $fields['limit'] ) ) {
				return new WF_Stop( __CLASS__ );
			}
			if ( ! empty( $fields['filter']['locks'] ) ) {
				$locks = absint( $fields['filter']['locks'] );
			}
			else {
				$locks = 0;
			}
			if ( $locks > 0 ) {
				$ip = $wp_arguments[0]['IP'];
				$stamp = time() - absint( $fields['filter']['period'] ) * 60;
				$lockouts = $wpdb->get_var( $wpdb->prepare( 'SELECT count(ip) FROM ' . CERBER_LOG_TABLE . ' WHERE ip = %s AND activity IN (10,11) AND stamp > %d', $ip, $stamp ) );
				$lockouts = absint( $lockouts );
				if ( ! $lockouts || $lockouts <= $locks ) {
					return new WF_Stop( __CLASS__ );
				}
			}

			return $wp_arguments[0];
		}

		static function getStarterInfo( $config, $context ) {
			return 'After IP address has been locked out by Cerber';
		}
	}

	class WF_WHOIS extends WF_Action {
		public static $section = 'network';
		public static $name = 'Get WHOIS info';
		public static $description = 'Get extended information about IP address';
		public static $form_help = '
		Sends request to a WHOIS server and retrieves details about a given IP address. The WHOIS information is publicly available and provided for free. 
		There are no reasons for security concerns, because a list of WHOIS servers are maintained by <a target="_blank" href="https://en.wikipedia.org/wiki/ICANN">ICANN</a>.
		<p>Bear in mind that each WHOIS request takes some time to retrieve data from the remote WHOIS server. A request can take up to 500 ms approximately, so the workflow will be waiting all this time for a response. 
		<p>The result is a list. To get a country name in the next action, use variable <code>{PREVIOUS.country-name}</code>, for the two-letter country code: <code>{PREVIOUS.country}</code>, for the abuse email address: <code>{PREVIOUS.abuse-mailbox}</code>, for the network as IP range: <code>{PREVIOUS.inetnum}</code>. 
		The full list of available fields depends on a network owner.
		You can request WHOIS data manually to find out what kind of fields are available on this page: <a target="_blank" href="http://wq.apnic.net/apnic-bin/whois.pl">http://wq.apnic.net/apnic-bin/whois.pl</a>.
    ';
		public static $fields = array(
			'ip' => array(
				'type'     => 'text',
				'label'    => 'IP address',
				'default'  => '{TRIGGER.IP}',
				'required' => 1,
			),
		);

		function execute( $fields ) {
			list ( $fields, $previous, $env, $wp_arguments ) = func_get_args();
			$ip = filter_var( $fields['ip'], FILTER_VALIDATE_IP );
			if ( ! $ip ) {
				return false;
			}
			$whois = cerber_ip_whois_info( $ip );
			if ( ! empty( $whois['error'] ) ) {
				return new WF_Error ( __CLASS__, 'Unable to obtain IP info' );
			}
			$ret = $whois['data'];

			if ( empty( $ret['abuse-mailbox'] ) && ! empty( $ret['OrgAbuseEmail'] ) ) {
				$ret['abuse-mailbox'] = $ret['OrgAbuseEmail'];
			}

			if ( ! is_email( $ret['abuse-mailbox'] ) ) {
				$ret['abuse-mailbox'] = '';
			}

			$ret['country-name'] = cerber_country_name( $ret['country'] );

			return $ret;
		}
	}

	wof_register( array( 'TR_IP_Locked', 'WF_WHOIS' ) );
}

