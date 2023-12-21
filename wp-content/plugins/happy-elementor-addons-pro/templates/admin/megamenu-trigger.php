<?php
/**
 * Nav menu page MegaMenu trigger template
 */

defined( 'ABSPATH' ) || exit;
?>
<script>
    var ha_megamenu_trigger_markup = `
    <div class="ha-megamenu-trigger" id="ha-megamenu-trigger">
        <div class="ha-toggle">
            <input id="ha-menu-metabox-input-is-enabled" <?php checked((isset($data['is_enabled']) ? $data['is_enabled'] : ''), '1'); ?> type="checkbox" class="ha-toggle__check ha-menu-is-enabled" name="is_enabled" value="1">
            <b class="ha-toggle__switch"></b>
            <b class="ha-toggle__track"></b>
        </div>
        <h3 class="ha-dashboard-widgets__item-title">
            <label for="ha-menu-metabox-input-is-enabled"><span class="branding"></span> Happy Menu</label>
        </h3>
    </div>
    `;
    var ha_megamenu_nonce = `<?php echo wp_create_nonce('wp_rest'); ?>`;
    var ha_admin_ajax = `<?php echo admin_url( 'admin-ajax.php' ); ?>`;
</script>
