<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class WafpDashboardHelper
{
  public static function display_referrals($affs, $id) {
    ?>
    <ul id="wafp-aff-referrals-<?php echo $id; ?>" class="wafp-aff-referrals">
    <?php
    foreach( $affs as $aff ) {
      $obj = $aff['object'];
      $children = $aff['children'];
      $profile = $obj->affiliate_profile();

      ?>
      <li>
        <span id="wafp-aff-name-<?php echo $obj->get_id(); ?>" class="wafp-aff-name"><?php echo $profile['name']; ?> &ndash; <?php printf( __( 'Affiliate: <b>%s</b>', 'affiliate-royale' , 'easy-affiliate'), $profile['is_affiliate'] ); ?> &ndash; <a href="#" class="wafp-aff-info-toggle" data-id="wafp-aff-info-<?php echo $obj->get_id(); ?>"><?php _e('info','affiliate-royale', 'easy-affiliate'); ?></a></span>
        <div id="wafp-aff-info-<?php echo $obj->get_id(); ?>" class="wafp-aff-info wafp-hidden">
            <?php
            foreach( $profile as $slug => $field ):
              if( in_array( $slug, array( 'name', 'referrer', 'is_affiliate' ) ) )
                continue;

              switch( $slug ):
                case 'level':
                  $name = __('Level','affiliate-royale', 'easy-affiliate');
                  break;
                case 'name':
                  $name = __('Name','affiliate-royale', 'easy-affiliate');
                  break;
                case 'is_affiliate':
                  $name = __('Affiliate?','affiliate-royale', 'easy-affiliate');
                  break;
                case 'id':
                  $name = __('Id','affiliate-royale', 'easy-affiliate');
                  break;
                case 'username':
                  $name = __('Username','affiliate-royale', 'easy-affiliate');
                  break;
                case 'referrer':
                  $name = __('Referrer','affiliate-royale', 'easy-affiliate');
                  break;
                case 'email':
                  $name = __('Email','affiliate-royale', 'easy-affiliate');
                  break;
                case 'sales':
                  $name = __('Sales','affiliate-royale', 'easy-affiliate');
                  break;
                default:
                  $name = apply_filters('wafp-affiliate-profile-label', __('Field','affiliate-royale', 'easy-affiliate'), $slug);
              endswitch;
              ?>
              <div class="wafp-aff-info-row wafp-aff-info-row-<?php echo $slug; ?>">
                <span class="wafp-aff-info-label wafp-aff-info-label-<?php echo $slug; ?>"><?php echo $name; ?></span>
                <span class="wafp-aff-info-value wafp-aff-info-value-<?php echo $slug; ?>"><?php echo $field; ?></span>
              </div>
          <?php
          endforeach;
          ?>
        </div>
      <?php

      if( is_array($children) and !empty($children) )
        self::display_referrals( $children, $obj->get_id() );

      ?>
      </li>
      <?php
    }

    ?>
    </ul>
    <?php
  }

  public static function referrals_csv($affs, $level=1) {
    if(!is_array($affs) or empty($affs)) { return; }
    foreach($affs as $aff) {
      $obj = $aff['object'];
      $profile = array_values($obj->affiliate_profile());
      array_unshift($profile,$level);
      echo WafpUtils::to_csv(array($profile));
      self::referrals_csv($aff['children'],(intval($level)+1));
    }
  }

  public static function active($link='home') {
    if( ( isset($_REQUEST['action']) and $_REQUEST['action']==$link ) or
        ( !isset($_REQUEST['action']) and $link=='home' ) ) {
      echo " class=\"wafp-nav-active\""; }
  }

  public static function nav() {
    global $wafp_options;
    $show_links = (bool)WafpLink::get_count();
    ?>
    <ul class="wafp-nav-bar">
      <li<?php WafpDashboardHelper::active('home'); ?>><a href="<?php echo $wafp_options->affiliate_page_url("action=home"); ?>"><?php _e('Home', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
      <li<?php WafpDashboardHelper::active('stats'); ?>><a href="<?php echo $wafp_options->affiliate_page_url("action=stats"); ?>"><?php _e('Stats', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
      <li<?php WafpDashboardHelper::active('payments'); ?>><a href="<?php echo $wafp_options->affiliate_page_url("action=payments"); ?>"><?php _e('Payment History', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
      <li<?php WafpDashboardHelper::active('account'); ?>><a href="<?php echo $wafp_options->affiliate_page_url("action=account"); ?>"><?php _e('Account', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
      <?php if( $show_links ): ?>
        <li<?php WafpDashboardHelper::active('links'); ?>><a href="<?php echo $wafp_options->affiliate_page_url("action=links"); ?>"><?php _e('Links &amp; Banners', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
      <?php endif; ?>
      <?php if( $wafp_options->dash_show_genealogy ): ?>
        <li<?php WafpDashboardHelper::active('referrals'); ?>><a href="<?php echo $wafp_options->affiliate_page_url("action=referrals"); ?>"><?php _e('Referrals', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
      <?php endif; ?>
      <?php foreach( $wafp_options->dash_nav as $page_id ):
              $page = get_post($page_id);
              $link = get_permalink($page_id); ?>
        <li><a href="<?php echo $link; ?>"><?php echo $page->post_title; ?></a></li>
      <?php endforeach; ?>
      <?php do_action('wafp-affiliate-dashboard-nav'); ?>
      <li><a href="<?php echo WafpUtils::logout_url(); ?>"><?php _e('Logout', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
    </ul>
    <?php
  }
}

