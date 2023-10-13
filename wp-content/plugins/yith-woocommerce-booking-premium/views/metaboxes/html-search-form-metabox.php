<?php
/**
 * Search Form Metabox
 *
 * @var YITH_WCBK_Search_Form $search_form The search form.
 * @var array                 $fields      The fields.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit();

$options = array(
	'search'     => array(
		'label'  => __( 'Search', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'label' => array(
				'label'             => __( 'Label', 'yith-booking-for-woocommerce' ),
				'type'              => 'text',
				'default'           => '',
				'custom_attributes' => array(
					'placeholder' => __( 'Search', 'yith-booking-for-woocommerce' ),
				),
			),
		),
	),
	'location'   => array(
		'label'  => __( 'Location', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'default_range' => array(
				'label'   => __( 'Default distance range', 'yith-booking-for-woocommerce' ),
				'type'    => 'number',
				'default' => 30,
			),
			'show_range'    => array(
				'label'   => __( 'Show distance range', 'yith-booking-for-woocommerce' ),
				'type'    => 'onoff',
				'default' => 'yes',
			),
		),
	),
	'categories' => array(
		'label' => __( 'Categories', 'yith-booking-for-woocommerce' ),
	),
	'tags'       => array(
		'label' => __( 'Tags', 'yith-booking-for-woocommerce' ),
	),
	'date'       => array(
		'label'  => __( 'Dates', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'type' => array(
				'type'    => 'radio',
				'options' => array(
					''             => __( 'Show two different date pickers', 'yith-booking-for-woocommerce' ),
					'range-picker' => __( 'Show date range picker selector', 'yith-booking-for-woocommerce' ),
				),
				'default' => '',
			),
		),
	),
	'persons'    => array(
		'label'  => __( 'People', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'type' => array(
				'type'    => 'radio',
				'options' => array(
					'persons'         => __( 'People field', 'yith-booking-for-woocommerce' ),
					'person-types'    => __( 'People type field', 'yith-booking-for-woocommerce' ),
					'people-selector' => __( 'People selector field', 'yith-booking-for-woocommerce' ),
				),
				'default' => 'persons',
			),
		),
	),
	'services'   => array(
		'label'  => __( 'Services', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'type' => array(
				'type'    => 'radio',
				'options' => array(
					''         => __( 'Show services as checkboxes', 'yith-booking-for-woocommerce' ),
					'selector' => __( 'Show service selector', 'yith-booking-for-woocommerce' ),
				),
				'default' => '',
			),
		),
	),
);

?>
<div class="yith-wcbk-admin-search-form-wrapper yith-plugin-ui">
	<h3 class="yith-wcbk-admin-search-form-section-title"><?php esc_html_e( 'Fields of this form', 'yith-booking-for-woocommerce' ); ?></h3>
	<div class="yith-wcbk-admin-search-form-fields yith-plugin-fw__boxed-table">

		<?php foreach ( $fields as $field_key => $field_data ) : ?>
			<?php
			$enabled   = isset( $field_data['enabled'] ) && 'yes' === $field_data['enabled'] ? 'yes' : 'no';
			$the_field = $options[ $field_key ];
			?>
			<div class="yith-wcbk-admin-search-form-field yith-plugin-fw__boxed-row">
				<div class="yith-wcbk-admin-search-form-field-title">
					<?php if ( ! empty( $the_field['fields'] ) ) : ?>
						<span class="yith-wcbk-admin-search-form-field-title__toggle yith-icon yith-icon-arrow-down"></span>
					<?php endif ?>
					<span class="yith-wcbk-admin-search-form-field-title__title"><?php echo esc_html( $the_field['label'] ); ?></span>
					<span class="yith-wcbk-admin-search-form-field-title__enable">
						<?php
						// Print an hidden field to prevent empty values for on-off.
						yith_plugin_fw_get_field(
							array(
								'type'  => 'hidden',
								'name'  => "_yith_wcbk_admin_search_form_fields[{$field_key}][enabled]",
								'value' => 'no',
							),
							true
						);

						yith_plugin_fw_get_field(
							array(
								'type'  => 'onoff',
								'id'    => "yith-wcbk-admin-search-form-field-enabled-{$field_key}",
								'name'  => "_yith_wcbk_admin_search_form_fields[{$field_key}][enabled]",
								'value' => $enabled,
							),
							true
						);
						?>
						</span>

				</div>
				<?php if ( ! empty( $the_field['fields'] ) ) : ?>
					<div class="yith-wcbk-admin-search-form-field-content">
						<?php foreach ( $the_field['fields'] as $_key => $_field ) : ?>
							<?php
							$field_label     = $_field['label'] ?? '';
							$_field['id']    = 'yith-wcbk-admin-search-form__field__' . $field_key . '-' . $_key;
							$_field['name']  = "_yith_wcbk_admin_search_form_fields[{$field_key}][{$_key}]";
							$_field['value'] = $field_data[ $_key ] ?? $_field['default'];

							if ( isset( $_field['label'] ) ) {
								unset( $_field['label'] );
							}
							unset( $_field['default'] );
							?>
							<div class="yith-wcbk-admin-search-form-field-row">
								<?php if ( ! empty( $field_label ) ) : ?>
									<label class="yith-wcbk-admin-search-form-field-row__label" for="yith-wcbk-admin-search-form-field-search-label"><?php echo esc_html( $field_label ); ?></label>
								<?php endif; ?>
								<div class="yith-wcbk-admin-search-form-field-row__content">
									<?php
									if ( in_array( $_field['type'], array( 'checkbox', 'onoff' ), true ) ) {
										// Print an hidden field to prevent empty values for on-off and checkboxes.
										yith_plugin_fw_get_field(
											array(
												'type'  => 'hidden',
												'name'  => $_field['name'],
												'value' => 'no',
											),
											true
										);
									}
									yith_plugin_fw_get_field( $_field, true );
									?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
