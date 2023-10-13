<?php  if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * Mute Screamer
 *
 * PHPIDS for Wordpress
 */
function countryCode($ip=''){
    if (!$ip)
        $ip = $_SERVER['REMOTE_ADDR'];

    $urls[]="http://ip2c.org/".$ip; //index ==0
    $urls[]="http://www.geoplugin.net/json.gp?ip=".$ip;
    // $urls[]="http://pro.ip-api.com/json/".$ip."?key=4DIRuWVYHi140cK&fields=countryCode";
    //$urls[]="https://freegeoip.net/json/".$ip;
    //$urls[]="http://ip-json.rhcloud.com/json/".$ip;
    //https://www.neutrinoapi.com/plans/ IPaddressAPI.com

    $index = rand(0,1);
    $response = @wp_remote_get($urls[$index], array('timeout'=> 3));

    //$response = @wp_remote_get("http://ipinfo.io/".$ip."/json", array('timeout'=> 3));
    if (200 == wp_remote_retrieve_response_code( $response )
        && 'OK' == wp_remote_retrieve_response_message( $response )
        && !is_wp_error( $response )) {

        if ($index > 0) //only for non-json index == 0: http://ip2c.org/
            $data = json_decode($response['body'], 1);
        else
            $data = $response['body'];

        if(isset($data['countryCode']))
            return $data['countryCode']; //code
        if(isset($data['country_code']))
            return $data['country_code']; //code
        if(isset($data['geoplugin_countryCode']))
            return $data['geoplugin_countryCode']; //code

        if ($data !="0"){
            $reply = explode(';',$data);
            if (isset($reply[1]))
                return $reply[1];
        }

    }
    return false;
}

require_once 'IDS/Log/Interface.php';

/**
 * Log Database
 *
 * Log reports using the wpdb class
 */
class HMWP_MS_Log_Database implements IDS_Log_Interface {

    /**
     * Holds current remote address
     *
     * @var string
     */
    private $ip = '0.0.0.0';

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->ip = HMWP_MS_Utils::ip_address();
	}

	/**
	* Inserts detected attacks into the database
	*
	* @param object
	* @return boolean
	*/
	public function execute( IDS_Report $report_data ) {
		global $wpdb, $current_user ;

        if (!$current_user)
            $user_id = 0;
        else
            $user_id = $current_user->ID;

		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			$_SERVER['REQUEST_URI'] = substr( $_SERVER['PHP_SELF'], 1 );
			if ( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] ) {
				$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

        $allowed = array(
            'a' => array(
                'href' => array()
            ),
            'strong' => array()
        );
                
                //Get user ip address
                $ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
                
		foreach ( $report_data as $event ) {
			$data['name']    = sanitize_text_field($event->getName());
			$data['value']   =  wp_kses( $event->getValue(), $allowed );
			$data['page']    = isset( $_SERVER['REQUEST_URI'] ) ? wp_kses($_SERVER['REQUEST_URI'], $allowed) : '';
			$data['tags']    = implode( ', ', $event->getTags() );
			$data['ip']      = sanitize_text_field($ipaddress);
            $data['user_id']  = $user_id; //hassan
			$data['impact']  = $event->getImpact();
            $data['total_impact']  =  $report_data->getImpact(); //hassan
			//$data['origin']  = sanitize_text_field($_SERVER['SERVER_ADDR']);
			$c = countryCode($this->ip);
			if (!$c)
				$c='';

			$data['origin']  = sanitize_text_field($c);
			$data['created'] = date( 'Y-m-d H:i:s', time() );

			if ( false === $wpdb->insert( $wpdb->hmwp_ms_intrusions, $data ) ) {
				return false;
			}
		}

		return true;
	}
}
