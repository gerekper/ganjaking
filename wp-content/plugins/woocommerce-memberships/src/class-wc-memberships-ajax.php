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
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\Memberships\Data_Stores;
use SkyVerge\WooCommerce\Memberships\Frontend\Profile_Fields as Profile_Fields_Frontend;
use SkyVerge\WooCommerce\Memberships\Helpers\Strings_Helper;
use SkyVerge\WooCommerce\Memberships\Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Memberships AJAX handler.
 *
 * @since 1.0.0
 */
class WC_Memberships_AJAX {


	/**
	 * Hooks in WordPress AJAX to add Memberships callbacks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// dismiss a noticed displayed to shop managers and admins when they browse restricted content
		add_action( 'wp_ajax_wc_memberships_dismiss_admin_restricted_content_notice', array( $this, 'dismiss_restricted_content_notice' ) );
		// dismiss notices (banners) displayed to shop managers, admins, or members
		add_action( 'wp_ajax_wc_memberships_dismiss_frontend_banner', [ $this, 'dismiss_frontend_banner' ] );

		// determine user membership start date by plan start date
		add_action( 'wp_ajax_wc_memberships_get_membership_plan_start_date', array( $this, 'get_membership_start_date' ) );
		// determine user membership expiration date by plan end date
		add_action( 'wp_ajax_wc_memberships_get_membership_plan_end_date',   array( $this, 'get_membership_expiration_date' ) );

		// user membership notes
		add_action( 'wp_ajax_wc_memberships_add_user_membership_note',    array( $this, 'add_user_membership_note' ) );
		add_action( 'wp_ajax_wc_memberships_delete_user_membership_note', array( $this, 'delete_user_membership_note' ) );

		// create a user to be added as member, when adding or transferring a user membership
		add_action( 'wp_ajax_wc_memberships_create_user_for_membership', array( $this, 'create_user_for_membership' ) );
		// transfer a membership from a user to another
		add_action( 'wp_ajax_wc_memberships_transfer_user_membership',   array( $this, 'transfer_user_membership' ) );

		// toggle editable status of member profile fields
		add_action( 'wp_ajax_wc_memberships_toggle_profile_field_editable_by', [ $this, 'toggle_member_profile_field_editable_by' ] );
		add_action( 'wp_ajax_wc_memberships_sort_profile_fields',              [ $this, 'sort_member_profile_fields' ] );
		add_action( 'wp_ajax_wc_memberships_save_profile_fields',              [ $this, 'save_member_profile_fields' ] );

		// handle profile field file form field uploads
		add_action( 'wp_ajax_wc_memberships_upload_profile_field_file',        [ $this, 'upload_member_profile_field_file' ] );
		add_action( 'wp_ajax_nopriv_wc_memberships_upload_profile_field_file', [ $this, 'upload_member_profile_field_file' ] );
		add_action( 'wp_ajax_wc_memberships_remove_profile_field_file',        [ $this, 'remove_member_profile_field_file' ] );
		add_action( 'wp_ajax_nopriv_wc_memberships_remove_profile_field_file', [ $this, 'remove_member_profile_field_file' ] );

		// enhanced select
		add_action( 'wp_ajax_wc_memberships_json_search_posts', array( $this, 'json_search_posts' ) );
		add_action( 'wp_ajax_wc_memberships_json_search_terms', array( $this, 'json_search_terms' ) );

		// filter out grouped products from WC JSON search results
		add_filter( 'woocommerce_json_search_found_products', array( $this, 'filter_json_search_found_products' ) );

		// batch jobs handling
		add_action( 'wp_ajax_wc_memberships_get_batch_job',                      array( $this, 'get_batch_job' ) );
		add_action( 'wp_ajax_wc_memberships_remove_batch_job',                   array( $this, 'remove_batch_job' ) );
		add_action( 'wp_ajax_wc_memberships_grant_retroactive_access',           array( $this, 'grant_retroactive_access' ) );
		add_action( 'wp_ajax_wc_memberships_reschedule_user_memberships_events', array( $this, 'reschedule_user_memberships_events' ) );
		add_action( 'wp_ajax_wc_memberships_export_user_memberships',            array( $this, 'export_user_memberships' ) );
		add_action( 'wp_ajax_wc_memberships_import_user_memberships',            array( $this, 'import_user_memberships' ) );

		// fetch product variation profile fields html
		add_action( 'wp_ajax_wc_memberships_get_variation_profile_fields', [ $this, 'get_product_profile_fields' ] );
		add_action( 'wp_ajax_nopriv_wc_memberships_get_variation_profile_fields', [ $this, 'get_product_profile_fields' ] );
	}


	/**
	 * Gets profile fields HTML for a product that grants access to a plan.
	 *
	 * @internal
	 *
	 * @since 1.21.4
	 */
	public function get_product_profile_fields() {

		/* @var Profile_Fields_Frontend $profile_fields_frontend instance */
		if ( ! class_exists( Profile_Fields_Frontend::class, false ) ) {
			$profile_fields_frontend = wc_memberships()->load_class( '/src/frontend/Profile_Fields.php', Profile_Fields_Frontend::class );
		} elseif ( $frontend = wc_memberships()->get_frontend_instance() ) {
			$profile_fields_frontend = $frontend->get_profile_fields_instance();
		} else {
			wp_send_json_error( 'Could not load profile fields front end instance.' );
		}

		check_ajax_referer( Profile_Fields_Frontend::GET_PRODUCT_PROFILE_FIELDS_ACTION, 'security' );

		$product_id = (int) filter_input( INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT );

		if ( 0 === $product_id || ! $profile_fields_frontend ) {
			wp_send_json_error( 'Could not determine product or variation to get profile fields for.' );
		}

		ob_start();

		$profile_fields_frontend->add_product_page_profile_fields( $product_id );

		wp_send_json_success( ob_get_clean() );
	}


	/**
	 * Flags the current admin user to no longer view restricted content notices.
	 *
	 * @internal
	 *
	 * @since 1.10.4
	 */
	public function dismiss_restricted_content_notice() {

		if ( current_user_can( 'wc_memberships_access_all_restricted_content' ) ) {
			wp_send_json_success( update_user_meta( get_current_user_id(), '_wc_memberships_show_admin_restricted_content_notice', 'no' ) );
		}

		wp_send_json_error();
	}


	/**
	 * Dismiss the specified frontend banner to make sure the current user don't see that notice again.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function dismiss_frontend_banner() {

		$message_id = Framework\SV_WC_Helper::get_posted_value( 'message_id' );
		$user_id    = get_current_user_id();

		if ( $message_id && $user_id ) {
			wp_send_json_success( update_user_meta( $user_id, sprintf( '_wc_memberships_show_%s_notice', str_replace( '-', '_', $message_id ) ), 'no' ) );
		}

		wp_send_json_error();
	}


	/**
	 * Returns a user membership date based on plan details.
	 *
	 * @since 1.7.0
	 *
	 * @param string $which_date either 'start' or 'end' date
	 */
	private function get_membership_date( $which_date ) {

		check_ajax_referer( 'get-membership-date', 'security' );

		if ( isset( $_POST['plan'] ) ) {

			$plan_id = (int) $_POST['plan'];

			if ( $plan  = wc_memberships_get_membership_plan( $plan_id ) ) {

				$date = null;

				if ( 'start' === $which_date ) {

					$date = $plan->get_local_access_start_date();

				} elseif ( 'end' === $which_date ) {

					$start_date     = ! empty( $_POST['start_date'] ) ? strtotime( $_POST['start_date'] ) : current_time( 'timestamp', true );
					$start_date_utc = wc_memberships_adjust_date_by_timezone( $start_date );

					$date = $plan->get_expiration_date( $start_date_utc );
				}

				if ( null !== $date ) {

					// might send a date or empty string
					wp_send_json_success( $date );
				}
			}
		}

		die();
	}


	/**
	 * Determines the user membership start date based on a plan start date.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 */
	public function get_membership_start_date() {

		$this->get_membership_date( 'start' );
	}


	/**
	 * Returns a membership expiration date.
	 *
	 * @internal
	 *
	 * @since 1.3.8
	 */
	public function get_membership_expiration_date() {

		$this->get_membership_date( 'end' );
	}


	/**
	 * Searches for posts and echoes JSON data.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function json_search_posts() {

		check_ajax_referer( 'search-posts', 'security' );

		$term      = (string) wc_clean( stripslashes( Framework\SV_WC_Helper::get_requested_value( 'term' ) ) );
		$post_type = (string) wc_clean( Framework\SV_WC_Helper::get_requested_value( 'post_type' ) );

		if ( empty( $term ) || empty( $post_type ) ) {
			die();
		}

		if ( is_numeric( $term ) ) {

			$args = array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__in'       => array( 0, $term ),
				'fields'         => 'ids'
			);

		} else {

			$args = array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				's'              => $term,
				'fields'         => 'ids'
			);

		}

		$post_ids = get_posts( $args );

		$found_posts = array();

		if ( $post_ids ) {
			foreach ( $post_ids as $post_id ) {
				$found_posts[ $post_id ] = sprintf( '%1$s (#%2$s)', get_the_title( $post_id ), $post_id );
			}
		}

		/**
		 * Filters posts found for JSON (AJAX) search.
		 *
		 * @since 1.0.0
		 *
		 * @param array $found_posts associative array of the found posts
		 */
		$found_posts = apply_filters( 'wc_memberships_json_search_found_posts', $found_posts );

		wp_send_json( $found_posts );
	}


	/**
	 * Searches for taxonomy terms and echoes JSON data.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function json_search_terms() {

		check_ajax_referer( 'search-terms', 'security' );

		$term     = (string) wc_clean( stripslashes( Framework\SV_WC_Helper::get_requested_value( 'term' ) ) );
		$taxonomy = (string) wc_clean( Framework\SV_WC_Helper::get_requested_value( 'taxonomy' ) );

		if ( empty( $term ) || empty( $taxonomy ) ) {
			die();
		}

		if ( is_numeric( $term ) ) {

			$args = array(
				'hide_empty' => false,
				'include'    => array( 0, $term ),
			);

		} else {

			$args = array(
				'hide_empty' => false,
				'search'     => $term,
			);
		}

		$terms = get_terms( array( $taxonomy ), $args );

		$found_terms = array();

		if ( is_array( $terms ) ) {

			foreach ( $terms as $term ) {

				$found_terms[ $term->term_id ] = sprintf( '%1$s (#%2$s)', $term->name, $term->term_id );
			}
		}

		/**
		 * Filters taxonomy terms found for JSON (AJAX) search.
		 *
		 * @since 1.0.0
		 *
		 * @param array $found_terms associative array of the found terms
		 */
		$found_terms = apply_filters( 'wc_memberships_json_search_found_terms', $found_terms );

		wp_send_json( $found_terms );
	}


	/**
	 * Adds a user membership note.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_user_membership_note() {

		check_ajax_referer( 'add-user-membership-note', 'security' );

		$post_id   = (int) $_POST['post_id'];
		$note_text = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );
		$notify    = isset( $_POST['notify'] ) && $_POST['notify'] === 'true';

		if ( $post_id > 0 ) {

			// load views abstract
			require_once( wc_memberships()->get_plugin_path() . '/src/admin/meta-boxes/views/abstract-wc-memberships-meta-box-view.php' );

			// load views
			require( wc_memberships()->get_plugin_path() . '/src/admin/meta-boxes/views/class-wc-memberships-meta-box-view-membership-note.php' );
			require( wc_memberships()->get_plugin_path() . '/src/admin/meta-boxes/views/class-wc-memberships-meta-box-view-membership-recent-activity-note.php' );

			$new_note_view            = new \WC_Memberships_Meta_Box_View_Membership_Note();
			$new_recent_activity_view = new \WC_Memberships_Meta_Box_View_Membership_Recent_Activity_Note();

			// get variables to pass to templates
			$user_membership = wc_memberships_get_user_membership( $post_id );
			$comment_id      = $user_membership->add_note( $note_text, $notify );
			$note            = get_comment( $comment_id );
			$note_classes    = get_comment_meta( $note->comment_ID, 'notified', true ) ? array( 'notified', 'note' ) : array( 'note' );

			$args = array(
				'note'         => $note,
				'note_classes' => $note_classes,
				'plan'         => $user_membership->get_plan(),
			);

			?>
			<div>
				<ul id="notes">
					<?php $new_note_view->output( $args ); ?>
				</ul>
				<ul id="recent-activity">
					<?php $new_recent_activity_view->output( $args ); ?>
				</ul>
			</div>
			<?php
		}

		exit;
	}


	/**
	 * Deletes a user membership note.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function delete_user_membership_note() {

		check_ajax_referer( 'delete-user-membership-note', 'security' );

		$note_id = (int) $_POST['note_id'];

		if ( $note_id > 0 ) {
			wp_delete_comment( $note_id );
		}

		exit;
	}


	/**
	 * Removes grouped products from JSON search results.
	 *
	 * Memberships is not compatible with Grouped products.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $products
	 * @return array $products
	 */
	public function filter_json_search_found_products( $products ) {

		// Remove grouped products
		if ( isset( $_REQUEST['screen'] ) && 'wc_membership_plan' === $_REQUEST['screen'] ) {
			foreach( $products as $id => $title ) {

				$product = wc_get_product( $id );

				if ( $product->is_type('grouped') ) {
					unset( $products[ $id ] );
				}
			}
		}

		return $products;
	}


	/**
	 * Creates a user while adding or transferring a user membership.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function create_user_for_membership() {

		check_ajax_referer( 'create-user-for-membership', 'security' );

		$username   = isset( $_POST['username']   ) ? trim( $_POST['username']   ) : '';
		$email      = isset( $_POST['email']      ) ? trim( $_POST['email']      ) : '';
		$first_name = isset( $_POST['first_name'] ) ? trim( $_POST['first_name'] ) : '';
		$last_name  = isset( $_POST['last_name']  ) ? trim( $_POST['last_name']  ) : '';
		$password   = isset( $_POST['password']   ) ? $_POST['password']           : '';
		$user_id    = wc_create_new_customer( $email, $username, $password );

		if ( ! is_numeric( $user_id ) ) {

			$error_message  = '';
			$error_messages = $user_id instanceof \WP_Error ? $user_id->get_error_messages() : null;

			if ( ! empty( $error_messages ) ) {

				// note: the following textdomain is not incorrect, this is to rectify a WC core message which would be unfit for the admin context here
				$login_message = __( 'An account is already registered with your email address. Please log in.', 'woocommerce' );

				foreach ( $error_messages as $message ) {
					if ( $login_message === $message ) {
						$error_message .= __( 'An account is already registered with this email address.', 'woocommerce-memberships' ) . '<br />';
					} else {
						$error_message .= $message . '<br />';
					}
				}

			} else {

				$error_message .= __( 'Please ensure you have entered valid user information.', 'woocommerce-memberships' );
			}

			wp_send_json_error( $error_message );

		} elseif ( $user_id > 0 ) {

			$user_full_name = array();

			if ( '' !== $first_name ) {
				$user_full_name['first_name'] = $first_name;
			}

			if ( '' !== $last_name ) {
				$user_full_name['last_name'] = $last_name;
			}

			if ( ! empty( $user_full_name ) ) {

				$user_full_name['ID'] = $user_id;

				wp_update_user( $user_full_name );
			}
		}

		wp_send_json_success( (int) $user_id );
	}


	/**
	 * Transfers a membership from one user to another.
	 *
	 * If successful also stores the previous users history in a membership post meta '_previous_owners'.
	 *
	 * @internal
	 *
	 * @since 1.4.0
	 */
	public function transfer_user_membership() {

		check_ajax_referer( 'transfer-user-membership', 'security' );

		if ( isset( $_POST['prev_user'], $_POST['new_user'] ) && ! empty( $_POST['membership'] ) ) {

			$prev_user          = (int) $_POST['prev_user'];
			$new_user           = (int) $_POST['new_user'];
			$user_membership_id = (int) $_POST['membership'];
			$user_membership    = wc_memberships_get_user_membership( $user_membership_id );

			if ( $user_membership && $user_membership->get_user_id() === $prev_user ) {

				try {

					if ( $user_membership->transfer_ownership( $new_user ) ) {
						wp_send_json_success( $user_membership->get_previous_owners() );
					}

				} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

					wp_send_json_error( $exception->getMessage() );
				}
			}
		}

		wp_send_json_error( __( 'An error occurred.', 'woocommerce-memberships' ) );
	}


	/**
	 * Toggles the "editable by" status of a profile field definition.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function toggle_member_profile_field_editable_by() {

		check_ajax_referer( 'toggle-profile-field-editable-by', 'security' );

		$profile_field_definition = Profile_Fields::get_profile_field_definition( isset( $_POST['profile_field'] ) ? $_POST['profile_field'] : '' );

		if ( ! $profile_field_definition ) {
			wp_send_json_error( 'Profile field definition invalid or not found.' );
		}

		if ( $profile_field_definition->is_editable_by( Profile_Field_Definition::EDITABLE_BY_ADMIN ) ) {
			$profile_field_definition->set_editable_by( Profile_Field_Definition::EDITABLE_BY_CUSTOMER );
		} else {
			$profile_field_definition->set_editable_by( Profile_Field_Definition::EDITABLE_BY_ADMIN );
		}

		try {

			$editable_by = $profile_field_definition->get_editable_by();

			// if setting to editable by customer, the field needs to be visible at least in one area for customers to edit
			if ( Profile_Field_Definition::EDITABLE_BY_CUSTOMER === $editable_by && empty( $profile_field_definition->get_visibility() ) ) {
				$profile_field_definition->set_visibility( [ Profile_Fields::VISIBILITY_PROFILE_FIELDS_AREA ] );
			}

			$profile_field_definition->save();

			wp_send_json_success( [
				'editable_by' => $editable_by,
				'visibility'  => Profile_Field_Definition::EDITABLE_BY_CUSTOMER === $editable_by ? $profile_field_definition->get_visibility() : [],
			] );

		} catch ( \Exception $e ) {

			wp_send_json_error( $e->getMessage() );
		}
	}


	/**
	 * Sorts profile field definitions.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function sort_member_profile_fields() {

		check_ajax_referer( 'sort-profile-fields', 'security' );

		$profile_fields = Framework\SV_WC_Helper::get_posted_value( 'profile_fields', [] );

		if ( empty( $profile_fields ) ) {
			wp_send_json_error();
		}

		$profile_field_definitions_store = new Data_Stores\Profile_Field_Definition\Option();
		$profile_field_definitions_store->sort( array_map( 'strval', (array) $profile_fields ) );

		wp_send_json_success();
	}


	/**
	 * Updates member profile fields.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function save_member_profile_fields() {

		check_ajax_referer( 'save-profile-fields', 'security' );

		$profile_fields     = Framework\SV_WC_Helper::get_posted_value( 'profile_fields', [] );
		$user_membership_id = Framework\SV_WC_Helper::get_posted_value( 'user_membership_id', 0 );
		$user_membership    = $user_membership_id > 0 ? wc_memberships_get_user_membership( $user_membership_id ) : null;

		if ( ! $user_membership || empty( $profile_fields ) || ! is_array( $profile_fields ) ) {
			wp_send_json_error();
		}

		$plan_profile_field_definitions = Profile_Fields::get_profile_field_definitions( [ 'membership_plan_ids' => [ $user_membership->get_plan_id() ] ] );
		$profile_fields_data            = $errors = $updated = [];

		foreach ( $profile_fields as $profile_field ) {

			if ( empty( $profile_field['slug'] ) || ! isset( $profile_field['value'] ) ) {
				continue;
			}

			// turn value into an array for fields with multiple values
			if ( isset( $profile_fields_data[ $profile_field['slug'] ] ) ) {
				$profile_fields_data[ $profile_field['slug'] ] = array_merge( (array) $profile_fields_data[ $profile_field['slug'] ], [ $profile_field['value'] ] );
			} else {
				$profile_fields_data[ $profile_field['slug'] ] = $profile_field['value'];
			}

			unset( $plan_profile_field_definitions[ $profile_field['slug'] ] );
		}

		// ensures that profile fields without a value sent in AJAX are overwritten with an empty value still
		foreach ( $plan_profile_field_definitions as $profile_field_definition ) {
			$profile_fields_data[ $profile_field_definition->get_slug() ] = $profile_field_definition->has_options() ? [] : '';
		}

		foreach ( $profile_fields_data as $slug => $value ) {

			if ( ! Profile_Fields::is_profile_field_slug( $slug ) ) {
				continue;
			}

			$profile_field = $user_membership->get_profile_field( $slug ) ?: new Profile_Fields\Profile_Field();
			$value         = is_string( $value ) ? trim( $value ) : $value;

			try {

				$profile_field->set_user_id( $user_membership->get_user_id() );
				$profile_field->set_slug( $slug );

				// single checkbox fields (TYPE_CHECKBOX) may arrive here as an array with its only option while it expects a string
				if ( $profile_field->get_definition()->is_type( Profile_Fields::TYPE_CHECKBOX ) && is_array( $value ) && isset( $value[0] ) ) {
					$value = $value[0];
				}

				$profile_field->set_value( $value );
				$profile_field->save();

				$update        = new \stdClass();
				$update->slug  = $profile_field->get_slug();
				$update->value = $profile_field->get_value();

				if ( $profile_field->get_definition()->is_type( Profile_Fields::TYPE_FILE ) ) {
					$media_library_url = $update->value > 0 ? get_edit_post_link( $update->value ) : '';
					$formatted_value   = $update->value > 0 && '' !== $media_library_url ? sprintf( '<a href="%s">%s</a>', esc_url( $media_library_url ), basename( get_attached_file( $update->value ) ) ) : '';
				} else {
					$formatted_value   = esc_html( $profile_field->get_formatted_value() );
				}

				$update->formatted_value = '' !== trim( $formatted_value ) ? $formatted_value : '&mdash;';

				$updated[] = $update;

			} catch ( \Exception $e ) {

				$error = new \stdClass();
				$error->slug  = $slug;
				$error->error = $e->getMessage();

				$errors[] = $error;
			}
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( [ 'errors' => $errors ] );
		}

		wp_send_json_success( [ 'updated' => $updated ] );
	}


	/**
	 * Handles a file profile field upload from front end.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function upload_member_profile_field_file() {

		check_ajax_referer( 'member-profile-field-upload-file', 'security' );

		$slug = Framework\SV_WC_Helper::get_posted_value( 'profile_field', '' );

		// bail if no files were posted or file upload errors occurred
		if ( empty( $_FILES ) || ( isset( $_FILES['file']['error'] ) && $_FILES['file']['error'] ) ) {
			return false;
		}

		// bail if invalid profile field was posted
		if ( ! Profile_Fields::is_profile_field_slug( $slug ) ) {
			return false;
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
		}

		$file_info = pathinfo( $_FILES['file']['name'] );

		// ensure names are unique in sessions
		$_FILES['file']['name'] = uniqid( $file_info['filename'] . '-', false ) . '.' . $file_info['extension'];

		$attachment_id = media_handle_upload( 'file', 0 );

		if ( is_wp_error( $attachment_id ) ) {

			echo json_encode( $attachment_id );

		} else {

			// store the file in session
			Profile_Fields::store_uploaded_profile_field_file_in_session( $slug, $attachment_id );

			echo json_encode( [
				'id'       => $attachment_id,
				'title'    => get_the_title( $attachment_id ) . '.' . strtolower( $file_info['extension'] ),
				'url'      => wp_get_attachment_url( $attachment_id ),
				'security' => wp_create_nonce( 'woocommerce-register' ),
			] );
		}

		exit;
	}


	/**
	 * Handles the removal of an uploaded profile field file from front end.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function remove_member_profile_field_file() {

		check_ajax_referer( 'member-profile-field-remove-file', 'security' );

		$slug = Framework\SV_WC_Helper::get_posted_value( 'profile_field', '' );
		$file = Framework\SV_WC_Helper::get_posted_value( 'file', '' );

		// bail if no file identifier was posted
		if ( empty( $file ) ) {
			wp_send_json_error( 'Missing file information.' );
		}

		// bail if no matching profile field was posted
		if ( ! Profile_Fields::is_profile_field_slug( $slug ) ) {
			wp_send_json_error( 'Invalid profile field slug' );
		}

		// delete the file from session (security check: prevents deleting somebody's else file)
		if ( Profile_Fields::remove_uploaded_profile_field_file_from_session( $slug, $file ) ) {

			// delete the attachment
			wp_delete_attachment( $file, true );

			wp_send_json_success( $file );
		}

		wp_send_json_error( 'Could not match requested file to delete from user files in session.' );
	}


	/**
	 * Fetches a batch job object.
	 *
	 * It will send null if the object wasn't found, which isn't necessarily an error.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function get_batch_job() {

		check_ajax_referer( 'get-memberships-batch-job', 'security' );

		$error = esc_html__( 'Must specify a valid job process name and job ID.', 'woocommerce-memberships' );

		if ( isset( $_POST['job_name'], $_POST['job_id'] ) && is_string( $_POST['job_name'] ) && ( is_string( $_POST['job_id'] ) || is_numeric( $_POST['job_id'] ) ) ) {

			$job_name = trim( $_POST['job_name'] );
			$job_id   = is_numeric( $_POST['job_id'] ) ? (int) $_POST['job_id'] : trim( $_POST['job_id'] );
			$job      = wc_memberships()->get_utilities_instance()->get_job_object( $job_name, $job_id );

			// something went wrong
			if ( false === $job ) {

				/* translators: Placeholder: %s - a background job task run by Memberships */
				$error = sprintf( esc_html__( 'Unknown job process "%s".', 'woocommerce-memberships' ), $job_name );

			// either the job exists (object) or is null
			} else {

				// perhaps process one batch too
				if ( $job && ! empty( $_POST['process'] ) ) {

					if ( $handler = wc_memberships()->get_utilities_instance()->get_job_handler( $job_name ) ) {

						try {

							wp_send_json_success( (array) $handler->process_job( $job ) );

						} catch ( \Exception $e ) {

							$error = $e->getMessage();
						}
					}

				} else {

					wp_send_json_success( $job ? (array) $job : null );
				}
			}
		}

		wp_send_json_error( $error );
	}


	/**
	 * Stops and deletes a batch job.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function remove_batch_job() {

		check_ajax_referer( 'remove-memberships-batch-job', 'security' );

		$error    = esc_html__( 'Must specify a valid job process name and job ID.', 'woocommerce-memberships' );
		$job_name = ! empty( $_POST['job_name'] ) && is_string( $_POST['job_name'] ) ? trim( $_POST['job_name'] ) : null;
		$job_id   = isset( $_POST['job_id'] ) && ( null === $_POST['job_id'] || is_string( $_POST['job_id'] ) || is_numeric( $_POST['job_id'] ) ) ? $_POST['job_id'] : false;

		if ( null !== $job_name && false !== $job_id ) {

			/* translators: Placeholder: %s - a batch job task name run by Memberships */
			$error    = sprintf( esc_html__( 'Could not find Memberships job handler for "%s".', 'woocommerce-memberships' ), $job_name );
			$handler  = wc_memberships()->get_utilities_instance()->get_job_handler( $job_name );

			if ( $handler ) {

				/* translators: Placeholder: %s - a batch job task name run by Memberships */
				$error = sprintf( esc_html__( 'Unknown job process "%s".', 'woocommerce-memberships' ), $job_name );
				$job   = wc_memberships()->get_utilities_instance()->get_job_object( $job_name, $job_id );

				if ( false !== $job ) {

					/* translators: Placeholder: %s - batch job ID */
					$error   = sprintf( esc_html__( 'Could not remove batch job "%s".', 'woocommerce-memberships' ), $job_id );
					$deleted = $handler->delete_job( $job );

					if ( false !== $deleted ) {
						wp_send_json_success( $job );
					}
				}
			}
		}

		wp_send_json_error( $error );
	}


	/**
	 * Grants access retroactively to users that meet a membership plan's access conditions.
	 *
	 * Creates a new batch job.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function grant_retroactive_access() {

		check_ajax_referer( 'grant-retroactive-access', 'security' );

		$error = esc_html__( 'Must specify a valid Membership Plan ID.', 'woocommerce-memberships' );

		// process grant access action for the given plan
		if ( isset( $_POST['plan_id'] ) && is_numeric( $_POST['plan_id'] ) ) {

			/* translators: Placeholder: %s Membership PLan ID */
			$error = sprintf( esc_html__( 'Could not get a valid published plan with ID %s.', 'woocommerce-memberships' ), (int) $_POST['plan_id'] );
			$plan  = wc_memberships_get_membership_plan( $_POST['plan_id'] );

			if ( $plan && 'publish' === $plan->post->post_status ) {

				wp_send_json_success( (array) wc_memberships()->get_utilities_instance()->get_grant_retroactive_access_instance()->create_job( array(
					'membership_plan_id' => $plan->get_id(),
					'user_ids'           => $plan->is_access_method( 'purchase' ) ? $this->get_users_for_retroactive_access( $plan->get_product_ids() ) : get_users( array( 'fields' => 'ID' ) ),
				) ) );
			}
		}

		wp_send_json_error( $error );
	}


	/**
	 * Returns users IDs from orders that contain products that could grant access to a given plan.
	 *
	 * TODO When WooCommerce starts using alternate data stores for products (perhaps from WC 3.5+) this method may require an update as it performs a direct SQL query assuming a standard WPDB data organization {FN 2018-07-23}
	 *
	 * @since 1.10.6
	 *
	 * @param int[] $access_product_ids array of product IDs that grant access to a plan upon purchase
	 * @return int[] array of user IDs
	 */
	private function get_users_for_retroactive_access( array $access_product_ids ) {
		global $wpdb;

		if ( ! empty( $access_product_ids ) ) {

			// get orders that contain an access granting product (or variation) to the given plan
			$product_ids = Strings_Helper::esc_sql_in_ids( $access_product_ids );
			$order_ids   = $wpdb->get_col(  "
				SELECT DISTINCT posts.ID
				FROM {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta,
				     {$wpdb->prefix}woocommerce_order_items AS order_items,
				     {$wpdb->prefix}posts AS posts
				WHERE order_items.order_item_id = order_item_meta.order_item_id
				AND order_items.order_id = posts.ID
				AND ( ( order_item_meta.meta_key LIKE '_product_id'   AND order_item_meta.meta_value IN ({$product_ids}) )
				 OR   ( order_item_meta.meta_key LIKE '_variation_id' AND order_item_meta.meta_value IN ({$product_ids}) ) )
			" );

			if ( ! empty( $order_ids ) ) {

				// get user IDs for the found orders
				$order_ids = Strings_Helper::esc_sql_in_ids( $order_ids );
				$user_ids  = $wpdb->get_col( "
					SELECT posts_meta.meta_value
					FROM {$wpdb->prefix}postmeta AS posts_meta
					WHERE posts_meta.post_id IN ({$order_ids})
					AND posts_meta.meta_key = '_customer_user'
				" );
			}
		}

		return ! empty( $user_ids ) ? array_unique( array_map( 'absint', array_values( $user_ids ) ) ) : [];
	}


	/**
	 * Reschedules user memberships events in background if there's a schedule change.
	 *
	 * Creates a new batch job.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function reschedule_user_memberships_events() {

		check_ajax_referer( 'reschedule-user-memberships-events', 'security' );

		$membership_plans   = wc_memberships_get_membership_plans();
		$user_memberships   = array();

		// gather user memberships from each plan...
		if ( ! empty( $membership_plans ) ) {

			foreach ( $membership_plans as $plan ) {
				$user_memberships[] = $plan->get_memberships( array( 'fields' => 'ids' ) );
			}

			$user_memberships = call_user_func_array( 'array_merge', $user_memberships );
		}

		$error = esc_html__( 'Could not start rescheduling user memberships events.', 'woocommerce-memberships' );

		if ( $job = wc_memberships()->get_utilities_instance()->get_user_memberships_reschedule_events_instance()->create_job( array( 'user_membership_ids' => $user_memberships ) ) ) {
			wp_send_json_success( (array) $job );
		}

		wp_send_json_error( $error );
	}


	/**
	 * Exports user memberships.
	 *
	 * Creates a new batch job.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function export_user_memberships() {

		check_ajax_referer( 'export-user-memberships', 'security' );

		$error_message  = esc_html__( 'Invalid or missing export parameters.', 'woocommerce-memberships' );
		$export_args    = isset( $_POST['export_params'] ) && is_array( $_POST['export_params'] ) ? $_POST['export_params'] : null;
		$export_handler = wc_memberships()->get_utilities_instance()->get_user_memberships_export_instance();

		if ( $export_handler && ! empty( $export_args ) ) {

			try {

				wp_send_json_success( (array) $export_handler->create_job( [
					'user_membership_ids'    => empty( $_POST['user_membership_ids'] ) ? $export_handler->get_user_memberships_ids_for_export( $export_args ) : $_POST['user_membership_ids'],
					'include_profile_fields' => isset( $export_args['include_profile_fields'] ) && 'yes' === $export_args['include_profile_fields'],
					'include_meta_data'      => isset( $export_args['include_meta'] ) && 'yes' === $export_args['include_meta'],
					'fields_delimiter'       => ! empty( $export_args['fields_delimiter'] ) ? $export_args['fields_delimiter'] : 'comma',
				] ) );

			} catch ( Framework\SV_WC_Plugin_Exception $e ) {

				$error_message = $e->getMessage();
			}
		}

		/* translators: Placeholder: %s - error message */
		wp_send_json_error( sprintf( esc_html__( 'An error occurred while starting the export process. %s', 'woocommerce-memberships' ), $error_message ) );
	}


	/**
	 * Import user memberships.
	 *
	 * Creates a new batch job.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function import_user_memberships() {

		check_ajax_referer( 'import-user-memberships', 'security' );

		$error_message =  esc_html__( 'You need to provide a valid CSV file to import memberships from.', 'woocommerce-memberships' );

		if ( ! empty( $_FILES ) && isset( $_FILES['file']['name'], $_FILES['file']['size'], $_FILES['file']['tmp_name'] ) ) {

			$import_handler = wc_memberships()->get_utilities_instance()->get_user_memberships_import_instance();

			try {

				wp_send_json_success( (array) $import_handler->create_job( array(
					'file'                       => $_FILES['file'],
					'create_new_memberships'     => isset( $_POST['create_new_memberships'] )     ? 'no'  !== $_POST['create_new_memberships']     : true,
					'merge_existing_memberships' => isset( $_POST['merge_existing_memberships'] ) ? 'no'  !== $_POST['merge_existing_memberships'] : true,
					'allow_memberships_transfer' => isset( $_POST['allow_memberships_transfer'] ) ? 'yes' === $_POST['allow_memberships_transfer'] : false,
					'create_new_users'           => isset( $_POST['create_new_users'] )           ? 'yes' === $_POST['create_new_users']           : false,
					'notify_new_users'           => isset( $_POST['notify_new_users'] )           ? 'yes' === $_POST['notify_new_users']           : false,
					'timezone'                   => ! empty( $_POST['timezone'] )                 ? $_POST['timezone']                             : wc_timezone_string(),
					'default_start_date'         => ! empty( $_POST['default_start_date'] )       ? $_POST['default_start_date']                   : date( 'Y-m-d', current_time( 'timestamp' ) ),
					'fields_delimiter'           => ! empty( $_POST['fields_delimiter'] )         ? $_POST['fields_delimiter']                     : 'comma',
				) ) );

			} catch ( Framework\SV_WC_Plugin_Exception $e ) {

				$error_message = $e->getMessage();
			}
		}

		/* translators: Placeholder: %s - optional error message */
		wp_send_json_error( sprintf( esc_html__( 'An error occurred while starting the import process. %s', 'woocommerce-memberships' ), $error_message ) );
	}


}
