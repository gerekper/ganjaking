<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpUser
{
  var $userdata;

  static $id_str              = 'ID';
  static $first_name_str      = 'first_name';
  static $last_name_str       = 'last_name';
  static $password_str        = 'user_pass';
  static $paypal_email_str    = 'wafp_paypal_email';
  static $address_one_str     = 'wafp_user_address_one';
  static $address_two_str     = 'wafp_user_address_two';
  static $city_str            = 'wafp_user_city';
  static $state_str           = 'wafp_user_state';
  static $zip_str             = 'wafp_user_zip';
  static $country_str         = 'wafp_user_country';
  static $tax_id_us_str       = 'wafp_user_tax_id_us';
  static $tax_id_int_str      = 'wafp_user_tax_id_int';
  static $is_affiliate_str    = 'wafp_is_affiliate';
  static $is_blocked_str      = 'wafp_is_blocked';
  static $blocked_message_str = 'wafp_blocked_message';
  static $referrer_str        = 'wafp-affiliate-referrer';
  static $recurring_str       = 'wafp_recurring';

  public function __construct( $id = '')
  {
    $this->load_user_data_by_id( $id );
  }

  public function load_user_data_by_id( $id = '' )
  {
    if( empty($id) or !$id )
      $this->userdata = array();
    else {
      $this->userdata = (array)WafpUtils::get_userdata($id);
      $this->load_metadata();
    }

    // This must be here to ensure that we don't pull an encrypted
    // password, encrypt it a second time and store it
    unset($this->userdata[self::$password_str]);
  }

  public function load_user_data_by_login( $login = '' )
  {
    if( empty($login) or !$login )
      $this->userdata = array();
    else
      $this->userdata = (array)WafpUtils::get_userdatabylogin($login);

    $this->load_metadata();

    // This must be here to ensure that we don't pull an encrypted
    // password, encrypt it a second time and store it
    unset($this->userdata[self::$password_str]);
  }

  public function load_user_data_by_email( $email = '' )
  {
    $user_id = email_exists($email);
    $this->load_user_data_by_id( $user_id );
  }

  public function load_posted_data()
  {
    $object_vars = get_object_vars($this);

    foreach( $object_vars as $key => $value )
    {
      if(preg_match('#^.*_str$#', $key))
        $this->userdata[ $value ] = $_POST[ $value ];
    }
  }

  public function load_metadata() {
    $this->userdata[self::$first_name_str] = get_user_meta($this->get_id(), self::$first_name_str, true);
    $this->userdata[self::$last_name_str] = get_user_meta($this->get_id(), self::$last_name_str, true);
    $this->userdata[self::$paypal_email_str] = get_user_meta($this->get_id(), self::$paypal_email_str, true);
    $this->userdata[self::$address_one_str] = get_user_meta($this->get_id(), self::$address_one_str, true);
    $this->userdata[self::$address_two_str] = get_user_meta($this->get_id(), self::$address_two_str, true);
    $this->userdata[self::$city_str] = get_user_meta($this->get_id(), self::$city_str, true);
    $this->userdata[self::$state_str] = get_user_meta($this->get_id(), self::$state_str, true);
    $this->userdata[self::$zip_str] = get_user_meta($this->get_id(), self::$zip_str, true);
    $this->userdata[self::$country_str] = get_user_meta($this->get_id(), self::$country_str, true);
    $this->userdata[self::$tax_id_us_str] = get_user_meta($this->get_id(), self::$tax_id_us_str, true);
    $this->userdata[self::$tax_id_int_str] = get_user_meta($this->get_id(), self::$tax_id_int_str, true);
    $this->userdata[self::$is_affiliate_str] = get_user_meta($this->get_id(), self::$is_affiliate_str, true);
    $this->userdata[self::$is_blocked_str] = get_user_meta($this->get_id(), self::$is_blocked_str, true);
    $this->userdata[self::$blocked_message_str] = get_user_meta($this->get_id(), self::$blocked_message_str, true);
    $this->userdata[self::$referrer_str] = get_user_meta($this->get_id(), self::$referrer_str, true);
    $this->userdata[self::$recurring_str] = get_user_meta($this->get_id(), self::$recurring_str, true);
  }

  public function get_id()
  {
    return isset($this->userdata[ self::$id_str ]) ? $this->userdata[ self::$id_str ] : false;
  }

  public function set_id( $value )
  {
    $this->userdata[ self::$id_str ] = $value;
  }

  public function get_first_name()
  {
    return $this->userdata[ self::$first_name_str ];
  }

  public function set_first_name($value)
  {
    $this->userdata[ self::$first_name_str ] = $value;
  }

  public function get_last_name()
  {
    return $this->userdata[ self::$last_name_str ];
  }
  public function set_last_name($value)
  {
    $this->userdata[ self::$last_name_str ] = $value;
  }

  public function get_full_name()
  {
    return $this->get_first_name() . ' ' . $this->get_last_name();
  }

  public function get_paypal_email()
  {
    return (isset($this->userdata[ self::$paypal_email_str ])?$this->userdata[ self::$paypal_email_str ]:'');
  }

  public function set_paypal_email($value)
  {
    $this->userdata[ self::$paypal_email_str ] = $value;
  }

  public function get_address_one()
  {
    return (isset($this->userdata[ self::$address_one_str ])?$this->userdata[ self::$address_one_str ]:'');
  }

  public function set_address_one($value)
  {
    $this->userdata[ self::$address_one_str ] = $value;
  }

  public function get_address_two()
  {
    return (isset($this->userdata[ self::$address_two_str ])?$this->userdata[ self::$address_two_str ]:'');
  }

  public function set_address_two($value)
  {
    $this->userdata[ self::$address_two_str ] = $value;
  }

  public function get_city()
  {
    return (isset($this->userdata[ self::$city_str ])?$this->userdata[ self::$city_str ]:'');
  }

  public function set_city($value)
  {
    $this->userdata[ self::$city_str ] = $value;
  }

  public function get_state()
  {
    return (isset($this->userdata[ self::$state_str ])?$this->userdata[ self::$state_str ]:'');
  }

  public function set_state($value)
  {
    $this->userdata[ self::$state_str ] = $value;
  }

  public function get_zip()
  {
    return (isset($this->userdata[ self::$zip_str ])?$this->userdata[ self::$zip_str ]:'');
  }

  public function set_zip($value)
  {
    $this->userdata[ self::$zip_str ] = $value;
  }

  public function get_country()
  {
    return (isset($this->userdata[ self::$country_str ])?$this->userdata[ self::$country_str ]:'');
  }

  public function set_country($value)
  {
    $this->userdata[ self::$country_str ] = $value;
  }

  public function get_password()
  {
    return (isset($this->userdata[ self::$password_str ])?$this->userdata[ self::$password_str ]:'');
  }

  public function set_password($value)
  {
    $this->userdata[ self::$password_str ] = $value;
  }

  public function get_is_affiliate()
  {
    return (isset($this->userdata[ self::$is_affiliate_str ])?$this->userdata[ self::$is_affiliate_str ]:false);
  }

  public function set_is_affiliate($value)
  {
    $this->userdata[ self::$is_affiliate_str ] = $value;
    do_action('wafp-set-is-affiliate', $this, $value);
  }

  public function get_is_blocked()
  {
    return (isset($this->userdata[ self::$is_blocked_str ])?$this->userdata[ self::$is_blocked_str ]:false);
  }

  public function set_is_blocked($value)
  {
    $this->userdata[ self::$is_blocked_str ] = $value;
  }

  public function get_blocked_message()
  {
    return (isset($this->userdata[ self::$blocked_message_str ])?$this->userdata[ self::$blocked_message_str ]:'');
  }

  public function set_blocked_message($value)
  {
    $this->userdata[ self::$blocked_message_str ] = $value;
  }

  public function get_tax_id_us()
  {
    return (isset($this->userdata[ self::$tax_id_us_str ])?$this->userdata[ self::$tax_id_us_str ]:'');
  }

  public function set_tax_id_us($value)
  {
    $this->userdata[ self::$tax_id_us_str ] = $value;
  }

  public function get_tax_id_int()
  {
    return (isset($this->userdata[ self::$tax_id_int_str ])?$this->userdata[ self::$tax_id_int_str ]:'');
  }

  public function set_tax_id_int($value)
  {
    $this->userdata[ self::$tax_id_int_str ] = $value;
  }

  public function get_referrer()
  {
    return (isset($this->userdata[ self::$referrer_str ])?$this->userdata[ self::$referrer_str ]:'');
  }

  public function set_referrer($value)
  {
    //Don't allow setting yourself as a referrer DOH!
    if($value != $this->get_id())
      $this->userdata[ self::$referrer_str ] = $value;
  }

  public function get_recurring()
  {
    global $wafp_options;

    if( isset( $this->userdata[ self::$recurring_str ] ) )
      return $this->userdata[ self::$recurring_str ];
    else // Only run the filter of the user data if the user override hasn't been set
      return apply_filters( 'wafp-recurring-commission', $wafp_options->recurring, $this->get_id() );
  }

  public function set_recurring($value)
  {
    $this->userdata[ self::$recurring_str ] = $value;
  }

  // Generic getters and setters for the userdata object
  public function get_field($name)
  {
    return (isset($this->userdata[$name])?$this->userdata[$name]:get_user_meta($this->get_id(), $name, true));
  }

  public function set_field($name, $value)
  {
    $this->userdata[$name] = $value;
  }

  // alias of get_is_affiliate
  public function is_affiliate()
  {
    return $this->get_is_affiliate();
  }

  // alias of get_is_blocked
  public function is_blocked()
  {
    return $this->get_is_blocked();
  }

  public function create()
  {
    if(isset($this->userdata[ self::$id_str ]))
      unset($this->userdata[ self::$id_str ]);

    $user_id = $this->store();

    $this->set_id($user_id);

    return $user_id;
  }

  // alias of store
  public function update()
  {
    return $this->store();
  }

  public function store()
  {
    global $wafp_options;

    if(isset($this->userdata[ self::$id_str ]) and is_numeric($this->userdata[ self::$id_str ]))
      $user_id = wp_update_user($this->userdata);
    else
      $user_id = wp_insert_user($this->userdata);

    if ($user_id)
    {
      update_user_meta($user_id, self::$paypal_email_str, $this->get_paypal_email());
      update_user_meta($user_id, self::$is_affiliate_str, $this->get_is_affiliate());
      update_user_meta($user_id, self::$referrer_str, $this->get_referrer());
      update_user_meta($user_id, self::$recurring_str, $this->get_recurring());
      if($wafp_options->show_address_fields)
      {
        update_user_meta($user_id, self::$address_one_str, $this->get_address_one());
        update_user_meta($user_id, self::$address_two_str, $this->get_address_two());
        update_user_meta($user_id, self::$city_str, $this->get_city());
        update_user_meta($user_id, self::$state_str, $this->get_state());
        update_user_meta($user_id, self::$zip_str, $this->get_zip());
        update_user_meta($user_id, self::$country_str, $this->get_country());
      }
      if($wafp_options->show_tax_id_fields)
      {
        update_user_meta($user_id, self::$tax_id_us_str, $this->get_tax_id_us());
        update_user_meta($user_id, self::$tax_id_int_str, $this->get_tax_id_int());
      }
    }

    return $user_id;
  }

  public function send_account_notifications($password='',$send_admin_notification=true, $send_affiliate_notification=true)
  {
    global $wafp_blogname, $wafp_blogurl, $wafp_options;

    $login_link = $login_url = WafpUtils::login_url();

    if($send_admin_notification)
    {
      // Send notification email to admin user
      $from_name  = $wafp_blogname; //senders name
      $from_email = get_option('admin_email'); //senders e-mail address
      $recipient  = "{$from_name} <{$from_email}>"; //recipient
      $header     = "From: {$recipient}"; //optional headerfields

      /* translators: In this string, %s is the Blog Name/Title */
      $subject    = sprintf( __("[%s] New Affiliate Signup",'affiliate-royale', 'easy-affiliate'), $wafp_blogname);

      /* translators: In this string, %1$s is the blog's name/title, %2$s is the user's real name, %3$s is the user's username and %4$s is the user's email */
      $message    = sprintf( __( "A new user just joined your Affiliate Program at %1\$s!\n\nName: %2\$s\nUsername: %3\$s\nE-Mail: %4\$s", 'affiliate-royale' , 'easy-affiliate'), $wafp_blogname, $this->get_full_name(), $this->get_field('user_login'), $this->get_field('user_email') ) . "\n\n";

      WafpUtils::wp_mail($recipient, $subject, $message, $header);
    }

    if($send_affiliate_notification)
    {
      // Send password email to new user
      $from_name  = $wafp_blogname; //senders name
      $from_email = get_option('admin_email'); //senders e-mail address
      $recipient  = "{$this->get_full_name()} <{$this->get_field('user_email')}>"; //recipient
      $header     = "From: {$from_name} <{$from_email}>"; //optional headerfields

      // Replacement Variables
      $site_name            = $wafp_blogname;
      $affiliate_first_name = $this->get_first_name();
      $affiliate_first_name = (empty($affiliate_first_name)?$this->get_field('user_login'):$affiliate_first_name);
      $affiliate_login      = $this->get_field('user_login');
      $affiliate_password   = (empty($password)?__("*** The password you created at signup ***", 'affiliate-royale', 'easy-affiliate'):$password);

      $rep_vars = compact( 'site_name', 'affiliate_first_name', 'affiliate_first_name', 'affiliate_login', 'affiliate_password', 'login_url' );

      $subject = WafpUtils::replace_text_variables($wafp_options->welcome_email_subject, $rep_vars);
      $message = WafpUtils::replace_text_variables($wafp_options->welcome_email_body, $rep_vars);

      WafpUtils::wp_mail($recipient, $subject, $message, $header);
    }
  }

  public function reset_form_key_is_valid($key)
  {
    $stored_key = $this->get_field( 'wafp_reset_password_key' );

    return ($stored_key and ($key == $stored_key));
  }

  public function send_reset_password_requested_notification()
  {
    global $wafp_blogname, $wafp_blogurl, $wafp_options;

    $key = md5(time() . $this->get_id());
    update_user_meta( $this->get_id(), 'wafp_reset_password_key', $key );

    $permalink = WafpUtils::login_url();

    $delim     = WafpAppController::get_param_delimiter_char($permalink);

    $reset_password_link = "{$permalink}{$delim}action=reset_password&mkey={$key}&u=" . $this->get_urlencoded_user_login();

    // Send password email to new user
    $from_name  = $wafp_blogname; //senders name
    $from_email = get_option('admin_email'); //senders e-mail address
    $recipient  = "{$this->get_full_name()} <{$this->get_field('user_email')}>"; //recipient
    $header     = "From: {$from_name} <{$from_email}>"; //optional headerfields

    /* translators: In this string, %s is the Blog Name/Title */
    $subject       = sprintf( __("[%s] Affiliate Password Reset",'affiliate-royale', 'easy-affiliate'), $wafp_blogname);

    /* translators: In this string, %1$s is the user's username, %2$s is the blog's name/title, %3$s is the blog's url, %4$s the reset password link */
    $message       = sprintf( __( "Someone requested to reset your password for %1\$s on the Affiliate Program at %2\$s at %3\$s\n\nTo reset your password visit the following address, otherwise just ignore this email and nothing will happen.\n\n%4\$s", 'affiliate-royale' , 'easy-affiliate'), $this->get_field('user_login'), $wafp_blogname, $wafp_blogurl, $reset_password_link );

    WafpUtils::wp_mail($recipient, $subject, $message, $header);
  }

  public function set_password_and_send_notifications($key, $password)
  {
    global $wafp_blogname, $wafp_blogurl, $wafp_options;

    if($this->reset_form_key_is_valid($key))
    {
      delete_user_meta( $this->get_id(), 'wafp_reset_password_key' );

      $this->set_password($password);
      $this->store();

      $edit_permalink = WafpUtils::dashboard_url();

      // Send notification email to admin user
      $from_name  = $wafp_blogname; //senders name
      $from_email = get_option('admin_email'); //senders e-mail address
      $recipient  = "{$from_name} <{$from_email}>"; //recipient
      $header     = "From: {$recipient}"; //optional headerfields

      /* translators: In this string, %s is the Blog Name/Title */
      $subject    = sprintf( __("[%s] Affiliate Password Lost/Changed",'affiliate-royale', 'easy-affiliate'), $wafp_blogname);

      /* translators: In this string, %1$s is the user's username */
      $message       = sprintf( __( "Affiliate Password Lost and Changed for user: %1\$s", 'affiliate-royale' , 'easy-affiliate'), $this->get_field('user_login') );

      WafpUtils::wp_mail($recipient, $subject, $message, $header);

    $login_link = WafpUtils::login_url();

      // Send password email to new user
      $from_name  = $wafp_blogname; //senders name
      $from_email = get_option('admin_email'); //senders e-mail address
      $recipient  = "{$this->get_full_name()} <{$this->get_field('user_email')}>"; //recipient
      $header     = "From: {$from_name} <{$from_email}>"; //optional headerfields

      /* translators: In this string, %s is the Blog Name/Title */
      $subject       = sprintf( __("[%s] Your new Affiliate Password",'affiliate-royale', 'easy-affiliate'), $wafp_blogname);

      /* translators: In this string, %1$s is the user's first name, %2$s is the blog's name/title, %3$s is the user's username, %4$s is the user's password, and %5$s is the blog's URL... */
      $message       = sprintf( __( "%1\$s,\n\nYour Affiliate Password was successfully reset on %2\$s!\n\nUsername: %3\$s\nPassword: %4\$s\n\nYou can login here: %5\$s", 'affiliate-royale' , 'easy-affiliate'), (empty($this->first_name)?$this->get_field('user_login'):$this->first_name), $wafp_blogname, $this->get_field('user_login'), $password, $login_link );

      WafpUtils::wp_mail($recipient, $subject, $message, $header);

      return true;
    }

    return false;
  }

  /** Figures out the commission type */
  public function get_commission_type() {
    global $wafp_options;

    if( $commission_type = get_user_meta( $this->get_id(), 'wafp_commission_type', true ) )
      return $commission_type;

    return apply_filters( 'wafp-commission-type', $wafp_options->commission_type, $this->get_id() );
  }

  /** Commission levels this user is eligible to receive. */
  public function get_commission_levels() {
    global $wafp_options;

    if($levels = get_user_meta( $this->get_id(), 'wafp_override', true ))
      return $levels;
    else
      // Account for the user override commission percentage -- if there is one
      return apply_filters( 'wafp_commission_percentages',
                            $wafp_options->commission,
                            $this->get_id() );
  }

  // How did this user become eligible for their current commission structure?
  public function get_commission_source() {
    global $wafp_options;

    if( get_user_meta( $this->get_id(), 'wafp_override', true ) )
      return array( 'slug' => 'user', 'label' => __('User Override','affiliate-royale', 'easy-affiliate') );
    else
      return apply_filters( 'wafp-commission-source',
                            array( 'slug' => 'global', 'label' => __('Global','affiliate-royale', 'easy-affiliate') ),
                            $this->get_id() );
  }

  /** Calculates the commission percentage for the current user on the given level.
    * This can now either be an actual percentage ... or a fixed amount based on
    * what the user selected in the Affiliate Royale options.
    */
  public function get_commission_percentage( $level = 0 )
  {
    $commissions = $this->get_commission_levels();
    if( !isset($commissions[$level]) ) { return 0.0; }
    return (float)$commissions[$level];
  }

  /** Calculates the commission amount for the current user for the amount on a given level */
  public function calculate_commission( $amount, $level = 0, $item_name )
  {
    global $wafp_options;

    $commission_percentage = $this->get_commission_percentage($level);
    $commission_type = $this->get_commission_type();

    if( $commission_type=='percentage' )
      $commission = (($commission_percentage !== false) ? WafpUtils::format_float((float)$amount * $commission_percentage / 100.00) : false);
    else if( $commission_type=='fixed' )
      $commission = (($commission_percentage !== false /* good place to check if amount is 0.00 ??? */) ? WafpUtils::format_float($commission_percentage) : false );

    //Deprecated
    $commission = apply_filters('wafp-calculate-commission', $commission, $amount, $level);
    return apply_filters('wafp-calculate-affiliate-commission', $commission, $amount, $level, $this, $item_name);
  }

  /** Get commission percentages for the affiliates above the current user.
    * This is used when calculating percentages ... it gives an accurate commission
    * level for the current sale ... for all the affiliates who can get a commission. */
  public function get_commission_percentages($im_the_first_affiliate=false, $compress_levels=false)
  {
    $commission_percentages = array();
    $affiliates = $this->get_ancestors($compress_levels);

    foreach($affiliates as $level => $affiliate)
      $commission_percentages[] = ( $affiliate->is_affiliate() ? $affiliate->get_commission_percentage($level) : 0.0 );

    return $commission_percentages;
  }

  /** Get commission amounts for the affiliates above the current user given the total sale amount */
  public function calculate_commissions($amount, $im_the_first_affiliate, $compress_levels, $item_name)
  {
    $commission_amounts = array();
    $affiliates = $this->get_ancestors($compress_levels);

    foreach($affiliates as $level => $affiliate)
      $commission_amounts[] = ( $affiliate->is_affiliate() ? $affiliate->calculate_commission($amount, $level, $item_name) : 0.0 );

    return $commission_amounts;
  }

  public function get_commission_percentages_total($im_the_first_affiliate=false, $compress_levels=false)
  {
    return (float)array_sum($this->get_commission_percentages($im_the_first_affiliate, $compress_levels));
  }

  public function calculate_commissions_total($amount, $im_the_first_affiliate, $compress_levels, $item_name)
  {
    return (float)array_sum($this->calculate_commissions($amount, $im_the_first_affiliate, $compress_levels, $item_name));
  }

  /** Returns an array of the ancestor affiliates for this user */
  public function get_ancestors( $compress_levels=false, $used_ids=array() )
  {
    global $wafp_options;

    if(in_array((int)$this->get_id(),$used_ids) or ($compress_levels and !$this->is_affiliate()))
      $ancestors = array(); // Skip me bro
    else
      $ancestors = array($this);

    $ref_id = $this->get_referrer();
    if( !empty($ref_id) and
        is_numeric($ref_id) and
        !in_array((int)$this->get_id(),$used_ids) and // Yeah ... avoid infinite recursion bro ... not cool
        $ref = new WafpUser($ref_id)) {
      $used_ids[] = (int)$this->get_id();
      $ancestors = array_merge( $ancestors, $ref->get_ancestors( $compress_levels, $used_ids ) );
    }

    return $ancestors;
  }

  public function get_children( $just_affiliates=false ) {
    return $this->get_descendants( 1, $just_affiliates );
  }

  public function child_count() {
    global $wpdb;
    $query = "SELECT count(*) FROM {$wpdb->usermeta} AS um WHERE um.meta_key=%s AND um.meta_value=%s";
    $query = $wpdb->prepare( $query, self::$referrer_str, $this->get_id() );
    return $wpdb->get_var($query);
  }

  /** Returns an array of the descendants for this user */
  public function get_descendants( $depth=10, $just_affiliates=false, $level=0, $user_ids=array() ) {
    global $wafp_options, $wpdb;

    $user_ids[] = $this->get_id();

    // Minimum depth of 1 level (always return at least children)
    if($depth < 1) { $depth = 1; }

    $aff_array = array();

    if( $level >= $depth )
      return $aff_array;

    $query = "SELECT um.user_id FROM {$wpdb->usermeta} AS um WHERE um.meta_key=%s AND um.meta_value=%s";
    $query = $wpdb->prepare( $query, self::$referrer_str, $this->get_id() );

    $aff_ids = $wpdb->get_col( $query );

    foreach( $aff_ids as $aff_id ) {
      if( is_numeric($aff_id) and ( $aff = new WafpUser($aff_id) ) ) {
        if($just_affiliates and !$aff->is_affiliate())
          continue;

        // Prevent issues from potential circular inheritance
        if( !in_array($aff_id, $user_ids) ) {
          $aff_array[] = array( 'object' => $aff,
                                'level' => $level,
                                'children' => $aff->get_descendants( $depth, $just_affiliates, ( $level + 1 ) , $user_ids ) );
        }
      }
    }

    return $aff_array;
  }

  public function pay_commission($is_recurring = false)
  {
    global $wafp_options;

    $pay_me = true;

    $errors = $this->check_forced_account_info();
      if(!empty($errors)) //User has not filled out their account info - let's abort
        return false;

    if($is_recurring)
    {
      $user_override_set = (get_user_meta($this->get_id(), 'wafp_override', true));

      if($user_override_set)
        $pay_me = $this->get_recurring();
      else
        $pay_me = $wafp_options->recurring;
    }

    return apply_filters('wafp_pay_commission', $pay_me, $is_recurring, $this->get_id());
  }

  public function sale_count() {
    global $wafp_db, $wpdb;

    $query = $wpdb->prepare("SELECT COUNT(*) FROM {$wafp_db->transactions} WHERE affiliate_id = %d AND status IN ('Completed','complete') AND type = 'commission'", $this->get_id());
    return $wpdb->get_var($query);
  }

  public function sales_total() {
    global $wafp_db, $wpdb;

    $query = $wpdb->prepare("SELECT SUM(sale_amount) FROM {$wafp_db->transactions} WHERE affiliate_id = %d AND status IN ('Completed','complete') AND type = 'commission'", $this->get_id());
    return $wpdb->get_var($query);
  }

  public function commissions_total() {
    global $wafp_db, $wpdb;

    $query = $wpdb->prepare("SELECT SUM(cm.commission_amount)-SUM(cm.correction_amount) FROM {$wafp_db->commissions} AS cm JOIN {$wafp_db->transactions} AS tr ON cm.transaction_id=tr.id WHERE cm.affiliate_id = %d AND tr.status IN ('Completed','complete') AND tr.type = 'commission'", $this->get_id());
    return $wpdb->get_var($query);
  }

  public function click_count() {
    global $wafp_db, $wpdb;

    $query = $wpdb->prepare("SELECT COUNT(*) FROM {$wafp_db->clicks} WHERE affiliate_id = %d", $this->get_id());
    return $wpdb->get_var($query);
  }

  public static function get_dashboard_stats( $affiliate_id ) {
    global $wafp_db, $wpdb, $wafp_options;

    $aff = new WafpUser($affiliate_id);

    return array( 'clicks'       => $aff->click_count(),
                  'transactions' => $aff->sale_count(),
                  'total'        => $aff->sales_total(),
                  'commission'   => $aff->commissions_total() );
  }

  /* Will return datatable code or list_table */
  public static function affiliate_datatable( $datatable=true,
                                              $order_by='',
                                              $order='',
                                              $paged='',
                                              $search='',
                                              $perpage=10 ) {
    global $wafp_db, $wpdb, $wafp_options;

    $year = date('Y');
    $month = date('m');
    $cols = array(
      'username' => "{$wpdb->users}.user_login", 'first_name' => 'um_first_name.meta_value',
      'last_name' => 'um_last_name.meta_value',
      'ID' => "{$wpdb->users}.ID",
      'mtd_clicks' => "(SELECT IFNULL(COUNT(*),0) FROM {$wafp_db->clicks} as clk WHERE clk.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN '{$year}-{$month}-01 00:00:00' AND NOW())",
      'ytd_clicks' => "(SELECT IFNULL(COUNT(*),0) FROM {$wafp_db->clicks} as clk WHERE clk.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN '{$year}-01-01 00:00:00' AND NOW())",
      'mtd_commissions' => "(SELECT CONCAT('{$wafp_options->currency_symbol}', FORMAT(IFNULL(SUM(commish.commission_amount),0.00),2) ) FROM {$wafp_db->commissions} AS commish WHERE commish.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN '{$year}-{$month}-01 00:00:00' AND NOW())",
      'ytd_commissions' => "(SELECT CONCAT('{$wafp_options->currency_symbol}', FORMAT(IFNULL(SUM(commish.commission_amount),0.00),2) ) FROM {$wafp_db->commissions} AS commish WHERE commish.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN '{$year}-01-01 00:00:00' AND NOW())",
      'signup_date' => "DATE({$wpdb->users}.user_registered)",
      'parent_name' => "CONCAT(um_parent_first_name.meta_value,' ', um_parent_last_name.meta_value, ' (', parent.user_login, ')')",
      'parent_id' => "parent.ID"
    );

    $joins = array(
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_first_name ON um_first_name.user_id={$wpdb->users}.ID AND um_first_name.meta_key='first_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_last_name ON um_last_name.user_id={$wpdb->users}.ID AND um_last_name.meta_key='last_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_affiliate_referrer ON um_affiliate_referrer.user_id={$wpdb->users}.ID AND um_affiliate_referrer.meta_key='".self::$referrer_str."'",
      "LEFT OUTER JOIN {$wpdb->users} AS parent ON parent.ID=um_affiliate_referrer.meta_value",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_parent_first_name ON um_parent_first_name.user_id=parent.ID AND um_parent_first_name.meta_key='first_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_parent_last_name ON um_parent_last_name.user_id=parent.ID AND um_parent_last_name.meta_key='last_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_is_affiliate ON um_is_affiliate.user_id={$wpdb->users}.ID AND um_is_affiliate.meta_key='wafp_is_affiliate'"
    );

    $args = array(
      'um_is_affiliate.meta_value IS NOT NULL',
      'um_is_affiliate.meta_value=1'
    );

    if($datatable)
      return WafpDb::datatable($cols, $wpdb->users, '', '', $joins, $args);
    else
      return WafpDb::list_table($cols, $wpdb->users, $joins, $args, $order_by, $order, $paged, $search, $perpage);
  }

/***** STATIC METHODS *****/
  public static function validate_signup($params,$errors)
  {
    global $wafp_options;

    extract($params);

    // $nonce_data = WafpNonceModel::get_cookie_data();
    // if($nonce_data === false || !WafpNonceModel::is_valid($nonce_data['nonce'], $nonce_data['ts']))
      // $errors[] = __('Robots not allowed', 'affiliate-royale');

    if(empty($user_login))
      $errors[] = __('Username must not be blank','affiliate-royale', 'easy-affiliate');

    if(!preg_match('#^[a-zA-Z0-9_]+$#',$user_login))
      $errors[] = __('Username must only contain letters, numbers and/or underscores','affiliate-royale', 'easy-affiliate');

    if ( username_exists( $user_login ) )
      $errors[] = __('Username is Already Taken.','affiliate-royale', 'easy-affiliate');

    if($wafp_options->payment_type == 'paypal' and (empty($wafp_paypal_email) || !is_email($wafp_paypal_email)))
      $errors[] = __('PayPal email address is required and must be a real and properly formatted email address','affiliate-royale', 'easy-affiliate');

    if(empty($user_email) || !is_email($user_email))
      $errors[] = __('Email must be a real and properly formatted email address','affiliate-royale', 'easy-affiliate');

    if(email_exists($user_email))
      $errors[] = __('Email Address has already been used by another user.','affiliate-royale', 'easy-affiliate');

    if(empty($wafp_user_password))
      $errors[] = __('You must enter a Password.','affiliate-royale', 'easy-affiliate');

    if(empty($wafp_user_password_confirm))
      $errors[] = __('You must enter a Password Confirmation.', 'affiliate-royale', 'easy-affiliate');

    if($wafp_user_password != $wafp_user_password_confirm)
      $errors[] = __('Your Password and Password Confirmation don\'t match.', 'affiliate-royale', 'easy-affiliate');

    if($wafp_options->show_address_fields and empty($wafp_user_address_one))
      $errors[] = __('You must enter an Address', 'affiliate-royale', 'easy-affiliate');

    if($wafp_options->show_address_fields and empty($wafp_user_city))
      $errors[] = __('You must enter a City', 'affiliate-royale', 'easy-affiliate');

    if($wafp_options->show_address_fields and empty($wafp_user_state))
      $errors[] = __('You must enter a State/Province', 'affiliate-royale', 'easy-affiliate');

    if($wafp_options->show_address_fields and empty($wafp_user_zip))
      $errors[] = __('You must enter a Zip/Postal Code', 'affiliate-royale', 'easy-affiliate');

    if($wafp_options->show_address_fields and empty($wafp_user_country))
      $errors[] = __('You must enter a Country', 'affiliate-royale', 'easy-affiliate');

    if(isset($wafp_honeypot) and !empty($wafp_honeypot))
      $errors[] = __('You must be a human to signup for this site', 'affiliate-royale', 'easy-affiliate');

    if($wafp_options->affiliate_agreement_enabled && !isset($wafp_user_signup_agreement))
      $errors[] = __('You must agree to the Affiliate Signup Agreement', 'affiliate-royale', 'easy-affiliate');

    return $errors;
  }

  public static function validate_login($params,$errors)
  {
    extract($params);

    if(empty($log))
      $errors[] = __('Username must not be blank','affiliate-royale', 'easy-affiliate');

    if(!username_exists($log))
      $errors[] = __('Username was not found','affiliate-royale', 'easy-affiliate');

    return $errors;
  }

  public static function validate_forgot_password($params,$errors)
  {
    extract($params);

    if(empty($wafp_user_or_email))
      $errors[] = __('You must enter a Username or Email','affiliate-royale', 'easy-affiliate');
    else
    {
      $is_email = (is_email($wafp_user_or_email) and email_exists($wafp_user_or_email));
      $is_username = username_exists($wafp_user_or_email);

      if(!$is_email and !$is_username)
        $errors[] = __('That Username or Email wasn\'t found.','affiliate-royale', 'easy-affiliate');
    }

    return $errors;
  }

  public static function validate_reset_password($params,$errors)
  {
    extract($params);

    if(empty($wafp_user_password))
      $errors[] = __('You must enter a Password.','affiliate-royale', 'easy-affiliate');

    if(empty($wafp_user_password_confirm))
      $errors[] = __('You must enter a Password Confirmation.', 'affiliate-royale', 'easy-affiliate');

    if($wafp_user_password != $wafp_user_password_confirm)
      $errors[] = __('Your Password and Password Confirmation don\'t match.', 'affiliate-royale', 'easy-affiliate');

    return $errors;
  }

  public function check_forced_account_info()
  {
    global $wafp_options;

    $errors = array();

    if($wafp_options->force_account_info)
    {
      if($wafp_options->show_tax_id_fields)
        if( empty($this->userdata['wafp_user_tax_id_us']) &&
            empty($this->userdata['wafp_user_tax_id_int']))
          $errors[] = __('You must fill out the tax ID field before you can begin promoting', 'affiliate-royale', 'easy-affiliate');

      if($wafp_options->payment_type == 'paypal' && empty($this->userdata['wafp_paypal_email']))
        $errors[] =  __('You must enter your PayPal Email before you can begin promoting', 'affiliate-royale', 'easy-affiliate');
    }

    return $errors;
  }

  //$str may be a user_login, or a User ID - let's attempt to figure out which and return the proper aff id
  public static function get_aff_id_from_string($str) {
    $str = urldecode($str);

    if($ID = username_exists($str)) {
      return $ID;
    }

    if(is_numeric($str)) {
      $aff = new WafpUser($str);

      if($aff->get_id() && $aff->is_affiliate()) {
        return $str;
      }
    }

    return false;
  }

  public function default_affiliate_url() {
    $h = home_url();
    $delim = preg_match( '/\?/', $h ) ? '&' : '?';
    $username = $this->get_urlencoded_user_login();

    if(is_email($this->get_field('user_login'))) {
      $username = $this->get_id(); //Use the ID instead of an email duh
    }

    return "{$h}{$delim}aff=".$username;
  }

  public function get_urlencoded_user_login() {
    return urlencode($this->get_field('user_login'));
  }

  public function affiliate_profile() {
    $ref_id = $this->get_referrer();

    if( !empty($ref_id) ) {
      $ref = new WafpUser( $ref_id );
      $refname = $ref->get_full_name() . ' (' . $ref->get_field('user_login') . ')';
    }
    else
      $refname = '';

    return apply_filters( 'wafp-affiliate-profile',
                          array( 'referrer' => $refname,
                                 'name' => $this->get_full_name(),
                                 'is_affiliate' => $this->get_is_affiliate() ? __('Yes','affiliate-royale', 'easy-affiliate') : __('No','affiliate-royale', 'easy-affiliate'),
                                 'id' => $this->get_id(),
                                 'username' => $this->get_field('user_login'),
                                 'email' => $this->get_field('user_email'),
                                 'sales' => $this->sale_count() ),
                          $this->get_id() );
  }
}

