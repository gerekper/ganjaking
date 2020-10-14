<?php
namespace memberpress\downloads\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base,
    memberpress\downloads\lib as lib,
    memberpress\downloads\models as models;

class File extends lib\BaseCptModel {
  public static $cpt = 'mpdl-file';
  public static $nonce_str = 'mpdl-file-nonce';
  public static $page_template_str = 'mpdl-file-page-template';
  public static $page_status_str = 'mpdl-file-page-status';
  public static $file_category_ctax = 'mpdl-file-categories';
  public static $file_tag_ctax = 'mpdl-file-tags';
  public static $permalink_slug = 'mp-files';
  public $statuses;
  public $download_count;

  public function __construct($obj = null) {
    parent::__construct($obj);
    $this->load_cpt(
      $obj,
      self::$cpt,
      array(
        'filename'        => array('default' => '',        'type' => 'string'),
        'filesize'        => array('default' => 0,         'type' => 'integer'),
        'filetype'        => array('default' => '',        'type' => 'string'),
        'status'          => array('default' => 'enabled', 'type' => 'string'),
        'page_template'   => array('default' => null,      'type' => 'string'),
      )
    );

    $this->statuses = array(
      'enabled',
      'disabled'
    );

    $this->download_count = $this->get_download_count();
  }

  /**
  * Translate the error message if defined
  * @param string $error_message Error message from UploadHandler
  * @return string Translated error message or $error_message
  */
  public static function translate_error_message($error_message) {
    switch($error_message) {
      case 'File is too big':
        return __('%s exceeds the maximum upload size for this site.', 'memberpress-downloads');
      default:
        return $error_message;
    }
  }

  /**
  * Upload directory for plugin
  * @return string|false Plugin upload directory
  */
  public static function upload_dir() {
    $wp_upload_dir = wp_upload_dir();
    if(isset($wp_upload_dir['basedir'])) {
      $upload_dir =  trailingslashit(trailingslashit($wp_upload_dir['basedir']) . base\SLUG_KEY);
      if(\is_dir($upload_dir)) {
        return $upload_dir;
      }
    }
    return false;
  }

  /**
  * Upload URL for plugin
  * @return string|false Plugin upload directory
  */
  public static function upload_url() {
    $wp_upload_dir = wp_upload_dir();
    if(isset($wp_upload_dir['baseurl'])) {
      $wp_upload_baseurl = is_ssl() ? \str_replace('http://', 'https://', $wp_upload_dir['baseurl']) : $wp_upload_dir['baseurl'];
      return \trailingslashit($wp_upload_baseurl) . trailingslashit(base\SLUG_KEY);
    }
    return false;
  }

  public function validate() {
    $this->validate_is_in_array($this->status, $this->statuses, 'status');
    $this->validate_not_empty($this->filename, 'filename');
  }

  public function sanitize() {
  }

  /**
  * Thumbnail url for images
  * @return string URL of the file thumbnail
  */
  public function thumb_url() {
    return self::upload_url() . 'thumbnail/' . $this->filename;
  }

  /**
  * Permalink download file
  * @return string Permalink for file accounting for file extension and SSL
  */
  public function permalink($link) {
    $query_args = array();
    $permalink = untrailingslashit($link);
    $permalink .= '.' . $this->extension();
    if(is_ssl() && preg_match('/^http:\/\//', $permalink)) {
      $permalink = \str_replace('http://', 'https://', $permalink);
    }
    $permalink = trailingslashit($permalink);
    $permalink = esc_url(add_query_arg(apply_filters('mpdl-file-query-args', $query_args), $permalink));

    return $permalink;
  }

  /**
  * File extension
  * @return string Extension of File
  */
  public function extension() {
    $file_parts = \pathinfo($this->filename);

    return $file_parts['extension'];
  }

  /**
  * Send the file to the browser
  * @param string $filename Name of file sent to browser
  * @return void
  */
  public function send_download($filename) {
    // Clean the output buffer so we don't end up with corrupted files
    if(ob_get_length()) {
      ob_clean();
    }

    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
    header('X-Content-Type-Options: nosniff');
    header('X-Robots-Tag: noindex, nofollow', true);
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: Mon, 07 Jul 1777 07:07:07 GMT'); // Battle of Hubbardton
    header('Content-type: application/octet-stream');
    header("Content-Disposition:attachment; filename=\"{$filename}\"");
    header("Content-Length: {$this->filesize}");
    readfile(self::upload_dir() . $this->filename);
    FileStat::create($this->ID);
    exit();
  }

  /**
  * FileDownload download_count convenience method
  * @return int Number of times a file has been downloaded
  */
  private function get_download_count() {
    $file_download = FileDownload::get_one(array('file_id' => $this->ID));
    if(isset($file_download)) {
      return $file_download->download_count;
    }
    else {
      return 0;
    }
  }

  /**
  * Destroy uploaded files and thumbnails
  * @return void
  */
  public function destroy_files() {
    if(\file_exists(self::upload_dir() . $this->filename)) {
      \unlink(self::upload_dir() . $this->filename);
    }
    if(\file_exists(self::upload_dir() . 'thumbnail/' . $this->filename)) {
      \unlink(self::upload_dir() . 'thumbnail/' . $this->filename);
    }
  }

  /**
  * Make a HEAD request to the permalink to check for a 404
  * @return bool
  */
  public function permalink_is_available() {
    $permalink = get_the_permalink($this->ID);
    $http_head = wp_remote_head($permalink);

    if(!is_wp_error($http_head) && $http_head['response']['code'] === 404) {
      return false;
    }

    return true;
  }
}
