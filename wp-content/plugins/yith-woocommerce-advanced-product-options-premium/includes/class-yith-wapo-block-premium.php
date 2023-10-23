<?php
/**
 * YITH_WAPO_Block_Premium Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 4.1.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Block_Premium' ) ) {

    /**
     *  Addon class.
     *  The class manage all the Addon behaviors.
     */
    class YITH_WAPO_Block_Premium extends YITH_WAPO_Block {


        /**
         *  Constructor
         *
         * @param array $args The args to instantiate the class.
         */
        public function __construct( $args ) {
            parent::__construct( $args );
        }

        /**
         * Return vendor_id of the current block.
         *
         * @return string
         */
        public function get_vendor_id() {
            return $this->vendor_id ?? 0;
        }

    }

}
