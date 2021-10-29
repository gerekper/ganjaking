<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
global $wafp_options;
if(WafpUtils::is_logged_in_and_an_admin())
{
  $filename = date("ymdHis",time()) . '_paypal_bulk_file.txt';
  header("Content-Type: text/plain");
  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");

  foreach($bulk_totals as $bulk_total)
  {
    $affiliate = new WafpUser($bulk_total->affiliate_id);
    echo $affiliate->get_paypal_email() . "\t" . WafpUtils::format_float( $bulk_total->paid ) . "\t{$wafp_options->currency_code}\t" . $bulk_total->affiliate_id . "\tYour {$wafp_blogname} Affiliate Commission Payment\n";
  }
}
else
  header("Location: " . $wafp_blogurl);
