<?php
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

/**
 * Class Updater
 *
 * @package Happy_Addons\Elementor
 * @since 1.5.0
 */
class Updater {

    const VERSION_DB_KEY = 'happyaddons_version';

    public static function init() {
        if ( self::should_update() ) {
            self::update();
            self::update_version();
        }
    }

    protected static function v_1_5_0() {
        add_option( 'elementor_icon_manager_needs_update', 'yes' );
        add_option( 'elementor_load_fa4_shim', 'yes' );
    }

    protected static function update() {
        if ( ! self::get_old_version() ) {
            self::v_1_5_0();
        }

        $assets_cache = new Assets_Cache();
        $assets_cache->delete_all();
    }

    protected static function get_old_version() {
        return get_option( self::VERSION_DB_KEY, '' );
    }

    protected static function get_new_version() {
        return HAPPY_ADDONS_VERSION;
    }

    protected static function update_version() {
        update_option( self::VERSION_DB_KEY, self::get_new_version() );
    }

    protected static function should_update() {
        if ( ! self::get_old_version() ) {
            return true;
        }
        return version_compare( self::get_new_version(), self::get_old_version(), '>' );
    }
}

Updater::init();
