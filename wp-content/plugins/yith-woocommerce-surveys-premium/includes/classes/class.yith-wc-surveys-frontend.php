<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WC_Surveys_Frontend' ) ) {

	class YITH_WC_Surveys_Frontend {
		/**
		 * @var YITH_WC_Surveys_Frontend static instance
		 */
		protected static $instance;

		public function __construct() {

			add_action( 'woocommerce_after_order_notes', array( $this, 'print_survey_in_checkout' ), 20, 1 );
			add_action( 'woocommerce_checkout_process', array( $this, 'validate_checkout_width_survey' ) );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'add_surveys_order_meta' ), 20, 1 );
		}

		/**
		 * @return YITH_WC_Surveys_Frontend
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * print all survey after order notes in checkout
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function print_survey_in_checkout( $checkout ) {


			$all_survey = YITH_Surveys_Type()->get_checkout_surveys( 'after_order_notes' );

			foreach ( $all_survey as $survey ) {

				$params = array(
					'survey_id' => $survey,
				);

				wc_get_template( 'survey_checkout_form.php', $params, '', YITH_WC_SURVEYS_TEMPLATE_PATH );
			}
		}

		/**
		 * validate surveys field in checkout
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function validate_checkout_width_survey() {

			if ( isset( $_POST['ywc_sur_ids'] ) && isset( $_POST['ywc_sur_answers'] ) ) {

				$survey_ids = $_POST['ywc_sur_ids'];
				$answs      = $_POST['ywc_sur_answers'];
				$error      = false;
				for ( $i = 0; $i < count( $survey_ids ); $i ++ ) {
					$survey_id   = $survey_ids[ $i ];
					$answ        = $answs[ $i ];
					$is_required = get_post_meta( $survey_id, '_yith_survey_required', true ) ? 'yes' : 'no';

					if ( 'yes' === $is_required && '' === $answ ) {

						$error = true;
						break;
					}
				}

				if ( $error ) {
					wc_add_notice( __( 'Please, give an answer to the questions to contribute to the survey', 'yith-woocommerce-survey' ), 'error' );
				}
			}
		}

		/** add surveys order meta
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function add_surveys_order_meta( $order_id ) {

			if ( isset( $_POST['ywc_sur_ids'] ) && isset( $_POST['ywc_sur_answers'] ) ) {

				$survey_meta = array();
				$survey_ids  = $_POST['ywc_sur_ids'];
				$answ_ids    = $_POST['ywc_sur_answers'];

				$order = wc_get_order( $order_id );
				$args  = array(
					'order_id' => $order_id,
					'total'    => $order->get_total()
				);

				for( $i = 0; $i < count( $survey_ids ); $i ++ ) {

					$answ_id = $answ_ids[ $i ];

					if ( '' !== $answ_id ) {

						YITH_WC_Surveys_Utility::update_answer_info( $answ_id, $args );

						$survey_id = $survey_ids[ $i ];
						$item      = array(
							'survey_id'    => $survey_id,
							'survey_title' => get_the_title( $survey_id ),
							'answer_title' => get_the_title( $answ_id ),
						);

						$survey_meta[] = $item;

					}
				}

				update_post_meta( $order_id, '_yith_order_survey_voting', $survey_meta );
				delete_transient( 'yith_surveys_results_transient' );
			}
		}
	}
}
/**
 * @return YITH_WC_Surveys_Frontend | YITH_WC_Surveys_Frontend_Premium
 */
function YITH_Surveys_Frontend() {

	if ( ! ywcsur_is_premium_active() ) {
		return YITH_WC_Surveys_Frontend::get_instance();
	}

	return YITH_WC_Surveys_Frontend_Premium::get_instance();
}