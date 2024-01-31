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
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Admin\Views\Meta_Boxes\Profile_Field;

use SkyVerge\WooCommerce\Memberships\Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;

/**
 * The meta box for the profile field definition data in the edit screen.
 *
 * @since 1.19.0
 */
class Data extends Meta_Box {


	/** @var array fields to display in this meta box */
	private $fields;


	/**
	 * Profile fields data meta box constructor.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition object
	 */
	public function __construct( Profile_Field_Definition $profile_field_definition ) {

		parent::__construct( $profile_field_definition );

		$this->fields = $this->get_fields();
	}


	/**
	 * Gets the fields to display in this meta box.
	 *
	 * @since 1.19.0
	 *
	 * @return array
	 */
	private function get_fields() {

		$profile_field_definition = $this->get_profile_field_definition();

		$is_new    = $profile_field_definition->is_new();
		$is_in_use = ! $is_new && $profile_field_definition->is_in_use();
		$readonly  = $is_in_use ? [ 'readonly' => 'readonly' ] : [];

		$fields = [
			'slug'                 => [
				'id'                     => 'slug',
				'type'                   => 'text',
				'label'                  => __( 'Slug', 'woocommerce-memberships' ),
				'placeholder'            => __( 'Enter unique slug or leave blank to create upon saving', 'woocommerce-memberships' ),
				'value'                  => $profile_field_definition->get_slug( 'edit' ),
				'custom_attributes'      => array_merge( $readonly, [
					'maxlength' => 255,
				] ),
			],
			'field_type'           => [
				'id'                     => 'type',
				'type'                   => 'select',
				'label'                  => __( 'Field type', 'woocommerce-memberships' ),
				'options'                => $is_new || ! $is_in_use ? Profile_Fields::get_profile_field_types() : Profile_Fields::get_compatible_profile_field_types( $profile_field_definition->get_type( 'edit' ) ), // if a member has already populated the field, the field type can be updated only with its compatible types
				'value'                  => $profile_field_definition->get_type( 'edit' ),
				'class'                  => 'wc-enhanced-select',
				'desc_tip'               => false,
				'description'            => ! $is_new && $is_in_use ? sprintf(
					/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
					__( 'This profile field is currently in use by one or more members, so %1$sField type%2$s editing is limited.', 'woocommerce-memberships' ),
					'<strong>', '</strong>'
				) : '',
			],
			'default_value'        => [
				'id'                     => 'default_value',
				'type'                   => 'radio',
				'label'                  => __( 'Default to', 'woocommerce-memberships' ),
				'description'            => __( 'The checkbox input default status.', 'woocommerce-memberships' ),
				'value'                  => $profile_field_definition->is_type( 'checkbox' ) ? wc_bool_to_string( $profile_field_definition->get_default_value( 'edit' ) ) : 'no',
				'options'                => [
					'yes' => _x( 'Checked', 'Checkbox checked status', 'woocommerce-memberships' ),
					'no'  => _x( 'Unchecked', 'Checkbox unchecked status', 'woocommerce-memberships' ),
				],
			],
			'membership_plan_ids'  => [
				'id'                     => 'membership_plan_ids',
				'type'                   => 'select_membership_plans', /** @see Data::render_select_membership_plans_field() */
				'label'                  => __( 'Membership plans', 'woocommerce-memberships' ),
				'description'            => __( 'This field will only be applied to prospective or current members of the selected plans. Leave blank to apply to all plans.', 'woocommerce-memberships' ),
				'value'                  => $profile_field_definition->get_membership_plan_ids( 'edit' ),
				'options'                => $this->get_membership_plans(),
				'class'                  => 'wc-enhanced-select',
				'custom_attributes'      => [
					'multiple'         => 'multiple',
					'data-placeholder' => __( 'Search or leave blank to apply to all plans', 'woocommerce-memberships' ),
				],
			],
			'editable_by'          => [
				'id'                     => 'editable_by',
				'description'            => __( 'Determine if members can view and edit their profile fields.', 'woocommerce-memberships' ),
				'type'                   => 'radio',
				'label'                  => __( 'Editable by', 'woocommerce-memberships' ),
				'options'                => [
					Profile_Field_Definition::EDITABLE_BY_ADMIN    => __( 'Admin-only', 'woocommerce-memberships' ),
					Profile_Field_Definition::EDITABLE_BY_CUSTOMER => __( 'Members and admins', 'woocommerce-memberships' )
				],
				'value'                  => $profile_field_definition->get_editable_by( 'edit' ),
			],
			'visibility'           => [
				'id'                     => 'visibility',
				'type'                   => 'select',
				'label'                  => __( 'Show field on', 'woocommerce-memberships' ),
				'description'            => __( 'Determines where this field is shown to prospective and current members.', 'woocommerce-memberships' ),
				'value'                  => $profile_field_definition->get_visibility( 'edit' ),
				'options'                => Profile_Fields::get_profile_fields_visibility_options( true ),
				'class'                  => 'wc-enhanced-select',
				// the inline style here will fix a bug where hiding/showing the visibility field is ignoring its css width rule
				'style'                  => 'width: 90%;',
				'custom_attributes'      => [
					'multiple' => 'multiple',
				]
			],
			'label'                => [
				'id'                     => 'label',
				'description'            => __( 'Optional descriptive label presented to members. Defaults to the field name if blank.', 'woocommerce-memberships' ),
				'type'                   => 'text',
				'label'                  => __( 'Label', 'woocommerce-memberships' ),
				'placeholder'            => __( 'Add label or leave blank to use the field name', 'woocommerce-memberships' ),
				'value'                  => stripslashes( $profile_field_definition->get_label( 'edit' ) ),
			],
			'description'          => [
				'id'                     => 'description',
				'description'            => __( 'Optional description shown to members as a field tooltip.', 'woocommerce-memberships' ),
				'type'                   => 'textarea',
				'label'                  => __( 'Description', 'woocommerce-memberships' ),
				'value'                  => stripslashes( $profile_field_definition->get_description( 'edit' ) ),
			],
			'required'             => [
				'id'                     => 'required',
				'description'            => __( 'If required, the member must update this field before registering or purchasing a membership plan.', 'woocommerce-memberships' ),
				'type'                   => 'checkbox',
				'label'                  => __( 'Required?', 'woocommerce-memberships' ),
				'value'                  => $profile_field_definition->get_required( 'edit' ),
			],
			'options'              => [
				'id'                     => 'options',
				'type'                   => 'options_repeater', /** @see Data::render_options_repeater_field() */
				'label'                  => __( 'Options', 'woocommerce-memberships' ),
				'description'            => $is_in_use ? __( "Changing or removing options won't impact this field on an existing member's profile until an admin or the member attempts to edit the field.", 'woocommerce-memberships' ) : '',
				'value'                  => $profile_field_definition->has_options() ? array_filter( array_map( 'stripslashes', (array) $profile_field_definition->get_options( 'edit' ) ) ) : [],
			],
		];

		if ( $is_in_use ) {

			// if the field is in use, editing should be disabled
			$fields['slug']['custom_attributes']['readonly'] = 'readonly';
		}

		return $fields;
	}


	/**
	 * Gets the custom attributes value for a field.
	 *
	 * @since 1.19.0
	 *
	 * @param array $field field data
	 * @return string[]
	 */
	private function get_field_custom_attributes( $field ) {

		$custom_attributes = [];

		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

			foreach ( $field['custom_attributes'] as $attribute => $value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}

		return $custom_attributes;
	}


	/**
	 * Gets the default tabs for this meta box.
	 *
	 * @since 1.19.0
	 *
	 * @return array
	 */
	private function get_panels() {

		return [
			'general' => [
				'label'  => __( 'General', 'woocommerce-memberships' ),
				'fields' => [
					'slug',
					'field_type',
					'default_value',
					'membership_plan_ids',
					'editable_by',
					'visibility',
					'label',
					'description',
					'required',
				],
			],
			'field_options' => [
				'label'  => __( 'Field options', 'woocommerce-memberships' ),
				'fields' => [
					'options'
				],
			],
		];
	}


	/**
	 * Gets a formatted array of membership plan IDs and names.
	 *
	 * @since 1.19.0
	 *
	 * @return array
	 */
	private function get_membership_plans() {

		$plans = [];

		foreach ( wc_memberships_get_membership_plans() as $plan ) {
			$plans[ $plan->get_id() ] = $plan->get_name();
		}

		return $plans;
	}


	/**
	 * Gets field data with filled-in defaults.
	 *
	 * @since 1.19.0
	 *
	 * @param array $field raw field data
	 * @return array
	 */
	private function get_field_data( $field ) {

		$field_data = wp_parse_args( $field, [
			'class'         => '',
			'desc_tip'      => true,
			'description'   => '',
			'id'            => '',
			'label'         => '',
			'name'          => '',
			'options'       => [],
			'placeholder'   => '',
			'style'         => '',
			'value'         => '',
			'wrapper_class' => '',
		] );

		$field_data['name'] = '' === $field_data['name'] ? $field_data['id'] : $field_data['name'];

		return $field_data;
	}


	/**
	 * Outputs the meta box HTML.
	 *
	 * @since 1.19.0
	 */
	public function render() {

		/**
		 * Fires before rendering the profile field definition form.
		 *
		 * @since 1.19.0
		 *
		 * @param Profile_Field_Definition $profile_field_definition the profile field definition object
		 * @param Data $data_meta_box the meta box instance
		 */
		do_action( 'wc_memberships_before_profile_fields_data', $this->get_profile_field_definition(), $this );

		$panels = $this->get_panels();

		?>
		<div class="panel-wrap data">
			<?php $this->render_tabs( $panels ); ?>
			<?php $this->render_panels( $panels ); ?>
		</div>
		<?php

		/**
		 * Fires after rendering the profile field definition form.
		 *
		 * @since 1.19.0
		 *
		 * @param Profile_Field_Definition $profile_field_definition the profile field definition object
		 * @param Data $data_meta_box the meta box instance
		 */
		do_action( 'wc_memberships_after_profile_fields_data', $this->get_profile_field_definition(), $this );
	}


	/**
	 * Renders the tabs for the meta box.
	 *
	 * @since 1.19.0
	 *
	 * @param array $panels
	 */
	private function render_tabs( array $panels = [] ) {

		?>
		<ul class="wc_membership_profile_field_definition_tabs wc-tabs">

			<?php foreach ( $panels as $panel_key => $panel ) : ?>

				<li class="wc-tab <?php echo sanitize_html_class( $panel_key . '_tab' ); ?>">
					<a href="<?php echo esc_attr( '#wc-memberships-profile-field-definition-panel-' . $panel_key ); ?>">
						<span><?php echo esc_html( $panel['label'] ); ?></span>
					</a>
				</li>

			<?php endforeach; ?>

		</ul>
		<?php
	}


	/**
	 * Renders the panels for the meta box.
	 *
	 * @since 1.19.0
	 *
	 * @param array $panels
	 */
	private function render_panels( array $panels = [] ) {

		foreach ( $panels as $panel_key => $panel ) :

			?>
			<div id="<?php echo sanitize_html_class( 'wc-memberships-profile-field-definition-panel-' . $panel_key ); ?>" class="panel woocommerce_options_panel" style="display: block;">
				<?php array_map( [ $this, 'output_field' ], $panel['fields'] ); ?>
			</div>
			<?php

		endforeach;
	}


	/**
	 * Outputs a field.
	 *
	 * @since 1.19.0
	 *
	 * @param string|array|null $field field key or field data
	 */
	private function output_field( $field = null ) {

		$field = ! is_array( $field ) && isset( $this->fields[ $field ] ) ? $this->fields[ $field ] : null;

		if ( ! is_array( $field ) || ! isset( $field['type'] ) ) {
			return;
		}

		$render_function = [ $this, 'render_' . $field['type'] . '_field' ];

		$field_data = $this->get_field_data( $field );

		if ( is_callable( $render_function ) ) {

			$this->render_field_wrapper( $field_data, $render_function );
		}
	}


	/**
	 * Renders a field wrapper (containing field output).
	 *
	 * @since 1.19.0
	 *
	 * @param array $field_data field data, already initialized
	 * @param callable $field_renderer the function to render the field
	 */
	private function render_field_wrapper( array $field_data, callable $field_renderer ) {

		$wrapper_classes = [
			'form-field',
			$field_data['id'] . '_field',
			$field_data['wrapper_class'],
		];

		?>
		<p class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
			<label for="<?php echo esc_attr( $field_data['id'] ); ?>">
				<?php echo wp_kses_post( $field_data['label'] ); ?>
			</label>
			<?php

			if ( isset( $field_data['options_note'] ) && '' !== $field_data['options_note'] ) :

				?>
				<span class="<?php echo esc_attr( $field_data['id'] ); ?>-options-note" style="display: none; font-style: italic;">
					<?php echo esc_html( $field_data['options_note'] ); ?>
				</span>
				<?php

			endif;

			?>
			<span class="<?php echo esc_attr( $field_data['id'] ) . '-outer'; ?>">
				<?php $field_renderer( $field_data ); ?>
			</span>
			<?php

			if ( ! empty( $field_data['description'] ) && 'options_repeater' !== $field_data['type'] ) :

				if ( false !== $field_data['desc_tip'] ) :

					echo wc_help_tip( $field_data['description'] );

				else :

					?>
					<span class="description">
						<?php echo wp_kses_post( $field_data['description'] ); ?>
					</span>
					<?php

				endif;

			endif;

			?>
		</p>
		<?php
	}


	/**
	 * Renders a checkbox field.
	 *
	 * @since 1.19.0
	 *
	 * @param array $field_data initialized field data
	 */
	private function render_checkbox_field( array $field_data ) {

		?>
		<input type="checkbox"
			id="<?php echo esc_attr( $field_data['id'] ); ?>"
			name="<?php echo esc_attr( $field_data['name'] ); ?>"
			class="<?php echo esc_attr( $field_data['class'] ); ?>"
			style="<?php echo esc_attr( $field_data['style'] ); ?>"
			value="yes"
			<?php checked( $field_data['value'], 'yes' ); ?>
			<?php implode( ' ', $this->get_field_custom_attributes( $field_data ) ); ?>
		/>
		<?php
	}


	/**
	 * Renders a radio field.
	 *
	 * @since 1.19.0
	 *
	 * @param array $field_data initialized field data
	 */
	private function render_radio_field( array $field_data ) {

		foreach ( $field_data['options'] as $option_value => $option_label ) :

			?>
			<label class="label-radio">
				<input type="radio"
					id="<?php echo esc_attr( sprintf( 'wc_memberships_profile_field_definition_%s_%s', esc_attr( $field_data['id'] ), $option_value ) ); ?>"
					name="<?php echo esc_attr( $field_data['name'] ); ?>"
					value="<?php echo esc_attr( $option_value ); ?>"
					style="<?php echo esc_attr( $field_data['style'] ); ?>"
					<?php checked( $option_value, $field_data['value'] ); ?>
					<?php implode( ' ', $this->get_field_custom_attributes( $field_data ) ); ?>
				/> <?php echo esc_attr( $option_label ); ?>
			</label>
			<?php

		endforeach;
	}


	/**
	 * Renders a select field.
	 *
	 * @since 1.19.0
	 *
	 * @param array $field_data initialized field data
	 */
	private function render_select_field( array $field_data ) {

		$multiple = ! empty( $field_data['custom_attributes'] ) && array_key_exists( 'multiple', $field_data['custom_attributes'] ) ? '[]' : '';

		?>
		<select
			id="<?php echo esc_attr( $field_data['id'] ); ?>"
			name="<?php echo esc_attr( $field_data['name'] . $multiple ); ?>"
			class="<?php echo esc_attr( $field_data['class'] ); ?>"
			style="<?php echo esc_attr( $field_data['style'] ); ?>"
			<?php echo implode( ' ', $this->get_field_custom_attributes( $field_data ) ); ?>>
			<?php foreach ( $field_data['options'] as $option_value => $option_label ) : ?>
				<?php $selected = is_array( $field_data['value'] ) ? in_array( $option_value, $field_data['value'], false ) : $option_value === $field_data['value']; ?>
				<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $selected ); ?>>
					<?php echo esc_html( $option_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}


	/**
	 * Renders a select membership plans field.
	 *
	 * @since 1.19.2
	 *
	 * @param array $field_data initialized field data
	 */
	private function render_select_membership_plans_field( array $field_data ) {

		?>
		<select
			id="<?php echo esc_attr( $field_data['id'] ); ?>"
			name="<?php echo esc_attr( $field_data['name'] . '[]' ); ?>"
			class="<?php echo esc_attr( $field_data['class'] ); ?>"
			style="<?php echo esc_attr( $field_data['style'] ); ?>"
			<?php echo implode( ' ', $this->get_field_custom_attributes( $field_data ) ); ?>>

			<?php foreach ( wc_memberships_get_membership_plans() as $membership_plan ) : ?>

				<?php

				$selected = is_array( $field_data['value'] ) ? in_array( $membership_plan->get_id(), array_map( 'intval', $field_data['value'] ), false ) : $membership_plan->get_id() === (int) $field_data['value'];
				$access   = [ Profile_Fields::VISIBILITY_PROFILE_FIELDS_AREA ];

				switch ( $membership_plan->get_access_method() ) {
					case 'signup' :
						$access[] = Profile_Fields::VISIBILITY_REGISTRATION_FORM;
					break;
					case 'purchase' :
						$access[] = Profile_Fields::VISIBILITY_PRODUCT_PAGE;
					break;
				}

				/**
				 * Filters a plan's visibility options by access method for profile fields.
				 *
				 * @since 1.19.2
				 *
				 * @param string[] $visibility_by_access_method visibility options
				 * @param \WC_Memberships_Membership_Plan $membership_plan the membership plan
				 */
				$visibility_by_access_method = (array) apply_filters( 'wc_memberships_profile_fields_membership_plan_visibility_options', $access, $membership_plan );

				?>
				<option
					value="<?php echo esc_attr( $membership_plan->get_id() ); ?>"
					data-visibility-options="<?php echo esc_attr( implode( ',', $visibility_by_access_method ) ); ?>"
					<?php selected( $selected ); ?>>
					<?php echo esc_html( $membership_plan->get_formatted_name() ); ?>
				</option>

			<?php endforeach; ?>

		</select>
		<?php
	}


	/**
	 * Renders a text field.
	 *
	 * @since 1.19.0
	 *
	 * @param array $field_data initialized field data
	 */
	private function render_text_field( $field_data ) {

		?>
		<input type="text"
			id="<?php echo esc_attr( $field_data['id'] ); ?>"
			name="<?php echo esc_attr( $field_data['name'] ); ?>"
			class="<?php echo esc_attr( $field_data['class'] ); ?>"
			style="<?php echo esc_attr( $field_data['style'] ); ?>"
			value="<?php echo esc_attr( $field_data['value'] ); ?>"
			placeholder="<?php echo esc_attr( $field_data['placeholder'] ); ?>"
			<?php echo implode( ' ', $this->get_field_custom_attributes( $field_data ) ); ?>
		/>
		<?php
	}


	/**
	 * Renders a textarea field.
	 *
	 * @since 1.19.0
	 *
	 * @param array $field_data initialized field data
	 */
	private function render_textarea_field( array $field_data ) {

		?>
		<textarea
			id="<?php echo esc_attr( $field_data['id'] ); ?>"
			name="<?php echo esc_attr( $field_data['name'] ); ?>"
			class="<?php echo esc_attr( $field_data['class'] ); ?>"
			placeholder="<?php echo esc_attr( $field_data['placeholder'] ); ?>"
			style="<?php echo esc_attr( $field_data['style'] ); ?>"
			<?php implode( ' ', $this->get_field_custom_attributes( $field_data ) ); ?>
		><?php echo wp_kses_post( $field_data['value'] ); ?></textarea>
		<?php
	}


	/**
	 * Renders a special input to manage the profile field options.
	 *
	 * This would only apply to fields that can accept options and should be disabled/hidden for fields who don't.
	 *
	 * @since 1.19.0
	 *
	 * @param array $field_data field data
	 */
	private function render_options_repeater_field( array $field_data ) {

		$options = isset( $field_data['value'] ) ? (array) $field_data['value'] : [];

		?>
		<span class="description" style="font-style: italic; font-size: 12px; margin-left: 0;">
			<?php if ( ! empty( $field_data['description'] ) ) : ?>
		        <span><?php echo $field_data['description']; ?></span>
			<?php endif; ?>
			<span class="show-if-options-required profile-field-validation-error" style="display: none;">
				<?php esc_html_e( 'Please add one or more field options before saving this profile field.', 'woocommerce-memberships' ); ?>
			</span>
			<span class="show-if-options-empty profile-field-validation-error" style="display: none;">
				<?php esc_html_e( 'Profile field options cannot be blank. Please enter a label for your option, or delete it.', 'woocommerce-memberships' ); ?>
			</span>
		</span>

		<div id="profile-field-options-wrap" class="table-wrap profile-field-options-wrap">
			<table id="profile-field-options-repeater" class="widefat rules profile-field-options-repeater striped">

				<thead>
					<tr>
						<td class="check-column" style="width: 5%;">
							<label class="screen-reader-text" for="profile-field-option-select-all"> <?php esc_html_e( 'Select all', 'woocommerce-memberships' ); ?></label>
							<input type="checkbox" id="profile-field-option-select-all" />
						</td>
						<th scope="col" class="profile-field-option-label" style="width: 80%;">
							<?php esc_html_e( 'Label', 'woocommerce-memberships' ); ?>
						</th>
						<th scope="col" class="profile-field-option-default" style="width: 10%;">
							<?php esc_html_e( 'Default', 'woocommerce-memberships' ); ?>
						</th>
						<th scope="col" class="profile-field-option-reorder" style="width: 5%;">
							&nbsp;
						</th>
					</tr>
				</thead>

				<tbody id="profile-field-options">

					<tr class="profile-field-no-options" style="<?php echo $this->get_profile_field_definition()->has_options( 'edit' ) ? 'display: none;' : ''; ?>">
						<td colspan="4">
							<?php printf(
								/* translators: Placeholder: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
								esc_html__( 'You don\'t have any options yet! Click the %1$sAdd new option%2$s button below to get started.', 'woocommerce-memberships' ),
								'<strong>', '</strong>'
							); ?>
						</td>
					</tr>

					<?php $this->render_option_row(); // template row ?>

					<?php foreach ( $options as $value => $label ) : ?>
						<?php $this->render_option_row( $value, $label ); ?>
					<?php endforeach; ?>

				</tbody>

				<tfoot>
					<tr>
						<th colspan="4">
							<button
								type="button"
								class="button button-primary add-profile-field-option">
								<?php esc_html_e( 'Add new option', 'woocommerce-memberships' ); ?>
							</button>
							<button
								type="button"
								class="button button-secondary remove-profile-field-options"
								style="<?php echo ! $this->get_profile_field_definition()->has_options() ? 'display: none;' : ''; ?>">
								<?php esc_html_e( 'Delete selected', 'woocommerce-memberships' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>

			</table>
		</div>
		<?php
	}


	/**
	 * Renders an option repeater row.
	 *
	 * @see Data::render_options_repeater_field()
	 *
	 * @since 1.19.0
	 *
	 * @param string $option_index the row identifier
	 * @param string $option_value the option name
	 */
	private function render_option_row( $option_index = 'template', $option_value = '' ) {

		$option_value  = empty( $option_value ) || ! is_string( $option_value ) ? '' : stripslashes( $option_value );
		$default_value = $this->get_profile_field_definition()->get_default_value( 'edit' );

		if ( is_array( $default_value ) || $this->get_profile_field_definition()->is_multiple() ) {
			$checked = ! empty( $default_value ) && in_array( $option_value, (array) $default_value, false );
		} else {
			$checked = ! empty( $default_value ) && $option_value === $default_value;
		}

		?>
		<tr class="profile-field-option-row" id="<?php echo esc_attr( 'profile-field-option--' . $option_index ); ?>">

			<th class="check-column">
				<input type="checkbox" />
			</th>

			<td class="profile-field-option-label">
				<input
					type="text"
					class="profile-field-option-field name-field"
					name="<?php echo esc_attr( 'options[' . $option_index . ']' ); ?>"
					value="<?php echo esc_attr( $option_value ); ?>"
					style="width: 100%;"
				/>
			</td>

			<td class="profile-field-option-default">
				<input
					type="checkbox"
					class="profile-field-option-field multi-default-field"
					name="<?php echo esc_attr( 'default_options[' . $option_index . ']' ); ?>"
					value="<?php echo esc_attr( $option_index ); ?>"
					style="<?php echo $this->get_profile_field_definition()->is_multiple() ? 'display: none;' : ''; ?>"
					<?php checked( $checked ); ?>
				/>
				<input
					type="radio"
					name="default_option"
					class="profile-field-option-field default-field"
					value="<?php echo esc_attr( $option_index ); ?>"
					style="<?php echo ! $this->get_profile_field_definition()->is_multiple() ? 'display: none;' : ''; ?>"
					<?php checked( $checked ); ?>
				/>
			</td>

			<td class="profile-field-option-reorder">
				<span class="dashicons dashicons-menu-alt3 wc-memberships-profile-field-sort-handle"></span>
			</td>

		</tr>
		<?php
	}


}
