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

use SkyVerge\WooCommerce\Memberships\Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The profile fields admin list screen table object.
 *
 * @since 1.19.0
 */
class List_Table extends \WP_List_Table {


	/** @var null|string the current filter to show only profile fields of a certain type */
	private $field_type_filter;

	/** @var null|string the current filter to show only profile fields editable by a type of user */
	private $editable_by_filter;


	/**
	 * Gets a list of profile field definition objects.
	 *
	 * @since 1.19.0
	 *
	 * @return Profile_Field_Definition[]
	 */
	private function get_profile_field_definitions() {

		$profile_field_definitions = Profile_Fields::get_profile_field_definitions();
		$filter_by_type            = $this->get_profile_field_definitions_type_filter();
		$filter_by_editable_by     = $this->get_profile_field_definitions_editable_by_filter();

		foreach ( $profile_field_definitions as $profile_field_slug => $profile_field_definition ) {

			if ( ! empty( $filter_by_type ) && ! $profile_field_definition->is_type( $_GET['type'] ) ) {
				unset( $profile_field_definitions[ $profile_field_slug ] );
				continue;
			}

			if ( ! empty( $filter_by_editable_by ) && ! $profile_field_definition->is_editable_by( $filter_by_editable_by ) ) {
				unset( $profile_field_definitions[ $profile_field_slug ] );
				continue;
			}
		}

		return $profile_field_definitions;
	}


	/**
	 * Gets the list of columns to display
	 *
	 * @since 1.19.0
	 *
	 * @return array associative array of column identifiers and their labels
	 */
	public function get_columns() {

		return [
			'cb'          => '',
			'name'        => _x( 'Name', 'Profile field name', 'woocommerce-memberships' ),
			'type'        => _x( 'Type', 'Profile field type', 'woocommerce-memberships' ),
			'editable_by' => _x( 'Member Editing', 'Profile field editable by', 'woocommerce-memberships' ),
			'visibility'  => _x( 'Show On', 'Profile field visibility', 'woocommerce-memberships' ),
			'sort_handle' => '',
		];
	}


	/**
	 * Prepares the list of items for displaying.
	 *
	 * Implements parent method.
	 *
	 * @since 1.19.0
	 */
	public function prepare_items() {

		$this->_column_headers = [ $this->get_columns(), [], [], ];

		$this->items = $this->get_profile_field_definitions();

		$this->set_pagination_args( [
			'total_items' => count( $this->items ),
			'per_page'    => 0,
		] );
	}


	/**
	 * Gets the column slug HTML.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $item the profile field definition object in row
	 * @return string HTML
	 */
	protected function column_cb( $item ) {

		return '<input type="hidden" name="bulk_action[]" value="' . ( $item instanceof Profile_Field_Definition ? $item->get_slug() : '' ) . '" />';
	}


	/**
	 * Gets the column name HTML.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $item the profile field definition object in row
	 * @return string HTML
	 */
	protected function column_name( Profile_Field_Definition $item ) {

		return sprintf(
			'<a href="%1$s" class="row-title" aria-label="%2$s">%3$s</a> <div class="row-actions"><span class="profile-field-slug">%4$s: %5$s </span></div>',
			wc_memberships()->get_admin_instance()->get_profile_fields_instance()->get_edit_profile_field_definition_screen_url( $item ),
			stripslashes( $item->get_name() ),
			stripslashes( $item->get_name() ),
			strtolower( _x( 'slug', 'Profile field slug', 'woocommerce-memberships' ) ),
			$item->get_slug()
		);
	}


	/**
	 * Gets the column type HTML.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $item the profile field definition object in row
	 * @return string HTML
	 */
	protected function column_type( Profile_Field_Definition $item ) {

		return $item->get_type_name();
	}


	/**
	 * Gets the column editable by HTML.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $item the profile field definition object in row
	 * @return string HTML
	 */
	protected function column_editable_by( Profile_Field_Definition $item ) {

		ob_start();

		?>
		<label class="wc-memberships-profile-field-switch">
			<input
				type="checkbox"
				data-profile-field="<?php echo esc_attr( $item->get_slug() ); ?>"
				data-prop="editable_by"
				<?php checked( $item->is_editable_by( Profile_Field_Definition::EDITABLE_BY_CUSTOMER ) ); ?>
			/><span class="wc-memberships-profile-field-slider"></span>
		</label>
		<?php

		return ob_get_clean();
	}


	/**
	 * Gets the column visibility HTML.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $item the profile field definition object in row
	 * @return string HTML
	 */
	protected function column_visibility( Profile_Field_Definition $item ) {

		$options = Profile_Fields::get_profile_fields_visibility_options( true );
		$html    = '';

		if ( Profile_Field_Definition::EDITABLE_BY_CUSTOMER === $item->get_editable_by() ) {

			foreach ( $item->get_visibility() as $visibility ) {

				if ( isset( $options[ $visibility ] ) ) {

					$html .= '<span>' . $options[ $visibility ] . '</span>';
				}
			}
		}

		return $html ?: '<span>&mdash;</span>';
	}


	/**
	 * Gets the column sort handle HTML.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $item the profile field definition object in row
	 * @return string HTML
	 */
	protected function column_sort_handle( Profile_Field_Definition $item ) {

		$enabled = ! $this->has_filters();

		ob_start();

		?>
		<span
			class="dashicons dashicons-menu-alt3 wc-memberships-profile-field-sort-handle <?php if ( ! $enabled ) { echo 'disabled'; } ?>"
			<?php if ( ! $enabled ) { echo ' title="' . esc_attr__( 'Please clear any filters before rearranging profile fields.', 'woocommerce-memberships' ) . '"'; } ?>>
		</span>
		<?php

		return ob_get_clean();
	}


	/**
	 * Outputs information and HTML when there are no profile fields found.
	 *
	 * Implements parent method.
	 *
	 * @since 1.19.0
	 */
	public function no_items() {

		if ( ! Profile_Fields::is_using_profile_fields() ) {

			printf(
				/* translators: Placeholders: %1$s - Opening HTML <a> link tag, %2$s - Closing </a> HTML link tag */
				__( 'You haven\'t created any profile fields yet &mdash; %1$sClick Here%2$s to create your first one!', 'woocommerce-memberships' ),
				'<a href="' . esc_url( wc_memberships()->get_admin_instance()->get_profile_fields_instance()->get_new_profile_field_definition_screen_url() ) . '">',
				'</a>'
			);

		} else {

			printf(
				/* translators: Placeholders: %1$s - opening HTML <a> link tag, %2$s - closing HTML </a> link tag */
				__( 'No profile fields found &mdash; %1$sClick Here%2$s to create one!', 'woocommerce-checkout-add-ons' ),
				'<a href="' . esc_url( wc_memberships()->get_admin_instance()->get_profile_fields_instance()->get_new_profile_field_definition_screen_url() ) . '">',
				'</a>'
			);

		}
	}


	/**
	 * Outputs HTML for the profile field definition filters.
	 *
	 * Implements parent method.
	 *
	 * @since 1.19.0
	 *
	 * @param string $position either 'top' or 'bottom'
	 */
	public function extra_tablenav( $position ) {

		// we currently don't handle the bottom area yet
		if ( 'top' !== $position ) {
			return;
		}

		?>
		<div class="alignleft actions wc-memberships-profile-field-filters">

			<label class="screen-reader-text" for="filter-by-profile-field-type"><?php esc_html_e( 'Filter by profile field type', 'woocommerce-memberships' ); ?></label>
			<select id="filter-by-profile-field-type" name="type">
				<option value="" <?php selected( '', $this->get_profile_field_definitions_type_filter() ); ?>><?php esc_html_e( 'Show all types', 'woocommerce-memberships' ); ?></option>
				<?php foreach ( Profile_Fields::get_profile_field_types() as $profile_field_type => $profile_field_name ) : ?>
					<option value="<?php echo esc_attr( $profile_field_type ); ?>" <?php selected( $profile_field_type, $this->get_profile_field_definitions_type_filter() ); ?>>
						<?php echo esc_html( $profile_field_name ); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<label class="screen-reader-text" for="filter-by-profile-field-editable-by"><?php esc_html_e( 'Filter by profile field edit access', 'woocommerce-memberships' ); ?></label>
			<select id="filter-by-profile-field-editable-by" name="editable_by">
				<option value="" <?php selected( '', $this->get_profile_field_definitions_type_filter() ); ?>><?php esc_html_e( 'Show all edit access', 'woocommerce-memberships' ); ?></option>
				<option value="<?php esc_attr_e( Profile_Field_Definition::EDITABLE_BY_CUSTOMER ); ?>" <?php selected( Profile_Field_Definition::EDITABLE_BY_CUSTOMER, $this->get_profile_field_definitions_editable_by_filter() ); ?>><?php esc_html_e( 'Members and admins', 'woocommerce-memberships' ); ?></option>
				<option value="<?php esc_attr_e( Profile_Field_Definition::EDITABLE_BY_ADMIN ); ?>" <?php selected( Profile_Field_Definition::EDITABLE_BY_ADMIN, $this->get_profile_field_definitions_editable_by_filter() ); ?>><?php esc_html_e( 'Admin-only', 'woocommerce-memberships' ); ?></option>
			</select>

			<?php submit_button( esc_html_x( 'Filter', 'Filter profile fields button text', 'woocommerce-memberships' ), '', 'filter_action', false ); ?>

		</div>
		<?php
	}


	/**
	 * Gets the current filter to show only profile fields pertaining to an input type.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	private function get_profile_field_definitions_type_filter() {

		if ( null === $this->field_type_filter ) {
			$this->field_type_filter = isset( $_GET['type'] ) ? $_GET['type'] : '';
		}

		return $this->field_type_filter;
	}


	/**
	 * Gets the current filter to show only profile fields editable by a type of user.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	private function get_profile_field_definitions_editable_by_filter() {

		if ( null === $this->editable_by_filter ) {
			$this->editable_by_filter = isset( $_GET['editable_by'] ) ? $_GET['editable_by'] : '';
		}

		return $this->editable_by_filter;
	}


	/**
	 * Determines whether there are list filters applied.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	private function has_filters() {

		return ! empty( $this->get_profile_field_definitions_editable_by_filter() ) || ! empty( $this->get_profile_field_definitions_type_filter() );
	}


	/**
	 * Checks the current user's permissions.
	 *
	 * Implements parent method.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	public function ajax_user_can() {

		return current_user_can( 'manage_woocommerce' );
	}


}
