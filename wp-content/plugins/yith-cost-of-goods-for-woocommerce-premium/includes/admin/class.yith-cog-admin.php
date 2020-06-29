<?php

/*
 * This file belongs to the YITH Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_COG_PATH' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_COG_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 *
 */
if ( ! class_exists( 'YITH_COG_Admin' ) ) {
    /**
     * Class YITH_COG_Admin
     *
     * @author
     */
    class YITH_COG_Admin {

        /**
         * Main Instance
         *
         * @var YITH_COG_Admin
         * @since 1.0
         */
        protected static $_instance = null;

        public $options = null;
        protected $_panel = null;
        protected $_panel_page = 'yith_cog_setting';
        protected $_main_panel_option;

        /**
         * Construct
         *
         * @since 1.0
         */
        public function __construct() {

            /* === Show Plugin Information === */
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_COG_PATH . '/' . basename( YITH_COG_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

        }

        /**
         * Main plugin Instance
         * @return stdClass
         * @var YITH_COG_Admin instance
         * @author
         */
        public static function get_instance() {
            $self = __CLASS__ . (class_exists(__CLASS__ . '_Premium') ? '_Premium' : '');

            if (is_null($self::$_instance)) {
                $self::$_instance = new $self;
            }
            return $self::$_instance;
        }

        public function action_links( $links ) {
            $links = yith_add_action_links( $links, $this->_panel_page, false );
            return $links;
        }

        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_COG_INIT' ) {
            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $new_row_meta_args['slug'] = YITH_COG_SLUG;
            }

            return $new_row_meta_args;
        }


    }
}
