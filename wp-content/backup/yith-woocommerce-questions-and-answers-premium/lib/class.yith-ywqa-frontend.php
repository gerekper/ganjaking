<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWQA_Frontend' ) ) {
	
	/**
	 *
	 * @class   YITH_YWQA_Frontend
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_YWQA_Frontend {
		
		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;
		
		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {
			
			$this->init_hooks();

		}
		
		public function init_hooks() {
			add_action( 'wp_ajax_show_question_list', array(
				$this,
				'show_questions_callback'
			) );
			
			add_action( 'wp_ajax_nopriv_show_question_list', array(
				$this,
				'show_questions_callback',
			) );
			
			add_action( 'yith_questions_and_answers_content', array(
				$this,
				'show_questions'
			) );
		}
		

		public function show_questions_callback() {
			if ( isset( $_POST['product_id'] ) ) {
				$product_id = intval( $_POST['product_id'] );
			} else {
				global $product;
				$product_id = yit_get_prop( $product, 'id' );
			}

			/* Compatibility fix with WPML */
            if( function_exists('wpml_object_id_filter') ){
                $product_id = wpml_object_id_filter( $product_id, 'product', false, ICL_LANGUAGE_CODE  );
            }
			
			$only_answered = isset( $_POST["answered"] ) && ( 'true' == $_POST["answered"] );
			$show_all      = isset( $_POST["show_all"] ) && ( 'true' == $_POST["show_all"] );
			
			ob_start();
			$this->show_question_list( $product_id, $only_answered, $show_all );
			
			$content = ob_get_contents();
			ob_end_clean();
			
			wp_send_json( array(
				"code"  => 1,
				"items" => $content,
			) );
		}
		
		public function show_questions() {
			
			global $product;
			$product_id = yit_get_prop( $product, 'id' );
			
			$this->show_question_list( $product_id );
		}
		
		/**
		 * Show a list of question related to a specific product
		 */
		public function show_question_list( $product_id, $only_answered = false, $show_all = false ) {
			
			wc_get_template( 'single-product/ywqa-product-questions.php',
				array(
					'max_items'     => $show_all ? - 1 : YITH_YWQA()->questions_to_show,
					'only_answered' => $only_answered,
					'product_id'    => $product_id,
				),
				'', YITH_YWQA_TEMPLATES_DIR );
		}
	}
}

YITH_YWQA_Frontend::get_instance();