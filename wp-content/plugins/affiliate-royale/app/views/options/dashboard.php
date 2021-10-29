<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="esaf-page-title"><?php _e('Dashboard Settings', 'affiliate-royale', 'easy-affiliate'); ?></div>
<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->custom_message_str; ?>">
          <?php
            _e('Welcome Message', 'affiliate-royale', 'easy-affiliate');
            WafpAppHelper::info_tooltip(
              'esaf-options-custom-message',
              __('Custom Welcome Message', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
              __('This is the customized message your affiliates will see on their Affiliate Dashboard welcome page.', 'affiliate-royale', 'easy-affiliate')
            );
          ?>
        </label>
      </th>
      <td>
        <div id="poststuff">
          <?php wp_editor($wafp_options->custom_message, $wafp_options->custom_message_str, array('media_buttons' => false, 'teeny' => true)); ?>
        </div>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->dash_show_genealogy_str; ?>"><?php _e('Show Referrals', 'affiliate-royale', 'easy-affiliate'); ?></label>
        <?php
          WafpAppHelper::info_tooltip(
            'esaf-options-custom-message',
            __('Show Referrals', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
            __('Show Affiliates their referral (genealogy) information?', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <input type="checkbox" name="<?php echo $wafp_options->dash_show_genealogy_str; ?>" id="<?php echo $wafp_options->dash_show_genealogy_str; ?>" <?php checked($wafp_options->dash_show_genealogy); ?>/>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="wafp-dash-pages">
          <?php
            _e('Custom Nav Pages', 'affiliate-royale', 'easy-affiliate');
            WafpAppHelper::info_tooltip(
              'esaf-options-custom-nav-pages',
              __('Custom Dashboard Nav Pages', 'affiliate-royale', 'easy-affiliate'),
              __('Customize Nav page links that will appear on the Affiliate Dashboard.', 'affiliate-royale', 'easy-affiliate')
            );
          ?>
        </label>
      </th>
      <td>
        <ol id="wafp-dash-pages" data-index="0"></ol>
        <a href="javascript:" id="wafp_add_nav_pages" class="button" ><?php _e('add page', 'affiliate-royale', 'easy-affiliate'); ?></a>
        <a href="javascript:" id="wafp_remove_nav_pages" class="wafp-hidden button"><?php _e('remove page', 'affiliate-royale', 'easy-affiliate'); ?></a>
        <div id="wafp-data-pages" class="wafp-hidden"><?php
            echo json_encode(
                   array_map(
                     function($page) {
                       return array(
                         'ID' => $page->ID,
                         'title' => strip_tags($page->post_title)
                       );
                     },
                     WafpUtils::get_pages()
                  )
                ); ?></div>
        <div id="wafp-data-selected" class="wafp-hidden"><?php echo json_encode(array_values($wafp_options->dash_nav)); ?></div>
      </td>
    </tr>
  </tbody>
</table>

