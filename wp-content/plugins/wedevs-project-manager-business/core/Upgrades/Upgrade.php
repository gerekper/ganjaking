<?php
namespace WeDevs\PM_Pro\Core\Upgrades;
use PM_Pro_Create_Table;

class Upgrade {

    /** @var array DB updates that need to be run */
    private static $updates = [
        '0.2'   => 'Upgrade_0_2',
        '0.3'   => 'Upgrade_0_3',
        '0.4'   => 'Upgrade_0_4'
    ];

    public static $instance = null;

    private static $_instance;

    public static function getInstance() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Binding all events
     *
     * @since 0.1
     *
     * @return void
     */
    function __construct() {
        add_action( 'admin_notices', array($this, 'show_update_notice') );
        add_action( 'admin_init', array( $this, 'init_upgrades' ) );
        add_action( 'admin_init', array( $this, 'do_updates' ) );
        add_action( 'wp_ajax_do_updates', array( $this, 'do_updates' ) );
    }

    public function init_upgrades() {
        if ( ! current_user_can( 'update_plugins' ) ) {
            return ;
        }

        self::$updates = array_map( function ( $update ) {
            $class = str_replace( '/', '\\', __NAMESPACE__ );
            $class .= '\\' .$update;

            if ( class_exists( $class ) ){
                return $update = new $class();
            }
        }, self::$updates);

    }

    /**
     * Check if need any update
     *
     * @since 1.0
     *
     * @return boolean
     */
    public static function is_needs_update() {
        $db_version = get_option( 'pm_pro_db_version' );
        $installed_version = ! empty( $db_version ) ? get_option( 'pm_pro_db_version' ) : '0.1';

        $updatable_versions = pm_pro_config('app.db_version');

        // may be it's the first install
        if ( ! $installed_version ) {
            if ( version_compare( $updatable_versions, '0.1' , '<=' ) ) {

                update_option( 'pm_db_version', 0.1 );
            } else {
                update_option( 'pm_db_version', $updatable_versions );
            }
            return false;
        }

        if ( version_compare( $installed_version, $updatable_versions , '<' ) ) {
            return true;
        }
        return false;
    }

    /**
     * Show update notice
     *
     * @since 1.0
     *
     * @return void
     */
    public function show_update_notice() {

        if ( ! current_user_can( 'update_plugins' ) || ! $this->is_needs_update() ) {
            return;
        }

            ?>
                <div class="wrap">
                    <div class="notice notice-warning">

                        <p>
                            <strong><?php esc_attr_e( 'WP Project Manager-Pro Data Update Required', 'wedevs-project-manager' ); ?></strong>
                            <?php esc_attr_e('&#8211; Please click the button below to update to the latest version.', 'wedevs-project-manager' ) ?>
                        </p>
                        <form action="" method="post" style="padding-bottom: 10px;" class="PmUpgradeFrom">
                            <?php wp_nonce_field( '_nonce', 'pm_pro_upgrade_nonce' ); ?>
                            <input type="submit" class="button button-primary" name="pm_pro_update" value="<?php esc_html_e( 'Run the Update', 'wedevs-project-manager' ); ?>">
                        </form>
                    </div>
                </div>
                <script type="text/javascript">
                    jQuery('form.PmUpgradeFrom').submit(function(event){
                        //event.preventDefault();

                        return confirm( '<?php esc_html_e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'wedevs-project-manager' ); ?>' );
                    });
                </script>
            <?php

    }

    /**
     * Do all updates when Run updater btn click
     *
     * @since 1.0
     * @since 1.2.7 save plugin install date
     *
     * @return void
     */
    public function do_updates() {

        if ( ! isset( $_POST['pm_pro_update'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pm_pro_upgrade_nonce'] ) ), '_nonce' ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $this->perform_updates();
    }

    /**
     * Perform all updates
     *
     * @since 1.0
     *
     * @return void
     */
    public function perform_updates() {

        if ( ! $this->is_needs_update() ) {
            return;
        }
        $installed_version = get_option( 'pm_pro_db_version' );

        foreach (self::$updates as $version => $object ) {

            if ( version_compare( $installed_version, $version, '<' ) ) {

                if ( method_exists( $object, 'upgrade_init' ) ){
                    $object->upgrade_init();
                    update_option( 'pm_pro_db_version', $version );
                }
            }
        }
    }
}


