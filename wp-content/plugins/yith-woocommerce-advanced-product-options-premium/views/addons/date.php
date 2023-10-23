<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var object $addon
 * @var string $addon_type
 * @var int    $x
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$years_options = array_combine(range(date("Y", strtotime('+50 years')), 1900), range(date("Y", strtotime('+50 years')), 1900));

?>

<div class="fields">

	<?php
	yith_wapo_get_view(
		'addon-editor/option-common-fields.php',
		array(
			'x'          => $x,
			'addon_type' => $addon_type,
			'addon'      => $addon
		),
        defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''
    );
	?>

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label>
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Date format', 'yith-woocommerce-product-add-ons' );
            ?>
        </label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'      => 'option-date-format',
					'name'    => 'options[date_format][]',
					'type'    => 'select',
					'class'   => 'wc-enhanced-select',
					'value'   => $addon->get_option( 'date_format', $x, 'd/m/Y', false ),
					'options' => array(
                        // translators: Add-on type date option
						'd/m/Y' => esc_html__( 'Day / Month / Year', 'yith-woocommerce-product-add-ons' ),
                        // translators: Add-on type date option
						'm/d/Y' => esc_html__( 'Month / Day / Year', 'yith-woocommerce-product-add-ons' ),
                        // translators: Add-on type date option
						'd.m.Y' => esc_html__( 'Day . Month . Year', 'yith-woocommerce-product-add-ons' ),
					),
				),
				true
			);
			?>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label><?php
            // translators: Add-on type date option
            echo esc_html__( 'Year', 'yith-woocommerce-product-add-ons' );
            ?>
        </label>
		<div class="start-end-year">
            <div class="field">
                <small><?php
                    // translators: Add-on type date option
                    echo strtoupper( esc_html__( 'Start year', 'yith-woocommerce-product-add-ons' ) );
                    ?>
                </small>
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'      => 'option-start-year-' . $x,
                        'name'    => 'options[start_year][]',
                        'class'   => 'micro wc-enhanced-select',
                        'type'    => 'select',
                        'value'   => esc_attr( $addon->get_option( 'start_year', $x, date("Y"), false ) ),
                        'options' => $years_options
                    ),
                    true
                );
                ?>

            </div>
            <div class="field">
                <small>
                    <?php
                    // translators: Add-on type date option
                    echo strtoupper( esc_html__( 'End year', 'yith-woocommerce-product-add-ons' ) );
                    ?>
                </small>
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'      => 'option-end-year-' . $x,
                        'name'    => 'options[end_year][]',
                        'class'   => 'micro wc-enhanced-select',
                        'type'    => 'select',
                        'value'   => esc_attr( $addon->get_option( 'end_year', $x, date("Y"), false ) ),
                        'options' => $years_options
                    ),
                    true
                );
                ?>
            </div>
        </div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
        <label>
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Default date', 'yith-woocommerce-product-add-ons' ) . esc_html( ':' );
            ?>
        </label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'      => 'option-date-default-' . $x,
					'class'   => 'option-date-default wc-enhanced-select',
					'name'    => 'options[date_default][]',
					'type'    => 'select',
					'value'   => $addon->get_option( 'date_default', $x, '', false ),
					'options' => array(
                        // translators: Add-on type date option
                        ''         => esc_html__( 'None', 'yith-woocommerce-product-add-ons' ),
                        // translators: Add-on type date option
                        'today'    => esc_html__( 'Current day', 'yith-woocommerce-product-add-ons' ),
                        // translators: Add-on type date option
                        'tomorrow' => esc_html__( 'Current day', 'yith-woocommerce-product-add-ons' ) . ' + 1',
                        // translators: Add-on type date option
                        'specific' => esc_html__( 'Set a specific day', 'yith-woocommerce-product-add-ons' ),
                        // translators: Add-on type date option
                        'interval' => esc_html__( 'Set a time interval from current day', 'yith-woocommerce-product-add-ons' ),
                        // translators: Add-on type date option
                        'firstavl' => esc_html__( 'First available day', 'yith-woocommerce-product-add-ons' ),
					),
				),
				true
			);
			?>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap option-date-default-day option-date-default-day-<?php echo esc_attr( $x ); ?> addon-field-grid" style="<?php echo $addon->get_option( 'date_default', $x, '', false ) !== 'specific' ? 'display: none;' : ''; ?>">
        <label>
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Specific day', 'yith-woocommerce-product-add-ons' ) . esc_html( ':' );
            ?>
        </label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-date-default-day-' . $x,
					'name'  => 'options[date_default_day][]',
					'type'  => 'datepicker',
					'value' => $addon->get_option( 'date_default_day', $x, '', false ),
				),
				true
			);
			?>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap option-date-default-interval option-date-default-interval-<?php echo esc_attr( $x ); ?> addon-field-grid" style="<?php echo $addon->get_option( 'date_default', $x, '', false ) !== 'interval' ? 'display: none;' : ''; ?>">
        <label>
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'For default date, calculate', 'yith-woocommerce-product-add-ons' ) . esc_html( ':' );
            ?>
        </label>
		<div class="option-date-default-interval">
            <div class="field">
                <?php

                yith_plugin_fw_get_field(
                    array(
                        'id'      => 'option-date-default-interval-num-' . $x,
                        'name'    => 'options[date_default_calculate_num][]',
                        'class'   => 'micro wc-enhanced-select',
                        'type'    => 'select',
                        'value'   => $addon->get_option( 'date_default_calculate_num', $x, '', false ),
                        'options' => array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 ),
                    ),
                    true
                );
                ?>
            </div>
            <div class="field">
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'      => 'option-date-default-interval-type-' . $x,
                        'name'    => 'options[date_default_calculate_type][]',
                        'class'   => 'micro wc-enhanced-select',
                        'type'    => 'select',
                        'value'   => $addon->get_option( 'date_default_calculate_type', $x, '', false ),
                        'options' => array(
                            // translators: Add-on type date option
                            'days'   => esc_html__( 'Days', 'yith-woocommerce-product-add-ons' ),
                            // translators: Add-on type date option
                            'months' => esc_html__( 'Months', 'yith-woocommerce-product-add-ons' ),
                            // translators: Add-on type date option
                            'years'  => esc_html__( 'Years', 'yith-woocommerce-product-add-ons' ),
                        ),
                    ),
                    true
                );
                ?>
            </div>
            <span style="line-height: 35px;">
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'from current day', 'yith-woocommerce-product-add-ons' );
            ?>
        </span>
        </div>
	</div>
	<!-- End option field -->

	<div class="field-wrap addon-field-grid">
        <label>
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Selectable dates', 'yith-woocommerce-product-add-ons' ) . esc_html( ':' );
            ?>
        </label>
		<div class="field">
			<?php
			$selectable_dates_option = $addon->get_option( 'selectable_dates', $x, '', false );
			yith_plugin_fw_get_field(
				array(
					'id'      => 'option-selectable-dates-' . $x,
					'class'   => 'option-selectable-dates wc-enhanced-select',
					'name'    => 'options[selectable_dates][]',
					'type'    => 'select',
					'value'   => $selectable_dates_option,
					'options' => array(
                        // translators: Add-on type date option
                        ''     => esc_html__( 'Set no limits', 'yith-woocommerce-product-add-ons' ),
                        // translators: Add-on type date option
                        'days' => esc_html__( 'Set a range of days', 'yith-woocommerce-product-add-ons' ),
                        // translators: Add-on type date option
                        'date' => esc_html__( 'Set a specific date range', 'yith-woocommerce-product-add-ons' ),
                        // translators: Add-on type date option
                        'before' => esc_html__( 'Disable dates previous to current day', 'yith-woocommerce-product-add-ons' ),
					),
				),
				true
			);
			?>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap option-selectable-days-ranges addon-field-grid" style="<?php echo 'days' === $selectable_dates_option ? '' : 'display: none;'; ?>">
        <label>
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Selectable days ranges', 'yith-woocommerce-product-add-ons' );
            ?>
        </label>
		<div class="option-selectable-days-ranges">
            <div class="field datepicker-micro">
                <small><?php
                    // translators: Add-on type date option
                    echo esc_html__( 'MIN', 'yith-woocommerce-product-add-ons' );
                    ?>
                </small>
                <!--<input type="text" name="options[days_min][]" id="option-days-min" value="<?php echo esc_attr( $addon->get_option( 'days_min', $x, '', false ) ); ?>" class="micro">-->
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'    => 'option-days_min-' . $x,
                        'name'  => 'options[days_min][]',
                        'type'  => 'text',
                        'value' => $addon->get_option( 'days_min', $x, '', false ),
                    ),
                    true
                );
                ?>
            </div>
            <div class="field datepicker-micro">
                <small><?php
                    // translators: Add-on type date option
                    echo esc_html__( 'MAX', 'yith-woocommerce-product-add-ons' );
                    ?>
                </small>
                <!--<input type="text" name="options[days_max][]" id="option-days-max" value="<?php echo esc_attr( $addon->get_option( 'days_max', $x, '', false ) ); ?>" class="micro">-->
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'    => 'option-days_max-' . $x,
                        'name'  => 'options[days_max][]',
                        'type'  => 'text',
                        'value' => $addon->get_option( 'days_max', $x, '', false ),
                    ),
                    true
                );
                ?>
            </div>
        </div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap option-selectable-date-ranges addon-field-grid" style="<?php echo 'date' === $selectable_dates_option ? '' : 'display: none;'; ?>">
        <label>
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Selectable date ranges', 'yith-woocommerce-product-add-ons' );
            ?>
        </label>
		<div class="option-selectable-date-ranges">
            <div class="field datepicker-micro">

                <small>
                    <?php
                    // translators: Add-on type date option
                    echo strtoupper( esc_html__( 'min', 'yith-woocommerce-product-add-ons' ) );
                    ?>
                </small>
                <!--<input type="text" name="options[days_min][]" id="option-days-min" value="<?php echo esc_attr( $addon->get_option( 'days_min', $x, '', false ) ); ?>" class="micro">-->
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'    => 'option-date_min-' . $x,
                        'name'  => 'options[date_min][]',
                        'type'  => 'datepicker',
                        'value' => $addon->get_option( 'date_min', $x, '', false ),
                    ),
                    true
                );
                ?>
            </div>
            <div class="field datepicker-micro">
                <small><?php
                    // translators: Add-on type date option
                    echo strtoupper( esc_html__( 'max', 'yith-woocommerce-product-add-ons' ) );
                    ?>
                </small>
                <!--<input type="text" name="options[days_max][]" id="option-days-max" value="<?php echo esc_attr( $addon->get_option( 'days_max', $x, '', false ) ); ?>" class="micro">-->
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'    => 'option-date_max-' . $x,
                        'name'  => 'options[date_max][]',
                        'type'  => 'datepicker',
                        'value' => $addon->get_option( 'date_max', $x, '', false ),
                    ),
                    true
                );
                ?>
            </div>
        </div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
        <label for="option-enable-disable-days-<?php echo esc_attr( $x ); ?>">
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Enable / disable specific days', 'yith-woocommerce-product-add-ons' );
            ?>
        </label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-enable-disable-days-' . $x,
					'name'  => 'options[enable_disable_days][]',
					'class' => 'enabler',
					'type'  => 'onoff',
					'value' => $addon->get_option( 'enable_disable_days', $x, '', false ),
				),
				true
			);
			?>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap enabled-by-option-enable-disable-days-<?php echo esc_attr( $x ); ?> addon-field-grid" style="display: none;">
        <label>
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Rule type', 'yith-woocommerce-product-add-ons' );
            ?>
        </label>
		<div id="disable-date-rules-<?php echo esc_attr( $x ); ?>" class="disable-date-rules">
			<div class="field rules-type" style="margin-bottom: 10px;">
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'      => 'option-enable-disable-days-type-' . $x,
						'name'    => 'options[enable_disable_date_rules][]',
						'class'   => 'micro wc-enhanced-select',
						'type'    => 'select',
						'value'   => $addon->get_option( 'enable_disable_date_rules', $x, 'enable', false ),
						'options' => array(
                            // translators: Add-on type date option
                            'enable'  => esc_html__( 'Enable', 'yith-woocommerce-product-add-ons' ),
                            // translators: Add-on type date option
                            'disable' => esc_html__( 'Disable', 'yith-woocommerce-product-add-ons' ),
						),
					),
					true
				);
				?>
                <span style="line-height: 35px;" class="rules-type-label">
                    <?php
                    // translators: Add-on type date option
                    echo esc_html__( 'these dates in calendar', 'yith-woocommerce-product-add-ons' );
                    ?>
                </span>
			</div>
			<div id="date-rules-<?php echo esc_attr( $x ); ?>" class="date-rules" style="clear: both;">
				<div class="date-rules-container">
				<?php
					$date_rules_count = count( (array) $addon->get_option( 'date_rule_what', $x, '', false ) );
				for ( $y = 0; $y < $date_rules_count; $y++ ) :
					$date_rule_what = $addon->get_option( 'date_rule_what', $x, 'enable', false )[ $y ];
					?>
					<div class="rule" style="margin-bottom: 10px;">
						<div class="field what">
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'      => 'date-rule-what-' . $x . '-' . $y,
								'name'    => 'options[date_rule_what][' . $x . '][]',
								'class'   => 'micro select_what wc-enhanced-select',
								'type'    => 'select',
								'value'   => $date_rule_what,
								'options' => array(
                                    // translators: Add-on type date option
                                    'days'     => esc_html__( 'Days', 'yith-woocommerce-product-add-ons' ),
                                    // translators: Add-on type date option
                                    'daysweek' => esc_html__( 'Days of the week', 'yith-woocommerce-product-add-ons' ),
                                    // translators: Add-on type date option
                                    'months'   => esc_html__( 'Months', 'yith-woocommerce-product-add-ons' ),
                                    // translators: Add-on type date option
                                    'years'    => esc_html__( 'Years', 'yith-woocommerce-product-add-ons' ),
								),
							),
							true
						);
						?>
						</div>

						<div class="field days" <?php echo 'daysweek' !== $date_rule_what && 'months' !== $date_rule_what && 'years' !== $date_rule_what ? '' : 'style="display: none;"'; ?>>
							<?php
							yith_plugin_fw_get_field(
								array(
									'id'    => 'date-rule-value-days-' . $x . '-' . $y,
									'name'  => 'options[date_rule_value_days][' . $x . '][' . $y . ']',
									'type'  => 'datepicker',
									'value' => isset( $addon->get_option( 'date_rule_value_days', $x, '', false )[ $y ] ) ? $addon->get_option( 'date_rule_value_days', $x, '', false )[ $y ] : '',
									'data'  => array(
										'date-format' => 'yy-mm-dd',
									),
								),
								true
							);
							?>
						</div>
						<div class="field daysweek" <?php echo 'daysweek' === $date_rule_what ? '' : 'style="display: none;"'; ?>>
							<?php
							yith_plugin_fw_get_field(
								array(
									'id'       => 'date-rule-value-daysweek-' . $x . '-' . $y,
									'name'     => 'options[date_rule_value_daysweek][' . $x . '][' . $y . ']',
									'type'     => 'select',
									'multiple' => true,
									'class'    => 'wc-enhanced-select',
									'options'  => array(
                                        // translators: Add-on type date option
                                        '1' => esc_html__( 'Monday', 'yith-woocommerce-product-add-ons' ),
										'2' => esc_html__( 'Tuesday', 'yith-woocommerce-product-add-ons' ),
										'3' => esc_html__( 'Wednesday', 'yith-woocommerce-product-add-ons' ),
										'4' => esc_html__( 'Thursday', 'yith-woocommerce-product-add-ons' ),
										'5' => esc_html__( 'Friday', 'yith-woocommerce-product-add-ons' ),
										'6' => esc_html__( 'Saturday', 'yith-woocommerce-product-add-ons' ),
										'0' => esc_html__( 'Sunday', 'yith-woocommerce-product-add-ons' ),
									),
									'value'    => isset( $addon->get_option( 'date_rule_value_daysweek', $x, '', false )[ $y ] ) ? $addon->get_option( 'date_rule_value_daysweek', $x, '', false )[ $y ] : '',
								),
								true
							);
							?>
						</div>

						<div class="field months" <?php echo 'months' === $date_rule_what ? '' : 'style="display: none;"'; ?>>
							<?php
							yith_plugin_fw_get_field(
								array(
									'id'       => 'date-rule-value-months-' . $x . '-' . $y,
									'name'     => 'options[date_rule_value_months][' . $x . '][' . $y . ']',
									'type'     => 'select',
									'multiple' => true,
									'class'    => 'wc-enhanced-select',
									'options'  => array(
                                        // translators: Add-on type date option
                                        '1'  => esc_html__( 'January', 'yith-woocommerce-product-add-ons' ),
										'2'  => esc_html__( 'February', 'yith-woocommerce-product-add-ons' ),
										'3'  => esc_html__( 'March', 'yith-woocommerce-product-add-ons' ),
										'4'  => esc_html__( 'April', 'yith-woocommerce-product-add-ons' ),
										'5'  => esc_html__( 'May', 'yith-woocommerce-product-add-ons' ),
										'6'  => esc_html__( 'June', 'yith-woocommerce-product-add-ons' ),
										'7'  => esc_html__( 'July', 'yith-woocommerce-product-add-ons' ),
										'8'  => esc_html__( 'August', 'yith-woocommerce-product-add-ons' ),
										'9'  => esc_html__( 'September', 'yith-woocommerce-product-add-ons' ),
										'10' => esc_html__( 'October', 'yith-woocommerce-product-add-ons' ),
										'11' => esc_html__( 'November', 'yith-woocommerce-product-add-ons' ),
										'12' => esc_html__( 'December', 'yith-woocommerce-product-add-ons' ),
									),
									'value'    => isset( $addon->get_option( 'date_rule_value_months', $x, '', false )[ $y ] ) ? $addon->get_option( 'date_rule_value_months', $x, '', false )[ $y ] : '',
								),
								true
							);
							?>
						</div>

						<div class="field years" <?php echo 'years' === $date_rule_what ? '' : 'style="display: none;"'; ?>>
							<?php
							$years = array();
							$datey = gmdate( 'Y' );
							for ( $yy = $datey; $yy < $datey + 10; $yy++ ) {
								$years[ $yy ] = $yy;
							}
							yith_plugin_fw_get_field(
								array(
									'id'       => 'date-rule-value-years' . $x . '-' . $y,
									'name'     => 'options[date_rule_value_years][' . $x . '][' . $y . ']',
									'type'     => 'select',
									'multiple' => true,
									'class'    => 'wc-enhanced-select',
									'options'  => $years,
									'value'    => isset( $addon->get_option( 'date_rule_value_years', $x, '' )[ $y ] ) ? $addon->get_option( 'date_rule_value_years', $x, '', false )[ $y ] : '',
								),
								true
							);
							?>
						</div>

						<img src="<?php echo esc_attr( YITH_WAPO_URL ); ?>/assets/img/delete.png" class="delete-rule">

						<div class="clear"></div>
					</div>
				<?php endfor; ?>
				</div>

				<div id="add-date-rule" class="add-date-rule" style="clear: both;"><a href="#">+ <?php echo esc_html__( 'Add rule', 'yith-woocommerce-product-add-ons' ); ?></a></div>

			</div>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label for="option-show-time-selector-<?php echo esc_attr( $x ); ?>">
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Show time selector', 'yith-woocommerce-product-add-ons' );
            ?>
        </label>
		<div class="field">
			<?php

            $show_time_selector = $addon->get_option( 'show_time_selector', $x, '', false );

			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-show-time-selector-' . $x,
					'name'  => 'options[show_time_selector][]',
					'class' => 'enabler show_time_selector',
					'type'  => 'onoff',
					'value' => $show_time_selector,
				),
				true
			);
			?>
		</div>
	</div>
	<!-- End option field -->

    <?php
    $time_selector_enabled_by = wc_string_to_bool( $show_time_selector ) ? '' : 'disabled-enabled-by';
    ?>

	<!-- Option field -->
	<div class="field-wrap enable-time-slots enabled-by-option-show-time-selector-<?php echo esc_attr( $x ); ?> addon-field-grid <?php echo esc_html( $time_selector_enabled_by ); ?>" style="display: none;">
		<label for="option-enable-time-slots-<?php echo esc_attr( $x );?>">
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Enable / disable time slots', 'yith-woocommerce-product-add-ons' );
            ?>
        </label>
		<div class="field">
			<?php
            $enable_time_slots = $addon->get_option( 'enable_time_slots', $x, '', false );

			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-enable-time-slots-' . $x,
					'name'  => 'options[enable_time_slots][]',
					'class' => 'enabler enable_time_slots',
					'type'  => 'onoff',
					'value' => $enable_time_slots,
				),
				true
			);
			?>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap time-slots-type enabled-by-option-enable-time-slots-<?php echo esc_attr( $x ); ?> addon-field-grid" style="display: none;">
		<label>
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Rule type', 'yith-woocommerce-product-add-ons' );
            ?>
        </label>
		<div id="enable-disable-time-slots-<?php echo esc_attr( $x ); ?>" class="time-slots-container">
			<div class="field rules-type" style="margin-bottom: 10px;">
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'      => 'option-time-slots-type-' . $x,
						'name'    => 'options[time_slots_type][]',
						'class'   => 'micro wc-enhanced-select',
						'type'    => 'select',
						'value'   => $addon->get_option( 'time_slots_type', $x, 'enable', false ),
						'options' => array(
							'enable'  => __( 'Enable', 'yith-woocommerce-product-add-ons' ),
							'disable' => __( 'Disable', 'yith-woocommerce-product-add-ons' ),
						),
					),
					true
				);
				?>
				<span style="line-height: 35px;" class="rules-type-label">
                    <?php
                    // translators: Add-on type date option
                    echo esc_html__( 'the following time slot(s)', 'yith-woocommerce-product-add-ons' ) . ':';
                    ?>
                </span>
			</div>
			<div id="time-slots-<?php echo esc_attr( $x ); ?>" class="time-slots" style="clear: both;">

				<?php
					$time_slots_count = count( (array) $addon->get_option( 'time_slot_from', $x, '', false ) );
				for ( $y = 0; $y < $time_slots_count; $y++ ) :
					?>
					<div class="slot">

						<span style="line-height: 35px; margin-right: 10px; float: left;"><?php
                            // translators: Add-on type date option
                            echo esc_html__( 'From', 'yith-woocommerce-product-add-ons' );
                            ?>
                        </span>
						<div class="time-slot-from-container">
							<div class="field time-slot-from">
								<?php
								yith_plugin_fw_get_field(
									array(
										'id'      => 'time-slot-from-' . $x . '-' . $y,
										'name'    => 'options[time_slot_from][' . $x . '][]',
										'class'   => 'micro slot-enhanced select_from wc-enhanced-select',
										'type'    => 'select',
										'value'   => isset( $addon->get_option( 'time_slot_from', $x, '', false )[ $y ] ) ? $addon->get_option( 'time_slot_from', $x, '', false )[ $y ] : '',
										'options' => array(
											'1'  => '01',
											'2'  => '02',
											'3'  => '03',
											'4'  => '04',
											'5'  => '05',
											'6'  => '06',
											'7'  => '07',
											'8'  => '08',
											'9'  => '09',
											'10' => '10',
											'11' => '11',
											'12' => '12',
										),
									),
									true
								);
								?>
							</div>

							<span class="time-slot-hour-separator">:</span>

							<div class="field time-slot-from-min">
								<?php
								$minutes_array = array();
								for ( $mn = 0; $mn < 60; $mn++ ) {
									$minutes_array[ $mn ] = str_pad( $mn, 2, '0', STR_PAD_LEFT );
								}
								yith_plugin_fw_get_field(
									array(
										'id'      => 'time-slot-from-min-' . $x . '-' . $y,
										'name'    => 'options[time_slot_from_min][' . $x . '][]',
										'class'   => 'micro slot-enhanced select_from_min wc-enhanced-select',
										'type'    => 'select',
										'value'   => isset( $addon->get_option( 'time_slot_from_min', $x, '', false )[ $y ] ) ? $addon->get_option( 'time_slot_from_min', $x, '', false )[ $y ] : '',
										'options' => $minutes_array,
									),
									true
								);
								?>
							</div>

							<div class="field time-slot-from-type">
								<?php
								yith_plugin_fw_get_field(
									array(
										'id'      => 'time-slot-from-type-' . $x . '-' . $y,
										'name'    => 'options[time_slot_from_type][' . $x . '][]',
										'class'   => 'micro slot-enhanced wc-enhanced-select',
										'type'    => 'select',
										'value'   => isset( $addon->get_option( 'time_slot_from_type', $x, '', false )[ $y ] ) ? $addon->get_option( 'time_slot_from_type', $x, '', false )[ $y ] : '',
										'options' => array(
											'am' => 'am',
											'pm' => 'pm',
										),
									),
									true
								);
								?>
							</div>
						</div>


						<span style="line-height: 35px; margin-right: 10px; float: left;">
                            <?php
                            // translators: Add-on type date option
                            echo esc_html__( 'To', 'yith-woocommerce-product-add-ons' );
                            ?>
                        </span>
						<div class="time-slot-to-container">
							<div class="field time-slot-to">
								<?php
								yith_plugin_fw_get_field(
									array(
										'id'      => 'time-slot-to-' . $x . '-' . $y,
										'name'    => 'options[time_slot_to][' . $x . '][]',
										'class'   => 'micro slot-enhanced select_to wc-enhanced-select',
										'type'    => 'select',
										'value'   => isset( $addon->get_option( 'time_slot_to', $x, '', false )[ $y ] ) ? $addon->get_option( 'time_slot_to', $x, '', false )[ $y ] : '',
										'options' => array(
											'1'  => '01',
											'2'  => '02',
											'3'  => '03',
											'4'  => '04',
											'5'  => '05',
											'6'  => '06',
											'7'  => '07',
											'8'  => '08',
											'9'  => '09',
											'10' => '10',
											'11' => '11',
											'12' => '12',
										),
									),
									true
								);
								?>
							</div>

							<span class="time-slot-hour-separator">:</span>

							<div class="field time-slot-to-min">
								<?php
								$minutes_array = array();
								for ( $mn = 0; $mn < 60; $mn++ ) {
									$minutes_array[ $mn ] = str_pad( $mn, 2, '0', STR_PAD_LEFT );
								}
								yith_plugin_fw_get_field(
									array(
										'id'      => 'time-slot-to-min-' . $x . '-' . $y,
										'name'    => 'options[time_slot_to_min][' . $x . '][]',
										'class'   => 'micro slot-enhanced select_to_min wc-enhanced-select',
										'type'    => 'select',
										'value'   => isset( $addon->get_option( 'time_slot_to_min', $x, '', false )[ $y ] ) ? $addon->get_option( 'time_slot_to_min', $x, '', false )[ $y ] : '',
										'options' => $minutes_array,
									),
									true
								);
								?>
							</div>

							<div class="field time-slot-to-type">
								<?php
								yith_plugin_fw_get_field(
									array(
										'id'      => 'time-slot-to-type-' . $x . '-' . $y,
										'name'    => 'options[time_slot_to_type][' . $x . '][]',
										'class'   => 'micro slot-enhanced wc-enhanced-select',
										'type'    => 'select',
										'value'   => isset( $addon->get_option( 'time_slot_to_type', $x, '', false )[ $y ] ) ? $addon->get_option( 'time_slot_to_type', $x, '', false )[ $y ] : '',
										'options' => array(
											'am' => 'am',
											'pm' => 'pm',
										),
									),
									true
								);
								?>
							</div>
						</div>


						<img src="<?php echo esc_attr( YITH_WAPO_URL ); ?>/assets/img/delete.png" class="delete-slot">

						<div class="clear"></div>
					</div>
				<?php endfor; ?>

			</div>
			<div id="add-time-slot" class="add-time-slot" style="clear: both;"><a href="#">+ <?php echo esc_html__( 'Add time slot', 'yith-woocommerce-product-add-ons' ); ?></a></div>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap time-interval-option enabled-by-option-show-time-selector-<?php echo esc_attr( $x ); ?> addon-field-grid" style="display: none;">
		<label>
            <?php
            // translators: Add-on type date option
            echo esc_html__( 'Time interval', 'yith-woocommerce-product-add-ons' );
            ?>
        </label>
		<div id="time-interval-<?php echo esc_attr( $x ); ?>" class="time-interval">

			<div class="field time-interval">
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'      => 'time-interval-' . $x,
						'name'    => 'options[time_interval][' . $x . ']',
						'class'   => 'micro select_interval wc-enhanced-select',
						'type'    => 'select',
						'value'   => $addon->get_option( 'time_interval', $x, '10', false ),
						'options' => array(
							'1'  => '1',
							'2'  => '2',
							'3'  => '3',
							'4'  => '4',
							'5'  => '5',
							'6'  => '6',
							'7'  => '7',
							'8'  => '8',
							'9'  => '9',
							'10' => '10',
							'11' => '11',
							'12' => '12',
							'13' => '13',
							'14' => '14',
							'15' => '15',
							'16' => '16',
							'17' => '17',
							'18' => '18',
							'19' => '19',
							'20' => '20',
							'21' => '21',
							'22' => '22',
							'23' => '23',
							'24' => '24',
							'25' => '25',
							'26' => '26',
							'27' => '27',
							'28' => '28',
							'29' => '29',
							'30' => '30',
						),
					),
					true
				);
				?>
			</div>

			<div class="field time-interval-type">
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'      => 'time-interval-type-' . $x,
						'name'    => 'options[time_interval_type][' . $x . ']',
						'class'   => 'wc-enhanced-select',
						'type'    => 'select',
						'value'   => $addon->get_option( 'time_interval_type', $x, 'minutes', false ),
						'options' => array(
                            // translators: Add-on type date option
                            'seconds' => __( 'Seconds', 'yith-woocommerce-product-add-ons' ),
							'minutes' => __( 'Minutes', 'yith-woocommerce-product-add-ons' ),
							'hours'   => __( 'Hours', 'yith-woocommerce-product-add-ons' ),
						),
					),
					true
				);
				?>
			</div>

		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label for="option-required-<?php echo esc_attr( $x ); ?>"><?php
            // translators: Add-on type date option
            echo esc_html__( 'Required', 'yith-woocommerce-product-add-ons' ) . esc_html( ':' );
            ?>
        </label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-required-' . $x,
					'name'  => 'options[required][' . $x . ']',
					'type'  => 'onoff',
					'value' => $addon->get_option( 'required', $x, 'no', false ),
				),
				true
			);
			?>
		</div>
	</div>
	<!-- End option field -->

</div>
