<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wrap">
  <?php WafpAppHelper::plugin_title(__('Activate','affiliate-royale', 'easy-affiliate')); ?>

  <?php require(WAFP_VIEWS_PATH . '/shared/errors.php'); ?>
  <?php if( !isset($li) or empty($li) ): ?>
    <p class="description"><?php printf(__('You must have a License Key to enable automatic updates for Affiliate Royale.<br/><br/>If you don\'t have a license please go to %1$s or %2$s to get one.<br/><br/>If you don\'t have a license you can login at %3$s or %4$s to manage your licenses and site activations.', 'affiliate-royale', 'easy-affiliate'), '<a href="http://affiliateroyale.com">AffiliateRoyale.com</a>', '<a href="http://memberpress.com">MemberPress.com</a>', '<a href="http://affiliateroyale.com/login">AffiliateRoyale.com/login</a>','<a href="http://memberpress.com/login">MemberPress.com/login</a>'); ?></p>
    <form name="activation_form" method="post" action="">
      <?php wp_nonce_field('activation_form'); ?>

      <table class="form-table">
        <tr class="form-field">
          <td valign="top" width="225px"><?php _e('Enter Your Affiliate Royale or MemberPress License Key:', 'affiliate-royale', 'easy-affiliate'); ?></td>
          <td>
            <input type="text" name="<?php echo $wafp_options->mothership_license_str; ?>" value="<?php echo (isset($_POST[$wafp_options->mothership_license_str])?$_POST[$wafp_options->mothership_license_str]:$wafp_options->mothership_license); ?>"/>
          </td>
        </tr>
      </table>
      <p class="submit">
        <input type="submit" name="Submit" class="button button-primary" value="<?php printf(__('Activate License Key on %s', 'affiliate-royale', 'easy-affiliate'), WafpUtils::site_domain()); ?>" />
      </p>
    </form>
  <?php else: ?>
    <div class="wafp-license-active">
      <div><h4><?php _e('Active License Key Information:', 'affiliate-royale', 'easy-affiliate'); ?></h4></div>
      <table>
        <tr>
          <td><?php _e('License Key:', 'affiliate-royale', 'easy-affiliate'); ?></td>
          <td>********-****-****-****-<?php echo substr($li['license_key']['license'], -12); ?></td>
        </tr>
        <tr>
          <td><?php _e('Status:', 'affiliate-royale', 'easy-affiliate'); ?></td>
          <td><?php printf(__('<b>Active on %s</b>', 'affiliate-royale', 'easy-affiliate'), WafpUtils::site_domain()); ?></td>
        </tr>
        <tr>
          <td><?php _e('Product:', 'affiliate-royale', 'easy-affiliate'); ?></td>
          <td><?php echo $li['product_name']; ?></td>
        </tr>
        <tr>
          <td><?php _e('Activations:', 'affiliate-royale', 'easy-affiliate'); ?></td>
          <td><?php printf('<b>%1$d of %2$s</b> sites have been activated with this license key', $li['activation_count'], ucwords($li['max_activations'])); ?></td>
        </tr>
      </table>
      <div class="wafp-deactivate-button"><a href="<?php echo admin_url('admin.php?page=affiliate-royale-activate&action=deactivate&_wpnonce='.wp_create_nonce('affiliate-royale_deactivate')); ?>" class="button button-primary" onclick="return confirm('<?php printf(__("Are you sure? Automatic updates of Affiliate Royale will not be available on %s if this License Key is deactivated.", 'affiliate-royale', 'easy-affiliate'), WafpUtils::site_domain()); ?>');"><?php printf(__('Deactivate License Key on %s', 'affiliate-royale', 'easy-affiliate'), WafpUtils::site_domain()); ?></a></div>
    </div>
    <?php require( WAFP_VIEWS_PATH.'/update/edge_updates.php' ); ?>
  <?php endif; ?>
</div>
