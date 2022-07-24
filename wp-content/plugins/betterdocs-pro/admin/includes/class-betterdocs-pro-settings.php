<?php 

if ( ! class_exists( 'BetterDocs_Pro_Settings' ) ) :
    class BetterDocs_Pro_Settings {
        /**
         * BetterDocs_Pro_Settings instance
         * @var BetterDocs_Pro_Settings
         */
        private static $_instance = null;
        /**
         * Singleton Instance of BetterDocs_Pro_Settings
         * @return BetterDocs_Pro_Settings
         */
        public static function instance() {
            return self::$_instance === null ? self::$_instance = new self() : self::$_instance;
        }

        public function __construct(){
            add_filter( 'betterdocs_settings_tab', array( $this, 'settings' ), 11 );
        }

        public function settings( $settings ) {
            if( ! current_user_can( 'activate_plugins' ) ) {
                return $settings;
            }

            $settings['go_license_tab'] = array(
                'title'    => __( 'License', 'betterdocs-pro' ),
                'priority' => 22,
                'type'     => 'func',
                'views'    => 'BetterDocs_Pro_Settings::license'
            );
            return $settings;
        }

        public static function license(){
            include BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-settings-sidebar.php';
        }

    }

    BetterDocs_Pro_Settings::instance();
endif;