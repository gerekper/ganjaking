<?php
!defined( 'YITH_POS' ) && exit; // Exit if accessed directly


if ( !class_exists( 'YITH_POS_Register_Session' ) ) {
	/**
	 * Class YITH_POS_Register_Session
	 * Register Session Management Class
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	class YITH_POS_Register_Session {


		static function get_session_object( $session_id, $cashiers_with_names = true ) {
			global $wpdb;

			$table_name = $wpdb->prefix . YITH_POS_DB::$register_session;

			$select = $wpdb->prepare( "SELECT * from $table_name where id=%d", $session_id );

			$result = $wpdb->get_row( $select, OBJECT );


			if ( $result ) {
				if( ! is_null( $result->cash_in_hand )  ){
					$result->cash_in_hand = unserialize( $result->cash_in_hand );
				}
				if( ! is_null( $result->cashiers )  ){
					if( $cashiers_with_names ){
						$cashiers = unserialize( $result->cashiers );
						$cashiers_with_name = array();
						if( $cashiers ){
							foreach ( $cashiers as $cashier ){
								$user = get_user_by('ID',$cashier['id']);
								if( $user ){
									$cashier['name'] = $user->first_name.' '.$user->last_name;
									$cashiers_with_name[] = $cashier;
								}
							}
						}

						$result->cashiers = $cashiers_with_name;
					}else{
						$result->cashiers = unserialize( $result->cashiers );
					}

				}
			}

			return $result;
		}

		/**
		 * Create a new session inside the database.
		 *
		 * @param $register_id
		 *
		 * @return bool|false|int
		 */
		static function add_session( $register_id ){
			global $wpdb;

			$table_name = $wpdb->prefix.YITH_POS_DB::$register_session;
			$register   = new YITH_POS_Register( $register_id );
			$store_id   = $register->get_store_id();

			//save the current cashier as object
			$current_user = wp_get_current_user();

			$cashier = array( 'id' => $current_user->ID, 'login' => date( 'Y-m-d H:i:s' ) );
			$cashiers   = array( $cashier );

			$insert_query = "INSERT INTO $table_name ( `store_id`, `register_id`, `open`, `cashiers`) VALUES ('" . $store_id . "', '" . $register_id . "', CURRENT_TIMESTAMP(), '" . serialize($cashiers) . "' )";

			$wpdb->query( $insert_query );

			return $wpdb->insert_id;
		}


		/**
		 * Add the new cashier to the cashier list.
		 *
		 * @param $session_id
		 *
		 * @return bool|false|int
		 */
		static function update_cashiers( $session_id ) {
			$session          = self::get_session_object( $session_id, false );
			$session_cashiers = ! is_null( $session->cashiers ) ? $session->cashiers : array();

			$current_user = wp_get_current_user();

			$cashier = array( 'id'    => $current_user->ID,
			                  'login' => date( 'Y-m-d H:i:s' )
			);

			array_push( $session_cashiers, $cashier );

			global $wpdb;

			$table_name   = $wpdb->prefix . YITH_POS_DB::$register_session;
			$update_query = "UPDATE $table_name SET `cashiers` =  '" . serialize( $session_cashiers ) . "' WHERE id = $session_id";

			return $wpdb->query( $update_query );

		}

		/**
		 * Close the session.
		 *
		 * @param $session_id
		 *
		 * @return bool|false|int
		 */
		static function close_session( $session_id ){
			global $wpdb;

			$table_name = $wpdb->prefix.YITH_POS_DB::$register_session;
			$update_query = "UPDATE $table_name SET `closed` =  CURRENT_TIMESTAMP() WHERE id = $session_id";

			return $wpdb->query( $update_query );
		}

		/**
		 * Update the cash in hand array.
		 *
		 * @param $session_id
		 * @param $cash_in_hand
		 *
		 * @return bool|false|int
		 */
		static function update_cash_in_hand( $session_id, $cash_in_hand ){
			global $wpdb;

			$table_name = $wpdb->prefix.YITH_POS_DB::$register_session;
			$update_query = "UPDATE $table_name SET `cash_in_hand` =  '". serialize($cash_in_hand)."' WHERE id = $session_id";

			return $wpdb->query( $update_query );
		}

		/**
		 * Save note on register session.
		 *
		 * @param $session_id
		 * @param $cash_in_hand
		 *
		 * @return bool|false|int
		 */
		static function close_register( $session_id, $totals, $note ){
			global $wpdb;
			$total = 0;
			if(  $totals  ){
				$key = array_search('total', array_column($totals, 'id'), true);
				$total = isset( $totals[$key] ) ?  $totals[$key]['amount'] : $total;
			}

			$table_name = $wpdb->prefix.YITH_POS_DB::$register_session;
			$update_query = "UPDATE $table_name SET `total`='".$total."', `note` =  '". sanitize_text_field($note)."', `report` = '".serialize($totals)."' WHERE id = $session_id";

			return $wpdb->query( $update_query );
		}
	
	}
}