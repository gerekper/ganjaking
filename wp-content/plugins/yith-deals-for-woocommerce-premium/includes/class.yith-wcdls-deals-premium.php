<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCDLS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Deals_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Deals_Premium' ) ) {
	/**
	 * Class YITH_Deals_Premium
	 *
	 * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
	 */
	class YITH_Deals_Premium extends YITH_Deals {
        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct(){
			add_filter( 'yith_wcdls_require_class', array( $this, 'load_premium_classes' ) );
            
			parent::__construct();
		}


		/**
		 * Add premium files to Require array
		 *
		 * @param $require The require files array
		 *
		 * @return Array
		 * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
		 * @since 1.0
		 *
		 */
		public function load_premium_classes( $require ){
			$frontend = array(
				'includes/class.yith-wcdls-frontend-premium.php',
			);
			$common = array(
				'includes/class.yith-wcdls-ajax-premium.php',
                'includes/class.yith-wcdls-offer-premium.php',
                'includes/functions.yith-wcdls-premium.php',
                'includes/class.yith-wcdls-functions-premium.php',
                'includes/class.yith-wcdls-deals-shortcode.php',
            );
			$require['admin'][]   = 'includes/class.yith-wcdls-admin-premium.php';
			$require['frontend']  = array_merge($require['frontend'],$frontend);
			$require['common']    = array_merge($require['common'],$common);

			return $require;
		}

        public function init_classes(){
            parent::init_classes();
            $this->shortcode = YITH_WCDLS_Deals_Shortcodes::init();

        }
    }
}