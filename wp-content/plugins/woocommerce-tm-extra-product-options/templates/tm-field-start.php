<?php
/**
 * The template for displaying the start of an element for the local mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-field-start.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $required, $field_id, $label, $original_rules, $rules, $rules_type, $field_type ) ) :
	$required       = (string) $required;
	$field_id       = (string) $field_id;
	$label          = (string) $label;
	$original_rules = (string) $original_rules;
	$rules          = (string) $rules;
	$rules_type     = (string) $rules_type;
	$field_type     = (string) $field_type;

	$extraliclass = '';
	if ( $required ) {
		$extraliclass .= ' tm-epo-has-required';
	}
	if ( isset( $li_class ) ) {
		$extraliclass .= ' ' . $li_class;
	}
	?>
<li id="<?php echo esc_attr( $field_id ); ?>" class="cpf-element tm-extra-product-options-field tc-row tc-cell<?php echo esc_attr( $extraliclass ); ?>">
	<span class="tc-epo-label tm-epo-element-label">
	<?php
	echo esc_html( $label );
	if ( $required && ! empty( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_required_indicator' ) ) && 'left' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_required_indicator_position' ) ) {
		// THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_required_indicator' ) may contain HTML code.
		echo '<span class="tm-epo-required">' . apply_filters( 'wc_epo_kses', wp_kses_post( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_required_indicator' ) ), THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_required_indicator' ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	?>
	</span>
	<div class="tc-element-container">
		<ul data-original-rules="<?php echo esc_attr( $original_rules ); ?>" data-rules="<?php echo esc_attr( $rules ); ?>" data-rulestype="<?php echo esc_attr( $rules_type ); ?>" class="tmcp-ul-wrap tmcp-attributes tm-extra-product-options-<?php echo esc_attr( $field_type ); ?>">
		<?php
endif;
