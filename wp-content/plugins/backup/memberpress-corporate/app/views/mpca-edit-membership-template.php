<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div id="mepr-corporate-account-options" class="mepr-product-adv-item">
  <input type="checkbox"
    name="mpca_is_corporate_product"
    id="mpca_is_corporate_product"
    class="mepr-toggle-checkbox"
    data-box="mepr_corporate_advanced_options_box"
    value="1"
    <?php checked($ca_enabled); ?> />

  <label for="mpca_is_corporate_product"><?php _e('Subscribers to this Membership are Corporate Accounts', 'memberpress-corporate') ?>
    <?php MeprAppHelper::info_tooltip('is-corporate',
                                        __('Corporate Accounts', 'memberpress-corporate'),
                                        __('Corporate accounts allow your members to manage their own members.', 'memberpress-corporate')); ?>
  </label>
</div>

<div id="" class="mepr-sub-box mepr_corporate_advanced_options_box">
  <div class="mepr-arrow mepr-gray mepr-up mepr-sub-box-arrow"></div>
  <label for="mpca_num_sub_accounts"><?php _e('Max Sub-accounts', 'memberpress-corporate') ?></label>
  <?php MeprAppHelper::info_tooltip('num-sub-accounts',
                                      __('Sub-account Limit', 'memberpress-corporate'),
                                      __('The number of allowed sub-accounts for this membership', 'memberpress-corporate')); ?>

  <input
    id="mpca_num_sub_accounts"
    type="number"
    name="mpca_num_sub_accounts"
    min="0"
    value=<?php echo $num_sub_accounts ?> />
</div>
