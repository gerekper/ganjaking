<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WC_Surveys_Utility' ) ) {

	class YITH_WC_Surveys_Utility {


		/**
		 * add new survey in user list
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 * @param array $survey
		 */
		public static function add( $survey ) {

			$user_id    = isset( $survey['user_id'] ) ? $survey['user_id'] : - 1;
			$survey_id  = isset( $survey['survey_id'] ) ? $survey['survey_id'] : - 1;
			$survey_qst = $survey['question'];
			$survey_ans = $survey['answer'];

			if ( $survey_id == - 1 ) {
				return 'invalid_survey';
			}


			if ( self::is_user_survey_in_list( $survey_id ) ) {
				return 'already_response';
			}

			if ( $user_id != - 1 ) {

				$user_list = get_user_meta( $user_id, '_yith_user_survey_meta', true );

				if ( empty( $user_list ) ) {
					$user_list = array();
				}

				$user_list[] = array(
					'survey_id' => $survey_id,
					'question'  => $survey_qst,
					'answer'    => $survey_ans
				);


				$res = update_user_meta( $user_id, '_yith_user_survey_meta', $user_list );


			} else {
				$cookie = array(
					'survey_id' => $survey_id,
					'question'  => $survey_qst,
					'answer'    => $survey_ans

				);

				$savelist_cookie = yith_getcookie( 'yith_user_surveys_cookie' );

				$savelist_cookie[] = $cookie;

				yith_setcookie( 'yith_user_surveys_cookie', $savelist_cookie );

				$res = true;

			}
			if ( $res ) {

				return "true";
			} else {
				return "error";
			}

		}

		/**
		 * check if the user has already responded to the survey
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $survey_id
		 *
		 * @return bool
		 */
		public static function is_user_survey_in_list( $survey_id ) {
			$exist = false;

			if ( is_user_logged_in() ) {


				$user_id = get_current_user_id();

				$user_list = get_user_meta( $user_id, '_yith_user_survey_meta', true );


				if ( empty( $user_list ) ) {
					return false;
				}


				$key   = 'survey_id';
				$value = $survey_id;


				foreach ( $user_list as $i => $list ) {

					if ( $list[ $key ] == $value ) {
						return true;
					}
				}


				return false;
			} else {
				$cookie = yith_getcookie( 'yith_user_surveys_cookie' );

				$key   = 'survey_id';
				$value = $survey_id;

				foreach ( $cookie as $k => $item ) {
					if ( $item[ $key ] == $value ) {
						$exist = true;
						break;
					}
				}

				return $exist;
			}

		}

		/**
		 * @param $survey_id
		 *
		 * @return bool|string
		 */
		public static function get_user_answer_by_survey_id( $survey_id ) {

			$answer = '';

			if ( is_user_logged_in() ) {


				$user_id = get_current_user_id();

				$user_list = get_user_meta( $user_id, '_yith_user_survey_meta', true );


				if ( empty( $user_list ) ) {
					return false;
				}


				$key   = 'survey_id';
				$value = $survey_id;


				foreach ( $user_list as $i => $list ) {

					if ( $list[ $key ] == $value ) {
						$answer = $list['answer'];
						break;
					}
				}


				return $answer;
			} else {
				$cookie = yith_getcookie( 'yith_user_surveys_cookie' );

				$key   = 'survey_id';
				$value = $survey_id;

				foreach ( $cookie as $k => $item ) {
					if ( $item[ $key ] == $value ) {
						$answer = $item['answer'];
						break;
					}
				}

				return $answer;
			}

		}

		public static function get_items( $survey_id ) {
			$items      = array();
			$all_answer = YITH_Surveys_Type()->get_survey_children( array( 'post_parent' => $survey_id ) );
			$survey_type   = get_post_meta( $survey_id, '_yith_survey_visible_in', true );
			foreach ( $all_answer as $answer_id ) {

				$answer = get_the_title( $answer_id );

				$tot_votes = get_post_meta( $answer_id, '_yith_answer_votes', true );

				$tot_votes = empty( $tot_votes ) ? 0 : $tot_votes;

				$new_item = array(
					'survey_id'     => $survey_id,
					'answer'        => $answer,
					'visible_in'    => $survey_type,
					'tot_votes'     => $tot_votes,
					'tot_order'     => 0,
					'order_details' => ''
				);

				if( 'checkout' == $survey_type ){

					$tot_order = get_post_meta( $answer_id, '_yith_answer_order_total', true );
					$order_details  = get_post_meta( $answer_id, '_yith_answer_order_details', true );
					$new_item['tot_order'] = empty( $tot_order ) ? 0 : $tot_order;
					$new_item['order_details'] = empty( $order_details ) ? array() : $order_details;
				}

				$items[] = $new_item;

			}

			return $items;
		}


		/**
		 * get data
		 * @author YIThemes
		 * @since 1.0.0
		 * @return array|mixed
		 */
		public static function generate_data() {
			$items = get_transient( 'yith_surveys_results_transient' );

			if ( false === $items ) {

				//get all surveys
				$items = array();
				$all_surveys = YITH_Surveys_Type()->get_surveys( array( 'field' => 'ids' ) );

				foreach ( $all_surveys as $survey_id ){

					$items = array_merge( self::get_items( $survey_id ), $items );
				}

				set_transient( 'yith_surveys_results_transient', $items, 24 * HOUR_IN_SECONDS );
			}

			return $items;
		}



		/**
		 * check if a couple (survey_id, answer ) is already present in items
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 * @param $items
		 * @param $value_1
		 * @param $value_2
		 *
		 * @return int|string
		 */
		public static function is_element_in_items( $items, $value_1, $value_2 ) {

			foreach ( $items as $key => $item ) {

				if ( ( isset( $item['survey_id'] ) && $item['survey_id'] == $value_1 ) && ( isset( $item['answer'] ) && strcasecmp( $item['answer'], $value_2 ) == 0 ) ) {
					return $key;
				}
			}

			return - 1;
		}

		/**
		 * @param int $answer_id
		 * @param array $args
		 */
		public static function update_answer_info( $answer_id, $args = array() ) {
			$answer_votes = get_post_meta( $answer_id, '_yith_answer_votes', true );
			$answer_votes = empty( $answer_votes ) ? 1 : $answer_votes + 1;
			update_post_meta( $answer_id, '_yith_answer_votes', $answer_votes );

			if ( isset( $args['order_id'] ) ) {
				$answer_order_details = get_post_meta( $answer_id, '_yith_answer_order_details', true );
				if ( empty( $answer_order_details ) ) {
					$answer_order_details = array( $args['order_id'] );
				} else {
					$answer_order_details[] = $args['order_id'];
				}
				update_post_meta( $answer_id, '_yith_answer_order_details', $answer_order_details );
			}

			if ( isset( $args['total'] ) ) {
				$answer_order_total = get_post_meta( $answer_id, '_yith_answer_order_total', true );
				$answer_order_total = empty( $answer_order_total ) ? $args['total'] : $answer_order_total + $args['total'];
				update_post_meta( $answer_id, '_yith_answer_order_total', $answer_order_total );
			}
		}

		/**
		 * @param WC_Order $order
		 */
		public static function convert_checkout_answer( $order ) {

			$total                  = $order->get_total();
			$order_id               = $order->get_id();
			$votes                  = $order->get_meta( '_yith_order_survey_voting', true );
			$order_import_completed = false;
			foreach ( $votes as $vote ) {
				$survey_id = isset( $vote['survey_id'] ) ? $vote['survey_id'] : YITH_Surveys_Type()->is_survey_child_exist( $vote['survey_title'], 0 );
				$answer    = $vote['answer_title'];
				$answer_id = get_posts( array( 'post_type'   => 'yith_wc_surveys',
				                               'name'        => $answer,
				                               'post_parent' => $survey_id,
				                               'fields'      => 'ids',
				                               'limit'       => 1
				) );
				$answer_id = isset( $answer_id[0] ) ? $answer_id[0] : false;

				if ( $answer_id ) {

					$args = array(
						'order_id' => $order_id,
						'total'    => $total
					);

					self::update_answer_info( $answer_id, $args );
					$order_import_completed = true;
				}
			}

			return $order_import_completed;


		}
	}
}