<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/admin/partials
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines display the report
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * This is construct of class where all users coupons listed.
 *
 * @package User_Reports_Log_List_Table
 * @link https://wpswings.com/?utm_source=wpswings-crp-woo&utm_medium=woo-backend&utm_campaign=official
 */
class Coupon_Referral_Program_Admin_Display_Report extends WP_List_Table {
	/**
	 * This is a variable .
	 *
	 * @var string example_data.
	 */
	public $example_data;

	/**
	 * This construct colomns in users logs table.
	 *
	 * @name get_columns.
	 * @link https://wpswings.com/?utm_source=wpswings-crp-woo&utm_medium=woo-backend&utm_campaign=official
	 */
	public function get_columns() {
		$columns = array(
			'user_name'      => __( 'User Name', 'coupon-referral-program' ),
			'user_email'     => __( 'User Email', 'coupon-referral-program' ),
			'referred_users' => __( 'Referred Users', 'coupon-referral-program' ),
			'utilize'        => __( 'Total Utilization', 'coupon-referral-program' ),
			'no_of_coupons'  => __( 'No. Of Coupons', 'coupon-referral-program' ),
		);
		return $columns;
	}
	/**
	 * This show users logs table list.
	 *
	 * @param array  $item .
	 * @param string $column_name .
	 */
	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {

			case 'user_name':
				$nonce = wp_create_nonce( 'mwb_crp_nonce' );
				$link  = '<div class="crp_view_details_log"><a href="' . admin_url( 'admin.php?page=wc-reports&tab=crp_report&user_id=' . $item['id'] . '&nonce=' . $nonce . '&action=view_details_log"' ) . '">' . __( 'View Coupon Details', 'coupon-referral-program' ) . '</a><br/><a href="" class="wps_crp_referral_reminder_email" data-user_id=' . $item['id'] . ' title="' . __( 'Send Referral Reminder Email', 'coupon-referral-program' ) . '">' . __( 'Send Email', 'coupon-referral-program' ) . '<a></div>';
				return $item[ $column_name ] . $link;
			case 'user_email':
				return '<b>' . $item[ $column_name ] . '</b>';
			case 'referred_users':
				return '<b>' . $item[ $column_name ] . '</b>';
			case 'utilize':
				return '<b>' . wc_price( $item[ $column_name ] ) . '</b>';
			case 'no_of_coupons':
				return '<b>' . $item[ $column_name ] . '</b>';
			default:
				return false;
		}
	}

	/**
	 * Returns an associative array containing the bulk action for sorting.
	 *
	 * @name get_sortable_columns.
	 * @return array
	 * @link https://wpswings.com/?utm_source=wpswings-crp-woo&utm_medium=woo-backend&utm_campaign=official
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'user_name'      => array( 'user_name', false ),
			'user_email'     => array( 'user_email', false ),
			'referred_users' => array( 'referred_users', false ),
			'utilize'        => array( 'utilize', false ),
			'no_of_coupons'  => array( 'no_of_coupons', false ),
		);
		return $sortable_columns;
	}

	/**
	 * This function is return data of the all users.
	 *
	 * @name get_user_report_data.
	 * @return array
	 * @link https://wpswings.com/?utm_source=wpswings-crp-woo&utm_medium=woo-backend&utm_campaign=official
	 * @param int $user_id .
	 */
	public function get_user_report_data( $user_id ) {
			$mwb_crp_data                  = array();
			$crp_public_obj            = new Coupon_Referral_Program_Public( 'coupon-referral-program', '1.0.0' );
			$users_crp_data            = $crp_public_obj->get_revenue( $user_id );
			$mwb_crp_user_name         = get_userdata( $user_id )->data->display_name;
			$mwb_crp_user_email        = get_userdata( $user_id )->data->user_email;
			$get_utilize_coupon_amount = $crp_public_obj->get_utilize_coupon_amount( $user_id );
			$mwb_crp_data              = array(
				'id'             => $user_id,
				'user_name'      => $mwb_crp_user_name,
				'user_email'     => $mwb_crp_user_email,
				'referred_users' => $users_crp_data['referred_users'],
				'utilize'        => $get_utilize_coupon_amount,
				'no_of_coupons'  => $users_crp_data['total_coupon'],
			);
			return $mwb_crp_data;
	}
	/**
	 * This function is return data of the all users.
	 *
	 * @name mwb_get_report_data.
	 * @return array
	 * @link https://wpswings.com/?utm_source=wpswings-crp-woo&utm_medium=woo-backend&utm_campaign=official
	 */
	public function mwb_get_report_data() {
		$users              = get_users( array( 'fields' => array( 'ID' ) ) );
		$mwb_crp_data_array = array();
		$user_id            = '';
		if ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) {
			$user_name      = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
			$args['search'] = '*' . $user_name . '*';
			$user_data      = get_user_by( 'email', $user_name );
			if ( isset( $user_data ) && ! empty( $user_data ) ) {
				$user_id = $user_data->ID;
			}
			$user_data = get_user_by( 'login', $user_name );
			if ( isset( $user_data ) && ! empty( $user_data ) ) {
				$user_id = $user_data->ID;
			}
			if ( ! empty( $user_id ) ) {
				$mwb_crp_data = $this->get_user_report_data( $user_id );
				if ( ! empty( $mwb_crp_data ) && is_array( $mwb_crp_data ) ) {
					array_push( $mwb_crp_data_array, $mwb_crp_data );
				}
			}
			return $mwb_crp_data_array;

		}
		foreach ( $users as $user_id ) {
			$mwb_crp_data = $this->get_user_report_data( $user_id->ID );
			if ( ! empty( $mwb_crp_data ) && is_array( $mwb_crp_data ) ) {
				array_push( $mwb_crp_data_array, $mwb_crp_data );
			}
		}
		return $mwb_crp_data_array;
	}

	/**
	 * Prepare items for sorting.
	 *
	 * @name prepare_items.
	 * @link https://wpswings.com/?utm_source=wpswings-crp-woo&utm_medium=woo-backend&utm_campaign=official
	 */
	public function prepare_items() {
		$per_page              = 10;
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();

		$this->example_data = $this->mwb_get_report_data();
		$data               = $this->example_data;

		usort( $data, array( $this, 'mwb_crp_usort_reorder' ) );

		$current_page = $this->get_pagenum();
		$total_items  = count( $data );
		$data         = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->items  = $data;
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

	}

	/**
	 * Return sorted associative array.
	 *
	 * @name mwb_wpr_usort_reorder.
	 * @link https://wpswings.com/?utm_source=wpswings-crp-woo&utm_medium=woo-backend&utm_campaign=official
	 * @param array $cloumna .
	 * @param array $cloumnb .
	 */
	public function mwb_crp_usort_reorder( $cloumna, $cloumnb ) {
		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'id';
		$order   = ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc';
		if ( is_numeric( $cloumna[ $orderby ] ) && is_numeric( $cloumnb[ $orderby ] ) ) {
			if ( $cloumna[ $orderby ] == $cloumnb[ $orderby ] ) {
				return 0;
			} elseif ( $cloumna[ $orderby ] < $cloumnb[ $orderby ] ) {
				$result = -1;
				return ( 'asc' === $order ) ? $result : -$result;
			} elseif ( $cloumna[ $orderby ] > $cloumnb[ $orderby ] ) {
				$result = 1;
				return ( 'asc' === $order ) ? $result : -$result;
			}
		} else {
			$result = strcmp( $cloumna[ $orderby ], $cloumnb[ $orderby ] );
			return ( 'asc' === $order ) ? $result : -$result;
		}
	}
}

/* This scetion used for the listing of the details of the users */
if ( isset( $_GET['action'] ) && isset( $_GET['user_id'] ) ) {
	if ( 'view_details_log' === $_GET['action'] ) {
		$user_id        = sanitize_text_field( wp_unslash( $_GET['user_id'] ) );
		$crp_public_obj = new Coupon_Referral_Program_Public( 'coupon-referral-program', '1.0.0' );
		?>
	<div class="mwb-crp-referral-table-wrapper">
		<table id="mwb-crp-referral-table" class="mwb-crp-referral-table">
			<thead >
				<tr >
					<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Coupon ', 'coupon-referral-program' ); ?></th>
					<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Coupon Created', 'coupon-referral-program' ); ?></th>
					<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Expiry Date', 'coupon-referral-program' ); ?></th>
					<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Event', 'coupon-referral-program' ); ?></th>
					<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Referred Users', 'coupon-referral-program' ); ?></th>
					<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Usage Count', 'coupon-referral-program' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<!-- signup coupon -->
				<?php
				if ( ! empty( $crp_public_obj->get_signup_coupon( $user_id ) ) && is_array( $crp_public_obj->get_signup_coupon( $user_id ) ) ) :
					$signup_coupon  = $crp_public_obj->get_signup_coupon( $user_id );
					$coupon = new WC_Coupon( $signup_coupon['singup'] );
					if ( 'publish' == get_post_status( $signup_coupon['singup'] ) ) :
						?>
				<tr>
					<td data-th="<?php esc_html_e( 'Coupon', 'coupon-referral-program' ); ?>">
						<div class="mwb-crp-coupon-code">
							<p id="<?php echo 'mwb' . esc_html( $signup_coupon['singup'] ); ?>">
								<?php echo esc_html( $coupon->get_code() ); ?>
							</p>
							<span class="mwb-crp-coupon-amount">
							<?php
							echo ( 'fixed_cart' === $coupon->get_discount_type() ) ?
								wp_kses_post( wc_price( $coupon->get_amount() ) ) : esc_html( $coupon->get_amount() ) . '%';
							?>
							</span> 
							<img class="mwb-crp-coupon-scissors" src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/scissors.png' ); ?>" alt="scissors image"> 
							<span class="mwb-crp-coupon-wrap">
								<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo esc_html( $signup_coupon['singup'] ); ?>" aria-label="copied">
									<span class="mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
									<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
								</button>
							</span>
						</div>
					</td>
					<td data-th="<?php esc_html_e( 'Coupon Created', 'coupon-referral-program' ); ?>"><?php echo esc_html( $crp_public_obj->mwb_crp_get_transalted_coupon_created_date( $coupon ) ); ?></td>
					<td data-th="<?php esc_html_e( 'Expiry Date', 'coupon-referral-program' ); ?>"><?php echo esc_html( $crp_public_obj->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ); ?></td>
					<td data-th="<?php esc_html_e( 'Event', 'coupon-referral-program' ); ?>"><?php esc_html_e( 'Signup Coupon', 'coupon-referral-program' ); ?></td>
					<td data-th="<?php esc_html_e( 'Referred Users', 'coupon-referral-program' ); ?>">----</td>
					<td data-th="<?php esc_html_e( 'Usage Count', 'coupon-referral-program' ); ?>"><?php echo esc_html( $coupon->get_usage_count() ); ?></td>
				</tr>
				<?php endif; ?>
				<?php endif; ?>
				<!-- End signup -->
				<!-- Start referal signup -->
				<?php
				if ( ! empty( $crp_public_obj->mwb_crp_get_referal_signup_coupon( $user_id ) ) ) :
					foreach ( $crp_public_obj->mwb_crp_get_referal_signup_coupon( $user_id ) as $coupon_code => $user_id_crp_coupon ) :
						$user_id_crp_coupon = esc_html( $user_id_crp_coupon );
						$coupon             = new WC_Coupon( $coupon_code );
						$flag               = false;
						if ( 'publish' === get_post_status( $coupon_code ) ) :
							$flag = true;
							?>
				<tr>
					<td data-th="<?php esc_html_e( 'Coupon ', 'coupon-referral-program' ); ?>">
						<div class="mwb-crp-coupon-code"><p id="mwb<?php echo esc_html( $coupon_code ); ?>"><?php echo esc_html( $coupon->get_code() ); ?></p>
							<span class="mwb-crp-coupon-amount">
							<?php
							echo ( 'fixed_cart' === $coupon->get_discount_type() ) ?
								wp_kses_post( wc_price( $coupon->get_amount() ) ) : esc_html( $coupon->get_amount() ) . '%';
							?>
								</span> <img class="mwb-crp-coupon-scissors" src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/scissors.png' ); ?>" alt="scissors image"> <span class="mwb-crp-coupon-wrap">
								<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo esc_html( $coupon_code ); ?>" aria-label="copied">
									<span class="mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
									<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
								</button>
							</span>
						</div>
					</td>
					<td data-th="<?php esc_html_e( 'Coupon Created', 'coupon-referral-program' ); ?>"><?php echo esc_html( $crp_public_obj->mwb_crp_get_transalted_coupon_created_date( $coupon ) ); ?></td>
					<td data-th="Expiry Date"><?php echo esc_html( $crp_public_obj->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ); ?></td>
					<td data-th="<?php esc_html_e( 'Event', 'coupon-referral-program' ); ?>"><?php esc_html_e( 'Referral Signup', 'coupon-referral-program' ); ?></td>
					<td data-th="<?php esc_html_e( 'Referred Users', 'coupon-referral-program' ); ?>"><?php echo esc_html( ( get_userdata( $user_id_crp_coupon ) ) ? esc_html( get_userdata( $user_id_crp_coupon )->data->display_name ) : esc_html__( 'User has been deleted', 'coupon-referral-program' ) ); ?></td>
					<td data-th="<?php esc_html_e( 'Usage Count', 'coupon-referral-program' ); ?>"><?php echo esc_html( $coupon->get_usage_count() ); ?></td>
				</tr>
				<?php endif ?>
						<?php
				endforeach;
				endif;
				?>
				<!-- End referal signup -->
				<!-- Start referal purchase -->
				<?php
				if ( ! empty( $crp_public_obj->get_referral_purchase_coupons( $user_id ) ) ) :
					foreach ( $crp_public_obj->get_referral_purchase_coupons( $user_id ) as $coupon_code => $user_id_crp_coupon ) :
						$coupon   = new WC_Coupon( $coupon_code );
						$flag     = false;
						$order_id = get_post_meta( $coupon->get_id(), 'coupon_created_to', true );
						if ( 'publish' === get_post_status( $coupon_code ) ) :
							$flag = true;
							?>
				<tr>
					<td data-th="<?php esc_html_e( 'Coupon ', 'coupon-referral-program' ); ?>">
						<div class="mwb-crp-coupon-code"><p id="mwb<?php echo esc_html( $coupon_code ); ?>"><?php echo esc_html( $coupon->get_code() ); ?></p>
							<span class="mwb-crp-coupon-amount">
							<?php
							echo ( 'fixed_cart' === $coupon->get_discount_type() ) ?
								wp_kses_post( wc_price( $coupon->get_amount() ) ) : esc_html( $coupon->get_amount() ) . '%';
							?>
								</span> <img class="mwb-crp-coupon-scissors" src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/scissors.png' ); ?>" alt="scissors image"> <span class="mwb-crp-coupon-wrap">
								<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo esc_html( $coupon_code ); ?>" aria-label="copied">
									<span class="mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
									<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
								</button>
							</span>
						</div>
					</td>
					<td data-th="<?php esc_html_e( 'Coupon Created', 'coupon-referral-program' ); ?>"><?php echo esc_html( $crp_public_obj->mwb_crp_get_transalted_coupon_created_date( $coupon ) ); ?></td>
					<td data-th="<?php esc_html_e( 'Expiry Date', 'coupon-referral-program' ); ?>"><?php echo esc_html( $crp_public_obj->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ); ?></td>
					<td data-th="<?php esc_html_e( 'Event', 'coupon-referral-program' ); ?>"><?php echo esc_html__( 'Referral Purchase For', 'coupon-referral-program' ) . ' #' . esc_html( $order_id ); ?></td>
					<td data-th="<?php esc_html_e( 'Referred Users', 'coupon-referral-program' ); ?>"><?php echo esc_html( ( get_userdata( esc_html( $user_id_crp_coupon ) ) ) ? esc_html( get_userdata( $user_id_crp_coupon )->data->display_name ) : esc_html__( 'User has been deleted', 'coupon-referral-program' ) ); ?></td>
					<td data-th="<?php esc_html_e( 'Usage Count', 'coupon-referral-program' ); ?>"><?php echo esc_html( $coupon->get_usage_count() ); ?></td>
				</tr>
				<?php endif ?>
						<?php
				endforeach;
				endif;
				?>
				<!-- end referal purchase -->
				<!-- start referal purchase coupon on guest user via referal code -->
				<?php
				if ( ! empty( $crp_public_obj->get_referral_purchase_coupons_on_guest( $user_id ) ) ) :
					foreach ( $crp_public_obj->get_referral_purchase_coupons_on_guest( $user_id ) as $coupon_code => $email ) :
						$coupon   = new WC_Coupon( $coupon_code );
						$order_id = get_post_meta( $coupon->get_id(), 'coupon_created_to', true );
						if ( 'publish' === get_post_status( $coupon_code ) ) :
							?>
				<tr>
					<td data-th="<?php esc_html_e( 'Coupon ', 'coupon-referral-program' ); ?>">
						<div class="mwb-crp-coupon-code"><p id="mwb<?php echo esc_html( $coupon_code ); ?>"><?php echo esc_html( $coupon->get_code() ); ?></p>
							<span class="mwb-crp-coupon-amount">
							<?php
							echo ( 'fixed_cart' === $coupon->get_discount_type() ) ?
								wp_kses_post( wc_price( $coupon->get_amount() ) ) : esc_html( $coupon->get_amount() ) . '%';
							?>
								</span> <img class="mwb-crp-coupon-scissors" src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/scissors.png' ); ?>" alt="scissors image"> <span class="mwb-crp-coupon-wrap">
								<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo esc_html( $coupon_code ); ?>" aria-label="copied">
									<span class="mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
									<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
								</button>
							</span>
						</div>
					</td>
					<td data-th="<?php esc_html_e( 'Coupon Created', 'coupon-referral-program' ); ?>"><?php echo esc_html( $crp_public_obj->mwb_crp_get_transalted_coupon_created_date( $coupon ) ); ?></td>
					<td data-th="<?php esc_html_e( 'Expiry Date', 'coupon-referral-program' ); ?>"><?php echo esc_html( $crp_public_obj->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ); ?></td>
					<td data-th="<?php esc_html_e( 'Event', 'coupon-referral-program' ); ?>"><?php echo esc_html__( 'Referral Purchase Via Guest User For', 'coupon-referral-program' ) . ' #' . esc_html( $order_id ); ?></td>
					<td data-th="<?php esc_html_e( 'Referred Users', 'coupon-referral-program' ); ?>"><?php echo $email ? esc_html( $email ) : esc_html__( 'Email not found', 'coupon-referral-program' ); ?></td>
					<td data-th="<?php esc_html_e( 'Usage Count', 'coupon-referral-program' ); ?>"><?php echo esc_html( $coupon->get_usage_count() ); ?></td>
				</tr>
					<?php endif; ?>
						<?php
				endforeach;
				endif;
				?>
				<!-- End referal purchase coupon on guest user via referal code -->
				<?php if ( empty( $crp_public_obj->get_referral_purchase_coupons( $user_id ) ) && empty( $crp_public_obj->get_signup_coupon( $user_id ) ) && empty( $crp_public_obj->get_referral_purchase_coupons_on_guest( $user_id ) ) && empty( $crp_public_obj->mwb_crp_get_referal_signup_coupon( $user_id ) ) ) : ?>
					<tr>
						<td colspan="6"><?php esc_html_e( 'No Coupons Or No Referred Users Yet', 'coupon-referral-program' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
		<br>
		<?php $nonce = wp_create_nonce( 'mwb_crp_nonce' ); ?>
		<a  href="<?php echo esc_html( admin_url( 'admin.php?page=wc-reports&tab=crp_report&nonce=' . $nonce ) ); ?>" class="button button-primary mwb_wpr_save_changes"><?php esc_html_e( 'Go Back', 'coupon-referral-program' ); ?></a> 
		<?php
	}
} else {
	?>
	<form method="post">
		<a class="btn button" id="wps_crp_export_report" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-reports&tab=crp_report&wps_crp_export_report=wps_crp_csv_report' ) ); ?>"><?php esc_attr_e( 'Export CSV', 'coupon-referral-program' ); ?></a>
		<?php
		$my_list_table = new Coupon_Referral_Program_Admin_Display_Report();
		$my_list_table->prepare_items();
		$my_list_table->search_box( esc_html__( 'Search Users', 'coupon-referral-program' ), 'mwb-crp-user' );
		$my_list_table->display();
		?>
	</form>
	<?php
}

