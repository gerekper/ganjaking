<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpAppHelper
{
  public static function get_extension( $mimetype ) { switch( $mimetype )
    {
      case "application/msword":
      case "application/rtf":
      case "text/richtext":
        return "doc";
      case "application/vnd.ms-excel":
        return "xls";
      case "application/vnd.ms-powerpoint":
        return "ppt";
      case "application/pdf":
        return "pdf";
      case "application/zip":
        return "zip";
      case "image/jpeg":
        return "jpg";
      case "image/gif":
        return "gif";
      case "image/png":
        return "png";
      case "image/tiff":
        return "tif";
      case "text/plain":
        return "txt";
      case "text/html":
        return "html";
      case "video/quicktime":
        return "mov";
      case "video/x-msvideo":
        return "avi";
      case "video/x-ms-wmv":
        return "wmv";
      case "video/ms-wmv":
        return "wmv";
      case "video/mpeg":
        return "mpg";
      case "audio/mpg":
        return "mp3";
      case "audio/x-m4a":
        return "aac";
      case "audio/m4a":
        return "aac";
      case "audio/x-wav":
        return "wav";
      case "audio/wav":
        return "wav";
      case "application/x-zip-compressed":
        return "zip";
      default:
        return "bin";
    }
  }

  public static function format_currency($number,$show_symbol=true) {
    global $wafp_options;

    //TODO: We may want to use $wp_locale in the future for this ... then we could just eliminate it from the options page.
    /* Example:

    global $wp_locale;

    if(is_numeric($number))
      return number_format($number, $num_decimals, $wp_locale->number_format['decimal_point'], '');
  */

    if($wafp_options->number_format == "#.###,##") {
      $dec = ',';
      $tho = '.';
    }
    else if($wafp_options->number_format == '####') {
      $dec = '';
      $tho = '';
    }
    else {
      $dec = '.';
      $tho = ',';
    }

    if($wafp_options->number_format == '####')
      return ($show_symbol?$wafp_options->currency_symbol:"") . (int)$number;
    else
      return ($show_symbol?$wafp_options->currency_symbol:"") . number_format($number, 2, $dec, $tho);
  }

  public static function format_date($datetime, $show_never = false, $format = 'Y-m-d')
  {
    if(empty($datetime) or preg_match('#^0000-00-00#',$datetime))
    {
      if($show_never)
        return __('Never', 'affiliate-royale', 'easy-affiliate');
      else
        return 0;
    }

    $ts = strtotime($datetime);
    return date($format, $ts);
  }

  public static function plugin_title($title, $new=false) {
    require WAFP_VIEWS_PATH . '/shared/title.php';
  }

  public static function display_affiliate_commissions($aff_id) {
    $aff = new WafpUser($aff_id);

    $commission_source = $aff->get_commission_source();
    $commission_type   = $aff->get_commission_type();
    $commissions       = $aff->get_commission_levels();

    ?>
    <div class="wafp-aff-commissions">
    <?php

    if( $commission_source['slug']!='global' ) {
      ?>
      <h3><?php printf( __('Commissions (%s)', 'affiliate-royale', 'easy-affiliate'), $commission_source['label'] ); ?></h3>
      <?php
    }
    else {
      ?>
      <h3><?php _e('Commissions', 'affiliate-royale', 'easy-affiliate'); ?></h3>
      <?php
    }

    for( $i=0; $i < count($commissions); $i++ ) {
      $commission_level = $commissions[$i];
      ?>
      <div class="wafp-aff-commission-level-row">
        <span class="wafp-aff-commission-level"><?php printf(__('Level %s', 'affiliate-royale', 'easy-affiliate'), ($i+1)); ?></span>
        <span class="wafp-aff-commission-percentage"><?php echo ( $commission_type=='fixed' ? WafpAppHelper::format_currency($commission_level) : WafpUtils::format_float($commission_level) . '%' ); ?></span>
      </div>
      <?php
    }

    ?>
    </div>
    <?php
  }

  public static function info_tooltip($id, $title, $info) {
    ?>
    <span id="esaf-tooltip-<?php echo $id; ?>" class="esaf-tooltip">
      <span><i class="ar-icon ar-icon-info-circled ar-16"></i></span>
      <span class="esaf-data-title esaf-hidden"><?php echo $title; ?></span>
      <span class="esaf-data-info esaf-hidden"><?php echo $info; ?></span>
    </span>
    <?php
  }
}
