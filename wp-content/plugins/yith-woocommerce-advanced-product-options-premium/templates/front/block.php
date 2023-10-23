<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var YITH_WAPO_Block $block
 * @var array  $addons
 * @var int    $x
 * @var string $style_addon_titles
 * @var string $style_addon_background
 * @var string $currency
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$block_classes       = apply_filters( 'yith_wapo_block_classes', 'yith-wapo-block', $block );

$required_message    = get_option( 'yith_wapo_required_option_text', _x( 'This option is required.', '[FRONT] Text to show when an option is required', 'yith-woocommerce-product-add-ons' ) );
$setting_hide_images = get_option( 'yith_wapo_hide_images', 'no' );
$hide_title_images   = wc_string_to_bool( get_option( 'yith-wapo-hide-titles-and-images', 'no' ) );

$html_types = array( 'html_heading', 'html_separator', 'html_text' );

?>

<div id="yith-wapo-block-<?php echo esc_attr( $block->id ); ?>" class="<?php echo esc_attr( $block_classes ); ?>">

	<?php
	foreach ( $addons as $key => $addon ) :
        /**
         * @var YITH_WAPO_Addon $addon
         */
		if ( yith_wapo_is_addon_type_available( $addon->type ) ) :

			$settings = $addon->get_formatted_settings();
			extract($settings );

            $toggle_addon   = $show_as_toggle !== 'no' ? 'wapo-toggle' : '';
            $toggle_status  = 'toggle-closed';
            $toggle_default = 'default-closed';

            if( 'no' !== $show_as_toggle ) {

                switch ( $show_as_toggle ) {

                    case 'no-toggle' :
                        $toggle_addon = '';
                        break;

                    case 'open' :
                        $toggle_addon   = 'wapo-toggle';
                        $toggle_status = 'toggle-open';
                        $toggle_default = 'default-open';

                        break;
                    case 'closed' :
                        $toggle_addon   = 'wapo-toggle';
                        $toggle_status = 'toggle-closed';
                        $toggle_default = 'default-closed';
                        break;
                }
            //General option.
            } elseif ( get_option( 'yith_wapo_show_in_toggle' ) === 'yes' ) {
                $toggle_addon   = 'wapo-toggle';
                $toggle_status  = get_option( 'yith_wapo_show_toggle_opened' ) === 'yes' ? 'toggle-open' : 'toggle-closed';
                $toggle_default = get_option( 'yith_wapo_show_toggle_opened' ) === 'yes' ? 'default-open' : 'default-closed';
            }

            /*if ( 'no' !== $show_as_toggle ) {
                $toggle_status  = $show_as_toggle === 'open' ? 'toggle-open' : 'toggle-closed';
                $toggle_default = $show_as_toggle === 'open' ? 'default-open' : 'default-closed';
            }*/

            if ( 'toggle' === $toggle_addon && '' === $addon_title ) {
                $addon_title = __( 'No title', 'yith-woocommerce-product-add-ons' );
            }

			// Advanced settings.
			$min_max_values         = array(
				'min' => '',
				'max' => '',
				'exa' => '',
			);
			if ( 'yes' === $enable_min_max && is_array( $min_max_rule ) ) {
				$min_max_rule_count = count( $min_max_rule );
				for ( $y = 0; $y < $min_max_rule_count; $y++ ) {
					$min_max_values[ $min_max_rule[ $y ] ] = $min_max_value[ $y ];
				}
			}

			$is_numbers_min_max_enabled = wc_string_to_bool( $enable_min_max_numbers );
			$hide_options_images        = wc_string_to_bool( $hide_option_images );
            $show_in_a_grid             = wc_string_to_bool( $show_in_a_grid );

            $required_addon    = false;

			if ( 'yes' === apply_filters( 'yith_wapo_addons_settings_required', $addon_required, $addon ) || 'select' === $addon_type && 'yes' === $enable_min_max && ( ! empty( $min_max_values['min'] ) || ! empty( $min_max_values['max'] ) || ! empty( $min_max_values['exa'] ) ) ) {
				$required_addon = true;
			}

			// Conditional logic.
			$enable_rules       = wc_string_to_bool( $enable_rules );
			$conditional_logic_class = '';
			if ( $enable_rules ) {

				$conditional_rule_addon    = apply_filters( 'yith_wapo_conditional_rule_addon', (array) $conditional_rule_addon );
				$conditional_logic_rules   = ! empty( $conditional_rule_addon );
				$conditional_rule_addon_is = ! empty( $conditional_rule_addon ) ? (array) $conditional_rule_addon_is : array();

				// Variations.
				$apply_variation_rule        = wc_string_to_bool( $enable_rules_variations );
				$conditional_logic_variation = apply_filters( 'yith_wapo_conditional_rule_variation', (array) $addon->get_setting( 'conditional_rule_variations' ) );
				$variations_logic            = $apply_variation_rule && ! empty( $conditional_logic_variation );
				if ( $apply_variation_rule ) {
					if ( ! $conditional_set_conditions ) {
						$conditional_rule_addon = false;
					}
				}

				if ( $conditional_logic_rules || $variations_logic ) { // If conditions or variations, apply the conditional logic.
					$conditional_logic_class = 'conditional_logic';
				} else {
					$enable_rules = false;
				}
			}

			$addon_classes       = apply_filters(
				'yith_wapo_addon_classes',
				'yith-wapo-addon yith-wapo-addon-type-' . esc_attr( $addon_type ) . ' ' . esc_attr( $toggle_addon ) . ' ' . esc_attr( $toggle_default ) . ' ' . esc_attr( $toggle_status ) . ' ' .
				esc_attr( $conditional_logic_class ) . ' ' . esc_attr( 'yes' === $sell_individually ? 'sell_individually' : '' ) . ' ' .
                esc_attr( $is_numbers_min_max_enabled ? 'numbers-check' : '' ) . ' ' . esc_attr( '' === $addon_title && ( ! in_array( $addon_type, $html_types ) ) ? 'empty-title' : '' ),
				$addon
			);



	?>

			<div id="yith-wapo-addon-<?php echo esc_attr( $addon->id ); ?>"
				class="<?php echo esc_attr( $addon_classes ); ?>"
				data-min="<?php echo esc_attr( $min_max_values['min'] ); ?>"
				data-max="<?php echo esc_attr( $min_max_values['max'] ); ?>"
				data-exa="<?php echo esc_attr( $min_max_values['exa'] ); ?>"
				data-addon-type="<?php echo esc_attr( $addon->type ); ?>"
                 <?php
                 if ( $is_numbers_min_max_enabled && '' !== $numbers_min ) { ?>
                    data-numbers-min="<?php echo esc_attr( $numbers_min ); ?>"
                 <?php }
                if ( $is_numbers_min_max_enabled && '' !== $numbers_max ) { ?>
                    data-numbers-max="<?php echo esc_attr( $numbers_max ); ?>"
                <?php } ?>
				<?php if ( $enable_rules ) : ?>
				data-addon_id="<?php echo esc_attr( $addon->id ); ?>"
				data-conditional_logic_display="<?php echo esc_attr( $conditional_logic_display ); ?>"
				data-conditional_logic_display_if="<?php echo esc_attr( $conditional_logic_display_if ); ?>"
				data-conditional_rule_addon="<?php echo ( $conditional_rule_addon ) ? esc_attr( implode( '|', $conditional_rule_addon ) ) : ''; ?>"
				data-conditional_rule_addon_is="<?php echo esc_attr( implode( '|', $conditional_rule_addon_is ) ); ?>"
				data-conditional_rule_variations="<?php echo ( $variations_logic ) ? esc_attr( implode( '|', $conditional_logic_variation ) ) : ''; ?>"
				<?php endif; ?>
				style="
				<?php
					echo 'background-color: ' . esc_attr( $style_addon_background ) . ';';
					echo $enable_rules ? ' display: none;' : '';
				?>
					">

                <div class="addon-header">
                    <?php
                    if ( ! $hide_title_images && 'yes' === $show_image && '' !== $addon_image ) :
                        ?>
                        <div class="title-image">
                            <img src="<?php echo esc_attr( $addon_image ); ?>">
                        </div>
                    <?php
                    endif;
                    ?>
				<?php if ( ! $hide_title_images && ! in_array( $addon_type, $html_types ) ) : ?>
					<<?php echo esc_attr( $style_addon_titles ); ?> class="wapo-addon-title <?php echo esc_attr( $toggle_status ); ?>">
                        <span><?php echo apply_filters( 'yith_wapo_addon_display_title', esc_html( $addon_title ) , $addon_title ); ?></span>
					<?php echo $required_addon ? '<span class="required">*</span>' : ''; ?>
					</<?php echo esc_attr( $style_addon_titles ); ?>>
				<?php endif; ?>

                </div>

				<?php

				if ( in_array( $addon_type, $html_types ) ) {
					wc_get_template(
                        '/front/addons/' . $addon_type . '.php',
						apply_filters(
							'yith_wapo_addon_html_args',
							array(
								'addon'               => $addon,
								'settings'            => $settings,
							),
							$addon
						),
						'',
						YITH_WAPO_DIR . '/templates'
					);
				} else {

                    $options_width_select_css = 'width: ' . ( $select_width ) . '% !important;';
                    $per_row                  = 'per-row-' . esc_attr( $options_per_row );

                    $options_total = is_array( $addon->options ) && isset( array_values( $addon->options )[0] ) ? count( array_values( $addon->options )[0] ) : 1;


                    if ( 'select' === $addon->type ) {
                        echo '<div class="options-container' . ' ' . esc_attr( $toggle_default ) . '">';
                        if ( '' !== $addon_description ) {
                            echo '<p class="wapo-addon-description">' . stripslashes( $addon_description ) . '</p>'; // phpcs:ignore
                        }
                        echo '<div class="options ' . ' ' . $per_row . ' ' . ( $show_in_a_grid ? ' grid' : '' ) . '"
                         style="' . esc_attr( $options_width_select_css ) . '">';

						if ( ! $hide_options_images ) {
							echo '<div class="option-image"></div>';
						}

						wc_get_template(
                            '/front/addons/select.php',
							apply_filters(
								'yith_wapo_addon_select_args',
								array(
									'addon'                    => $addon,
									'setting_hide_images'      => $setting_hide_images,
									'required_message'         => $required_message,
									'settings'                 => $settings,
									'options_total'            => $options_total,
									'options_width_select_css' => $options_width_select_css,
									'currency'                 => $currency
								),
								$addon
							),
							'',
							YITH_WAPO_DIR . '/templates'
						);

					} else {

                        $grid_styles = $addon->get_grid_rules();

                        echo '<div class="options-container' . ' ' . esc_attr( $toggle_default ) . '">';
                        if ( '' !== $addon_description ) {
                            echo '<p class="wapo-addon-description">' . stripslashes( $addon_description ) . '</p>'; // phpcs:ignore
                        }
                        echo '<div class="options ' . ' ' . $per_row . ' ' . ( $show_in_a_grid ? ' grid' : '' ) . '"
                            style="' . wp_kses_post( $grid_styles ) . '";
                        >';

						for ( $x = 0; $x < $options_total; $x++ ) {
							if ( file_exists( YITH_WAPO_DIR . '/templates/front/addons/' . $addon->type . '.php' ) ) {

								$enabled = $addon->get_option( 'addon_enabled', $x, 'yes', false );

								if ( wc_string_to_bool( $enabled ) ) {

									$option_show_image  = $addon->get_option( 'show_image', $x, false );
									$option_image       = $option_show_image ? $addon->get_option( 'image', $x ) : '';
									$option_description = $addon->get_option( 'description', $x );

									// todo: improve price calculation.
									$price_method = $addon->get_option( 'price_method', $x, 'free', false );
									$price_type   = $addon->get_option( 'price_type', $x, 'fixed', false );
									$price        = $addon->get_price( $x );
									$price_sale   = $addon->get_sale_price( $x );
									$price        = floatval( str_replace( ',', '.', $price ) );
									$price_sale   = '' !== $price_sale ? floatval( str_replace( ',', '.', $price_sale ) ) : '';

									// todo: improve price calculation.
									if ( 'free' === $price_method ) {
										$price      = '0';
										$price_sale = '0';
									} elseif ( 'decrease' === $price_method ) {
										$price      = $price > 0 ? - $price : 0;
										$price_sale = '0';
									} elseif ( 'product' === $price_method ) {
										$price      = $price > 0 ? $price : 0;
										$price_sale = '0';
									} else {
										$price      = $price > 0 ? $price : '0';
										$price_sale = $price_sale >= 0 ? $price_sale : 'undefined';
									}

									$addon_image_position = $addon->get_image_position( $x );

									wc_get_template(
                                        '/front/addons/' . $addon_type . '.php',
										apply_filters(
											'yith_wapo_addon_arg',
											array(
												'addon'               => $addon,
												'x'                   => $x,
												'setting_hide_images' => $setting_hide_images,
												'required_message'    => $required_message,
												'settings'            => $settings,
												// Addon options.
												'option_description'   => $option_description,
												'addon_image_position' => $addon_image_position,
												'option_image'         => is_ssl() ? str_replace( 'http://', 'https://', $option_image ) : $option_image,
												'price'                => $price,
												'price_method'         => $price_method,
												'price_sale'           => $price_sale,
												'price_type'           => $price_type,
                                                'currency'             => $currency,
											),
											$addon
										),
										'',
										YITH_WAPO_DIR . '/templates'
									);
								}
							}
						}
					}

					if ( ( 'select' === $addon->type || 'radio' === $addon->type ) && 'yes' === $sell_individually ) {
						echo '<input type = "hidden" name = "yith_wapo_sell_individually[' . esc_attr( $addon->id ) . ']" value = "yes" >';
					}
					?>
					</div>
                    <?php
                    if ( 'yes' === $addon_required || 'yes' === $enable_min_max ) :
                        ?>
                        <div class="min-error" style="display: none;">
                            <span class="min-error-message"></span>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>
					<?php
				}
				?>

			</div>

		<?php endif; ?>
	<?php endforeach; ?>

</div>
