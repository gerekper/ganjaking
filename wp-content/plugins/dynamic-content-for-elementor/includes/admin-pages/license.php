<?php

namespace DynamicContentForElementor\AdminPages;

use DynamicContentForElementor\Assets;
use DynamicContentForElementor\LicenseSystem;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
class License
{
    public static function show_license_form()
    {
        ?>

		<div class="wrap">

		<h1><?php 
        echo esc_html(get_admin_page_title());
        ?></h1>

		<?php 
        if ('POST' === $_SERVER['REQUEST_METHOD'] && (!isset($_POST['dce-settings-page']) || !wp_verify_nonce($_POST['dce-settings-page'], 'dce-settings-page'))) {
            wp_die(__('Nonce verification error.', 'dynamic-content-for-elementor'));
        }
        $license_system = Plugin::instance()->license_system;
        if (isset($_POST['license_key'])) {
            if ($_POST['license_activated']) {
                list($success, $msg) = $license_system->deactivate_license();
                if (!$success) {
                    \DynamicContentForElementor\Plugin::instance()->admin_pages->notices->error($msg);
                } else {
                    $msg = esc_html__('License key successfully deactivated for this site', 'dynamic-content-for-elementor');
                    \DynamicContentForElementor\Plugin::instance()->admin_pages->notices->success($msg);
                }
            } else {
                $license_key = $_POST['license_key'];
                list($success, $msg) = $license_system->activate_new_license_key($license_key);
                if (!$success) {
                    \DynamicContentForElementor\Plugin::instance()->admin_pages->notices->error($msg);
                } else {
                    $msg = esc_html__('License key successfully activated for this site', 'dynamic-content-for-elementor');
                    \DynamicContentForElementor\Plugin::instance()->admin_pages->notices->success($msg);
                }
            }
        } else {
            $license_system->refresh_and_repair_license_status();
        }
        $license_system->domain_mismatch_check();
        $license_key = $license_system->get_license_key();
        if (isset($_POST['beta_status'])) {
            if (isset($_POST['dce_beta'])) {
                $license_system->activate_beta_releases();
            } else {
                $license_system->deactivate_beta_releases();
            }
        }
        $is_license_active = $license_system->is_license_active();
        $license_domain = get_option(DCE_PREFIX . '_license_domain');
        $classes = $is_license_active ? 'dce-success dce-notice-success' : 'dce-error dce-notice-error';
        if ($is_license_active && $license_domain && $license_domain !== Plugin::instance()->license_system->get_current_domain()) {
            $classes = 'dce-warning dce-notice-warning';
        }
        ?>
		<div class="dce-notice <?php 
        echo $classes;
        ?>">
			<h2><?php 
        _e('License Status', 'dynamic-content-for-elementor');
        ?></h2>

			<form action="" method="post">
				<?php 
        wp_nonce_field('dce-settings-page', 'dce-settings-page');
        ?>
				<?php 
        _e('Your key', 'dynamic-content-for-elementor');
        ?> <input type="password" autocomplete="off" name="license_key" value="<?php 
        echo $license_key;
        ?>" id="license_key" style="width: 240px; max-width: 100%;">
				<input type="hidden" name="license_activated" value="<?php 
        echo $is_license_active;
        ?>">
			<?php 
        $is_license_active ? submit_button(__('Deactivate', 'dynamic-content-for-elementor'), 'cancel') : submit_button(__('Save key and activate', 'dynamic-content-for-elementor'));
        ?>
			</form>
		<?php 
        if ($is_license_active) {
            if ($license_domain && $license_domain !== Plugin::instance()->license_system->get_current_domain()) {
                ?>
					<p><strong style="color:#f0ad4e;"><?php 
                _e('Your license is valid but there is something wrong: license mismatch.', 'dynamic-content-for-elementor');
                ?></strong></p>
					<p><?php 
                _e('Your license key doesn\'t match your current domain. This is most likely due to a change in the domain URL. Please deactivate the license and reactivate it', 'dynamic-content-for-elementor');
                ?></p>
				<?php 
            } else {
                ?>
					<p><strong style="color:#46b450;"><?php 
                echo \sprintf(__('Your license ending in \'%1$s\' is valid and active.', 'dynamic-content-for-elementor'), Plugin::instance()->license_system->get_license_key_last_4_digits());
                ?></strong></p>
				<?php 
            }
        } else {
            ?>
				<p><?php 
            _e('Enter your license here to keep the plugin updated, obtaining new features, future compatibility, more stability and security.', 'dynamic-content-for-elementor');
            ?></p>
				<p><?php 
            _e('You still don\'t have one?', 'dynamic-content-for-elementor');
            ?> <a href="https://www.dynamic.ooo" class="button button-small" target="_blank"><?php 
            _e('Get it now!', 'dynamic-content-for-elementor');
            ?></a></p>
		<?php 
        }
        ?>
		</div>

		<?php 
        if ($is_license_active) {
            $dce_beta = get_option(DCE_PREFIX . '_beta');
            ?>
			<div class="dce-notice dce-success dce-notice-success">
				<h3><?php 
            _e('Beta Release', 'dynamic-content-for-elementor');
            ?></h3>
				<form action="" method="post">
					<?php 
            wp_nonce_field('dce-settings-page', 'dce-settings-page');
            ?>
					<label><input type="checkbox" name="dce_beta" value="beta"<?php 
            if ($dce_beta) {
                ?> checked="checked"<?php 
            }
            ?>> <?php 
            _e('Enable beta releases. Important: Do not use in production, consider this only for staging sites.', 'dynamic-content-for-elementor');
            ?></label>
					<input type="hidden" name="beta_status" value="1" id="beta_status">
					<?php 
            submit_button(__('Save my preference', 'dynamic-content-for-elementor'));
            ?>
				</form>
			</div>

			<?php 
            $rollback_versions = \DynamicContentForElementor\Plugin::instance()->rollback_manager->get_rollback_versions();
            $confirm = esc_attr__('Are you sure you want to make rollback Dynamic.ooo - Dynamic Content for Elementor to a previous version?', 'dynamic-content-for-elementor');
            ?>
			<div class="dce-notice dce-success dce-notice-success">
				<h3><?php 
            _e('Rollback version', 'dynamic-content-for-elementor');
            ?></h3>
				<form id='dce-rollback-form' action="<?php 
            echo admin_url('admin-post.php?action=dce_rollback');
            ?>" method="post" data-confirm="<?php 
            echo $confirm;
            ?>">
					<?php 
            wp_nonce_field('dce-settings-page', 'dce-settings-page');
            ?>
					<h4><?php 
            _e('Your current version', 'dynamic-content-for-elementor');
            ?>: <?php 
            echo DCE_VERSION;
            ?></h4>
					<p><?php 
            echo \sprintf(__('Experiencing an issue with Dynamic.ooo - Dynamic Content for Elementor version %s? Rollback to a previous version before the issue appeares.', 'dynamic-content-for-elementor'), DCE_VERSION);
            ?>
					<br />

					<?php 
            if (!empty($rollback_versions)) {
                ?>
						<label><?php 
                _e('Select version', 'dynamic-content-for-elementor');
                ?>:</label>
						<select name="version" id="version">
						<?php 
                foreach ($rollback_versions as $aversion) {
                    ?>
							<option value="<?php 
                    echo $aversion;
                    ?>"><?php 
                    echo $aversion;
                    ?></option>
							<?php 
                }
                ?>
						</select>
						<?php 
                submit_button(__('Rollback now', 'dynamic-content-for-elementor'));
            } else {
                _e('No versions available for rollback.', 'dynamic-content-for-elementor');
            }
            ?>
				</form>
			</div>
			<?php 
        }
        ?>
		</div>
		<?php 
    }
}
