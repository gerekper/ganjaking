<?php  if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * Mute Screamer
 *
 * PHPIDS for Wordpress
 */

require_once 'IDS/Log/Email.php';

/**
 * Log Email
 *
 * Log reports via email
 */
class HMWP_MS_Log_Email extends IDS_Log_Email {

	/**
	* Prepares data
	*
	* Converts given data into a format that can be read in an email.
	* You might edit this method to your requirements.
	*
	* @param mixed $data the report data
	* @return string
	*/
	protected function prepareData( $data ) {
        global $user_ID;
		$format  =  "The following potential attack has been detected by HMWP IDS. \n\nIf it's you please Exclude that parameter or increase Notify Threshold from IDS settings.\nIn most cases you don't need to do anything. Hide My WP protects you!\n\n";
		$format .=  "IP: %s \n" ;
        $format .=  "User ID: %s\n";

        $format .=  "Date: %s \n";
		$format .=  "Total Impact: %d \n";
		$format .=  "Affected tags: %s \n\n";
        //hassan


		$attackedParameters = '';
		foreach ( $data as $event ) {
			$attackedParameters .= esc_attr($event->getName()) . '=' .
				( ( ! isset( $this->urlencode ) || $this->urlencode )
				? urlencode( esc_attr($event->getValue()) )
				: $event->getValue() ) . ', ';
		}

		$format .=  "Affected parameters: %s \n\n";
		$format .=  "Request URI: %s \n";
		$format .=  "Origin: %s \n";

		return sprintf( $format,
			$this->ip,
            $user_ID,
			date( 'c' ),
			$data->getImpact(),
			join( ' ', $data->getTags() ),
			trim( $attackedParameters ),
			esc_url(htmlspecialchars( $_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8' )),
			$_SERVER['SERVER_ADDR']
		);
	}

	/**
	* Sends an email
	*
	* @param string $address  email address
	* @param string $data     the report data
	* @param string $headers  the mail headers
	* @param string $envelope the optional envelope string
	* @return boolean
	*/
	protected function send( $address, $data, $headers, $envelope = null ) {
        include_once(ABSPATH . '/wp-includes/pluggable.php');
		return wp_mail( $address, $this->subject, $data );

	}
}
