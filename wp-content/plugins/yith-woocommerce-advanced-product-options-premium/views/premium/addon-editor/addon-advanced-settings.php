<?php
/**
 * Addon Advanced Options Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var YITH_WAPO_Addon $addon
 * @var int $addon_id
 * @var string $addon_type
 * @var YITH_WAPO_Block $block
 * @var int $block_id
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$min_max_rule           = (array) $addon->get_setting( 'min_max_rule', 'min', false );
$min_max_value          = (array) $addon->get_setting( 'min_max_value', 0, false );

$addon_type = isset( $_GET['addon_type'] ) ? $_GET['addon_type'] : $addon->type; //phpcs:ignore

?>

<div id="tab-advanced-settings" style="display: none;">
	<?php
	$options_configuration = $addon->get_options_configuration_array();
	$default_options       = get_default_configuration_options();

	foreach( $options_configuration as $config_id => $config_options ) {

		$config_options = array_merge( $default_options['parent'], $config_options );

		foreach( $config_options as $config_option_id => &$config_option_values ) {
			if ( 'field' === $config_option_id ) {
				foreach( $config_option_values as &$field_values ){
					$field_values = array_merge( $default_options['field'], $field_values );
				}
			}
		}

		if ( 'addon-min-exa-rules' === $config_id ) {

			?>
			<!-- Option field -->
			<div class="field-wrap enabled-by-addon-enable-min-max addon-field-grid" style="display: none;">
				<label for="min-exa-rules"><?php
                    // translators: [ADMIN] Add-on editor > Options configuration option'
                    echo yith_wapo_get_string_by_addon_type( 'proceed_purchase', $addon_type ); ?>
                </label>
				<div id="min-exa-rules" class="field">
					<?php

					$min_max_count = count( $min_max_rule );
					for ( $y = 0; $y < $min_max_count; $y++ ) :

						$min_max_options = array(
							'min' => _x( 'At least', '[ADMIN] Options configuration tab when editing add-on', 'yith-woocommerce-product-add-ons' ),
							//'max' => _x( 'A maximum of', '[ADMIN] Options configuration tab when editing add-on', 'yith-woocommerce-product-add-ons' ),
							'exa' => _x( 'Exactly', '[ADMIN] Options configuration tab when editing add-on', 'yith-woocommerce-product-add-ons' ),
						);

						if ( 'max' === $min_max_rule[ $y ] ) { // Max has changed to another field.
							continue;
						}

						if ( $y > 0 && ( 'min' === $min_max_rule[ $y ] || 'exa' === $min_max_rule[ $y ] ) ) {
							$min_max_options = array(
								$min_max_rule[ $y ] => $min_max_options[ $min_max_rule[ $y ] ],
							);
						}

						?>
						<div class="field rule min-exa-rules">
							<?php
							yith_plugin_fw_get_field(
								array(
									'id'      => 'addon-min-max-rule',
									'name'    => 'addon_min_max_rule[]',
									'type'    => 'select',
									'class'   => 'wc-enhanced-select',
									'value'   => $min_max_rule[ $y ],
									'options' => $min_max_options,
								),
								true
							);
							yith_plugin_fw_get_field(
								array(
									'id'    => 'addon-min-max-value',
									'name'  => 'addon_min_max_value[]',
									'type'  => 'number',
									'min'   => '0',
									'value' => $min_max_value[ $y ],
								),
								true
							);
							?>
							<span class="description">
							<?php echo sprintf( _x( '%s', '[ADMIN] Add-on editor > Options configuration option', 'yith-woocommerce-product-add-ons' ), yith_wapo_get_string_by_addon_type( 'options', $addon_type ) ) ?>
						</span>
						</div>
					<?php endfor; ?>
				</div>
                <span class="description">
                    <?php echo yith_wapo_get_string_by_addon_type( 'proceed_purchase_description', $addon_type ) ?>
				    </span>
			</div>
			<!-- End option field -->
			<?php

			continue;
		} elseif ( 'addon-max-rule' === $config_id ) {

         ?>
			<!-- Option field -->
		<div class="field-wrap select-max-rules addon-field-grid" style="display: none;">
			<label for="max-rules"><?php echo yith_wapo_get_string_by_addon_type( 'can_select_max', $addon_type ); ?></label>
			<div id="max-rules" class="field">
				<?php

				$max_value = '';
				$has_max   = array_keys( $min_max_rule, 'max'  );
				if ( $has_max ) {
					$max_value = $min_max_value[ $has_max[0] ];
				}

				?>
					<div class="field rule max-rules">
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'      => 'addon-max-rule',
								'name'    => 'addon_min_max_rule[]',
								'type'    => 'hidden',
								'class'   => '',
								'value'   => 'max',
							),
							true
						);
						yith_plugin_fw_get_field(
							array(
								'id'    => 'addon-min-max-value',
								'name'  => 'addon_min_max_value[]',
								'type'  => 'number',
								'min'   => '0',
								'value' => $max_value,
							),
							true
						);
						?>
						<span class="description">
							<?php echo sprintf( _x( '%s', '[ADMIN] Add-on editor > Options configuration option', 'yith-woocommerce-product-add-ons' ), yith_wapo_get_string_by_addon_type( 'options', $addon_type ) ) ?>
						</span>
					</div>
			</div>
            <span class="description">
					<?php
                    echo yith_wapo_get_string_by_addon_type( 'can_select_max_description', $addon_type );
                    ?>
				</span>
		</div>
		<!-- End option field -->

			<?php
			continue;
		}


		yith_wapo_get_view(
			'addon-editor/addon-field.php',
			array(
				'addon'      => $addon,
				'addon_id'   => $addon_id,
				'addon_type' => $addon_type,
				'config_id'  => $config_id,
				'config_options' => $config_options,
			)
		);

	}
	?>
</div>
