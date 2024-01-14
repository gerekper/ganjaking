<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionForNominee' ) ) {

	class RSFunctionForNominee {

		public static function init() {

			add_action( 'woocommerce_after_order_notes', array( __CLASS__, 'display_nominee_field_in_checkout' ) );

			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'save_nominee_in_checkout' ), 10, 2 );

			if ( 'yes' == get_option( 'rs_reward_content' ) ) {
				add_action( 'woocommerce_after_my_account', array( __CLASS__, 'display_nominee_field_in_my_account' ) );
			}

			add_action( 'woocommerce_after_checkout_validation', array( __CLASS__, 'checkout_validation_for_nominee' ), 11, 2 );
		}

		public static function display_nominee_field_in_checkout() {
			if ( 2 == get_option( 'rs_show_hide_nominee_field_in_checkout' ) ) {
				return;
			}

			$UserList = self::get_users_for_nominee( 'checkout' );
			if ( ! srp_check_is_array( $UserList ) ) {
				esc_html_e( 'You have no Nominee', 'rewardsystem' );
			} else {
				self::nominee_field( 'checkout' );
			}
		}

		public static function display_nominee_field_in_my_account() {

			if ( 2 == get_option( 'rs_show_hide_nominee_field' ) ) {
				return;
			}

			$NomineeData = array(
				'usertype' => get_option( 'rs_select_type_of_user_for_nominee' ),
				'userlist' => get_option( 'rs_select_users_list_for_nominee' ),
				'title'    => get_option( 'rs_my_nominee_title', 'My Nominee' ),
				'name'     => get_option( 'rs_select_type_of_user_for_nominee_name' ),
				'userrole' => get_option( 'rs_select_users_role_for_nominee' ),
			);
			self::nominee_field( 'myaccount', $NomineeData );
		}

		public static function nominee_field( $Nominee, $NomineeData = array() ) {
			$BanType = check_banning_type( get_current_user_id() );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			$ClassName = ( 'checkout' == $Nominee ) ? 'rs_select_nominee_in_checkout' : 'rs_select_nominee';
			$Title     = ( 'checkout' == $Nominee ) ? get_option( 'rs_my_nominee_title_in_checkout' ) : $NomineeData['title'];
			$NomineeId = ( 'checkout' == $Nominee ) ? get_user_meta( get_current_user_id(), 'rs_selected_nominee_in_checkout', true ) : get_user_meta( get_current_user_id(), 'rs_selected_nominee', true );
			$Name      = ( 'checkout' == $Nominee ) ? get_option( 'rs_select_type_of_user_for_nominee_name_checkout' ) : $NomineeData['name'];
			$UserList  = self::get_users_for_nominee( $Nominee, $NomineeData );
			ob_start();
			?>
			<h2><?php echo esc_html( $Title ); ?></h2>
			<table class="form-table">
				<tr valign="top">
					<td>
						<label><?php esc_html_e( 'Select Nominee', 'rewardsystem' ); ?></label>
					</td>
				</tr>
				<tr valign="top">
					<td>
						<select name="<?php echo esc_attr( $ClassName ); ?>" id="<?php echo esc_attr( $ClassName ); ?>" class="short <?php echo esc_attr( $ClassName ); ?>">
							<option value=""><?php esc_html_e( 'Choose Nominee', 'rewardsystem' ); ?></option>
							<?php
							foreach ( $UserList as $UserId ) {
								$UserInfo = get_user_by( 'id', $UserId );
								?>
								<option value="<?php echo esc_html( $UserId ); ?>" <?php echo $NomineeId == $UserId ? 'selected=selected' : ''; ?>>
									<?php
									if ( '1' == $Name ) {
										echo esc_html( $UserInfo->display_name ) . ' (#' . absint( $UserInfo->ID ) . ' &ndash; ' . esc_html( $UserInfo->user_email ) . ')';
									} else {
										echo esc_html( $UserInfo->display_name );
									}
									?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<?php if ( 'checkout' != $Nominee ) { ?>
					<tr valign="top">
						<td>
							<input type="button" value="Add" class="rs_add_nominee"/>
						</td>
					</tr>
				<?php } ?>
			</table>
			<?php
			return ob_get_contents();
		}

		public static function get_users_for_nominee( $Nominee, $NomineeData = array() ) {
			$UserIds           = array();
			$UserSelectionType = ( 'checkout' == $Nominee ) ? get_option( 'rs_select_type_of_user_for_nominee_checkout' ) : $NomineeData['usertype'];
			if ( 1 == $UserSelectionType ) {
				$UserList = ( 'checkout' == $Nominee ) ? get_option( 'rs_select_users_list_for_nominee_in_checkout' ) : $NomineeData['userlist'];
				if ( ! empty( $UserList ) ) {
					$UserIds = srp_check_is_array( $UserList ) ? $UserList : array_filter( array_map( 'absint', (array) explode( ',', $UserList ) ) );
				}
			} else {
				$UserRoles = ( 'checkout' == $Nominee ) ? get_option( 'rs_select_users_role_for_nominee_checkout' ) : $NomineeData['userrole'];
				if ( ! srp_check_is_array( $UserRoles ) ) {
					return $UserIds;
				}

				$UserIds = get_users(
					array(
						'role__in' => $UserRoles,
						'fields'   => 'ID',
					)
				);
			}
			return $UserIds;
		}

		/**
		 * Save Nominee in Checkout.
		 *
		 * @param int $order_id Order ID.
		 * @param int $user_id User ID.
		 */
		public static function save_nominee_in_checkout( $order_id, $user_id ) {
			$NomineeId = isset( $_REQUEST['rs_select_nominee_in_checkout'] ) ? wc_clean( wp_unslash( $_REQUEST['rs_select_nominee_in_checkout'] ) ) : '';
			$order     = wc_get_order( $order_id );
			$order->update_meta_data( 'rs_selected_nominee_in_checkout', $NomineeId );
			$order->save();
		}

		public static function checkout_validation_for_nominee( $data, $error ) {

			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_nominee_activated' ) ) {
				return;
			}

			if ( '2' == get_option( 'rs_show_hide_nominee_field_in_checkout' ) ) {
				return;
			}

			$BanType = check_banning_type( get_current_user_id() );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			if ( ! isset( $_REQUEST['rs_select_nominee_in_checkout'] ) ) {
				return;
			}

			if ( '1' == get_option( 'rs_nominee_selection_in_checkout', 1 ) ) {
				return;
			}

			if ( ! empty( $_REQUEST['rs_select_nominee_in_checkout'] ) ) {
				return;
			}

			$error->add( 'error', __( 'Please select Nominee', 'rewardsystem' ) );
		}
	}

	RSFunctionForNominee::init();
}
