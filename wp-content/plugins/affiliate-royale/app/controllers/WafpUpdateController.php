<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class WafpUpdateController
{
  public static function load_hooks()
  {
    add_filter('pre_set_site_transient_update_plugins', 'WafpUpdateController::queue_update');
    add_filter('plugins_api', 'WafpUpdateController::plugin_info', 11, 3);
    add_action('admin_enqueue_scripts', 'WafpUpdateController::enqueue_scripts');
    add_action('admin_notices', 'WafpUpdateController::activation_warning');
    //add_action('wafp_display_options', 'WafpUpdateController::queue_button');
    add_action('admin_init', 'WafpUpdateController::activate_from_define');
    add_action('wp_ajax_wafp_edge_updates', 'WafpUpdateController::wafp_edge_updates');
  }

  public static function route()
  {
    if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
      return self::process_form();
    else
      if(isset($_GET['action']) and $_GET['action'] == 'deactivate' and isset($_GET['_wpnonce']) and wp_verify_nonce($_GET['_wpnonce'], 'affiliate-royale_deactivate'))
        return self::deactivate();
      else
        return self::display_form();
  }

  public static function display_form($message='', $errors=array())
  {
    $wafp_options = WafpOptions::fetch();

    // We just force the queue to update when this page is visited
    // that way we ensure the license info transient is set
    self::manually_queue_update();

    if(!empty($wafp_options->mothership_license) and empty($errors)) {
      $li = get_site_transient( 'wafp_license_info' );
    }

    require( WAFP_VIEWS_PATH.'/update/ui.php' );
  }

  public static function process_form()
  {
    if(!isset($_POST['_wpnonce']) or !wp_verify_nonce($_POST['_wpnonce'],'activation_form'))
      wp_die(_e('Why you creepin\'?', 'affiliate-royale', 'easy-affiliate'));

    $wafp_options = WafpOptions::fetch();

    if(!isset($_POST[$wafp_options->mothership_license_str]))
    {
      self::display_form();
      return;
    }

    $message = '';
    $errors = array();
    $wafp_options->mothership_license = stripslashes($_POST[$wafp_options->mothership_license_str]);
    $domain = urlencode(WafpUtils::site_domain());

    try
    {
      $args = compact('domain');
      $act = self::send_mothership_request("/license_keys/activate/{$wafp_options->mothership_license}", $args, 'post');
      self::manually_queue_update();
      $wafp_options->store(false);
      $message = $act['message'];
    }
    catch(Exception $e)
    {
      $errors[] = $e->getMessage();
    }

    self::display_form($message, $errors);
  }

  public static function activate_from_define() {
    $wafp_options = WafpOptions::fetch();

    if( defined('AFFILIATE_ROYALE_LICENSE_KEY') and
        $wafp_options->mothership_license != AFFILIATE_ROYALE_LICENSE_KEY ) {
      $lk = AFFILIATE_ROYALE_LICENSE_KEY;
    }
    elseif( defined('MEMBERPRESS_LICENSE_KEY') and
        $wafp_options->mothership_license != MEMBERPRESS_LICENSE_KEY ) {
      $lk = MEMBERPRESS_LICENSE_KEY;
    }
    else {
      return;
    }

    $message = '';
    $errors = array();
    $wafp_options->mothership_license = stripslashes($lk);
    $domain = urlencode(WafpUtils::site_domain());

    try {
      $args = compact('domain');

      if(!empty($wafp_options->mothership_license))
        $act = self::send_mothership_request("/license_keys/deactivate/{$wafp_options->mothership_license}", $args, 'post');

      $act = self::send_mothership_request("/license_keys/activate/{$lk}", $args, 'post');

      self::manually_queue_update();

      // If we're using defines then we have to do this with defines too
      $wafp_options->edge_updates = false;
      $wafp_options->store(false);

      $message = $act['message'];
      $callback = function() use ($message) {
        require( WAFP_VIEWS_PATH . '/shared/errors.php' );
      };
    }
    catch(Exception $e) {
      $callback = function() use ($error) {
        require( WAFP_VIEWS_PATH . '/update/activation_warning.php' );
      };
    }

    add_action( 'admin_notices', $callback );
  }

  public static function deactivate()
  {
    $wafp_options = WafpOptions::fetch();
    $domain = urlencode(WafpUtils::site_domain());
    $message = '';
    $errors = array();

    try
    {
      $args = compact('domain');
      $act = self::send_mothership_request("/license_keys/deactivate/{$wafp_options->mothership_license}", $args, 'post');
      self::manually_queue_update();
      $wafp_options->mothership_license = '';
      $wafp_options->store(false);
      $message = $act['message'];
    }
    catch(Exception $e)
    {
      $errors[] = $e->getMessage();
    }

    self::display_form($message, $errors);
  }

  public static function queue_update($transient, $force=false) {
    $wafp_options = WafpOptions::fetch();

    if( $force or ( false === ( $update_info = get_site_transient('wafp_update_info') ) ) ) {
      if(empty($wafp_options->mothership_license))
      {
        // Just here to query for the current version
        $args = array();
        if( $wafp_options->edge_updates or ( defined( "AFFILIATE_ROYALE_EDGE" ) and AFFILIATE_ROYALE_EDGE ) )
          $args['edge'] = 'true';

        $version_info = self::send_mothership_request( "/versions/latest/".WAFP_EDITION, $args );
        $curr_version = $version_info['version'];
        $download_url = '';
      }
      else
      {
        try
        {
          $domain = urlencode(WafpUtils::site_domain());
          $args = compact('domain');

          if( $wafp_options->edge_updates or ( defined( "AFFILIATE_ROYALE_EDGE" ) and AFFILIATE_ROYALE_EDGE ) )
            $args['edge'] = 'true';

          $license_info = self::send_mothership_request("/versions/info/".WAFP_EDITION."/{$wafp_options->mothership_license}", $args, 'get');
          $curr_version = $license_info['version'];
          $download_url = $license_info['url'];
          set_site_transient( 'wafp_license_info',
                              $license_info,
                              WafpUtils::hours(12) );
        }
        catch(Exception $e)
        {
          try
          {
            // Just here to query for the current version
            $args = array();
            if( $wafp_options->edge_updates or ( defined( "AFFILIATE_ROYALE_EDGE" ) and AFFILIATE_ROYALE_EDGE ) )
              $args['edge'] = 'true';

            $version_info = self::send_mothership_request("/versions/latest/".WAFP_EDITION, $args);
            $curr_version = $version_info['version'];
            $download_url = '';
          }
          catch(Exception $e)
          {
            if(isset($transient->response[WAFP_PLUGIN_SLUG]))
              unset($transient->response[WAFP_PLUGIN_SLUG]);

            return $transient;
          }
        }
      }

      set_site_transient( 'wafp_update_info',
                          compact( 'curr_version', 'download_url' ),
                          WafpUtils::hours(12) );
    }
    else
      extract( $update_info );

    if(isset($curr_version) and version_compare($curr_version, WAFP_VERSION, '>'))
    {
      $transient->response[WAFP_PLUGIN_SLUG] = (object)array(
        'id'          => $curr_version,
        'slug'        => 'affiliate-royale',
        'new_version' => $curr_version,
        'url'         => 'http://affiliateroyale.com',
        'package'     => $download_url
      );
    }
    else
      unset( $transient->response[WAFP_PLUGIN_SLUG] );

    return $transient;
  }

  public static function manually_queue_update()
  {
    $transient = get_site_transient("update_plugins");
    set_site_transient("update_plugins", self::queue_update($transient, true));
  }

  public static function queue_button()
  {
    ?>
    <a href="<?php echo admin_url('admin.php?page=affiliate-royale-options&action=queue&_wpnonce=' . wp_create_nonce('WafpUpdateController::manually_queue_update')); ?>" class="button"><?php _e('Check for Update', 'affiliate-royale', 'easy-affiliate')?></a>
    <?php
  }

  public static function plugin_info($api, $action, $args)
  {
    global $wp_version;

    if(!isset($action) or $action != 'plugin_information')
      return $api;

    if(isset( $args->slug) and !preg_match("#.*".$args->slug.".*#", WAFP_PLUGIN_SLUG))
      return $api;

    $wafp_options = WafpOptions::fetch();

    if(empty($wafp_options->mothership_license))
    {
      // Just here to query for the current version
      $args = array();
      if( $wafp_options->edge_updates or ( defined( "AFFILIATE_ROYALE_EDGE" ) and AFFILIATE_ROYALE_EDGE ) )
        $args['edge'] = 'true';

      $version_info = self::send_mothership_request("/versions/latest/".WAFP_EDITION, $args);
      $curr_version = $version_info['version'];
      $version_date = $version_info['version_date'];
      $download_url = '';
    }
    else
    {
      try
      {
        $domain = urlencode(WafpUtils::site_domain());
        $args = compact('domain');

        if( $wafp_options->edge_updates or ( defined( "AFFILIATE_ROYALE_EDGE" ) and AFFILIATE_ROYALE_EDGE ) )
          $args['edge'] = 'true';

        $license_info = self::send_mothership_request("/versions/info/".WAFP_EDITION."/{$wafp_options->mothership_license}", $args, 'get');
        $curr_version = $license_info['version'];
        $version_date = $license_info['version_date'];
        $download_url = $license_info['url'];
      }
      catch(Exception $e)
      {
        try
        {
          $args = array();
          if( $wafp_options->edge_updates or ( defined( "AFFILIATE_ROYALE_EDGE" ) and AFFILIATE_ROYALE_EDGE ) )
            $args['edge'] = 'true';

          // Just here to query for the current version
          $version_info = self::send_mothership_request("/versions/latest/".WAFP_EDITION, $args);
          $curr_version = $version_info['version'];
          $version_date = $version_info['version_date'];
          $download_url = '';
        }
        catch(Exception $e)
        {
          if(isset($transient->response[WAFP_PLUGIN_SLUG]))
            unset($transient->response[WAFP_PLUGIN_SLUG]);

          return $transient;
        }
      }
    }

    return (object) array("slug" => WAFP_PLUGIN_NAME,
                          "name" => WAFP_DISPLAY_NAME,
                          "author" => '<a href="http://blairwilliams.com">' . WAFP_AUTHOR . '</a>',
                          "author_profile" => "http://blairwilliams.com",
                          "contributors" => array("Caseproof" => "http://caseproof.com"),
                          "homepage" => "http://affiliateroyale.com",
                          "version" => $curr_version,
                          "new_version" => $curr_version,
                          "requires" => $wp_version,
                          "tested" => $wp_version,
                          "compatibility" => array($wp_version => array($curr_version => array( 100, 0, 0))),
                          "rating" => "100.00",
                          "num_ratings" => "1",
                          "downloaded" => "1000",
                          "added" => "2012-12-02",
                          "last_updated" => $version_date,
                          "tags" => array("membership" => __("Membership", 'affiliate-royale', 'easy-affiliate'),
                                          "membership software" => __("Membership Software", 'affiliate-royale', 'easy-affiliate'),
                                          "members" => __("Members", 'affiliate-royale', 'easy-affiliate'),
                                          "payment" => __("Payment", 'affiliate-royale', 'easy-affiliate'),
                                          "protection" => __("Protection", 'affiliate-royale', 'easy-affiliate'),
                                          "rule" => __("Rule", 'affiliate-royale', 'easy-affiliate'),
                                          "lock" => __("Lock", 'affiliate-royale', 'easy-affiliate'),
                                          "access" => __("Access", 'affiliate-royale', 'easy-affiliate'),
                                          "community" => __("Community", 'affiliate-royale', 'easy-affiliate'),
                                          "admin" => __("Admin", 'affiliate-royale', 'easy-affiliate'),
                                          "pages" => __("Pages", 'affiliate-royale', 'easy-affiliate'),
                                          "posts" => __("Posts", 'affiliate-royale', 'easy-affiliate'),
                                          "plugin" => __("Plugin", 'affiliate-royale', 'easy-affiliate')),
                          "sections" => array("description" => "<p>" . WAFP_DESCRIPTION . "</p>",
                                              "faq" => "<p>" . sprintf(__('You can access in-depth information about Affiliate Royale at %1$sthe Affiliate Royale User Manual%2$s.', 'affiliate-royale', 'easy-affiliate'), "<a href=\"http://affiliateroyale.com/user-manual\">", "</a>") . "</p>", "changelog" => "<p>".__('No Additional information right now', 'affiliate-royale', 'easy-affiliate')."</p>"),
                          "download_link" => $download_url );
  }

  public static function send_mothership_request( $endpoint,
                                                  $args=array(),
                                                  $method='get',
                                                  $domain='http://mothership.caseproof.com',
                                                  $blocking=true )
  {
    $uri = "{$domain}{$endpoint}";

    $arg_array = array( 'method'    => strtoupper($method),
                        'body'      => $args,
                        'timeout'   => 15,
                        'blocking'  => $blocking,
                        'sslverify' => false
                      );

    $resp = wp_remote_request($uri, $arg_array);

    // If we're not blocking then the response is irrelevant
    // So we'll just return true.
    if($blocking == false)
      return true;

    if(is_wp_error($resp))
      throw new Exception(__('You had an HTTP error connecting to Caseproof\'s Mothership API', 'affiliate-royale', 'easy-affiliate'));
    else
    {
      if(null !== ($json_res = json_decode($resp['body'], true)))
      {
        if(isset($json_res['error']))
          throw new Exception($json_res['error']);
        else
          return $json_res;
      }
      else
        throw new Exception(__( 'Your License Key was invalid', 'affiliate-royale', 'easy-affiliate'));
    }

    return false;
  }

  public static function enqueue_scripts($hook)
  {
    if($hook == 'affiliate-royale_page_affiliate-royale-activate')
    {
      wp_enqueue_style('mepr-activate-css', WAFP_CSS_URL.'/admin-activate.css', array(), WAFP_VERSION);
      wp_enqueue_script('mepr-activate-js', WAFP_JS_URL.'/admin_activate.js', array(), WAFP_VERSION);
    }
  }

  public static function activation_warning()
  {
    $wafp_options = WafpOptions::fetch();

    if(empty($wafp_options->mothership_license) and
       (!isset($_REQUEST['page']) or
         $_REQUEST['page']!='affiliate-royale-activate'))
      require(WAFP_VIEWS_PATH.'/update/activation_warning.php');
  }

  public static function wafp_edge_updates()
  {
    if(!is_super_admin() or !wp_verify_nonce($_POST['wpnonce'],'wp-edge-updates'))
      die(json_encode(array('error' => __('You do not have access.', 'affiliate-royale', 'easy-affiliate'))));

    if(!isset($_POST['edge']))
      die(json_encode(array('error' => __('Edge updates couldn\'t be updated.', 'affiliate-royale', 'easy-affiliate'))));

    $wafp_options = WafpOptions::fetch();
    $wafp_options->edge_updates = ($_POST['edge']=='true');
    $wafp_options->store(false);

    // Re-queue updates when this is checked
    self::manually_queue_update();

    die(json_encode(array('state' => ($wafp_options->edge_updates ? 'true' : 'false'))));
  }
} //End class

