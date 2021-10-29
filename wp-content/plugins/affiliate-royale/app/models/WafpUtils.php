<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpUtils
{
  public static function get_user_id_by_email($email)
  {
    if(isset($email) and !empty($email))
    {
      global $wpdb;
      $query = "SELECT ID FROM {$wpdb->users} WHERE user_email=%s";
      $query = $wpdb->prepare($query, esc_sql($email));
      return (int)$wpdb->get_var($query);
    }

    return '';
  }

  public static function is_image($filename)
  {
    if(!file_exists($filename))
      return false;

    $file_meta = getimagesize($filename);

    $image_mimes = array("image/gif", "image/jpeg", "image/png");

    return in_array($file_meta['mime'], $image_mimes);
  }

  public static function rewriting_on()
  {
    $permalink_structure = get_option('permalink_structure');

    return ($permalink_structure and !empty($permalink_structure));
  }

  // Returns a list of just user data from the wp_users table
  public static function get_raw_users($where = '', $order_by = 'user_login')
  {
    global $wpdb;

    static $raw_users;

    if(!isset($raw_users))
    {
      $where    = ((empty($where))?'':" WHERE {$where}");
      $order_by = ((empty($order_by))?'':" ORDER BY {$order_by}");

      $query = "SELECT * FROM {$wpdb->users}{$where}{$order_by}";
      $raw_users = $wpdb->get_results($query);
    }

    return $raw_users;
  }

  public static function is_robot()
  {
    $ua_string = trim(urldecode($_SERVER['HTTP_USER_AGENT']));

    // Yah, if the whole user agent string is missing -- wtf?
    if(empty($ua_string))
      return 1;

    // Some bots actually say they're bots right up front let's get rid of them asap
    if(preg_match("#(bot|spider|crawl)#i",$ua_string))
      return 1;

    $browsecap = WafpUtils::php_get_browser($ua_string);
    $btype = trim($browsecap['browser']);

    $crawler = $browsecap['crawler'];

    // If php_browsecap tells us its a bot, let's believe it
    if($crawler == 1)
      return 1;

    // If the Browser type was unidentifiable then it's most likely a bot
    if(empty($btype))
      return 1;

    return 0;
  }

  public static function site_domain()
  {
    return preg_replace('#^https?://(www\.)?([^\?\/]*)#','$2', home_url());
  }

  public static function send_affiliate_sale_notification($params)
  {
    global $wafp_options, $wafp_blogname;

    if($wafp_options->affiliate_email)
    {
      extract($params);
      $email_body = self::replace_text_variables( $wafp_options->affiliate_email_body, $params );

      // Send notification email to admin user (to and from the admin user)
      $to_email = $affiliate_email;
      $to_name  = "{$affiliate_first_name} {$affiliate_last_name}";
      $nice_to_email = "{$to_name} <{$to_email}>";

      $from_email = get_option('admin_email'); //senders name
      $nice_from_email = "{$wafp_blogname} <{$from_email}>";
      $headers = array("From: {$nice_from_email}"); //optional headerfields

      $fn = function($content_type) {
        return 'text/plain';
      };

      add_filter('wp_mail_content_type', $fn);
      WafpUtils::wp_mail($nice_to_email, $wafp_options->affiliate_email_subject, $email_body, $headers);
      remove_filter('wp_mail_content_type', $fn);
    }
  }

  public static function send_affiliate_sale_notifications($params, $affiliates)
  {
    global $wafp_options;

    $payment_amount = $params['payment_amount'];
    $params['payment_amount']        = WafpAppHelper::format_currency( $params['payment_amount']);
    foreach($affiliates as $level => $affiliate)
    {
      // Prevent blocked affiliates from getting emails
      if($affiliate->is_blocked()) { continue; }

      $curr_percentage = $affiliate->get_commission_percentage( $level );

      if( (float)$curr_percentage <= 0.00 )
        continue;

      $params['affiliate_login']       = $affiliate->get_field('user_login');
      $params['affiliate_email']       = $affiliate->get_field('user_email');
      $params['affiliate_first_name']  = $affiliate->get_first_name();
      $params['affiliate_last_name']   = $affiliate->get_last_name();

      $params['affiliate_id']          = $affiliate->get_id();
      $params['commission_percentage'] = WafpUtils::format_float($curr_percentage);
      $params['commission_amount']     = $affiliate->calculate_commission( $payment_amount, $level, $params['item_name'] );

      $params['commission_percentage'] = ($affiliate->get_commission_type()=='fixed' ? $wafp_options->currency_symbol : '' ).$params['commission_percentage'].($affiliate->get_commission_type()=='percentage' ? "%" : '');
      $params['commission_amount']     = WafpAppHelper::format_currency( $params['commission_amount']);
      $params['payment_level']         = $level + 1; // we're doing 1 based level any time its displayed

      WafpUtils::send_affiliate_sale_notification($params);
    }
  }

  public static function send_admin_sale_notification($params, $affiliates)
  {
    global $wafp_options, $wafp_blogname;

    if($wafp_options->admin_email)
    {
      extract($params);

      $email_body = '';
      $payment_amount_num = $payment_amount;
      $payment_amount     = WafpAppHelper::format_currency( $payment_amount);
      $i = 0;
      foreach($affiliates as $level => $affiliate)
      {
        $curr_percentage = $affiliate->get_commission_percentage( $level );

        if( (float)$curr_percentage <= 0.00 )
          continue;

        $affiliate_login       = $affiliate->get_field('user_login');
        $affiliate_email       = $affiliate->get_field('user_email');
        $affiliate_first_name  = self::with_default($affiliate->get_first_name(),$affiliate_login);
        $affiliate_last_name   = $affiliate->get_last_name();

        $affiliate_id          = $affiliate->get_id();
        $commission_percentage = WafpUtils::format_float($curr_percentage);
        $commission_amount     = $affiliate->calculate_commission( $payment_amount_num, $level, $item_name );

        $commission_percentage = ($affiliate->get_commission_type()=='fixed' ? $wafp_options->currency_symbol : '' ).$commission_percentage.($affiliate->get_commission_type()=='percentage' ? "%" : '');
        $commission_amount     = WafpAppHelper::format_currency( $commission_amount);
        $payment_level         = $level + 1;

        $rep_vars = array_merge( $params, compact( 'payment_amount_num', 'payment_amount', 'affiliate_login', 'affiliate_email', 'affiliate_first_name', 'affiliate_last_name', 'affiliate_id', 'commission_percentage', 'commission_amount', 'payment_level' ) );

        $email_body .= self::replace_text_variables( $wafp_options->admin_email_body, $rep_vars );

        if($i < (count($affiliates) - 1))
          $email_body .= "\n=====================================\n\n";

        $i++;
      }

      $from_email = get_option('admin_email'); //senders name
      $nice_from_email = "{$wafp_blogname} <{$from_email}>";
      $headers = array("From: {$nice_from_email}"); //optional headerfields
      $to_email = apply_filters('wafp-admin-notify-email', $nice_from_email);

      $fn = function($content_type) {
        return 'text/plain';
      };

      add_filter('wp_mail_content_type', $fn);
      WafpUtils::wp_mail($to_email, $wafp_options->admin_email_subject, $email_body, $headers);
      remove_filter('wp_mail_content_type', $fn);
    }
  }

  public static function is_logged_in_and_current_user($user_id)
  {
    $current_user = WafpUtils::get_currentuserinfo();

    return (WafpUtils::is_user_logged_in() and ($current_user->ID == $user_id));
  }

  public static function is_logged_in_and_an_admin()
  {
    return (WafpUtils::is_user_logged_in() and WafpUtils::is_admin());
  }

  public static function is_logged_in_and_a_subscriber()
  {
    return (WafpUtils::is_user_logged_in() and WafpUtils::is_subscriber());
  }

  public static function is_admin()
  {
    return current_user_can('administrator');
  }

  public static function is_subscriber()
  {
    return (current_user_can('subscriber') and !current_user_can('contributor'));
  }

  public static function array_to_string($my_array, $debug=false, $level=0)
  {
    if(is_array($my_array))
    {
      $my_string = '';

      if($level<=0 and $debug)
        $my_string .= "<pre>";

      foreach($my_array as $my_key => $my_value)
      {
        for($i=0; $i<$level; $i++)
          $my_string .= "    ";

        $my_string .= "{$my_key} => " . WafpUtils::array_to_string($my_value, $debug, $level+1) . "\n";
      }

      if($level<=0 and $debug)
        $my_string .= "</pre>";

      return $my_string;
    }
    else if(is_string($my_array))
      return $my_array;
    else
      return '';
  }

  public static function object_to_string($object)
  {
    ob_start();
    print_r($object);
    $obj_string = ob_get_contents();
    ob_end_clean();
    return $obj_string;
  }

  public static function replace_text_variables($text, $variables)
  {
    $patterns = array();
    $replacements = array();

    foreach($variables as $var_key => $var_val)
    {
      $patterns[] = '/\{\$' . preg_quote( $var_key, '/' ) . '\}/';
      $replacements[] = preg_replace( '/\$/', '\\\$', $var_val ); // $'s must be escaped for some reason
    }

    $preliminary_text = preg_replace( $patterns, $replacements, $text );

    // Clean up any failed matches
    return preg_replace( '/\{\$.*?\}/', '', $preliminary_text );
  }

  public static function with_default($variable, $default)
  {
    if(isset($variable))
    {
      if(is_numeric($variable))
        return $variable;
      elseif(!empty($variable))
        return $variable;
    }

    return $default;
  }

  public static function format_float($number, $num_decimals = 2)
  {
    return number_format((float)$number, $num_decimals, '.', '');
  }

  public static function is_subdir_install()
  {
    return preg_match( '#^https?://[^/]+/.+$#', home_url() );
  }

  public static function dashboard_url() {
    global $wafp_options;

    if($wafp_options->dashboard_page_id > 0)
      return get_permalink($wafp_options->dashboard_page_id);
    else
      return home_url();
  }

  public static function signup_url() {
    global $wafp_options;

    if($wafp_options->signup_page_id > 0)
      return get_permalink($wafp_options->signup_page_id);
    else
      return wp_login_url() . '?action=register';
  }

  public static function login_url() {
    global $wafp_options;

    if($wafp_options->login_page_id > 0)
      return get_permalink($wafp_options->login_page_id);
    else
      return wp_login_url(self::dashboard_url());
  }

  public static function logout_url() {
    return wp_logout_url(self::login_url());
  }

  public static function get_pages() {
    global $wpdb;

    $query = "SELECT * FROM {$wpdb->posts} WHERE post_status=%s AND post_type=%s";

    $query = $wpdb->prepare( $query, "publish", "page" );

    $results = $wpdb->get_results( $query );

    if($results)
      return $results;
    else
      return array();
  }

  /**
  * Formats a line (passed as a fields array) as CSV and returns the CSV as a string.
  * Adapted from http://us3.php.net/manual/en/function.fputcsv.php#87120
  */
  public static function to_csv( $struct,
                                 $delimiter = ',',
                                 $enclosure = '"',
                                 $enclose_all = false,
                                 $null_to_mysql_null = false ) {
    $delimiter_esc = preg_quote($delimiter, '/');
    $enclosure_esc = preg_quote($enclosure, '/');

    $csv = '';
    $line_num = 0;

    if((!is_array($struct) and !is_object($struct)) or empty($struct)) { return $csv; }

    foreach( $struct as $line ) {
      $output = array();

      foreach( $line as $field ) {
        if( is_null($field) and $null_to_mysql_null ) {
          $output[] = 'NULL';
          continue;
        }

        // Enclose fields containing $delimiter, $enclosure or whitespace
        if( $enclose_all or preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) )
          $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
        else
          $output[] = $field;
      }

      $csv .= implode( $delimiter, $output ) . "\n";
      $line_num++;
    }

    return $csv;
  }

  public static function random_string( $length=10, $lowercase=true, $uppercase=false, $symbols=false ) {
    $characters = '0123456789';
    $characters .= $uppercase?'ABCDEFGHIJKLMNOPQRSTUVWXYZ':'';
    $characters .= $lowercase?'abcdefghijklmnopqrstuvwxyz':'';
    $characters .= $symbols?'@#*^%$&!':'';
    $string = '';
    $max_index = strlen($characters) - 1;

    for($p = 0; $p < $length; $p++)
      $string .= $characters[mt_rand(0, $max_index)];

    return $string;
  }

  /* Keys to indexes, indexes to keys ... do it! */
  public static function array_invert($invertable) {
    $inverted = array();

    foreach( $invertable as $key => $orray ) {
      foreach($orray as $index => $value) {
        if(!isset($inverted[$index])) { $inverted[$index] = array(); }
        $inverted[$index][$key] = $value;
      }
    }

    return $inverted;
  }

/* PLUGGABLE FUNCTIONS AS TO NOT STEP ON OTHER PLUGINS' CODE */
  public static function get_currentuserinfo()
  {
    WafpUtils::_include_pluggables('wp_get_current_user');
    $current_user = wp_get_current_user();

    if(isset($current_user->ID) && $current_user->ID > 0)
      return new WafpUser($current_user->ID);
    else
      return false;
  }

  public static function get_userdata($id)
  {
    WafpUtils::_include_pluggables('get_userdata');
    $data = get_userdata($id);
    // Handle the returned object for wordpress > 3.2
    if (!empty($data->data))
    {
      return $data->data;
    }
    return $data;
  }

  public static function get_userdatabylogin($screenname)
  {
    WafpUtils::_include_pluggables('get_user_by');
    $data = get_user_by('login', $screenname);
    //$data = get_userdatabylogin($screenname);
    // Handle the returned object for wordpress > 3.2
    if(isset($data->data) and !empty($data->data)) {
      return $data->data;
    }
    return $data;
  }

  public static function minutes($n = 1) /*wrapperTested*/
  {
    return $n * 60;
  }

  public static function hours($n = 1) /*wrapperTested*/
  {
    return $n * self::minutes(60);
  }

  public static function days($n = 1) /*wrapperTested*/
  {
    return $n * self::hours(24);
  }

  public static function weeks($n = 1) /*tested*/
  {
    return $n * self::days(7);
  }

  public static function months($n, $month_timestamp) /*tested*/
  {
    $seconds = 0;

    for($i=0; $i < $n; $i++)
    {
      $month_seconds = self::days((int)date('t', $month_timestamp));
      $seconds += $month_seconds;
      $month_timestamp += $month_seconds;
    }

    return $seconds;
  }

  public static function years($n, $year_timestamp)
  {
    $seconds = 0;

    for($i=0; $i < $n; $i++)
    {
      $seconds += $year_seconds = self::days(365 + (int)date('L', $year_timestamp));
      $year_timestamp += $year_seconds;
    }

    return $seconds;
  }

  public static function wp_mail($recipient, $subject, $message, $header) {
    //Prevent duplicate emails since we're running some emails through the_content filter like idiots
    static $here;
    if(!isset($here) || empty($here)) { $here = array(); }
    $md5 = md5($recipient.$subject);
    if(isset($here[$md5])) { return; }
    $here[$md5] = true;

    WafpUtils::_include_pluggables('wp_mail');

    //Let's get rid of the pretty TO's -- causing too many problems
    //mbstring?
    if(extension_loaded('mbstring')) {
      if(mb_strpos($recipient, '<') !== false) {
        $recipient = mb_substr($recipient, (mb_strpos($recipient, '<') + 1), -1);
      }
    }
    else {
      if(strpos($recipient, '<') !== false) {
        $recipient = substr($recipient, (strpos($recipient, '<') + 1), -1);
      }
    }

    return wp_mail($recipient, $subject, $message, $header);
  }

  public static function is_user_logged_in()
  {
    WafpUtils::_include_pluggables('is_user_logged_in');
    return is_user_logged_in();
  }

  public static function get_avatar( $id, $size )
  {
    WafpUtils::_include_pluggables('get_avatar');
    return get_avatar( $id, $size );
  }

  public static function wp_hash_password( $password_str )
  {
    WafpUtils::_include_pluggables('wp_hash_password');
    return wp_hash_password( $password_str );
  }

  public static function wp_generate_password( $length, $special_chars )
  {
    WafpUtils::_include_pluggables('wp_generate_password');
    return wp_generate_password( $length, $special_chars );
  }

  public static function wp_redirect( $location, $status=302 )
  {
    WafpUtils::_include_pluggables('wp_redirect');
    return wp_redirect( $location, $status );
  }

  public static function wp_salt( $scheme='auth' )
  {
    WafpUtils::_include_pluggables('wp_salt');
    return wp_salt( $scheme );
  }

  public static function _include_pluggables($function_name)
  {
    if(!function_exists($function_name))
      require_once(ABSPATH . WPINC . '/pluggable.php');
  }
}
