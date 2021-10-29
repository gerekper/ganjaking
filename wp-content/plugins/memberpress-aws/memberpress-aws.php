<?php
/*
Plugin Name: MemberPress AWS
Plugin URI: http://memberpress.com
Description: Allows you to protect and reliably serve up all kinds of files using MemberPress and Amazon S3.
Version: 1.3.5
Author: Caseproof, LLC
Author URI: http://caseproof.com
Text Domain: memberpress-aws
Copyright: 2004-2015, Caseproof, LLC
*/

if( !defined( 'ABSPATH' ) ) { die( 'You are not allowed to call this page directly.' ); }

define( 'MPAWS_PLUGIN_SLUG', plugin_basename( __FILE__ ) );
define( 'MPAWS_PLUGIN_NAME', dirname( MPAWS_PLUGIN_SLUG ) );
define( 'MPAWS_PATH', WP_PLUGIN_DIR . '/' . MPAWS_PLUGIN_NAME );
define( 'MPAWS_URL', plugins_url( $path = '/' . MPAWS_PLUGIN_NAME ) );
define( 'MPAWS_JS_URL', MPAWS_URL . '/js' );
define( 'MPAWS_CSS_URL', MPAWS_URL . '/css' );
define( 'MPAWS_EDITION', 'memberpress-aws' );

// Autoload all the requisite classes
function mpaws_autoloader( $class_name ) {
  // Only load MPAWS classes here
  if( preg_match( '/^Mpaws.+$/', $class_name ) &&
      ( $class_name !== 'MpawsSdk' ) ) {
    $filepath = MPAWS_PATH . "/{$class_name}.php";
    if( file_exists( $filepath ) ) { require_once( $filepath ); }
  }
}

// if __autoload is active, put it on the spl_autoload stack
if( is_array( spl_autoload_functions() ) and
    in_array( '__autoload', spl_autoload_functions() ) ) {
   spl_autoload_register( '__autoload' );
}

// Add the autoloader
spl_autoload_register( 'mpaws_autoloader' );

// We only use the SDK if the webserver's requirements have been met
function mpaws_can_use_sdk() {
  return ( version_compare( PHP_VERSION, '5.3.3', '>=' ) &&
           function_exists( 'curl_version' ) &&
           ( $curl_version = curl_version() ) &&
           isset( $curl_version['version'] ) &&
           version_compare( $curl_version['version'], '7.16.2', '>=' ) &&
           isset( $curl_version['ssl_version'] ) &&
           isset( $curl_version['libz_version'] ) );
}

define( 'MPAWS_CAN_USE_SDK', mpaws_can_use_sdk() );

class MpawsAppController {
  public $memberpress_active;

  public function __construct() {
    $this->load_hooks();
  }

  public function load_hooks() {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if( is_plugin_active( 'memberpress/memberpress.php' ) ) {
      $this->memberpress_active = true;
      add_action( 'mepr_display_options_tabs', array( $this, 'display_option_tab') );
      add_action( 'mepr_display_options', array( $this, 'display_option_fields') );
      add_action( 'mepr-process-options', array( $this, 'store_option_fields') );
      add_action( 'mepr-partial-content-codes', array( $this, 'shortcode_documentation' ) );

      // So, this is for reverse compatibility
      add_shortcode( 'mepr-s3-url', array( $this, 'url_shortcode' ) );
      add_shortcode( 'mepr-s3-link', array( $this, 'link_shortcode' ) );
      add_shortcode( 'mepr-s3-audio', array( $this, 'audio_shortcode' ) );
      add_shortcode( 'mepr-s3-video', array( $this, 'video_shortcode' ) );
    }
    else {
      $this->memberpress_active = false;
      add_action( 'admin_menu', array( $this, 'menu' ) );
    }

    add_filter( 'wp_video_shortcode', array( $this, 'modify_shortcode_html' ) );
    add_filter( 'wp_audio_shortcode', array( $this, 'modify_shortcode_html' ) );

    add_shortcode( 'aws-s3-url', array( $this, 'url_shortcode' ) );
    add_shortcode( 'aws-s3-link', array( $this, 'link_shortcode' ) );
    add_shortcode( 'aws-s3-audio', array( $this, 'audio_shortcode' ) );
    add_shortcode( 'aws-s3-video', array( $this, 'video_shortcode' ) );

    add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ), 99 );
  }

  public function menu() {
    add_options_page( __( 'MemberPress AWS', 'memberpress-aws' ), __( 'MemberPress AWS', 'memberpress-aws' ), 'administrator', 'aws-settings', array( $this, 'display_settings_page' ) );
  }

  public function load_admin_scripts() {
    wp_enqueue_style( 'mpaws-styles', MPAWS_URL . '/styles.css' );

    wp_enqueue_script( 'mpaws-tooltip', MPAWS_URL.'/tooltip.js', array( 'jquery', 'wp-pointer' ) );

    //wp_localize_script('mpaws-tooltip', 'MeprTooltip', array( 'show_about_notice' => self::show_about_notice(),
    //                                                         'about_notice' => self::about_notice() ));
  }

  // This is a nasty, dirty hack ... but necessary for v4 to work
  public function modify_shortcode_html( $output ) {
    $settings = MpawsUtils::get_settings();

    extract( $settings );

    if( $v4_enabled ) {
      // In order to get this blasted v4 signature working we've got to scrub that underscore parameter
      // Not sure how this will affect the compatibility with wordpress / mediaelementjs
      // Looks like the controls fail to work properly when there are multiple players
      $output = preg_replace( '!&(#0?38;)?_=\d+!', '', $output );
    }

    return $output;
  }

  public function display_option_tab() {
    ?>
      <a class="nav-tab" id="aws" href="#"><?php _e( 'AWS', 'memberpress-aws' ); ?></a>
    <?php
  }

  public function display_settings_page() {
    if( MpawsUtils::is_post_request() ) {
      // TODO: Validate license key before continuing
      update_option( 'mepr_aws_license_key', $_POST['mepr_aws_license_key'] );
      $this->store_option_fields();
    }

    if( isset( $_POST['mepr_aws_license_key'] ) and !empty( $_POST['mepr_aws_license_key'] ) ) {
      $license_key = $_POST['mepr_aws_license_key'];
    }
    else {
      $license_key = get_option('mepr_aws_license_key');
    }

    require( MPAWS_PATH . '/views/settings_page.php' );
  }

  public function display_option_fields() {
    if( isset( $_POST['mepr_aws_access_key'] ) and !empty( $_POST['mepr_aws_access_key'] ) ) {
      $access_key = $_POST['mepr_aws_access_key'];
    }
    else {
      $access_key = get_option( 'mepr_aws_access_key' );
    }

    if( isset( $_POST['mepr_aws_secret_key'] ) and !empty( $_POST['mepr_aws_secret_key'] ) ) {
      $secret_key = $_POST['mepr_aws_secret_key'];
    }
    else {
      $secret_key = get_option( 'mepr_aws_secret_key' );
    }

    if( MPAWS_CAN_USE_SDK ) {
      require_once( MPAWS_PATH . '/MpawsSdk.php' );

      if( isset( $_POST['mepr_aws_access_key'] ) ) { // Just use access_key to see if we've just posted something
        $v4_enabled = isset( $_POST['mepr_aws_v4_enabled'] );
      }
      else {
        $v4_enabled = get_option( 'mepr_aws_v4_enabled' );
      }

      if( isset( $_POST['mepr_aws_region'] ) and !empty( $_POST['mepr_aws_region'] ) ) {
        $region = $_POST['mepr_aws_region'];
      }
      else {
        $region = get_option( 'mepr_aws_region' );
      }

      $regions = MpawsSdk::get_regions();
    }
    else {
      $v4_enabled = false;
      $region = false;
      $regions = array();
    }

    require( MPAWS_PATH . '/views/settings.php' );
  }

  public function store_option_fields() {
    update_option( 'mepr_aws_access_key', $_POST['mepr_aws_access_key'] );
    update_option( 'mepr_aws_secret_key', $_POST['mepr_aws_secret_key'] );

    if( MPAWS_CAN_USE_SDK ) {
      update_option( 'mepr_aws_region', $_POST['mepr_aws_region'] );
      update_option( 'mepr_aws_v4_enabled', isset( $_POST['mepr_aws_v4_enabled'] ) );
    }
  }

  public function url_shortcode( $atts, $content = null ) {
    $content = is_null( $content ) ? __( 'Download', 'memberpress-aws' ) : $content;

    // Backwards compatibility
    if( isset( $atts['bucket'] ) && isset( $atts['path'] ) ) { $atts['src'] = "{$atts['bucket']}/{$atts['path']}"; }

    $atts = shortcode_atts( array(
      'src' => '',
      'expires' => '+5 minutes',
      'rule' => null,
      'download' => false
    ), $atts );

    // Check against rules if one's set
    if( $this->memberpress_active &&
        !is_null($atts['rule']) &&
        MeprRule::is_protected_by_rule($atts['rule']) )
    { return ''; }

    if( false === ( $s3_url = MpawsUtils::get_aws_url( $atts['src'], $atts['expires'], ($atts['download'] == 'force') ) ) ) { return ''; }

    return $s3_url;
  }

  public function link_shortcode( $atts, $content = null ) {
    $s3_url = self::url_shortcode( $atts, $content );
    if( empty( $s3_url ) ) { return ''; }
    $target = ( isset( $atts['target'] ) && $atts['target'] == 'new' ) ? " target=\"_blank\"" : "";
    return "<a href=\"{$s3_url}\" class=\"mepr-aws-link\"{$target}>{$content}</a>";
  }

  public function audio_shortcode( $atts, $content='', $tag='' ) {
    return $this->av_shortcode( 'audio', $atts, $content, $tag );
  }

  public function video_shortcode( $atts, $content='', $tag='' ) {
    return $this->av_shortcode( 'video', $atts, $content, $tag );
  }

  private function av_shortcode( $av, $atts, $content='', $tag='' ) {
    if( !in_array( $av, array('audio', 'video') ) ) { return ''; }

    // Backwards compatibility
    if( isset( $atts['bucket'] ) && isset( $atts['path'] ) ) { $atts['src'] = "{$atts['bucket']}/{$atts['path']}"; }

    $link_atts = shortcode_atts( array(
      'expires' => '+1 hour', // Default 1 hour ...
      'rule' => null
    ), $atts );

    if( $this->memberpress_active &&
        !is_null( $link_atts['rule'] ) &&
        MeprRule::is_protected_by_rule( $link_atts['rule'] )  )
    { return ''; }

    if( $av === 'video' ) {
      $exts = $av_atts = wp_get_video_extensions();
    }
    else {
      $exts = $av_atts = wp_get_audio_extensions();
    }

    $av_atts[] = 'src'; // we look for this along with av exts

    // for backwards compatibility
    if( isset( $atts['autostart'] ) ) { $atts['autoplay'] = $atts['autostart']; }
    if( isset( $atts['repeat'] ) ) { $atts['loop'] = $atts['repeat']; }
    if( $av==='video' && isset( $atts['image'] ) ) { $atts['poster'] = $atts['image']; }

    // Eliminate AWS specific stuff ... if present
    if( isset($atts['bucket']) ) { unset( $atts['bucket'] ); }
    if( isset($atts['path']) ) { unset( $atts['path'] ); }
    if( isset($atts['expires']) ) { unset( $atts['expires'] ); }

    $funcs = array();
    foreach( $av_atts as $att ) {
      if( !isset( $atts[$att] ) ) { continue; }

      $atts[$att] = MpawsUtils::get_aws_url( $atts[$att], $link_atts['expires'] );

      if( empty( $atts[$att] ) ) { continue; }

      // This is a brutal and ugly hack due to a bug in WP's
      // wp_check_filetype function which was fixed in WordPress 4.1
      global $wp_version;

      preg_match( '!\.([^\.]*)$!i', $atts[$att], $m );
      $fake_ext = $m[1];

      // Ugh, the att key has to be the fake_ext
      if( $att !== 'src' ) {
        $atts[strtolower( $fake_ext )] = $atts[$att];
        unset( $atts[$att] );
        $att = strtolower( $fake_ext );
      }

      $mimes = wp_get_mime_types();
      $type = wp_check_filetype( preg_replace( '!\?.*$!', '', $atts[$att] ), $mimes );

      if( in_array( $type['ext'], $exts ) ) {
        $funcs[] = (object)array(
          'mime' => function( $mt ) use ( $fake_ext, $type ) {
            return array_merge( $mt, array( preg_quote($fake_ext, '!' ) => $type['type']) );
          },
          'ext' => function( $e ) use ( $fake_ext ) {
            return array_merge( $e, array( strtolower( $fake_ext ) ) );
          }
        );
      }
    }

    // Artificially modify the mime types filter and the video extensions so we can work around that bug
    // Only happens on versions of wp < 4.1
    foreach( $funcs as $func ) {
      add_filter( 'mime_types', $func->mime );
      add_filter( "wp_{$av}_extensions", $func->ext );
    }

    if( $av === 'video' ) {
      $html = wp_video_shortcode( $atts, $content );
    }
    else {
      $html = wp_audio_shortcode( $atts, $content );
    }

    // Set things back in place lest we have serious issues because of our hack
    foreach( $funcs as $func ) {
      remove_filter( 'mime_types', $func->mime );
      remove_filter( "wp_{$av}_extensions", $func->ext );
    }

    return $html;
  }

  public function shortcode_documentation($rule) {
    require( MPAWS_PATH . '/views/documentation.php' );
  }
} //End Class

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if( is_plugin_active( 'memberpress/memberpress.php' ) ) {
  require_once( WP_PLUGIN_DIR . '/memberpress/app/lib/MeprAddonUpdates.php' );
  new MeprAddonUpdates(
    MPAWS_EDITION,
    MPAWS_PLUGIN_SLUG,
    'mepr_aws_license_key',
    __( 'MemberPress AWS', 'memberpress-aws' ),
    __( 'Protect and reliably serve up files using MemberPress and Amazon S3.', 'memberpress-aws' )
  );
}

new MpawsAppController();
