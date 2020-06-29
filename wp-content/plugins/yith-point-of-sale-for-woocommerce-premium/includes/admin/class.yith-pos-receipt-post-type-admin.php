<?php
! defined( 'YITH_POS' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_POS_Receipt_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_POS_Receipt_Post_Type_Admin
	 * Main Class
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	class YITH_POS_Receipt_Post_Type_Admin {

		/** @var string post type */
		public $post_type;

		/** @var YITH_POS_Receipt_Post_Type_Admin */
		protected static $_instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_POS_Receipt_Post_Type_Admin
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_POS_Receipt_Post_Type_Admin constructor
		 */
		public function __construct() {
			$this->post_type = YITH_POS_Post_Types::$receipt;

			add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );
			add_action( 'admin_init', array( $this, 'add_meta_boxes' ), 1 );

			add_action( 'yith_pos_preview_receipt', array( $this, 'preview_receipt_template' ) );


			add_filter( 'get_user_option_screen_layout_' . $this->post_type, '__return_true' );

			//List table
			add_filter( 'manage_' . $this->post_type . '_posts_columns', array( $this, 'manage_list_columns' ) );
			add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'render_list_columns' ), 10, 2 );

            add_filter( 'post_row_actions', array( $this, 'manage_row_actions' ), 10, 2 );
		}

        /**
         * Manage the row actions in the Receipts List
         *
         * @param array   $actions
         * @param WP_Post $post
         * @return array
         */
        public function manage_row_actions( $actions, $post ) {
            if ( YITH_POS_Post_Types::$receipt === get_post_type( $post ) ) {
                if ( isset( $actions[ 'inline hide-if-no-js' ] ) ) {
                    unset( $actions[ 'inline hide-if-no-js' ] );
                }
            }
            return $actions;
        }


		/**
		 * Manage the column name on list table.
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function manage_list_columns( $columns ) {
			unset( $columns[ 'date' ] );
			$columns[ 'registers' ]     = __( 'Registers with this template', 'yith-point-of-sale-for-woocommerce' );
			return $columns;
		}

		/**
		 * Render the columns of the Store List
		 *
		 * @param array $column
		 * @param int   $post_id
		 */
		public function render_list_columns( $column, $post_id ) {
			$receipt = yith_pos_get_receipt( $post_id );

			if( 'registers' ==  $column ){
				$registers = $receipt->get_registers();
				if ( $registers ) {
					yith_pos_compact_list( array_filter( array_map( 'yith_pos_get_register_full_name', $registers ) ) );
				}
			}
		}


		/**
		 * Remove publish box from edit booking
		 */
		public function remove_publish_box() {
			remove_meta_box( 'submitdiv', $this->post_type, 'side' );
		}

		/**
		 * Add meta boxes to edit the the receipt
		 */
		public function add_meta_boxes() {
			$args = require_once( YITH_POS_DIR . '/plugin-options/metabox/receipt-options.php' );

			foreach ( $args as $key => $metabox_args ) {
				$metabox_template = YIT_Metabox( $key );
				$metabox_template->init( $metabox_args );
			}
		}

		/**
		 * Get the receipt preview template field to show inside the metabox.
		 */
		public function preview_receipt_template() {
			yith_pos_get_view( 'fields/receipt-preview-template.php' );
		}




	}

}