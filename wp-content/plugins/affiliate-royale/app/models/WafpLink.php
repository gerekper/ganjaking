<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpLink
{
  /** STATIC CRUD METHODS **/
  public static function create( $args )
  {
    global $wafp_db;
    return $wafp_db->create_record($wafp_db->links, $args);
  }

  public static function update( $id, $args )
  {
    global $wafp_db;
    return $wafp_db->update_record($wafp_db->links, $id, $args);
  }

  public static function update_image( $id, $image, $width, $height )
  {
    global $wafp_db;

    $args = compact( 'image', 'width', 'height' );
    return $wafp_db->update_record($wafp_db->links, $id, $args);
  }

  public static function update_target_url( $id, $target_url )
  {
    global $wafp_db;

    $args = compact( 'target_url' );
    return $wafp_db->update_record($wafp_db->links, $id, $args);
  }


  public static function delete( $id )
  {
    global $wafp_db;

    $link = WafpLink::get_stored_object($id);

    if(!empty($link->rec->image))
      @unlink($link->image_path());

    $args = compact( 'id' );
    return $wafp_db->delete_records($wafp_db->links, $args);
  }

  public static function get_one($id)
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->get_one_record($wafp_db->links, $args);
  }

  public static function get_count()
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->links);
  }

  public static function add_file( $image_path )
  {
    $size_path = $image_path;
    if ( ! ini_get('allow_url_fopen' )) {
      $dir_adjustment = WafpUtils::is_subdir_install()?'..':'.';
      $size_path = ABSPATH . $dir_adjustment . wp_make_link_relative( $image_path );
    }
    $image_meta = @getimagesize( $size_path );
    return array( 'image'  => $image_path,
                  'width'  => $image_meta[0],
                  'height' => $image_meta[1] );
  }

  public static function get_all($order_by = '', $limit = '')
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->links, array(), $order_by, $limit);
  }

  public static function get_all_objects($order_by = '', $limit = '', $force = false)
  {
    $all_records = WafpLink::get_all($order_by, $limit);

    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = WafpLink::get_stored_object($record->id, $force);

    return $my_objects;
  }

  public static function get_stored_object($id, $force = false)
  {
    static $my_objects;

    if( !isset($my_objects) )
      $my_objects = array();

    if( $force or
        !isset($my_objects[$id]) or
        empty($my_objects[$id]) or
        !is_object($my_objects[$id]) )
      $my_objects[$id] = new WafpLink($id);

    return $my_objects[$id];
  }

  public static function is_valid_slug($slug, $id, $slug_array) {
    foreach( $slug_array as $link_id => $link_slug ) {
      if($link_slug == $slug and $link_id != $id)
        return false;
    }

    return true;
  }

  public static function validate($link_array, &$errors)
  {
    extract( $link_array );

    if( empty($target_url) )
      $errors[] = __("Target URL can't be blank", 'affiliate-royale', 'easy-affiliate');

    if( !empty($target_url) and
        !preg_match('/^http.?:\/\/.*\..*$/', $target_url ) and
        !preg_match('!^(http|https)://(localhost|127\.0\.0\.1)(:\d+)?(/[\w- ./?%&=]*)?!', $target_url ) )
      $errors[] = __("Target URL must be a valid URL", 'affiliate-royale', 'easy-affiliate') . ": " . $target_url;

    $slug_array = $_REQUEST['wafp_link_slug'];
    $slug_array[] = $_REQUEST['wafp_new_link_slug'];
    if( !empty($slug) and !self::is_valid_slug( $slug, $id, $slug_array ) )
      $errors[] = __("The slug must be unique to this link", 'affiliate-royale', 'easy-affiliate') . ": " . $target_url;
  }

  public static function get_link_from_slug( $slug ) {
    global $wafp_db, $wpdb;

    $query = "SELECT id FROM {$wafp_db->links} WHERE slug=%s";
    $link_id = $wpdb->get_var($wpdb->prepare($query, $slug));

    if(!is_null($link_id)) { //Got something from the DB
      return new WafpLink($link_id);
    }
    elseif(is_numeric($slug)) { //The user never gave this link a slug, so let's use the id instead
      return new WafpLink($slug);
    }
    else { //Nothing matched
      return false;
    }
  }

  public static function track($affiliate_id, $link_id=0)
  {
    global $wpdb, $wafp_options;

    $user = new WafpUser( $affiliate_id );
    if( $user->is_affiliate() )
    {
      $errors = $user->check_forced_account_info();
      if(!empty($errors)) //User has not filled out their account info - let's abort
        return false;

      $first_click = 0;

      $click_ip = $_SERVER['REMOTE_ADDR'];
      $click_referrer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';

      $click_uri = $_SERVER['REQUEST_URI'];
      $click_user_agent = $_SERVER['HTTP_USER_AGENT'];

      $cookie_name = "wafp_click";
      $cookie_expire_time = time()+60*60*24* $wafp_options->expire_after_days; // Expire in 60 days

      $old_cookie = isset($_COOKIE[$cookie_name])?$_COOKIE[$cookie_name]:false;
      if( $old_cookie )
        $first_click = (((int)$old_cookie != (int)$affiliate_id)?1:0);
      else
        $first_click = 1;

      // Set cookie -- overwrite the cookie if it's already there -- we'll employ a "last touch" methodology
      setcookie($cookie_name,$affiliate_id,$cookie_expire_time,'/');
      do_action('wafp-setcookie', $affiliate_id, $cookie_expire_time, '/');


      return WafpClick::create( $click_ip, $click_user_agent, $click_referrer, $click_uri, $link_id, $affiliate_id, $first_click );
    }

    return false;
  }

  /** INSTANCE VARIABLES & METHODS **/
  public $rec;

  public function __construct($id)
  {
    $this->rec         = WafpLink::get_one($id);
    $target_path_array = wp_upload_dir();
    $this->upload_url  = "{$target_path_array['baseurl']}/affiliate-royale/banners";
    $this->upload_path = "{$target_path_array['basedir']}/affiliate-royale/banners";
  }

  public function display_url($affiliate_id) {
    global $wafp_options;

    $slug = (empty($this->rec->slug) or is_null($this->rec->slug))?$this->rec->id:$this->rec->slug;
    $user = new WafpUser($affiliate_id);
    $username = $user->get_urlencoded_user_login();

    if(is_email($user->get_field('user_login'))) {
      $username = $user->get_id(); //Use the ID instead of an email duh
    }

    if($wafp_options->pretty_affiliate_links) {
      return home_url( '/' . $username . '/' . $slug );
    }
    else {
      $delim = preg_match('/\?/', home_url()) ? '&' : '?';
      return home_url("{$delim}aff=" . $username . "&p=" . $slug);
    }
  }

  public function link_code($affiliate_id, $target='')
  {
    if(!empty($target))
      $target = " target=\"{$target}\"";

    if( isset($this->rec->image) and !empty($this->rec->image))
    {
       $attrib = null;
       if ($this->rec->width)
         $attrib .= sprintf(' width="%s"', $this->rec->width);

       if ($this->rec->width)
         $attrib .= sprintf(' height="%s"', $this->rec->height);

       return apply_filters( 'wafp_link_code_image', "<a href=\"". $this->display_url($affiliate_id) . "\"{$target}><img src=\"{$this->rec->image}\"$attrib /></a>", $affiliate_id, $target, $this->display_url($affiliate_id), $this->rec->image, $attrib, $this );
    }
    else {
      $description = empty($this->rec->description)?__('Affiliate Link', 'affiliate-royale', 'easy-affiliate'):stripslashes($this->rec->description);

      return apply_filters( 'wafp_link_code_text', "<a href=\"". $this->display_url($affiliate_id) . "\"{$target}>{$description}</a>", $affiliate_id, $target, $this->display_url($affiliate_id), $description, $this );
    }
  }

  public function image_url()
  {
    return apply_filters('wafp_image_url',"{$this->upload_url}/" . basename($this->rec->image));
  }

  public function image_path()
  {
    return "{$this->upload_path}/" . basename($this->rec->image);
  }

  public function track_and_redirect($affiliate_id)
  {
    self::track($affiliate_id, $this->rec->id);

    // 301's can interfere with tracking by caching the redirect so we're doing a 307/302 here
    if($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.0')
      header("HTTP/1.1 302 Found");
    else
      header("HTTP/1.1 307 Temporary Redirect");

    header("Location: " . apply_filters('wafp_affiliate_target_url', $this->rec->target_url, $affiliate_id));
    exit;
  }
}
