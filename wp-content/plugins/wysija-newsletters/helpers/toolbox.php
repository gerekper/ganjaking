<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_toolbox extends WYSIJA_object{

    function __construct(){
        parent::__construct();
    }

    /**
     * Get the url of a wysija file based on the filename and the wysija folder
     * @param type $filename
     * @param type $folder
     * @return string
     */
    function url($filename,$folder='temp'){
        $upload_dir = wp_upload_dir();

        if(file_exists($upload_dir['basedir'].DS.'wysija')){
            $url=$upload_dir['baseurl'].'/wysija/'.$folder.'/'.$filename;
        }else{
            $url=$upload_dir['baseurl'].'/'.$filename;
        }
        return $url;
    }

    function closetags($html) {
        #put all opened tags into an array
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];   #put all closed tags into an array
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        # all tags are closed
        if(count($closedtags) === $len_opened) {
            return $html;
        }

        $openedtags = array_reverse($openedtags);
        # close tags
        for($i=0; $i < $len_opened; $i++) {
            if(!in_array($openedtags[$i], $closedtags)){
                $html .= '</'.$openedtags[$i].'>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }

    /**
     * make an excerpt with a certain number of words
     * @param type $text
     * @param type $num_words
     * @param type $more
     * @return type
     */
    function excerpt($text, $num_words = 8, $more = ' &hellip;'){
        return wp_trim_words($text, $num_words, $more);
    }

    /**
     * make a domain name out of a url
     * @param type $url
     * @return type
     */
    function _make_domain_name($url=false){
        if(!$url) $url=admin_url('admin.php');

        $domain_name=str_replace(array('https://','http://','www.'),'',strtolower($url));
        //$domain_name=preg_replace(array('#^https?://(www\.)*#i','#^www\.#'),'',$url);
        $domain_name=explode('/',$domain_name);
        return $domain_name[0];
    }

    /**
     * get base url of the current site or base url of a specific url WITHOUT http, https, www
     * @param string $url
     * @return string
     */
    function get_base_uri($url = null) {
        $url = !empty($url) ? $url : site_url();
        return str_replace(array('https://','http://','www.'),'',strtolower($url));
    }

    /**
     * Detect if this is an internal link, otherwise, it will be an external one
     * @param string $url
     * @return boolean
     */
    function is_internal_link($url) {
        $str_pos = strpos($this->get_base_uri($url), $this->get_base_uri());
        // an internal link must CONTAIN base_uri of the current site and must START with that base_uri
        return ($str_pos !== false && $str_pos === 0);
    }

    /**
     * creates a duration string to tell when is the next batch to be processed for instance
     * TODO we should add an estimation parameter so that instead of having a precise remaining time 39 minutes left,
     * we have something based on the sending frequency : about 45 minutes left becomes about 30 minutes left, becomes about 15 minutes left
     * with a 15 minutes sending frequency
     * @param int $value_seconds it can be a duration or a timestamp
     * @param boolean $entered_value_is_duration
     * @param int $number_of_units should I get just on unit days, or should I get 2 units days and hours
     * @param int $precision how precise should be the duration calculated, until the second ? or just a number of hours by default in minutes
     * @return string
     */
    function duration_string($value_seconds, $entered_value_is_duration=false, $number_of_units=1, $precision=4){

        if($entered_value_is_duration){
            // the time entered is already a duration
            $duration = $value_seconds;
        }else{
            // the time entered is a unix time
            $duration = time() - $value_seconds;
        }

        //Clever Maths
        $array_duration = $this->convert_seconds_to_array($duration);

        // when we don't show the seconds in our result we need to add one minute to the min key
        if($precision == 4){
            $array_duration['mins'] ++;
        }

        // Display for date, can be modified more to take the S off
        $str = '';
        $current_level = 0;
        if ($current_level < $number_of_units && $array_duration['years'] >= 1) { $str .= sprintf(_n( '%1$s year', '%1$s years', $array_duration['years'], WYSIJA ),$array_duration['years']).' ';$current_level++; }
        if ($precision >0 && $current_level < $number_of_units && $array_duration['weeks'] >= 1) { $str .= sprintf(_n( '%1$s week', '%1$s weeks', $array_duration['weeks'], WYSIJA ),$array_duration['weeks']).' ';$current_level++; }
        if ($precision >1 && $current_level < $number_of_units && $array_duration['days'] >= 1) { $str .= sprintf(_n( '%1$s day', '%1$s days', $array_duration['days'], WYSIJA ),$array_duration['days']).' ';$current_level++; }
        if ($precision >2 && $current_level < $number_of_units && $array_duration['hours'] >= 1) { $str .= sprintf(_n( '%1$s hour', '%1$s hours', $array_duration['hours'], WYSIJA ),$array_duration['hours']).' ';$current_level++; }
        if ($precision >3 && $current_level < $number_of_units && $array_duration['mins'] >= 1) { $str .= sprintf(_n( '%1$s minute', '%1$s minutes', $array_duration['mins'], WYSIJA ),$array_duration['mins']).' ';$current_level++; }
        if ($precision >4 && $current_level < $number_of_units && $array_duration['secs'] >= 1) { $str .= sprintf(_n( '%1$s second', '%1$s seconds', $array_duration['secs'], WYSIJA ),$array_duration['secs']).' ';$current_level++; }

        return $str;

    }

    /**
     * this array is to be used in some of the functions below
     * @return array
     */
    private function get_duration_units($unit = false){

        // keep it in that order otherwise it will count first the second and will just retunr the number of seconds left
        $units = array(
            'years' => 60*60*24*365,
            'weeks' => 60*60*24*7,
            'days' => 60*60*24,
            'hours' => 60*60,
            'mins' => 60,
            'secs' => 1,
        );

        if($unit === false ) return $units;
        elseif(isset($units[$unit])) return $units[$unit];
    }

    /**
     * enter a number of seconds as input it will return an array calculating the duration in years,weeks,days,hours and seconds
     * @param int $duration
     * @param boolean $split_the_duration_between_each_unit I didn't know how to explain that value,
     * basically it's either 1hour 33min 7seconds translate into
     * array(years=>0, weeks=>0, days=>0, hours=>1, mins=>33, secs=7)
     * or
     * array(years=>0, weeks=>0, days=>0, hours=>1, mins=>93, secs=5587)
     * @return array with each units
     */
    public function convert_seconds_to_array( $duration , $split_the_duration_between_each_unit = true){
        $result = array();
        $duration = array( 'seconds_left' => $duration);
        $units = $this->get_duration_units();

        foreach($units as $unit => $unit_in_seconds){
            if($split_the_duration_between_each_unit){
                $duration = $this->get_duration( $duration['seconds_left'] , $unit);
                $result[$unit] = $duration['number'];
            }else{
                $result_duration = $this->get_duration( $duration['seconds_left'] , $unit);
                $result[$unit] = $result_duration['number'];
            }

        }

        return $result;
    }

    /**
     * convert one duration in seconds to a unit you specify (mins,hours,days,weeks,years)
     * @param int $duration
     * @param string $unit
     * @return array
     */
    public function get_duration($duration, $unit = 'days'){
        $result = array();

        $result['number'] = floor($duration / $this->get_duration_units($unit));
        $result['seconds_left'] = $duration % $this->get_duration_units($unit);

        return $result;
    }

    /**
     *
     * @param type $time
     * @param type $justtime
     * @return type
     */
    function localtime($time,$justtime=false){
        if($justtime) $time=strtotime($time);


        $time_format = get_option('time_format');
        // in some rare cases the time format option may be empty in which case we want it to default to g:i a
        if(empty($time_format)) $time_format = 'g:i a';
        $time = date($time_format, $time);
        return $time;
    }

    /**
     * return the offseted time formated(used in post notifications)
     * @param type $val
     * @return string
     */
    function time_tzed($val=false){
        return gmdate( 'Y-m-d H:i:s', $this->servertime_to_localtime($val) );
    }

    /**
     * specify a unix server time int and it will convert it to the local time if you don't specify any unixTime value it will convert the current time
     * @param type $unixTime
     * @return int
     */
    function servertime_to_localtime($unixTime=false){

         //this should get GMT-0  time in int date('Z') is the server's time offset compared to GMT-0
        $current_server_time = time();
        $gmt_time = $current_server_time - date('Z');

        //this is the local time on this site :  current time at GMT-0 + the offset chosen in WP settings
        $current_local_time = $gmt_time + ( get_option( 'gmt_offset' ) * 3600 );

        if(!$unixTime) return $current_local_time;
        else{
            //if we've specified a time value in the function, we calculate the difference between the current servertime and the offseted current time
            $time_difference = $current_local_time - $current_server_time;
            //unix time was recorded non offseted so it's the server's time we add the timedifference to it to get the local time
            return $unixTime + $time_difference;
        }
    }

    /**
     * specify a local time int and we will convert it to the server time
     * mostly used with values produced with strtotime() strtotime converts Monday 5pm to the server time's 5pm
     * and if we want to get Monday 5pm of the local time in the server time we need to do a conversion of that value from local to server
     * @param int $server_time time value recorded in the past using time() or strtotime()
     * @return int
     */
    function localtime_to_servertime($server_time){
        //this should get GMT-0  time in int date('Z') is the server's time offset compared to GMT-0
        $current_server_time = time();
        $gmt_time = $current_server_time - date('Z');

        //this is the local time on this site :  current time at GMT-0 + the offset chosen in WP settings
        $current_local_time = $gmt_time + ( get_option( 'gmt_offset' ) * 3600 );

        //this is the time difference between the t
        $time_difference = $current_local_time - $current_server_time;
        //unix time was recorded as local time we substract to it the time difference
        return $server_time - $time_difference;
    }

    function site_current_time($date_format = 'H:i:s'){
        // display the current time
        $current_server_time = time();
        $gmt_time = $current_server_time - date('Z');

        //this is the local time on this site :  current time at GMT-0 + the offset chosen in WP settings
        $current_local_time = $gmt_time + ( get_option( 'gmt_offset' ) * 3600 );
        return date($date_format , $current_local_time);
    }

    /**
     * get the translated day name based on a lowercase day namekey
     * @param type $day if specified we return only one value otherwise we return the entire array
     * @return mixed
     */
    function getday($day=false){

        $days=array('monday'=>__('Monday',WYSIJA),
                    'tuesday'=>__('Tuesday',WYSIJA),
                    'wednesday'=>__('Wednesday',WYSIJA),
                    'thursday'=>__('Thursday',WYSIJA),
                    'friday'=>__('Friday',WYSIJA),
                    'saturday'=>__('Saturday',WYSIJA),
                    'sunday'=>__('Sunday',WYSIJA));
        if(!$day || !isset($days[$day])) return $days;
        else return $days[$day];
    }

    /**
     * get the translated day name based on a lowercase day namekey
     * @param type $week if specified we return only one, otherwise we return the entire array
     * @return mixed
     */
    function getweeksnumber($week=false){
        $weeks=array(
                    '1'=>__('1st',WYSIJA),
                    '2'=>__('2nd',WYSIJA),
                    '3'=>__('3rd',WYSIJA),
                    '4'=>__('Last',WYSIJA),
                    );
        if(!$week || !isset($weeks[$week])) return $weeks;
        else return $weeks[$week];
    }

    /**
     * get the translated day number based on the number in the month until 29th
     * @param type $day if specified we just return one otherwise we return the entire array
     * @return mixed
     */
    function getdaynumber($day=false){
        $daynumbers=array();
        //prepare an array of numbers
        for($i = 1;$i < 29;$i++) {
            switch($i){
                case 1:
                    $number=__('1st',WYSIJA);
                    break;
                case 2:
                    $number=__('2nd',WYSIJA);
                    break;
                case 3:
                    $number=__('3rd',WYSIJA);
                    break;
                default:
                    $number=sprintf(__('%1$sth',WYSIJA),$i);
            }

            $daynumbers[$i] = $number;
        }

        if(!$day || !isset($daynumbers[$day])) return $daynumbers;
        else return $daynumbers[$day];
    }

    /**
     * we use to deal with the WPLANG constant but that's silly considering there are plugins like
     * WPML which needs to alter that value
     * @param type $get_country when true if we find pt_BR as the language code we return BR if we find en_GB we return GB
     * @return string
     */
    function get_language_code($get_country = false){

        // in WP Multisite if we have a WPLANG defined in the wp-config,
        // it won't be used each site needs to have a WPLANG option defined and if it's not defined it will be empty and default to en_US
        if ( is_multisite() ) {
		// Don't check blog option when installing.
		if ( defined( 'WP_INSTALLING' ) || ( false === $ms_locale = get_option( 'WPLANG' ) ) )
			$ms_locale = get_site_option('WPLANG');

		if ( $ms_locale !== false )
			$locale = $ms_locale;
                // make sure we don't default to en_US if we have an empty locale and a WPLANG defined
                if(empty($locale) && defined('WPLANG')) $locale = WPLANG;
                else $locale = get_locale();
	}else{
            $locale = get_locale();
        }

        if($locale!=''){
            if(strpos($locale, '_')!==false){
                $locale = explode('_',$locale);
                if($get_country === true){
                    $language_code = $locale[1];
                }else{
                    $language_code = $locale[0];
                }
            }else{
                $language_code = $locale;
            }
        }else{
            $language_code = 'en';
        }
        return $language_code;
    }

    /**
     * check if a domain exist
     * @param type $domain
     * @return boolean
     */
    function check_domain_exist($domain){

        $mxhosts = array();
        // 1 - Check if the domain exists
        $checkDomain = getmxrr($domain, $mxhosts);
        // 2 - Sometimes the returned host is checkyouremailaddress-hostnamedoesnotexist262392208.com ... not sure why!
        // But we remove it if it's the case...
        if(!empty($mxhosts) && strpos($mxhosts[0],'hostnamedoesnotexist')) array_shift($mxhosts);


        if(!$checkDomain || empty($mxhosts)){
                // 3 - Lets check with another function in case of...
                $dns = @dns_get_record($domain, DNS_A);
                if(empty($dns)) return false;
        }
        return true;
    }

    function check_email_domain($email){
        return $this->check_domain_exist(substr($email,strrpos($email,'@')+1));
    }

    /**
     * let us know if the visitor is a european member
     * @return boolean
     */
    function is_european(){
        $european_members = array('AT','BE','BG','CY','CZ','DK','EE','DE','PT','EL','ES','FI','HU','LU','MT','SI',
		'FR','GB','IE','IT','LV','LT','NL','PL','SK','RO','SE','HR');

        // if the language of the site is an european
        if(in_array(strtoupper($this->get_language_code(true)), $european_members) )    return true;
        return false;
    }
}
