<?php
/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';


add_action( 'plugins_loaded', 'cpm_pro_init', 90 );

function cpm_pro_init() {

    $cpm_version = get_option('cpm_version');

    if ( $cpm_version && version_compare( $cpm_version, '2.0' , '<' ) ) {
        add_action( 'admin_notices', 'pm_version_notice' );
        return;
    }

	if ( !$cpm_version && !class_exists( 'WeDevs\\PM\\Core\\WP\\Frontend' ) ) {
        add_action( 'admin_notices', 'pm_pro_notice' );
        add_action( 'wp_ajax_pm_pro_install_wp_project_manager',  'pm_pro_install_project_manager' );
        return;
    }

	pm_pro_load_libs();
    pm_pro_view();
    //pm_pro_migrate_db();
    pm_pro_pseed_db();
    pm_pro_load_routes();
    pm_pro_register_routes();

    do_action( 'pm_pro_loaded' );
}

function pm_version_notice() {
     echo sprintf( '<div class="error"><p><strong>WP Project Manager</strong> required version 2.0 or above. Please update now.</p></div>' );
}


 /**
 * Show message if plugin not capable with WPERP
 *
 * @since 2.0.2
 */
function pm_pro_notice() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="error" id="pm-installer-notice" style="padding: 1em; position: relative;">
        <h2><?php _e( 'Your Project Manager pro is almost ready.', 'pm-pro' ); ?></h2>
        <p><?php echo sprintf( __('You just need to active the <strong>Project Manager free</strong> to make it functional.', 'pm-pro') ); ?></p>

            <p>
                <button id="pm-installer" class="button"><?php _e( 'Active', 'pm-pro' ); ?></button>
            </p>

    </div>

    <script type="text/javascript">
        (function ($) {
            var wrapper = $('#pm-installer-notice');

            wrapper.on('click', '#pm-installer', function (e) {
                var self = $(this);

                e.preventDefault();
                self.addClass('install-now updating-message');
                self.text('<?php echo esc_js( 'Installing...', 'pm-pro' ); ?>');

                var data = {
                    action: 'pm_pro_install_wp_project_manager',
                    _wpnonce: '<?php echo wp_create_nonce('pm-installer-nonce'); ?>'
                };

                $.post(ajaxurl, data, function (response) {
                    if (response.success) {
                        self.attr('disabled', 'disabled');
                        self.removeClass('install-now updating-message');
                        self.text('<?php echo esc_js( 'Activated', 'pm-pro' ); ?>');

                        window.location.reload();
                    }
                });
            });
        })(jQuery);
    </script>
    <?php
}


/**
 * Install the WP project Manager plugin via ajax
 *
 * @since 2.0.2
 *
 * @return json
 */
function pm_pro_install_project_manager() {

    if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'pm-installer-nonce' ) ) {
        wp_send_json_error( __( 'Error: Nonce verification failed', 'pm-pro') );
    }

    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );

    $plugin = 'wedevs-project-manager';
    $api    = plugins_api( 'plugin_information', array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) ) );


    if (is_wp_error($api)) {
        die(sprintf(__('ERROR: Error fetching plugin information: %s', 'pm-pro'), $api->get_error_message()));
    }

    add_filter( 'upgrader_package_options', function ( $options ) {
        $options['clear_destination'] = true;
        $options['hook_extra'] = [
            'type' => 'plugin',
            'action' => 'install',
            'plugin'  => 'wedevs-project-manager/cpm.php',
        ];
        return  $options;
    });

    $result   = $upgrader->install( $api->download_link );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $result );
    }

    $result = activate_plugin( 'wedevs-project-manager/cpm.php' );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $result );
    }
    wp_send_json_success();
}

