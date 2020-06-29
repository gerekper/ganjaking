<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_http extends WYSIJA_object{
    
    function __construct(){
        parent::__construct();
    }

    /**
     * try three different methods for http request,
     * @param type $url
     * @return type
     */
    function request($url){
        // use curl_get first if it is activated
        if(function_exists('curl_init')) {
            $this->opts = array(
                CURLOPT_HEADER => FALSE,
                CURLOPT_RETURNTRANSFER => TRUE
            );
            $result=$this->curl_get($url);
            return $result['cr'];
        // then try http_get
        }elseif(function_exists('http_get')){
            return http_parse_message(http_get($url))->body;

        // finally we have file_get_contents which is quite often deactivated
        }elseif(ini_get('allow_url_fopen')){
            return file_get_contents($url);
        }else{
            $this->error(__('Your server doesn\'t support remote exchanges.',WYSIJA));
            $this->error(__('Contact your administrator to modify that, it should be configurable.',WYSIJA));
            $this->error('<strong>CURL library</strong> DISABLED');
            $this->error('<strong>allow_url_fopen</strong> DISABLED');
            $this->error('<strong>PECL pecl_http >= 0.1.0</strong> DISABLED');
            return false;
        }
    }

    function wp_request($url){
        global $wp_version;

        $active  = get_option( 'active_plugins', array() );
        $to_send = (object) compact('plugins', 'active');

	$options = array(
		'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3),
		'body' => array( 'plugins' => serialize( $to_send ) ),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);

	$raw_response = wp_remote_post($url, $options);

        if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ){
            if(method_exists($raw_response, 'get_error_messages')){
                $this->error($raw_response->get_error_messages());
            }
            return false;
        }

	return maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
    }

    function request_timeout($url,$timeout='3'){
        if(function_exists('curl_init')) {

            $ch = curl_init( $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 0 );
            curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
            $result = curl_exec( $ch );

            return   curl_close( $ch );
        }elseif(ini_get('allow_url_fopen')){
            ini_set('default_socket_timeout',(int)$timeout);
            return @file_get_contents($url);
        }elseif(function_exists('http_get')){
            return @http_get($url, array('timeout'=>(int)$timeout));
        }else{
            $this->error(__('Your server doesn\'t support remote exchanges.',WYSIJA));
            $this->error(__('Contact your administrator to modify that, it should be configurable.',WYSIJA));
            $this->error('<strong>CURL library</strong> DISABLED');
            $this->error('<strong>allow_url_fopen</strong> DISABLED');
            $this->error('<strong>PECL pecl_http >= 0.1.0</strong> DISABLED');
            return false;
        }
    }

    function curl_request($ch,$opt){
        # assign global options array
        $opts = $this->opts;
        # assign user's options
        foreach($opt as $k=>$v){$opts[$k] = $v;}
        curl_setopt_array($ch,$opts);
        curl_exec($ch);
        $r['code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $r['cr'] = curl_exec($ch);
        $r['ce'] = curl_errno($ch);
        curl_close($ch);
        return $r;
    }

    function curl_get($url='',$opt=array()){
        # create cURL resource
        $ch = curl_init($url);
        return $this->curl_request($ch,$opt);
    }

   /* function curl_post($url='',$data=array(),$opt=array()){
        # set POST options
        $opts[CURLOPT_POST] = TRUE;
        $opts[CURLOPT_POSTFIELDS] = $data;

        # create cURL resource
        $ch = curl_init($url);
        return $this->curl_request($ch,$opt);
    }*/
}

