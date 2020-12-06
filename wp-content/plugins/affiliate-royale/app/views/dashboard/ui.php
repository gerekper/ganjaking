<div id="wafp-dash-wrapper">
<?php
  // if $show_nav isn't set we'll just show it
  if(!isset($show_nav) || $show_nav)
  {
    $default_link = false;
    if(isset($wafp_options->default_link_id) && $wafp_options->default_link_id > 0)
      $default_link = new WafpLink($wafp_options->default_link_id);

    require(WAFP_VIEWS_PATH."/dashboard/nav.php");
  }

  require(WAFP_VIEWS_PATH."/dashboard/{$action}.php");
?>
</div>
