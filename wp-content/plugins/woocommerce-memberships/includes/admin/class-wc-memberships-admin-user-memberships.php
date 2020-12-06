<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\Memberships\Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Membership Plans admin handler.
 *
 * This class handles all the admin-related functionality for membership plans, like the list screen, meta boxes, etc.
 *
 * @since 1.0.0
 */
class WC_Memberships_Admin_User_Memberships {


	/** @var string the prefix used to make profile field columns unique among the other columns */
	private $profile_field_column_prefix = 'profile-field-';


	/**
	 * Handler constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// admin notices for User Memberships
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		// List Table screen hooks - edit screen columns
		add_filter( 'manage_edit-wc_user_membership_columns',          [ $this, 'customize_columns' ] );
		add_filter( 'manage_edit-wc_user_membership_sortable_columns', [ $this, 'customize_sortable_columns' ] );
		add_action( 'manage_wc_user_membership_posts_custom_column',   [ $this, 'custom_column_content' ], 10, 2 );
		add_filter( 'hidden_columns',                                  [ $this, 'set_hidden_columns' ], 10, 3 );
		add_filter( 'display_post_states',                             [ $this, 'remove_post_states' ] );
		add_filter( 'the_title',                                       [ $this, 'user_membership_title' ], 10, 2 );

		// Add/Edit screen hooks
		add_action( 'post_submitbox_misc_actions', array( $this, 'normalize_edit_screen' ) );

		// filter post clauses and sorting handler
		add_filter( 'request',       array( $this, 'request_query' ) );
		add_filter( 'posts_clauses', array( $this, 'posts_clauses' ), 10, 2 );

		// render dropdowns for user membership filters
		add_action( 'restrict_manage_posts', array( $this, 'restrict_user_memberships' ) );

		// post actions
		add_filter( 'post_row_actions', array( $this, 'customize_row_actions' ), 10, 2 );
		// custom admin actions
		add_action( 'admin_action_pause',  array( $this, 'pause_membership' ) );
		add_action( 'admin_action_resume', array( $this, 'resume_membership' ) );
		add_action( 'admin_action_cancel', array( $this, 'cancel_membership' ) );
		// bulk actions
		add_filter( 'bulk_actions-edit-wc_user_membership', array( $this, 'remove_bulk_actions' ) );
		add_action( 'admin_footer-edit.php',                array( $this, 'add_bulk_actions' ) );
		add_action( 'bulk_edit_custom_box',                 array( $this, 'bulk_edit' ) );
		add_filter( 'months_dropdown_results',              '__return_empty_array' );

		// advanced filters
		add_action( 'manage_posts_extra_tablenav', [ $this, 'add_profile_fields_filters' ] );

		// User Membership validation
		add_action( 'wp_insert_post_empty_content', array( $this, 'validate_user_membership' ), 1, 2 );
		add_action( 'load-post-new.php',            array( $this, 'maybe_prevent_adding_user_membership' ), 1 );
	}


	/**
	 * Customizes user memberships columns.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns
	 * @return array
	 */
	public function customize_columns( $columns ) {

		// remove title and date columns
		unset( $columns['title'],  $columns['date'] );

		$columns['title'] = __( 'Name', 'woocommerce-memberships' );  // member name column
		$columns['email'] = __( 'Email', 'woocommerce-memberships' ); // member email

		// profile fields
		foreach( Profile_Fields::get_profile_field_definitions() as $profile_field_definition ) {

			// using a prefix will prevent overriding a column in case a profile field definition has the same slug as its key
			$column_key = sprintf( '%s%s', $this->profile_field_column_prefix, $profile_field_definition->get_slug() );

			$columns[ $column_key ] = stripslashes( $profile_field_definition->get_name() );
		}

		$columns['plan']         = __( 'Plan', 'woocommerce-memberships' );         // associated membership plan
		$columns['status']       = __( 'Status', 'woocommerce-memberships' );       // user membership status
		$columns['member_since'] = __( 'Member since', 'woocommerce-memberships' ); // membership created
		$columns['expires']      = __( 'Expires', 'woocommerce-memberships' );      // expiration date-time
		$columns['last_login']   = __( 'Last login', 'woocommerce-memberships' );   // last login since

		return $columns;
	}


	/**
	 * Customizes user memberships sortable columns.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param array $columns
	 * @return array
	 */
	public function customize_sortable_columns( $columns ) {

		$columns['title']        = 'name';
		$columns['email']        = 'email';
		$columns['status']       = 'post_status';
		$columns['member_since'] = 'start_date';
		$columns['expires']      = 'expiry_date';

		return $columns;
	}


	/**
	 * Customizes user memberships row actions
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions
	 * @param \WP_Post $post
	 * @return array
	 */
	public function customize_row_actions( $actions, \WP_Post $post ) {

		if ( 'wc_user_membership' === $post->post_type ) {

			// remove quick edit action and move to trash
			unset( $actions['inline hide-if-no-js'], $actions['trash'] );

			if ( $user_membership = wc_memberships_get_user_membership( $post ) ) {

				$post_link = remove_query_arg( 'action', get_edit_post_link( $post->ID, '' ) );

				if ( $user_membership->is_paused() ) {
					$resume_link = add_query_arg( 'action', 'resume', wp_nonce_url( $post_link, 'wc-memberships-resume-membership-' . $post->ID ) );
					$actions['resume'] = '<a href="' . esc_url( $resume_link ) . '">' . esc_html__( 'Resume', 'woocommerce-memberships' ) . '</a>';
				} elseif ( ! $user_membership->is_cancelled() ) {
					$pause_link = add_query_arg( 'action', 'pause', wp_nonce_url( $post_link, 'wc-memberships-pause-membership-' . $post->ID ) );
					$actions['pause']  = '<a href="' . esc_url( $pause_link ) . '">'  . esc_html__( 'Pause', 'woocommerce-memberships' )  . '</a>';
				}

				if ( ! $user_membership->is_cancelled() ) {
					$cancel_link = add_query_arg( 'action', 'cancel', wp_nonce_url( $post_link, 'wc-memberships-cancel-membership-' . $post->ID ) );
					$actions['cancel'] = '<a href="' . esc_url( $cancel_link ) . '">' . esc_html__( 'Cancel', 'woocommerce-memberships' ) . '</a>';
				}

				if ( current_user_can( 'delete_post', $user_membership->get_id() ) ) {
					$actions['delete'] = "<a class='submitdelete delete-membership' title='" . esc_attr__( 'Delete this membership permanently', 'woocommerce-memberships' ) . "' href='" . esc_url( get_delete_post_link( $post->ID, '', true ) ) . "'>" . esc_html__( 'Delete', 'woocommerce-memberships' ) . '</a>';
				}
			}
		}

		return $actions;
	}


	/**
	 * Disables move to trash bulk action.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param array $actions
	 * @return array
	 */
	public function remove_bulk_actions( $actions ) {

		unset( $actions['trash'] );

		return $actions;
	}


	/**
	 * Customizes user memberships bulk actions.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 */
	public function add_bulk_actions() {
		global $post_type;

		if ( $post_type === 'wc_user_membership' && current_user_can( 'manage_woocommerce_user_memberships' ) ) :

			?>
			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					var exportLabel = '<?php esc_html_e( 'Export to CSV', 'woocommerce-memberships' ); ?>',
						deleteLabel = '<?php esc_html_e( 'Delete', 'woocommerce-memberships' ); ?>';
					$( '<option>' ).val( 'export' ).text( exportLabel ).appendTo( 'select[name="action"]' );
					$( '<option>' ).val( 'export' ).text( exportLabel ).appendTo( 'select[name="action2"]' );
					$( '<option>' ).val( 'delete' ).text( deleteLabel ).appendTo( "select[name='action']" );
					$( '<option>' ).val( 'delete' ).text( deleteLabel ).appendTo( "select[name='action2']" );
				} );
			</script>
			<?php

		endif;
	}


	/**
	 * Customizes bulk edit form.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $column
	 */
	public function bulk_edit( $column ) {

		if ( 'status' !== $column ) {
			return;
		}

		// prepare options
		$status_options = array();
		foreach ( wc_memberships_get_user_membership_statuses() as $status => $labels ) {
			$status_options[ $status ] = $labels['label'];
		}

		/**
		 * Filters the status options available in user memberships bulk edit box.
		 *
		 * @since 1.0.0
		 *
		 * @param array $options associative array of option value => label pairs
		 */
		$status_options = apply_filters( 'wc_memberships_bulk_edit_user_memberships_status_options', $status_options );

		?>
		<fieldset class="inline-edit-col-right" id="wc-memberships-fields-bulk">
			<div class="inline-edit-col">
				<div class="inline-edit-group">
					<label class="inline-edit-status alignleft">
						<span class="title"><?php esc_html_e( 'Status', 'woocommerce-memberships' ); ?></span>
						<select name="_status">
							<option value="-1"><?php echo '&mdash; ' . esc_html__( 'No Change', 'woocommerce-memberships' ) . ' &mdash;'; ?></option>
							<?php
								if ( ! empty( $status_options ) ) {
									foreach ( $status_options as $status => $label ) {
										echo "\t<option value='" . esc_attr( $status ) . "'>" . esc_html( $label ) . "</option>" . PHP_EOL;
									}
								}
							?>
						</select>
					</label>
				</div>
			</div>
		</fieldset>
		<?php
	}


	/**
	 * Outputs custom column content.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $column
	 * @param int $post_id
	 */
	public function custom_column_content( $column, $post_id ) {

		$user_membership = wc_memberships_get_user_membership( $post_id );
		$user            = $user_membership ? get_userdata( $user_membership->get_user_id() ) : null;
		$date_format     = wc_date_format();
		$time_format     = wc_time_format();

		switch ( $column ) {

			case 'email':
				echo $user ? $user->user_email : '';
			break;

			case 'plan':

				if ( $user_membership ) {

					// It shouldn't normally ever happen that the plan can't be found,
					// but prevents fatal errors on borked installations where the associated plan disappeared.
					if ( $plan = $user_membership->get_plan() ) {
						echo '<a href="' . esc_url( get_edit_post_link( $user_membership->get_plan_id() ) ) . '">' . $plan->get_formatted_name() . '</a>';
					} else {
						echo '-';
					}
				}

			break;

			case 'status':

				$statuses     = wc_memberships_get_user_membership_statuses();
				$status       = $user_membership ? $user_membership->get_status() : '';
				$status_key   = "wcm-{$status}";

				if ( 'paused' === $status && ( $paused_date = $user_membership->get_local_paused_date( 'timestamp' ) ) ) {
					/* translators: Placeholder: %s - date since the membership was paused */
					printf( __( 'Paused since %s', 'woocommerce-memberships' ), date_i18n( wc_date_format(), $paused_date ) . ' ' . date_i18n( wc_time_format(), $paused_date ) );
				} elseif ( 'cancelled' === $status && ( $cancelled_date = $user_membership->get_local_cancelled_date( 'timestamp' ) ) ) {
					/* translators: Placeholder: %s - date on which the membership was cancelled */
					printf( __( 'Cancelled on %s', 'woocommerce-memberships'), date_i18n( wc_date_format(), $cancelled_date ) . ' ' . date_i18n( wc_time_format(), $cancelled_date ) );
				} elseif ( isset( $statuses[ $status_key ]['label'] ) ) {
					echo esc_html( $statuses[ $status_key ]['label'] );
				}

			break;

			case 'member_since':

				if ( $user_membership ) {

					$since_time = $user_membership->get_local_start_date( 'timestamp' );

					$date = esc_html( date_i18n( $date_format, (int) $since_time ) );
					$time = esc_html( date_i18n( $time_format, (int) $since_time ) );

					printf( '<span class="member-since-date">%1$s %2$s</span>', $date, $time );

					$order_id = $user_membership->get_order_id();
					$order    = $order_id ? wc_get_order( $order_id ) : null;

					if ( $order_id && $order ) {
						/* translators: Placeholder: %s - order number */
						printf( '<span class="member-since-order"><small>' . __( 'Order: %s', 'woocommerce-memberships' ) . '</small></span>', '<a href="' . esc_url( get_edit_post_link( $order_id ) ) . '">#' . esc_html( $order->get_order_number() ) . '</a>' );
					}
				}

			break;

			case 'expires':

				if ( $user_membership ) {

					// do not calculate paused time if the membership has expired already
					$end_time = $user_membership->get_local_end_date( 'timestamp', ! $user_membership->is_expired() );

					if ( ! empty( $end_time ) && is_numeric( $end_time ) ) {
						$date = esc_html( date_i18n( $date_format, (int) $end_time ) );
						$time = esc_html( date_i18n( $time_format, (int) $end_time ) );
					} else {
						$date = esc_html__( 'Never', 'woocommerce-memberships' );
						$time = '';
					}

					printf( '%1$s %2$s', $date, $time );
				}

			break;

			case 'last_login' :

				$last_active = $user instanceof \WP_User ? get_user_meta( $user->ID, 'wc_last_active', true ) : null;

				echo is_numeric( $last_active ) ? sprintf(
					/* translators: Placeholder: %s last login since */
					esc_html__( '%s ago', 'woocommerce-memberships' ),
					human_time_diff( (int) $last_active )
				) : '&mdash;';

			break;

			// variable profile fields
			default:

				$user_id = $user_membership->get_user_id();

				if ( $user_id && 0 === strpos( $column, $this->profile_field_column_prefix ) ) {

					// removes the column header prefix to extract the profile field definition slug
					$profile_field_slug = str_replace( $this->profile_field_column_prefix, '', $column );

					if ( Profile_Fields::is_profile_field_slug( $profile_field_slug ) ) {

						$no_profile_field_value = '&mdash;';

						// if it's a valid profile field definition slug, then attempts to retrieve the profile field for the user
						if( $profile_field = $user_membership->get_profile_field( $profile_field_slug ) ) {

							if ( $profile_field->get_definition()->is_type( Profile_Fields::TYPE_FILE ) ) {
								$media_library_url = get_edit_post_link( $profile_field->get_value() );
								echo $media_library_url ? sprintf( '<a href="%s">%s</a>', esc_url( $media_library_url ), basename( get_attached_file( $profile_field->get_value() ) ) ) : $no_profile_field_value;
							} else {
								echo $profile_field->get_value() ? esc_html( $profile_field->get_formatted_value() ) : $no_profile_field_value;
							}

						} else {

							echo $no_profile_field_value;
						}
					}
				}

			break;
		}
	}


	/**
	 * Add profile field columns to the list of hidden columns.
	 *
	 * Ensures that columns for new profile fields are hidden by default.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 *
	 * @param array $hidden hidden columns
	 * @param \WP_Screen $screen current screen
	 * @param bool $use_defaults whether the table is using the default hidden columns
	 * @return array
	 */
	public function set_hidden_columns( $hidden, $screen, $use_defaults ) {
		global $pagenow;

		if ( 'edit.php' === $pagenow && $screen instanceof \WP_Screen ) {

			$hidden_profile_field_columns = get_user_meta( get_current_user_id(), '_wc_memberships_hidden_profile_field_columns', true );
			$profile_field_columns        = [];

			if ( ! is_array( $hidden_profile_field_columns ) ) {
				$hidden_profile_field_columns = [];
			}

			foreach ( Profile_Fields::get_profile_field_definitions() as $profile_field_definition ) {

				// using a prefix will prevent overriding a column in case a profile field definition has the same slug as its key
				$column_key = sprintf( '%s%s', $this->profile_field_column_prefix, $profile_field_definition->get_slug() );

				// hide profile field column if the default columns are being shown
				// hide profile field column that have never been hidden (for example columns for fields added after the user customized the columns to show)
				if ( $use_defaults || ! in_array( $column_key, $hidden_profile_field_columns, true ) ) {

					$hidden_profile_field_columns[] = $column_key;
					$hidden[]                       = $column_key;
				}

				$profile_field_columns[] = $column_key;
			}

			// remove repeated columns and columns for definitions that no longer exist
			$hidden_profile_field_columns = array_intersect( array_unique( $hidden_profile_field_columns ), $profile_field_columns );
			$hidden                       = array_unique( $hidden );

			// update the list of hidden columns to ensure we don't list them as hidden in the user option again
			update_user_meta( get_current_user_id(), '_wc_memberships_hidden_profile_field_columns', $hidden_profile_field_columns );

			// update list of hidden columns if the user already customized them to make sure column for new fields remain hidden until enabled
			if ( ! $use_defaults ) {
				update_user_option( get_current_user_id(), 'manage' . $screen->id . 'columnshidden', $hidden, true );
			}
		}

		return $hidden;
	}


	/**
	 * Hides default publishing box, etc.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function normalize_edit_screen() {

		?>
		<style type="text/css">
			#post-body-content, #titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none; }
		</style>
		<?php
	}


	/**
	 * Filters and sorting handler.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $vars
	 * @return array
	 */
	public function request_query( $vars ) {
		global $typenow;

		if ( 'wc_user_membership' === $typenow ) {

			// filter by plan ID (post parent)
			if ( isset( $_GET['post_parent'] ) ) {
				$vars['post_parent'] = $_GET['post_parent'];
			}

			// filter by expiry date
			if ( isset( $_GET['expires'] ) ) {

				$min_date = $max_date = null;

				switch ( $_GET['expires'] ) {

					case 'today':

						$min_date = date( 'Y-m-d H:i:s', strtotime( 'today midnight' ) );
						$max_date = date( 'Y-m-d H:i:s', strtotime( 'tomorrow midnight' ) - 1 );

					break;

					case 'this_week':

						$min_date = date( 'Y-m-d H:i:s', strtotime( 'this week midnight' ) );
						$max_date = date( 'Y-m-d H:i:s', strtotime( 'next week midnight' ) - 1 );

					break;

					case 'this_month':

						$min_date = date( 'Y-m-d H:i:s', strtotime( 'first day of midnight' ) );
						$max_date = date( 'Y-m-d H:i:s', strtotime( 'first day of +1 month midnight' ) - 1 );

					break;

				}

				if ( $min_date && $max_date ) {

					$vars['meta_query'] = isset( $vars['meta_query'] ) ? $vars['meta_query'] : array();
					$vars['meta_query'] = array_merge( $vars['meta_query'], array( array(
						'key'     => '_end_date',
						'value'   => array( $min_date, $max_date ),
						'compare' => 'BETWEEN',
						'type'    => 'DATETIME',
					) ) );
				}
			}

			// filter by profile fields
			if ( $profile_fields = $this->parse_profile_fields_filters() ) {
				$vars['author__in'] = $this->get_user_ids_by_profile_fields( $profile_fields );
			}

			// sorting order
			if ( isset( $vars['orderby'] ) ) {

				switch ( $vars['orderby'] ) {

					// order by plan (abusing title column)
					case 'title':
						$vars['orderby'] = 'post_parent';
					break;

					// order by start date (member since)
					case 'start_date':

						$vars['meta_key'] = '_start_date';
						$vars['orderby']  = 'meta_value';

					break;

					// order by end date (expires)
					case 'expiry_date':

						$vars['meta_key'] = '_end_date';
						$vars['orderby']  = 'meta_value';

					break;

				}
			}
		}

		return $vars;
	}


	/**
	 * Parses a JSON string from form submissions into an object.
	 *
	 * @since 1.19.0
	 *
	 * @return \stdClass|null
	 */
	private function parse_profile_fields_filters() {

		$filter = null;

		if ( ! empty( $_GET['profile_fields'] ) ) {

			$filter = json_decode( wp_unslash( $_GET['profile_fields'] ), false );

			if ( json_last_error() ) {
				$filter = null;
			}
		}

		return $filter;
	}


	/**
	 * Gets the user IDs from a JSON containing profile fields filter rules.
	 *
	 * @since 1.19.0
	 *
	 * @param \stdClass $filter an object containing an operand and rules properties
	 * @return int[] user ids matching the conditions in the profile fields filter
	 */
	private function get_user_ids_by_profile_fields( $filter ) {

		try {

			$operand = $filter->operand;

			if ( ! in_array( $operand, [ 'AND', 'OR' ], true ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Invalid profile field filter operand.' );
			}

			$rules = $filter->rules;

			if ( empty( $rules ) || ! is_array( $rules ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Invalid profile field filter ruleset.' );
			}

			$rule_id     = 0;
			$users_in    = [ [] ];
			$users_query = [ 'fields' => 'ID' ];
			$meta_query  = [];

			foreach ( $rules as $rule ) {

				if ( ! isset( $rule->comparator, $rule->slug, $rule->value ) ) {
					throw new Framework\SV_WC_Plugin_Exception( 'Invalid profile field filter clause.' );
				}

				if ( $profile_field_definition = Profile_Fields::get_profile_field_definition( $rule->slug ) ) {

					$meta_key               = Profile_Fields::get_profile_field_user_meta_key( $profile_field_definition->get_slug() );
					$meta_query[ $rule_id ] = [
						'key' => $meta_key,
					];

					// normalize SQL comparator and values
					if ( 'is_empty' === $rule->comparator ) {

						// special handling, we skip this meta query
						unset( $meta_query[ $rule_id ] );

						// we need to search for instances where the meta either was never been set or it was set but the value is empty, the WordPress meta query doesn't allow for both at the same time
						$users_in[] = get_users( [
							'fields'     => 'ID',
							'meta_query' => [
								'relation' => 'OR',
								[
									'key'     => $meta_key,
									'compare' => '=',
									'value'   => '',
								],
								[
									'key'     => $meta_key,
									'compare' => 'NOT EXISTS',
								],
							],
						] );

					// for files we need special handling since the user may input a file name or URL but file profile fields are storied as attachment IDs
					} elseif ( ! is_numeric( $rule->value ) && ! is_array( $rule->value ) && 'file' === $profile_field_definition->is_type( Profile_Fields::TYPE_FILE ) ) {

						// this should search attachment post titles, which by default are also file names
						$files = get_posts( [
							's'         => $rule->value,
							'post_type' => 'attachment',
							'fields'    => 'ids',
							'nopaging'  => true,
						] );

						$meta_query[ $rule_id ]['compare'] = in_array( $rule->comparator, [ 'is', 'includes' ], true ) ? 'IN' : 'NOT IN';

						if ( ! empty( $files ) ) {
							$meta_query[ $rule_id ]['value'] = $files;
						} else {
							// if we searched for a keyword but not files were found, a 0 attachment ID would produce no results, whereas if we were searching for a negative keyword, then we should allow results
							$meta_query[ $rule_id ]['value'] = 'IN' === $meta_query[ $rule_id ]['compare'] ? [ 0 ] : [];
						}

					// searching in serialized arrays won't be super accurate but this could get close enough...
					} elseif ( is_array( $rule->value ) ) {

						$comparator = 'is' === $rule->comparator ? 'LIKE' : 'NOT LIKE';

						if ( 1 === count( $rule->value ) ) {

							$meta_query[ $rule_id ]['compare'] = $comparator;
							$meta_query[ $rule_id ]['value']   = current( $rule->value );

						} else {

							unset( $meta_query[ $rule_id ] );

							foreach ( $rule->value as $value ) {

								$meta_query[ $rule_id ]['compare'] = $comparator;
								$meta_query[ $rule_id ]['key']     = $meta_key;
								$meta_query[ $rule_id ]['value']   = $value;

								$rule_id++;
							}
						}

					} else {

						switch ( $rule->comparator ) {
							case 'doesnt_include' :
								$comparator = 'NOT LIKE';
							break;
							case 'includes' :
								$comparator = 'LIKE';
							break;
							case 'is_not' :
							case 'not_in' :
								$comparator = '!=';
							break;
							case 'is' :
							case 'in' :
							default :
								$comparator = '=';
							break;
						}

						$meta_query[ $rule_id ]['compare'] = $comparator;
						$meta_query[ $rule_id ]['value']   = $rule->value;
					}
				}

				$rule_id++;
			}

			if ( ! empty( $meta_query ) ) {

				if ( count( $meta_query ) > 1 ) {
					$meta_query['relation'] = $operand;
				}

				$users_query['meta_query'] = $meta_query;
			}

			if ( count( $users_in ) > 1 ) {
				$users_in = array_filter( array_merge( ...$users_in ) );
				$user_ids = array_intersect( get_users( $users_query ), $users_in );
			} else {
				$user_ids = get_users( $users_query );
			}

			if ( empty( $user_ids ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'No users found matching the filters criteria.' );
			}

		} catch ( \Exception $e ) {

			$user_ids = [ 0 ];
		}

		return $user_ids;
	}


	/**
	 * Alters posts query clauses.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $pieces
	 * @param \WP_Query $wp_query
	 * @return array
	 */
	public function posts_clauses( $pieces, \WP_Query $wp_query ) {
		global $wpdb;

		// bail out if not the correct post type
		if ( 'wc_user_membership' !== $wp_query->query['post_type'] ) {
			return $pieces;
		}

		// whether to add a join clause for users table or not
		$join_users = false;

		// search
		if ( isset( $wp_query->query['s'] ) ) {

			// remove prefixing underscores as they'd end up escaped strings
			// and would produce empty search results
			$keyword  = trim( ltrim( $wp_query->query['s'], '_' ) );

			if ( ! empty( $keyword ) ) {

				$join_users = true;
				$keyword    = '%' . $keyword . '%';

				if ( ! empty( $_GET['post_status'] ) ) {
					$where_post_status = $wpdb->prepare( "$wpdb->posts.post_status = '%s'", $_GET['post_status'] );
				} else {
				 	$where_post_status = "$wpdb->posts.post_status = 'wcm-active' OR $wpdb->posts.post_status = 'wcm-free_trial' OR $wpdb->posts.post_status = 'wcm-delayed' OR $wpdb->posts.post_status = 'wcm-complimentary' OR $wpdb->posts.post_status = 'wcm-pending' OR $wpdb->posts.post_status = 'wcm-paused' OR $wpdb->posts.post_status = 'wcm-expired' OR $wpdb->posts.post_status = 'wcm-cancelled'";
				}

				// Do a LIKE search in user fields:
				$where_post_type    = "$wpdb->posts.post_type = 'wc_user_membership'";
				$where_title        = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $keyword );
				$where_user_login   = $wpdb->prepare( "$wpdb->users.user_login LIKE %s", $keyword );
				$where_user_email   = $wpdb->prepare( "$wpdb->users.user_email LIKE %s", $keyword );
				$where_display_name = $wpdb->prepare( "$wpdb->users.display_name LIKE %s", $keyword );
				$where_user_id      = $wpdb->prepare( "$wpdb->users.id LIKE %s", $keyword );

				$where = " AND $where_post_type AND ($where_post_status) AND ( ($where_title) OR ($where_user_login) OR ($where_user_email) OR ($where_display_name) OR ($where_user_id) )";

				// replace the where clauses
				$pieces['where'] = $where;
			}
		}

		// order by
		if ( isset( $wp_query->query['orderby'] ) ) {

			switch ( $wp_query->query['orderby'] ) {

				case 'email':

					$join_users = true;
					$pieces['orderby'] = " $wpdb->users.user_email " . strtoupper( $wp_query->query['order'] ) . " ";

				break;

				case 'name';

					$join_users = true;
					$pieces['orderby'] = " $wpdb->users.display_name " . strtoupper( $wp_query->query['order'] ) . " ";

				break;

			}
		}

		// join users table, if needed
		if ( $join_users ) {
			$pieces['join'] .= " LEFT JOIN $wpdb->users ON $wpdb->posts.post_author = $wpdb->users.ID ";
		}

		return $pieces;
	}


	/**
	 * Uses membership plan name as user membership title.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $title Original title
	 * @param int $post_id Post ID
	 * @return string Modified title
	 */
	public function user_membership_title( $title, $post_id = null ) {
		global $pagenow;

		if ( $post_id && 'wc_user_membership' === get_post_type( $post_id ) ) {

			$user_membership = wc_memberships_get_user_membership( $post_id );

			if ( $user_membership ) {

				$user = get_userdata( $user_membership->get_user_id() );
				$plan = $user_membership->get_plan();

				if ( $user && ( 'edit.php' === $pagenow || ! $plan ) ) {
					$title = $user->display_name;
				} elseif ( $plan ) {
					$title = $plan->get_name();
				}
			}
		}

		return $title;
	}


	/**
	 * Removes post states (such as "Password protected") from list table.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $states
	 * @return array
	 */
	public function remove_post_states( $states ) {

		return [];
	}


	/**
	 * Renders dropdowns for user membership filters.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function restrict_user_memberships() {
		global $typenow;

		if ( 'wc_user_membership' !== $typenow ) {
			return;
		}

		// membership plan options
		$membership_plans = wc_memberships_get_membership_plans();
		$selected_plan    = isset( $_GET['post_parent'] ) ? $_GET['post_parent'] : null;

		$statuses        = wc_memberships_get_user_membership_statuses();
		$selected_status = isset( $_GET['post_status'] ) ? $_GET['post_status'] : null;

		/**
		 * Filters the expiry terms dropdown menu.
		 *
		 * @since 1.0.0
		 *
		 * @param array $terms associative array of expiry term keys and labels
		 */
		$expires = apply_filters( 'wc_memberships_expiry_terms_dropdown_options', array(
			'today'      => __( 'Today', 'woocommerce-memberships' ),
			'this_week'  => __( 'This week', 'woocommerce-memberships' ),
			'this_month' => __( 'This month', 'woocommerce-memberships' ),
		) );

		$selected_expiry_term = isset( $_GET['expires'] ) ? $_GET['expires'] : null;

		?>
		<select
			name="post_parent"
			class="wc-enhanced-select">
			<option value=""><?php esc_html_e( 'All plans', 'woocommerce-memberships' ); ?></option>
			<?php
				if ( ! empty( $membership_plans ) ) {
					foreach ( $membership_plans as $membership_plan ) {
						echo "\t<option value='" . esc_attr( $membership_plan->get_id() ) . "'" . selected( $membership_plan->get_id() , $selected_plan, false ) . '>' . esc_html( $membership_plan->get_formatted_name() ) . '</option>' . PHP_EOL;
					}
				}
			?>
		</select>

		<select
			name="post_status"
			class="wc-enhanced-select">
			<option value=""><?php esc_html_e( 'All statuses', 'woocommerce-memberships' ); ?></option>
			<?php
				if ( ! empty( $statuses ) ) {
					foreach ( $statuses as $status => $labels ) {
						echo "\t<option value='" . esc_attr( $status ) . "' " . selected( $status, $selected_status, false ) . '>' . esc_html( $labels['label'] ) . '</option>' . PHP_EOL;
					}
				}
			?>
		</select>

		<select
			name="expires"
			class="wc-enhanced-select">
			<option value=""><?php esc_html_e( 'Expires', 'woocommerce-memberships' ); ?></option>
			<?php
				if ( ! empty( $expires ) ) {
					foreach ( $expires as $expiry_term => $label ) {
						echo "\t<option value='" . esc_attr( $expiry_term ) . "' " . selected( $expiry_term, $selected_expiry_term, false ) . '>' . esc_html( $label ) . '</option>' . PHP_EOL;
					}
				}
			?>
		</select>
		<?php
	}


	/**
	 * Adds a profile fields filtering UI in the list screen.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 *
	 * @param string $which either top or bottom
	 */
	public function add_profile_fields_filters( $which ) {

		if ( 'top' !== $which || ! Profile_Fields::is_using_profile_fields() ) :
			return;
		endif;

		ob_start();

		?>
		<select
			name="profile_fields_operand"
			aria-label="<?php esc_html_e( 'Choose to apply any or all filters', 'woocommerce-memberships' ); ?>">
			<option value="AND" <?php selected( isset( $_GET['profile_fields_operand'] ) && 'AND' === $_GET['profile_fields_operand'] ); ?>><?php echo esc_html_x( 'all', 'Choose all filters (AND operand)', 'woocommerce-memberships' ); ?></option>
			<option value="OR" <?php selected( isset( $_GET['profile_fields_operand'] ) && 'OR' === $_GET['profile_fields_operand'] ); ?>><?php echo esc_html_x( 'any', 'Choose any filters (OR operand)', 'woocommerce-memberships' ); ?></option>
		</select>
		<?php

		$filter_operand = ob_get_clean();

		?>
		<br class="clear" />
		<div id="wc-memberships-user-memberships-advanced-filters">

			<div class="wc-memberships-user-memberships-advanced-filters-header">
				<h3><?php printf(
					/* translators: Placeholder: %s - dropdown input with filters operand (any/all) */
					esc_html__( 'User memberships match %s filters', 'woocommerce-memberships' ),
					$filter_operand
				); ?></h3>
			</div>

			<div class="wc-memberships-user-memberships-advanced-filters-body">

				<input
					type="hidden"
					name="profile_fields"
					value=""
				/>

				<ul class="wc-memberships-user-membership-advanced-filters-list">
					<?php

					$filters = $this->parse_profile_fields_filters();
					$rows    = isset( $filters->rules ) && is_array( $filters->rules ) ? $filters->rules : [];

					foreach ( $rows as $index => $row ) :
						if ( isset( $row->slug, $row->comparator, $row->value ) ) :
							$this->output_profile_fields_filter_row( $index, $row );
						endif;
					endforeach;

					$this->output_profile_fields_filter_row(); // template row

					?>
				</ul>

			</div>

			<div class="wc-memberships-user-memberships-advanced-filters-add-filter">
				<div>
					<button
						type="button"
						aria-expanded="false">
						<svg class="gridicon gridicons-add-outline" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M12 4c4.41 0 8 3.59 8 8s-3.59 8-8 8-8-3.59-8-8 3.59-8 8-8m0-2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm5 9h-4V7h-2v4H7v2h4v4h2v-4h4v-2z"></path></g></svg>
						<?php esc_html_e( 'Add a filter', 'woocommerce-memberships' ); ?>
					</button>
				</div>
			</div>

			<div class="wc-memberships-user-memberships-advanced-filters-footer">
				<div>
					<button
						type="button"
						disabled="disabled"
						class="button button-primary">
						<?php echo esc_html_x( 'Filter', 'Apply filters', 'woocommerce-memberships' ); ?>
					</button>
				</div>
				<div>
					<a
						class="wc-memberships-user-memberships-advanced-filters-clear"
						href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_user_membership' ) ); ?>">
						<?php echo esc_html_e( 'Clear all filters', 'woocommerce-memberships' ); ?>
					</a>
				</div>
			</div>

		</div>
		<?php
	}


	/**
	 * Outputs a profile field filter row HTML.
	 *
	 * @since 1.19.0
	 *
	 * @param null|string|int $row_index row index (default null for template row)
	 * @param null|\StdClass $row_data row data (default null for template row)
	 */
	private function output_profile_fields_filter_row( $row_index = null, $row_data = null ) {

		$row_index      = is_numeric( $row_index )       ? sprintf( '-item-%d', $row_index ) : '-template';
		$row_slug       = isset( $row_data->slug )       ? $row_data->slug                   : null;
		$row_comparator = isset( $row_data->comparator ) ? $row_data->comparator             : 'is';
		$row_value      = isset( $row_data->value )      ? $row_data->value                  : '';

		?>
		<li id="wc-memberships-user-membership-advanced-filter<?php echo $row_index; ?>" class="wc-memberships-user-membership-advanced-filters-list-item">
			<div class="wc-memberships-user-membership-advanced-filter-wrapper">

				<div class="wc-memberships-user-membership-advanced-filter-column">
					<select class="wc-memberships-filter-slug">
						<option value=""><?php esc_html_e( 'Select field', 'woocommerce-memberships' ); ?></option>
						<?php foreach ( Profile_Fields::get_profile_field_definitions() as $profile_field_definition ) : ?>
							<?php $slug = $profile_field_definition->get_slug(); ?>
							<option
								data-type="<?php echo esc_attr( $profile_field_definition->get_type() ); ?>"
								value="<?php echo esc_attr( $slug ); ?>" <?php selected( $slug, $row_slug ); ?>>
								<?php echo esc_html( $profile_field_definition->get_name() ) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="wc-memberships-user-membership-advanced-filter-column">
					<?php if ( ! $row_data ) : ?>

						<select class="wc-memberships-filter-comparator" disabled="disabled">
							<option value=""><?php esc_html_e( 'is', 'woocommerce-memberships' ); ?></option>
						</select>

					<?php elseif ( $profile_field_definition = Profile_Fields::get_profile_field_definition( $row_slug ) ) : ?>

						<select class="wc-memberships-filter-comparator">
							<option value="is" <?php selected( 'is', $row_comparator ); ?>><?php echo esc_html_x( 'is', 'Comparator: <value> is <something>', 'woocommerce-memberships' ); ?></option>
							<option value="is_not" <?php selected( 'is_not', $row_comparator ); ?>><?php echo esc_html_x( 'is not', 'Comparator: <value> is not <something>', 'woocommerce-memberships' ); ?></option>
							<?php if ( ! $profile_field_definition->has_options() ) : ?>
								<option value="includes" <?php selected( 'includes', $row_comparator ); ?>><?php echo esc_html_x( 'includes', 'Comparator: <value> includes <something>',  'woocommerce-memberships' ); ?></option>
								<option value="doesnt_include" <?php selected( 'doesnt_include', $row_comparator ); ?>><?php echo esc_html_x( "doesn't include", "Comparator: <value> doesn't include <something>", 'woocommerce-memberships' ); ?></option>
								<option value="is_empty" <?php selected( 'is_empty', $row_comparator ); ?>><?php echo esc_html_x( 'is empty', 'Comparator: <value> is empty', 'woocommerce-memberships' ); ?></option>
							<?php endif; ?>
						</select>

					<?php endif; ?>
				</div>

				<div class="wc-memberships-user-membership-advanced-filter-column">

					<?php $profile_field_definition = Profile_Fields::get_profile_field_definition( $row_slug ); ?>

					<input
						class="wc-memberships-filter-text-entry"
						type="text"
						<?php disabled( ! $row_data ); ?>
						placeholder="<?php esc_attr_e( 'Enter the profile field value', 'woocommerce-memberships' ); ?>"
						value="<?php echo ! is_array( $row_value ) || ( $profile_field_definition && ! $profile_field_definition->has_options( ) ) ? esc_attr( $row_value ) : ''; ?>"
					/>

					<div class="wc-memberships-filter-select-entry-wrapper">
						<select class="wc-memberships-filter-select-entry" <?php echo is_array( $row_value ) || ( $profile_field_definition && $profile_field_definition->has_options() ) ? 'multiple="multiple"' : ''; ?>>
							<?php if ( $profile_field_definition->is_type( Profile_Fields::TYPE_CHECKBOX ) ) : ?>

								<option value="yes" <?php selected( 'yes' === $row_value ); ?>><?php echo esc_html_x( 'selected', 'Checkbox field status', 'woocommerce-memberships' ); ?></option>
								<option value="no" <?php selected( 'no' === $row_value ); ?>><?php echo esc_html_x( 'unselected', 'Checkbox field status', 'woocommerce-memberships' ); ?></option>

							<?php elseif ( $profile_field_definition && $profile_field_definition->has_options() ) : ?>

								<?php foreach ( $profile_field_definition->get_options() as $option ) : ?>
									<option value="<?php echo esc_attr( $option ); ?>" <?php selected( in_array( $option, (array) $row_value, false ) ); ?>><?php echo esc_html( $option ); ?></option>
								<?php endforeach; ?>

							<?php endif; ?>
						</select>
					</div>

				</div>

				<div class="wc-memberships-user-membership-advanced-filter-column">
					<svg class="gridicon gridicons-cross-small wc-memberships-filter-remove-row" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M17.705 7.705l-1.41-1.41L12 10.59 7.705 6.295l-1.41 1.41L10.59 12l-4.295 4.295 1.41 1.41L12 13.41l4.295 4.295 1.41-1.41L13.41 12l4.295-4.295z"></path></g></svg>
				</div>

			</div>
		</li>
		<?php
	}


	/**
	 * Pauses a membership.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function pause_membership() {

		if ( empty( $_REQUEST['post'] ) ) {
			return;
		}

		// get the post
		$id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : '';

		check_admin_referer( 'wc-memberships-pause-membership-' . $id );

		$user_membership = wc_memberships_get_user_membership( $id );
		$user_membership->pause_membership();

		wp_redirect( add_query_arg( array('paused' => 1, 'ids' => $_REQUEST['post'] ), $this->get_sendback_url() ) );
		exit();
	}


	/**
	 * Resumes a membership.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function resume_membership() {

		if ( empty( $_REQUEST['post'] ) ) {
			return;
		}

		// get the post
		$id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : '';

		check_admin_referer( 'wc-memberships-resume-membership-' . $id );

		$user_membership = wc_memberships_get_user_membership( $id );

		$user_membership->activate_membership();

		wp_redirect( add_query_arg( array('resumed' => 1, 'ids' => $_REQUEST['post'] ), $this->get_sendback_url() ) );
		exit();
	}


	/**
	 * Cancels a membership.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function cancel_membership() {

		if ( empty( $_REQUEST['post'] ) ) {
			return;
		}

		// get the post
		$id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : '';

		check_admin_referer( 'wc-memberships-cancel-membership-' . $id );

		$user_membership = wc_memberships_get_user_membership( $id );
		$user_membership->cancel_membership();

		wp_redirect( add_query_arg( array('cancelled' => 1, 'ids' => $_REQUEST['post'] ), $this->get_sendback_url() ) );
		exit();
	}


	/**
	 * Returns the sendback URL.
	 *
	 * Mimics the WordPress core sendback url in wp-admin/post.php
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_sendback_url() {

		if ( isset( $_GET['post'] ) ) {
			$post_id = (int) $_GET['post'];
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = (int) $_POST['post_ID'];
		} else {
			$post_id = 0;
		}

		$post = $post_type = null;

		if ( $post_id ) {
			$post = get_post( $post_id );
		}

		if ( $post ) {
			$post_type = $post->post_type;
		}

		$sendback = wp_get_referer();

		if (    ! $sendback
		     ||   strpos( $sendback, 'post.php' )     !== false
		     ||   strpos( $sendback, 'post-new.php' ) !== false ) {

			$sendback = admin_url( 'edit.php' );
			$sendback .= ( ! empty( $post_type ) ) ? '?post_type=' . $post_type : '';

		} else {

			$sendback = remove_query_arg( array(
				'trashed',
				'untrashed',
				'deleted',
				'paused',
				'resumed',
				'cancelled',
				'updated',
				'ids'
			), $sendback );

		}

		return $sendback;
	}


	/**
	 * Displays custom admin notices.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function admin_notices() {
		global $pagenow;

		if ( 'edit.php' === $pagenow ) {

			$message = '';

			if ( isset( $_REQUEST['paused'] ) && (int) $_REQUEST['paused'] ) {
				/* translators: Placeholder: %s - number of memberships paused */
				$message = sprintf( _n( 'User membership paused.', '%s user memberships paused.', $_REQUEST['paused'] ), number_format_i18n( $_REQUEST['paused'] ), 'woocommerce-memberships' );
			}

			if ( isset( $_REQUEST['cancelled'] ) && (int) $_REQUEST['cancelled'] ) {
				/* translators: Placeholder: %s - number of memberships cancelled */
				$message = sprintf( _n( 'User membership cancelled.', '%s user memberships cancelled.', $_REQUEST['cancelled'] ), number_format_i18n( $_REQUEST['cancelled'] ), 'woocommerce-memberships' );
			}

			if ( isset( $_REQUEST['resumed'] ) && (int) $_REQUEST['resumed'] ) {
				/* translators: Placeholder: %s - number of memberships resumed */
				$message = sprintf( _n( 'User membership resumed.', '%s user memberships resumed.', $_REQUEST['resumed'] ), number_format_i18n( $_REQUEST['resumed'] ), 'woocommerce-memberships' );
			}

			if ( $message ) {
				echo "<div class='updated'><p>{$message}</p></div>";
			}

			if ( ! wc_memberships()->get_admin_notice_handler()->is_notice_dismissed( 'memberships-profile-fields-columns-screen-options-prompt' ) && Profile_Fields::get_profile_field_definitions() ) {

				wc_memberships()->get_admin_notice_handler()->add_admin_notice(
					sprintf(
						/** translators: Placeholders: %1$s - <strong> HTML tag, %2$s - </strong> HTML tag */
						__( 'Looking for your member profile fields? Click the %1$sScreen Options%2$s button at the top of this page to decide which profile fields should be visible on the members list.', 'woocommerce-memberships' ),
						'<strong>', '</strong>'
					),
					'memberships-profile-fields-columns-screen-options-prompt'
				);
			}
		}
	}


	/**
	 * Validates user membership data before saving.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param bool $maybe_empty checks if the post is empty
	 * @param array $postarr array of post data
	 * @return bool $maybe_empty
	 */
	public function validate_user_membership( $maybe_empty, $postarr ) {

		// bail out if not user membership
		if ( $postarr['post_type'] !== 'wc_user_membership' ) {
			return $maybe_empty;
		}

		// bail out if doing autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $maybe_empty;
		}

		// prevent saving memberships with no plan
		if ( ! $postarr['post_parent'] && isset( $_POST['post_ID'] ) ) {

			wc_memberships()->get_admin_instance()->get_message_handler()->add_error( __( 'Please select a membership plan.', 'woocommerce-memberships' ) );

			wp_redirect( wp_get_referer() );
			exit;
		}

		return $maybe_empty;
	}


	/**
	 * Prevents adding a user membership if user is already a member of all plans.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function maybe_prevent_adding_user_membership() {
		global $pagenow;

		if ( 'post-new.php' === $pagenow ) {

			// get user details
			$user_id = isset( $_GET['user'] ) ? $_GET['user'] : null;
			$user    = $user_id ? get_userdata( $user_id ) : null;

			if ( ! $user_id || ! $user ) {

				wc_memberships()->get_admin_instance()->get_message_handler()->add_error( __( 'Please select a user to add as a member.', 'woocommerce-memberships' ) );
				wp_redirect( wp_get_referer() ); exit;
			}

			// all the user memberships
			$user_memberships = wc_memberships_get_user_memberships( $user->ID );
			$membership_plans = wc_memberships_get_membership_plans( array(
				'post_status' => array( 'publish', 'private', 'future', 'draft', 'pending', 'trash' )
			) );

			if ( count( $user_memberships ) === count( $membership_plans ) ) {

				wc_memberships()->get_admin_instance()->get_message_handler()->add_message( __( 'This user is already a member of every plan.', 'woocommerce-memberships' ) );
				wp_redirect( wp_get_referer() ); exit;
			}
		}
	}


}
