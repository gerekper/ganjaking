<?php


/*
 * This file belongs to the YITH Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_COG_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_COG_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 */

if ( ! class_exists( 'YITH_COG_Premium' ) ) {
    /**
     * Class YITH_COG_Premium
     *
     * @author
     */
    class YITH_COG_Premium extends YITH_COG {

        public $version = YITH_COG_VERSION;

        protected static $_instance = null;

        public $admin = null;

        public $frontend = null;

        public function __construct(){

            add_filter('yith_cog_require_class', array($this,'load_premium_classes'));

            parent::__construct();
        }

        public function load_premium_classes( $require ){

            $frontend = array();
            $common = array();
            $admin = array(
                'includes/admin/class.yith-cog-admin-premium.php',
            );

            $require['frontend'] = array_merge($require['frontend'],$frontend);
            $require['admin'] = array_merge($require['admin'],$admin);
            $require['common'] = array_merge($require['common'],$common);

            return $require;
        }

    }
}

