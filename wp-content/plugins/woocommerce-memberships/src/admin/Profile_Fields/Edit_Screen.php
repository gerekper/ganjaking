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

namespace SkyVerge\WooCommerce\Memberships\Admin\Profile_Fields;

use SkyVerge\WooCommerce\Memberships\Admin\Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Admin\Views\Meta_Boxes\Profile_Field\Data;
use SkyVerge\WooCommerce\Memberships\Admin\Views\Meta_Boxes\Profile_Field\Publish;
use SkyVerge\WooCommerce\Memberships\Profile_Fields as Profile_Fields_Handler;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The profile fields admin edit screen handler.
 *
 * @since 1.19.0
 */
class Edit_Screen {


	/** @var Profile_Fields admin instance */
	private $profile_fields_admin_instance;


	/**
	 * The profile fields admin edit screen constructor.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Fields $profile_fields instance
	 */
	public function __construct( Profile_Fields $profile_fields ) {

		$this->profile_fields_admin_instance = $profile_fields;

		add_meta_box(
			'wc_memberships_profile_field_definition_data_meta_box',
			__( 'Profile Field Data', 'woocommerce-memberships' ),
			[ $this, 'render_data_meta_box' ],
			get_current_screen(),
			'normal',
			'high'
		);

		add_meta_box(
			'wc_memberships_profile_field_definition_publish_meta_box',
			__( 'Publish', 'woocommerce-memberships' ),
			[ $this, 'render_publish_meta_box' ],
			get_current_screen(),
			'side',
			'high'
		);
	}


	/**
	 * Renders the profile definition data meta box.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function render_data_meta_box() {

		require_once( wc_memberships()->get_plugin_path() . '/src/admin/Views/Meta_Boxes/Profile_Field/Data.php' );

		Data::create_and_render( $this->get_profile_fields_admin_handler()->get_admin_screen_profile_field_definition() ?: new Profile_Field_Definition() );
	}


	/**
	 * Renders the profile definition publish meta box.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function render_publish_meta_box() {

		require_once( wc_memberships()->get_plugin_path() . '/src/admin/Views/Meta_Boxes/Profile_Field/Publish.php' );

		Publish::create_and_render( $this->get_profile_fields_admin_handler()->get_admin_screen_profile_field_definition() ?: new Profile_Field_Definition() );
	}


	/**
	 * Gets the profile fields admin handler instance.
	 *
	 * @since 1.19.0
	 *
	 * @return Profile_Fields instance
	 */
	private function get_profile_fields_admin_handler() {

		return $this->profile_fields_admin_instance;
	}


	/**
	 * Outputs the screen HTML.
	 *
	 * @since 1.19.0
	 */
	public function render() {

		if ( $this->get_profile_fields_admin_handler()->is_delete_profile_field_definition_screen() ) {
			$this->delete_profile_field_definition();
		} elseif ( ! empty( $_POST ) && ( $this->get_profile_fields_admin_handler()->is_new_profile_field_definition_screen() || $this->get_profile_fields_admin_handler()->is_edit_profile_field_definition_screen() ) ) {
			$this->save_profile_field_definition();
		}

		$profile_field_definition = $this->get_profile_fields_admin_handler()->get_admin_screen_profile_field_definition() ?: new Profile_Field_Definition();

		$page_title = ! $profile_field_definition->is_new() ? __( 'Edit Profile Field', 'woocommerce-memberships' ) : __( 'Add New Profile Field', 'woocommerce-memberships' );

		?>
		<div class="wrap woocommerce wc-memberships-profile-fields">
			<form method="post" id="mainform" action="" enctype="multipart/form-data">

				<h1 class="wp-heading-inline"><?php echo esc_html( $page_title ); ?></h1>
				<a href="<?php echo esc_url( $this->get_profile_fields_admin_handler()->get_new_profile_field_definition_screen_url() ); ?>" class="page-title-action"><?php echo esc_html_x( 'Add profile field', 'Profile field page title action', 'woocommerce-memberships' ); ?></a>
				<hr class="wp-header-end">

				<?php wp_nonce_field( 'wc_memberships_profile_field_definitions_data' ); ?>

				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div id="titlediv">
								<div id="titlewrap">
									<label class="screen-reader-text" id="title-prompt-text" for="title"><?php esc_html_e( 'Profile field name', 'woocommerce-memberships' ); ?></label>
									<input
										type="text"
										id="title"
										name="name"
										value="<?php echo esc_attr( stripslashes( $profile_field_definition->get_name( 'edit' ) ) ); ?>"
										placeholder="<?php esc_attr_e( 'Profile field name', 'woocommerce-memberships' ); ?>"
										minlength="1"
										size="30"
										spellcheck="true"
										autocomplete="off"
									/>
									<input
										type="hidden"
										name="id"
										value="<?php echo esc_attr( $profile_field_definition->get_id() ); ?>"
									/>
								</div>
								<span class="show-if-no-name profile-field-validation-error" style="display:none;"><?php esc_html_e( 'Profile field names cannot be blank. Please enter a name for this profile field.', 'woocommerce-memberships' ); ?></span>
							</div>
						</div>

						<div id="postbox-container-1" class="postbox-container">
							<?php do_meta_boxes( 'admin_page_wc_memberships_profile_fields', 'side', $profile_field_definition ); ?>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<?php do_meta_boxes( 'admin_page_wc_memberships_profile_fields', 'normal', $profile_field_definition ); ?>
						</div>

					</div>
				</div>
			</form>
		</div>
		<?php
	}


	/**
	 * Saves the profile field definition upon edit form submission.
	 *
	 * @since 1.19.0
	 */
	private function save_profile_field_definition() {

		check_admin_referer( 'wc_memberships_profile_field_definitions_data' );

		try {

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'You do not have sufficient permissions to perform this action.', 'woocommerce-memberships' ) );
			}

			$profile_field_definition = $this->get_profile_fields_admin_handler()->get_admin_screen_profile_field_definition() ?: new Profile_Field_Definition();
			$default_data             = new Profile_Field_Definition();
			$posted_data              = wp_parse_args( $_POST, $default_data->get_data() );

			if ( empty( $posted_data['id'] ) ) {
				unset( $posted_data['id'] );
			}

			foreach ( $posted_data as $key => $value ) {

				// remove slashes recursively, but from string values only
				$value = stripslashes_deep( $value );

				// ensure values are arrays (Select2 may pass a string if single choice only is present)
				if ( in_array( $key, [ 'membership_plan_ids', 'visibility', 'options' ], true ) ) {
					$value = empty( $value ) ? [] : (array) $value;
				}

				// handle options data & default option
				if ( 'options' === $key && ! empty( $value ) && in_array( $posted_data['type'], [ \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_SELECT, \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_RADIO, \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_MULTISELECT, \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_MULTICHECKBOX ], true ) ) {

					$options        = [];
					$default_option = in_array( $posted_data['type'], [ \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_MULTISELECT, \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_MULTICHECKBOX ], true ) ? [] : '';

					foreach ( $value as $index => $option ) {

						if ( 'template' === $option || empty( trim( $option ) ) ) {
							continue;
						}

						$options[] = (string) $option;

						if ( is_array( $default_option ) && isset( $posted_data['default_options'] ) && is_array( $posted_data['default_options'] ) && array_key_exists( $index, $posted_data['default_options'] ) ) {
							$default_option[] = $option;
						} elseif ( is_string( $default_option ) && isset( $posted_data['default_option'] ) && (string) $index === (string) $posted_data['default_option'] ) {
							$default_option = $option;
						}
					}

					$profile_field_definition->set_options( $options );
					$profile_field_definition->set_default_value( $default_option );

					unset( $posted_data['default_value'] );

					continue;
				}

				if ( ! is_callable( [ $profile_field_definition, "set_{$key}" ] ) ) {
					continue;
				}

				$profile_field_definition->{"set_$key"}( $value );
			}

			$profile_field_definition->save();

			wc_memberships()->get_message_handler()->add_message( __( 'Profile field saved.', 'woocommerce-memberships' ) );

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			wc_memberships()->get_message_handler()->add_error( $exception->getMessage() );
		}

		if ( isset( $profile_field_definition ) && $profile_field_definition instanceof Profile_Field_Definition ) {

			if ( $profile_field_definition->is_new() ) {
				wp_safe_redirect( $this->get_profile_fields_admin_handler()->get_new_profile_field_definition_screen_url() );
			} else {
				wp_safe_redirect( $this->get_profile_fields_admin_handler()->get_edit_profile_field_definition_screen_url( $profile_field_definition ) );
			}

			exit;
		}
	}


	/**
	 * Deletes the profile field definition.
	 *
	 * Important note: this will also destroy all associated profile fields in user meta table!
	 * The edit screen should have already validated this choice, prompting the admin with a confirmation modal.
	 *
	 * @since 1.19.0
	 */
	private function delete_profile_field_definition() {
		global $wpdb;

		try {

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'You do not have permission to perform this action.', 'woocommerce-memberships' ) );
			}

			$profile_field_definition = $this->get_profile_fields_admin_handler()->get_admin_screen_profile_field_definition();

			if ( ! $profile_field_definition instanceof Profile_Field_Definition ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'The profile field could not be deleted.', 'woocommerce-memberships' ) );
			}

			check_admin_referer( 'delete_profile_field_definition_' . str_replace( '-', '_', $profile_field_definition->get_id() ), 'security' );

			$profile_field_slug = Profile_Fields_Handler::get_profile_field_user_meta_key( $profile_field_definition->get_slug( 'edit' ) );

			if ( empty( $profile_field_slug ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'The profile field could not be deleted.', 'woocommerce-memberships' ) );
			}

			$profile_field_definition->delete( true );

			$wpdb->delete(
				$wpdb->usermeta,
				[ 'meta_key' => $profile_field_slug ],
				[ '%s' ]
			);

			wc_memberships()->get_message_handler()->add_message( __( 'Profile field deleted.', 'woocommerce-memberships' ) );

		} catch ( \Exception $e ) {

			wc_memberships()->get_message_handler()->add_error( $e->getMessage() );

			if ( isset( $profile_field_definition ) && $profile_field_definition instanceof Profile_Field_Definition ) {
				wp_safe_redirect( $this->get_profile_fields_admin_handler()->get_edit_profile_field_definition_screen_url( $profile_field_definition ) );
				exit;
			}
		}

		wp_safe_redirect( $this->get_profile_fields_admin_handler()->get_profile_field_definitions_list_screen_url() );
		exit;
	}


}
