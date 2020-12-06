<?php
namespace memberpress\downloads\helpers;
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class Files {
  /**
  * Convert bytes to human readable
  * @param int $bytes
  * @param int $decimals Number of decimal places
  * @return string Human readable size
  * @see Credit: http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
  */
  public static function human_filesize($bytes, $decimals = 2) {
    $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), @$sizes[$factor]);
  }

  /**
  * Convert filetype to the thumbnail class
  * @param string $filetype mimetype of file
  * @return string Class for icon
  */
  public static function file_thumb($filetype) {
    if(\preg_match('/audio\/\w+/', $filetype)) {
      return 'icon-file-audio';
    }
    elseif(\preg_match('/video\/\w+/', $filetype)) {
      return 'icon-file-video';
    }
    else {
      switch($filetype) {
        case 'application/x-tar':
        case 'application/zip':
        case 'multipart/x-zip':
        case 'application/zip-compressed':
        case 'application/x-zip-compressed':
        case 'application/x-gzip':
        case 'application/rar':
        case 'application/x-7z-compressed':
          $icon_class = 'icon-file-archive';
          break;
        case 'application/vnd.ms-excel':
        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
        case 'application/vnd.ms-excel.sheet.macroEnabled.12':
        case 'application/vnd.ms-excel.sheet.binary.macroEnabled.12':
        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.template':
        case 'application/vnd.ms-excel.template.macroEnabled.12':
        case 'application/vnd.ms-excel.addin.macroEnabled.12':
          $icon_class = 'icon-file-excel';
          break;
        case 'application/msword':
        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
        case 'application/vnd.ms-word.document.macroEnabled.12':
        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.template':
        case 'application/vnd.ms-word.template.macroEnabled.12':
        case 'application/vnd.ms-write':
        case 'application/vnd.ms-xpsdocument':
          $icon_class = 'icon-file-word';
          break;
        case 'application/vnd.ms-powerpoint':
        case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
        case 'application/vnd.ms-powerpoint.presentation.macroEnabled.12':
        case 'application/vnd.openxmlformats-officedocument.presentationml.slideshow':
        case 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12':
        case 'application/vnd.openxmlformats-officedocument.presentationml.template':
        case 'application/vnd.ms-powerpoint.template.macroEnabled.12':
        case 'application/vnd.ms-powerpoint.addin.macroEnabled.12':
        case 'application/vnd.openxmlformats-officedocument.presentationml.slide':
        case 'application/vnd.ms-powerpoint.slide.macroEnabled.12':
          $icon_class = 'icon-file-powerpoint';
          break;
        case 'application/pdf':
          $icon_class = 'icon-file-pdf';
          break;
        case 'text/plain':
        case 'text/csv':
        case 'text/tab-separated-values':
        case 'text/calendar':
        case 'text/richtext':
        case 'text/vtt':
        case 'text/markdown':
        case 'application/ttaf+xml':
        case 'application/rtf':
        case 'application/vnd.oasis.opendocument.text':
        case 'application/wordperfect':
        case 'application/vnd.apple.pages':
          $icon_class = 'icon-doc-text';
          break;
        case 'application/javascript':
        case 'application/x-javascript':
        case 'application/json':
        case 'application/yaml':
        case 'application/x-yaml':
        case 'application/x-python':
        case 'application/python':
        case 'application/x-ruby':
        case 'application/ruby':
        case 'application/x-php':
        case 'application/php':
        case 'application/x-java':
        case 'application/java':
        case 'application/x-shockwave-flash':
        case 'text/yaml':
        case 'text/x-yaml':
        case 'text/css':
        case 'text/html':
        case 'text/javascript':
        case 'text/x-php':
        case 'text/php':
        case 'text/x-python-script':
        case 'text/x-ruby-script':
        case 'text/xml':
        case 'application/xml':
        case 'application/xhtml+xml':
          $icon_class = 'icon-file-code';
          break;
        default:
          $icon_class = 'icon-doc';
          break;
      }
      return $icon_class;
    }
  }

  /**
  * Format post row action link
  * @param string $text Link text
  * @param array $classes link classes
  * @param array $attrs additional link attributes
  * @return string link HTML
  */
  public static function post_row_action($text = '', $classes = array(), $attrs = array()) {
    $attrs_html = \join(' ', \array_map(function($k, $v) { return $k .'="'. $v .'"'; }, \array_keys($attrs), $attrs));
    $classes_str = \join(' ', $classes);
    \ob_start();
      ?>
        <a class="<?php echo $classes_str; ?>" href="#" rel="permalink" <?php echo $attrs_html; ?>><?php echo $text; ?></a>
      <?php
    return \ob_get_clean();
  }

  /**
  * File download link class
  * @param string $filetype Mime type of file
  * @return string Link class
  */
  public static function file_link_class($filetype) {
    $icon_class = self::file_thumb($filetype);
    return \str_replace('icon', 'mpdl', $icon_class);
  }

  /**
   * Get downloads
   *
   * @param boolean   $for_blocks    Is the query for use in a Gutenberg block? If yes, change the returned keys.
   *
   * @return array
   */
  public static function get_downloads( $for_blocks = false ) {
    $values = array( 'ID', 'post_title' );
    $posts = get_posts( array(
      'post_type' => 'mpdl-file',
      'posts_per_page' => -1
    ) );
    $id_key = $for_blocks ? 'value' : 'ID';
    $title_key = $for_blocks ? 'label' : 'title';
    $downloads = array();
    foreach ( $posts as $post ) {
      $downloads[] = array(
        $id_key => $post->ID,
        $title_key => $post->post_title
      );
    }
    return $downloads;
  }
}
