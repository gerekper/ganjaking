<?php
if ( ! empty( $_GET['action'] ) && $_GET['action'] == 'active_license' && ! empty( $_GET['siteKey'] ) && ! empty( $_GET['licenseKey'] ) ) {
    if (isset($_GET['nonce']) && wp_verify_nonce( $_GET['nonce'], "active_nonce" )) {
        PAFE_License_Service::set_key( $_GET['siteKey'], $_GET['licenseKey'] );
        $share_data = isset($_GET['shareData']) && $_GET['shareData'] == 'yes';
        PAFE_License_Service::set_share_data($share_data ? 'yes' : 'no');
        PAFE_License_Service::clean_get_info_cache();
    }
    echo '<meta http-equiv="refresh" content="0; url=' . get_admin_url(null,'admin.php?page=piotnet-addons-for-elementor') . '" />';
    return;
}

$has_key = PAFE_License_Service::has_key();
$message = '';

if (isset($_POST['action']) && $_POST['action'] == 'remove_license'){
    if ($has_key) {
        $res = PAFE_License_Service::remove_license();
        if (isset($res['data']) && isset($res['data']['status']) && $res['data']['status'] == "S") {
            $message = "Deactivate license successfully.";
        }
    }
    PAFE_License_Service::clear_license_data();
    PAFE_License_Service::clear_key();
    $has_key = false;
}

$license_data = PAFE_License_Service::get_license_data(true);

if (isset($license_data) && isset($license_data['error'])) {
    $license_error = $license_data['error'];
    $res_msg = isset($license_error['message']) ? $license_error['message'] : 'Unknown message';
    $res_code = isset($license_error['code']) ? $license_error['code'] : '9999';
    $message = "$res_msg [$res_code]";
}

$license_data = PAFE_License_Service::get_license_data();
$has_valid_license = PAFE_License_Service::has_valid_license();

?>
<div class="pafe-dashboard pafe-dashboard--templates">
    <div class="pafe-header">
        <div class="pafe-header__left">
            <div class="pafe-header__logo">
                <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/images/piotnet.svg'; ?>" alt="">
            </div>
            <h2 class="pafe-header__headline"><?php esc_html_e( 'Piotnet Addons For Elementor Settings (PAFE PRO)', 'pafe' ); ?></h2>
        </div>
        <div class="pafe-header__right">
                <a class="pafe-header__button pafe-header__button--gradient" href="https://pafe.piotnet.com/?wpam_id=1" target="_blank">
                <?php echo __( 'Go to Piotnet Addons', 'pafe' ); ?>
                </a>
        </div>
    </div>
    <div class="pafe-dashboard__sidebar">
        <div class="pafe-dashboard__category">
            <?php
                $templates_categories = [
                    'license' => __('License', 'pafe'),
                    'general' => __('General', 'pafe'),
                    'features' => __('Features', 'pafe'),
                    'integration' => __('Integration', 'pafe'),
                    'about' => __('About', 'pafe'),
                ];

                $tab = !empty($_GET['tab']) ? $_GET['tab'] : 'license';

                foreach ($templates_categories as $key => $templates_category) :
            ?>
                <div class="pafe-dashboard__category-item<?php if($key == $tab) { echo ' active'; } ?>" data-pafe-dashboard-category='<?php echo $key;?>'><?php echo $templates_category; ?></div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="pafe-dashboard__content">
        <div class="pafe-dashboard__item<?php if($tab == 'license') { echo ' active'; } ?>" data-pafe-dashboard-item-category="license">
            <div class="piotnetforms-dashboard__title"><?php echo __('License', 'piotnetforms'); ?></div>
            <div class="piotnetforms-dashboard__item-content">
                <?php require_once "admin-license.php"; ?>
            </div>
        </div>
        <div class="pafe-dashboard__item<?php if($tab == 'general') { echo ' active'; } ?>" data-pafe-dashboard-item-category="general">
            <div class="piotnetforms-dashboard__title"><?php echo __('General', 'piotnetforms'); ?></div>
            <div class="piotnetforms-dashboard__item-content">
                <?php require_once "admin-general.php"; ?>
            </div>
        </div>
        <div class="pafe-dashboard__item<?php if($tab == 'features') { echo ' active'; } ?>" data-pafe-dashboard-item-category="features">
            <div class="piotnetforms-dashboard__title"><?php echo __('Features', 'piotnetforms'); ?></div>
            <div class="piotnetforms-dashboard__item-content">
                <?php require_once "admin-features.php"; ?>
            </div>
        </div>
        <div class="pafe-dashboard__item<?php if($tab == 'integration') { echo ' active'; } ?>" data-pafe-dashboard-item-category="integration">
            <div class="piotnetforms-dashboard__title"><?php echo __('Integration', 'piotnetforms'); ?></div>
            <div class="piotnetforms-dashboard__item-content">
                <?php require_once "admin-integration.php"; ?>
            </div>
        </div>
        <div class="pafe-dashboard__item<?php if($tab == 'about') { echo ' active'; } ?>" data-pafe-dashboard-item-category="about">
            <div class="piotnetforms-dashboard__title"><?php echo __('About', 'piotnetforms'); ?></div>
            <div class="piotnetforms-dashboard__item-content">
                <h3><?php _e('Tutorials','pafe'); ?></h3>
                <a href="https://pafe.piotnet.com/?wpam_id=1" target="_blank">https://pafe.piotnet.com</a>
                <h3><?php _e('Support','pafe'); ?></h3>
                <a href="mailto:support@piotnet.com">support@piotnet.com</a>
                <h3><?php _e('Version','pafe'); ?></h3>
                <?php
                    $pro_version = 'Pro v' . PAFE_PRO_VERSION;
                    if (defined('PAFE_VERSION')) {
                        $free_version = 'Free v' . constant('PAFE_VERSION');
                        echo "$free_version - $pro_version";
                    } else {
                        echo $pro_version;
                    }
                ?>
            </div>
        </div>
    </div>
</div>
