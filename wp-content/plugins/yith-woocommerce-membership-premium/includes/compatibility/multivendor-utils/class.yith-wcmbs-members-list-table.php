<?php
defined( 'YITH_WCMBS' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'YITH_WCMBS_Members_List_Table' ) ) {
	/**
	 * List table class
	 *
	 * @since    1.0.0
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCMBS_Members_List_Table extends WP_List_Table {

		public $columns;
		public $hidden;
		public $sortable;

		/**
		 * Constructor
		 */
		public function __construct( $columns = array(), $hidden = array(), $sortable = array() ) {

			$this->columns  = $columns;
			$this->hidden   = $hidden;
			$this->sortable = $sortable;

			parent::__construct(
				array(
					'singular' => 'yith_wcmbs_member',
					'plural'   => 'yith_wcmbs_members',
					'ajax'     => true,
					'screen'   => 'yith-wcmbs-members-list',
				)
			);
		}

		public function get_columns() {
			$columns = array(
				'member'                           => __( 'Member', 'yith-woocommerce-membership' ),
				'yith_wcmbs_user_membership_plans' => __( 'Membership Plans', 'yith-woocommerce-membership' ),
			);

			return $columns;
		}

		public function get_sortable() {
			$sortable = array(
				'member' => array( 'member', false ),
			);

			return $sortable;
		}

		public function get_hidden() {
			return array();
		}

		public function prepare_items( $items = array() ) {
			$per_page = max( 1, absint( $_REQUEST['f_per_page'] ?? 10 ) );

			$columns  = $this->get_columns();
			$hidden   = $this->get_hidden();
			$sortable = $this->get_sortable();

			$this->_column_headers = array( $columns, $hidden, $sortable );

			$my_items = ! empty( $items ) ? $items : $this->get_members();
			usort( $my_items, array( $this, 'usort_reorder' ) );

			$current_page = $this->get_pagenum();
			$total_items  = count( $my_items );

			$my_items = array_slice( $my_items, ( ( $current_page - 1 ) * $per_page ), $per_page );

			$this->items = $my_items;

			$order_by = $_REQUEST['orderby'] ?? '';
			$order    = $_REQUEST['order'] ?? '';
			$order_by = ! ! $order_by ? $order_by : 'user';
			$order    = ! ! $order ? $order : 'asc';

			$this->set_pagination_args(
				array(
					'total_items' => $total_items,
					'per_page'    => $per_page,
					'total_pages' => ceil( $total_items / $per_page ),
					'orderby'     => $order_by,
					'order'       => $order,
				)
			);
		}

		/**
		 * @return array
		 */
		public function get_members() {
			$items = array();

			$vendor_taxonomy_name = YITH_WCMBS_Multivendor_Compatibility::get_vendor_taxonomy_name();
			$vendor               = YITH_WCMBS_Multivendor_Compatibility::get_vendor( 'current', 'user' );
			$vendor_id            = YITH_WCMBS_Multivendor_Compatibility::get_vendor_id( $vendor );

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				$users = get_users(
					array(
						'fields' => 'ids',
					)
				);


				$vendor_members = array();
				if ( ! empty( $users ) ) {
					foreach ( $users as $user_id ) {
						$member       = YITH_WCMBS_Members()->get_member( $user_id );
						$member_plans = $member->get_membership_plans(
							array(
								'return' => 'id',
								'status' => 'any',
							)
						);

						if ( $member_plans ) {
							foreach ( $member_plans as $plan_id ) {
								$vendor_ids = wp_get_post_terms( $plan_id, $vendor_taxonomy_name, array( 'fields' => 'ids' ) );
								$vendor_ids = array_map( 'absint', $vendor_ids );

								if ( $vendor_ids && in_array( $vendor_id, $vendor_ids, true ) ) {
									$vendor_members[] = $user_id;
									break;
								}
							}
						}
					}
				}
				$vendor_members = array_unique( $vendor_members );

				if ( ! empty( $vendor_members ) ) {
					foreach ( $vendor_members as $member_id ) {
						$item = array();
						$user = get_user_by( 'id', $member_id );
						if ( $user ) {
							$item['ID']     = $member_id;
							$item['member'] = $user->user_login;
							$member         = YITH_WCMBS_Members()->get_member( $member_id );
							$member_plans   = $member->get_membership_plans(
								array(
									'return' => 'complete',
									'status' => 'any',
								)
							);

							if ( $member_plans ) {
								foreach ( $member_plans as $membership ) {
									if ( $membership instanceof YITH_WCMBS_Membership ) {
										$vendor_ids = wp_get_post_terms( $membership->plan_id, $vendor_taxonomy_name, array( 'fields' => 'ids' ) );
										$vendor_ids = array_map( 'absint', $vendor_ids );

										if ( $vendor_ids && in_array( $vendor_id, $vendor_ids, true ) ) {
											$item['yith_wcmbs_user_membership_plans'][] = $membership;
										}
									}
								}
							}
							$items[] = $item;
						}
					}
				}
			}

			return $items;
		}

		function column_default( $item, $column_name ) {
			$ret = '';

			switch ( $column_name ) {
				case 'yith_wcmbs_user_membership_plans':
					$memberships = $item[ $column_name ];
					if ( ! empty( $memberships ) ) {
						foreach ( $memberships as $membership ) {
							if ( $membership instanceof YITH_WCMBS_Membership ) {
								$p_name  = $membership->get_plan_title();
								$p_dates = $membership->get_plan_info_html();

								$ret .= "<span class='yith-wcmbs-users-membership-info tips {$membership->status}' data-tip='{$p_dates}'>{$p_name}</span>";
							}
						}
					}
					break;

				default:
					$ret = $item[ $column_name ];
					break;
			}

			return $ret;
		}

		function usort_reorder( $a, $b ) {
			$order_by = $_GET['orderby'] ?? '';
			$order_by = ! ! $order_by ? $order_by : 'member';

			$order = $_GET['order'] ?? '';
			$order = ! ! $order ? $order : 'asc';

			$result = strcmp( $a[ $order_by ], $b[ $order_by ] );

			return 'asc' === $order ? $result : - $result;
		}
	}
}
