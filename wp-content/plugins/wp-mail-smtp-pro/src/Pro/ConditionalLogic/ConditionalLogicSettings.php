<?php

namespace WPMailSMTP\Pro\ConditionalLogic;

use WPMailSMTP\WP;

/**
 * Class ConditionalLogicSettings.
 *
 * @since 3.7.0
 */
class ConditionalLogicSettings {

	/**
	 * Conditions properties.
	 *
	 * @since 3.7.0
	 *
	 * @var array
	 */
	private $properties;

	/**
	 * Constructor.
	 *
	 * @since 3.7.0
	 *
	 * @param array $properties Conditions properties.
	 */
	public function __construct( $properties = [] ) {

		$this->properties = $properties;
	}

	/**
	 * Register hooks.
	 *
	 * @since 3.7.0
	 */
	public function hooks() {

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', [ $this, 'enqueue_assets' ], 5 );
	}

	/**
	 * Enqueue required JS and CSS.
	 *
	 * @since 3.7.0
	 */
	public function enqueue_assets() {

		$min = WP::asset_min();

		wp_enqueue_script(
			'wp-mail-smtp-conditional-logic',
			wp_mail_smtp()->plugin_url . "/assets/pro/js/smtp-pro-conditional-logic{$min}.js",
			[ 'jquery' ],
			WPMS_PLUGIN_VER,
			true
		);

		wp_localize_script(
			'wp-mail-smtp-conditional-logic',
			'wp_mail_smtp_conditional_logic',
			[
				'properties' => $this->properties,
			]
		);
	}

	/**
	 * Build the conditional logic settings.
	 *
	 * @since 3.7.0
	 *
	 * @param array $args Data is needed for a block to be generated properly.
	 * @param bool  $echo Whether to return or print. Default: print.
	 *
	 * @return string|void
	 */
	public function block( $args = [], $echo = true ) { // phpcs:ignore Generic.Arrays.DisallowLongArraySyntax.Found, Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded

		$field_name      = ! empty( $args['field_name'] ) ? $args['field_name'] : '';
		$conditionals    = ! empty( $args['conditionals'] ) ? $args['conditionals'] : [ 'group-0' => [ 'rule-0' => [] ] ];
		$properties      = $this->properties;
		$rule_data_attrs = 'data-input-name="' . esc_attr( $field_name ) . '"';

		ob_start();

		// Block open markup.
		echo '<div class="wp-mail-smtp-conditional">';

		// Go through each conditional logic group.
		foreach ( $conditionals as $group_id => $group ) :
			// Individual group open markup.
			echo '<div class="wp-mail-smtp-conditional__group">';

			echo '<table><tbody>';

			foreach ( $group as $rule_id => $rule ) :
				// Set default value as first property from the list.
				if ( empty( $rule['property'] ) ) {
					$rule['property'] = array_keys( $properties )[0];
				}

				$property_type = ! empty( $properties[ $rule['property'] ]['type'] ) ? $properties[ $rule['property'] ]['type'] : 'text';

				// Individual rule table row.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<tr class="wp-mail-smtp-conditional__row" ' . $rule_data_attrs . '>';

				echo '<td class="wp-mail-smtp-conditional__property-col">';

				printf(
					'<select name="%1$s[conditionals][%2$s][%3$s][property]" class="wp-mail-smtp-conditional__property" data-groupid="%2$s" data-ruleid="%3$s">',
					esc_attr( $field_name ),
					esc_attr( $group_id ),
					esc_attr( $rule_id )
				);

				if ( ! empty( $properties ) ) {
					foreach ( $properties as $property_value => $property ) {
						if ( isset( $rule['property'] ) ) {
							$selected = $rule['property'];
						} else {
							$selected = false;
						}

						$selected = selected( $selected, $property_value, false );

						printf( '<option value="%s" %s>%s</option>', esc_attr( $property_value ), esc_attr( $selected ), esc_html( $property['label'] ) );
					}
				}

				echo '</select>';

				echo '</td>';

				// Rule operator - allows the user to determine the comparison operator used for processing.
				echo '<td class="wp-mail-smtp-conditional__operator-col">';

				printf(
					'<select name="%s[conditionals][%s][%s][operator]" class="wp-mail-smtp-conditional__operator">',
					esc_attr( $field_name ),
					esc_attr( $group_id ),
					esc_attr( $rule_id )
				);

				$selected_operator = ! empty( $rule['operator'] ) ? $rule['operator'] : false;

				$operators = [
					[
						'value' => 'c',
						'label' => esc_html__( 'Contains', 'wp-mail-smtp-pro' ),
					],
					[
						'value' => '!c',
						'label' => esc_html__( 'Does not contain', 'wp-mail-smtp-pro' ),
					],
					[
						'value' => '==',
						'label' => esc_html__( 'Is', 'wp-mail-smtp-pro' ),
					],
					[
						'value' => '!=',
						'label' => esc_html__( 'Is not', 'wp-mail-smtp-pro' ),
					],
					[
						'value' => '^',
						'label' => esc_html__( 'Starts with', 'wp-mail-smtp-pro' ),
					],
					[
						'value' => '~',
						'label' => esc_html__( 'Ends with', 'wp-mail-smtp-pro' ),
					],
				];

				foreach ( $operators as $operator ) {
					printf(
						'<option value="%s" %s %s>%s</option>',
						esc_attr( $operator['value'] ),
						selected( $selected_operator, $operator['value'] ),
						disabled( $property_type === 'select' && ! in_array( $operator['value'], [ '==', '!=' ], true ) ),
						esc_html( $operator['label'] )
					);
				}

				echo '</select>';

				echo '</td>';

				// Rule value - allows the user to determine the value we are using for comparison.
				echo '<td class="wp-mail-smtp-conditional__value-col">';

				if ( ! empty( $rule['property'] ) ) {
					$rule_value = ! empty( $rule['value'] ) ? $rule['value'] : '';

					if ( $property_type === 'select' ) {
						printf(
							'<select name="%1$s[conditionals][%2$s][%3$s][value]" class="wp-mail-smtp-conditional__value">',
							esc_attr( $field_name ),
							esc_attr( $group_id ),
							esc_attr( $rule_id )
						);

						if ( ! empty( $properties[ $rule['property'] ]['choices'] ) ) {
							foreach ( $properties[ $rule['property'] ]['choices'] as $option_value => $option_label ) {
								printf(
									'<option value="%1$s" %2$s>%3$s</option>',
									esc_attr( $option_value ),
									selected( $option_value, $rule_value, false ),
									esc_html( trim( $option_label ) )
								);
							}
						}

						echo '</select>';
					} else {
						printf(
							'<input type="text" name="%s[conditionals][%s][%s][value]" value="%s" class="wp-mail-smtp-conditional__value">',
							esc_attr( $field_name ),
							esc_attr( $group_id ),
							esc_attr( $rule_id ),
							esc_attr( $rule_value )
						);
					}
				} else {
					echo '<select></select>';
				}

				echo '</td>';

				// Rule actions.
				echo '<td class="wp-mail-smtp-conditional__actions">';
				echo '<button class="wp-mail-smtp-conditional__add-rule wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-grey" title="' . esc_attr__( 'Create new rule', 'wp-mail-smtp-pro' ) . '">' . esc_html_x( 'And', 'Conditional Logic: new rule logic.', 'wp-mail-smtp-pro' ) . '</button>';

				echo '<button class="wp-mail-smtp-conditional__delete-rule" title="' . esc_attr__( 'Delete rule', 'wp-mail-smtp-pro' ) . '"><i class="dashicons dashicons-trash" aria-hidden="true"></i></button>';
				echo '</td>';

				echo '</tr>'; // Close individual rule table row.

			endforeach; // End foreach() for individual rules.

			echo '</tbody></table>';

			echo '<div class="wp-mail-smtp-conditional__group-delimiter">' . esc_html_x( 'or', 'Conditional Logic: new rule logic.', 'wp-mail-smtp-pro' ) . '</div>';

			echo '</div>'; // Close individual group markup.

		endforeach; // End foreach() for conditional logic groups.

		echo '<button class="wp-mail-smtp-conditional__add-group wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-grey">' . esc_html__( 'Add New Group', 'wp-mail-smtp-pro' ) . '</button>';

		echo '</div>'; // Close block markup.

		$output = ob_get_clean();

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $output;
		} else {
			return $output;
		}
	}
}
