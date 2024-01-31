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

defined( 'ABSPATH' ) or exit;

/**
 * Outputs a profile field form field.
 *
 * @see \woocommerce_form_field() WooCommerce core function wrapper
 *
 * @since 1.19.0
 *
 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field $profile_field profile field object
 * @param array $args optional array of arguments
 * @return string|void
 */
function wc_memberships_profile_field_form_field( \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field $profile_field, array $args = [] ) {

	$field_key  = 'member_profile_fields[' . $profile_field->get_slug() . ']';
	$field_args = wp_parse_args( $args, [
		'type'        => $profile_field->get_definition()->get_type(),
		'id'          => sprintf( 'wc-memberships-member-profile-field-%s', $profile_field->get_slug() ),
		'class'       => [ 'wc-memberships-member-profile-field', sprintf( 'wc-memberships-member-profile-field-input-%s', $profile_field->get_definition()->get_type() ) ],
		'label_class' => [],
		'input_class' => [],
		'label'       => stripslashes( $profile_field->get_definition()->get_label() ?: $profile_field->get_definition()->get_name() ),
		'description' => wp_kses_post( stripslashes( $profile_field->get_definition()->get_description() ?: '' ) ),
		'options'     => $profile_field->get_definition()->get_options(),
		'required'    => wc_string_to_bool( $profile_field->is_required() ),
	] );

	if ( empty( $field_args['default'] ) && $profile_field->is_new() ) {
		if ( $profile_field->get_definition()->is_type( [ \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_RADIO, \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_SELECT, \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_CHECKBOX ] ) ) {
			$field_args['default'] = stripslashes( $profile_field->get_definition()->get_default_value() );
		} elseif ( $profile_field->get_definition()->is_multiple() ) {
			$field_args['default'] = array_map( 'stripslashes', $profile_field->get_definition()->get_options() );
		}
	}

	if (
		   \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_FILE          === $field_args['type']
		|| \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_MULTICHECKBOX === $field_args['type']
		|| \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_MULTISELECT   === $field_args['type']
		|| \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_RADIO         === $field_args['type']
	) :

		if ( ! empty( $field_args['clear'] ) ) {
			$after = '<div class="clear"></div>';
		} else {
			$after = '';
		}

		if ( $field_args['required'] ) {
			$field_args['input_class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr_x( 'required', 'Required input field', 'woocommerce-memberships'  ) . '">*</abbr>';
		} else {
			$required = ' <span class="optional">(' . esc_html_x( 'optional', 'Optional input field', 'woocommerce-memberships' ) . ')</span>';
		}

		$custom_attributes = [];

		if ( ! empty( $field_args['custom_attributes'] ) && is_array( $field_args['custom_attributes'] ) ) {
			foreach ( $field_args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		ob_start();

		if ( \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_FILE === $field_args['type'] ) :

			$value = $profile_field->get_value();
			$title = $value ? get_the_title( $value ) : '';
			$url   = $value ? wp_get_attachment_url( $value ) : '';

			?>
			<div class="form-row <?php echo implode( ' ', $field_args['class'] ); ?> " id="<?php echo esc_attr( $field_key ); ?>_field">
				<span class="woocommerce-input-wrapper">

					<?php if ( $field_args['label'] ) : ?>
						<label for="<?php echo esc_attr( $field_key ); ?>" class="<?php echo implode( ' ', $field_args['label_class'] ); ?>"><?php echo $field_args['label'] . $required; ?></label>
					<?php endif; ?>

					<div class="wc-memberships-profile-field-input-file-plupload <?php echo implode( ' ', $field_args['input_class'] ); ?>">

						<a class="wc-memberships-profile-field-input-file-dropzone <?php echo $url ? 'hide' : ''; ?>"><?php esc_html_e( 'Drag file here or click to upload', 'woocommerce-memberships' ); ?>
							<div class="wc-memberships-profile-field-input-file-upload-progress hide"><div class="bar"></div></div>
						</a>

						<div class="wc-memberships-profile-field-input-file-preview <?php echo ! $url ? 'hide' : ''; ?>">
							<a href="<?php echo esc_url( $url ); ?>" class="file" target="_blank"><?php echo esc_html( $title ); ?></a>
							<a href="#" class="remove-file"><?php esc_html_e( 'Remove', 'woocommerce-memberships' ); ?></a>
						</div>

						<div class="wc-memberships-profile-field-input-file-feedback hide"></div>

						<input
							type="hidden"
							name="<?php echo esc_attr( $field_key ); ?>"
							value="<?php echo esc_attr( $value ); ?>"
							data-slug="<?php echo esc_attr( $profile_field->get_slug() ); ?>"
						/>

						<noscript><?php esc_html_e( 'You need to enable Javascript to upload files.', 'woocommerce-memberships' ); ?></noscript>

					</div>

					<?php if ( isset( $field_args['description'] ) && '' !== $field_args['description'] ) : ?>
						<span class="description" id="<?php echo esc_attr( $field_key . '-description' ); ?>"><?php echo $field_args['description']; ?></span>
					<?php endif; ?>

				</span>
			</div>
			<?php

			echo $after;

		elseif ( \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_MULTICHECKBOX === $field_args['type'] || \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_RADIO === $field_args['type'] ) :

			$default_value = $profile_field->get_definition()->get_default_value();

			if ( $profile_field->is_multiple() ) {

				$default_value = array_map( 'stripslashes', (array) $default_value );

				if ( $profile_field->is_new() ) {
					$value = $profile_field->get_value() ?: $default_value;
				} else {
					$value = $profile_field->get_value();
				}

			} else {

				if ( $profile_field->is_new() ) {
					$value = $profile_field->get_value() ?: $default_value;
				} else {
					$value = $profile_field->get_value();
				}
			}

			if ( ! empty( $field_args['options'] ) ) :

				?>
				<p class="form-row <?php echo implode( ' ', $field_args['class'] ); ?>" id="<?php echo esc_attr( $field_key ); ?>_field">
					<span class="woocommerce-input-wrapper">

						<?php // allows clearing the field by sending an empty value if no boxes are checked ?>
						<input type="hidden" name="<?php echo esc_attr( $field_key ); ?>" value="" />

						<?php if ( $field_args['label'] ) : ?>
							<label for="<?php echo esc_attr( $field_key ); ?>" class="<?php echo implode( ' ', $field_args['label_class'] ); ?>"><?php echo esc_html( $field_args['label'] ) . $required; ?></label>
						<?php endif; ?>

						<?php foreach ( $field_args['options'] as $option_key => $option_text ) : ?>

							<label for="<?php echo esc_attr( $field_key ) . '_' . esc_attr( $option_key ); ?>" class="checkbox <?php echo implode( ' ', $field_args['label_class'] ); ?>">
								<input
									type="<?php echo esc_attr( $profile_field->is_multiple() ? 'checkbox' : 'radio' ); ?>"
									id="<?php echo esc_attr( $field_key ) . '_' . esc_attr( $option_key ); ?>"
									name="<?php echo esc_attr( $field_key ) . ( $profile_field->is_multiple() ? '[]' : '' ); ?>"
									class="input-<?php echo esc_attr( $profile_field->get_definition()->get_type() ); ?>"
									value="<?php echo esc_attr( $option_key ); ?>"
									<?php checked( $profile_field->is_multiple() ? in_array( $option_key, $value, false ) : $option_key === $value ); ?>
								/> <?php echo esc_html( $option_text ); ?>
							</label>

						<?php endforeach; ?>

						<?php if ( isset( $field_args['description'] ) && '' !== $field_args['description'] ) : ?>
							<span class="description" id="<?php echo esc_attr( $field_key . '-description' ); ?>"><?php echo $field_args['description']; ?></span>
						<?php endif; ?>

					</span>
				</p>
				<?php

				echo $after;

			endif;

		elseif ( \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_MULTISELECT === $field_args['type'] ) :

			if ( ! empty( $field_args['options'] ) ) :

				if ( $profile_field->is_new() ) {
					$value = $profile_field->get_value() ?: wp_unslash( (array) $profile_field->get_definition()->get_default_value() );
				} else {
					$value = $profile_field->get_value();
				}

				?>
				<p class="form-row <?php echo implode( ' ', $field_args['class'] ); ?>" id="<?php echo esc_attr( $field_key );?>_field">
					<span class="woocommerce-input-wrapper">

						<?php // allow clearing the field by sending an empty value if no options are selected ?>
						<input type="hidden" name="<?php echo esc_attr( $field_key ); ?>" value="" />

						<?php if ( $field_args['label'] ) : ?>
							<label for="<?php echo esc_attr( $field_key ); ?>" class="<?php echo implode( ' ', $field_args['label_class'] ); ?>"><?php echo esc_html( $field_args['label'] ) . $required; ?></label>
						<?php endif; ?>

						<select
							name="<?php echo esc_attr( $field_key ); ?>[]"
							id="<?php echo esc_attr( $field_key ); ?>"
							class="select select2-search__field <?php echo implode( ' ', $field_args['input_class'] ); ?>"
							multiple="multiple"
							<?php echo ! empty( $custom_attributes ) ? implode( ' ', $custom_attributes ) : ''; ?>>
							<?php foreach ( $field_args['options'] as $option_key => $option_label ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( in_array( $option_key, (array) $value, false ) ); ?>><?php echo esc_html( $option_label ); ?></option>
							<?php endforeach; ?>
						</select>

						<?php if ( isset( $field_args['description'] ) && '' !== $field_args['description'] ) : ?>
							<span class="description" id="<?php echo esc_attr( $field_key . '-description' ); ?>"><?php echo $field_args['description']; ?></span>
						<?php endif; ?>

					</span>
				</p>
				<?php

				echo $after;

			endif;

		endif;

		$field = ob_get_clean();

	elseif ( \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_CHECKBOX === $field_args['type'] ) :

		// allows clearing the field by sending an empty value if the checkbox is not checked
		$field  = '<input type="hidden" name="' . esc_attr( $field_key ) . '" value="" />';
		$field .= woocommerce_form_field( $field_key, array_merge( $field_args, [ 'return' => true ] ), $profile_field->get_value() ?: null );

	elseif ( \SkyVerge\WooCommerce\Memberships\Profile_Fields::TYPE_SELECT === $field_args['type'] ) :

		$value = $profile_field->get_value() ?: null;

		// show a empty placeholder to force the user to select a value
		if ( empty( $field_args['default'] ) || ( $value && ! isset( $field_args['options'][ $value ] ) ) ) {
			$field_args['options'] = [ '' => '' ] + $field_args['options'];
		}

		$field = woocommerce_form_field( $field_key, array_merge( $field_args, [ 'return' => true ] ), $value );

	else :

		$field = woocommerce_form_field( $field_key, array_merge( $field_args, [ 'return' => true ] ), $profile_field->get_value() ?: null );

	endif;

	if ( empty( $field_args['return'] ) ) :
		echo $field;
	endif;

	return $field;
}


/**
 * Gets the Profile Fields Area query var.
 *
 * @since 1.19.0
 *
 * @return string
 */
function wc_memberships_get_profile_fields_area_query_var() {

	return 'profile_fields_area';
}


/**
 * Gets the Profile Fields Area endpoint.
 *
 * @since 1.19.0
 *
 * @return string
 */
function wc_memberships_get_profile_fields_area_endpoint() {

	if ( get_option( 'permalink_structure' ) ) {
		$endpoint = (string) get_option( 'woocommerce_myaccount_profile_fields_area_endpoint', 'my-profile' );
	} else {
		$endpoint = wc_memberships_get_profile_fields_area_query_var();
	}

	/**
	 * Filters the Profile Fields Area endpoint for the account.
	 *
	 * The original profile fields area endpoint (unfiltered) is available as the second parameter in case you need to compare it with the filtered value.
	 *
	 * @since 1.19.0
	 *
	 * @param string $endpoint the profile fields area endpoint
	 * @param string $original_endpoint the original profile fields area endpoint
	 */
	return (string) apply_filters( 'wc_memberships_profile_fields_area_endpoint', $endpoint, $endpoint );
}
