<?php
/**
 * Preset filter - Term edit
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Admin
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $id           int
 * @var $term         WP_Term
 * @var $term_id      int
 * @var $term_name    string
 * @var $term_options array
 * @var $taxonomy     string
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="term_<?php echo esc_attr( $id ); ?>_<?php echo esc_attr( $term_id ); ?>" class="term-box" data-term_id="<?php echo esc_attr( $term_id ); ?>">
	<h4><?php echo esc_html( $term_name ); ?></h4>

	<p class="yith-plugin-fw-field-wrapper yith-plugin-fw-text-field-wrapper term-label">
		<label for="filters_<?php echo esc_attr( $id ); ?>_terms_<?php echo esc_attr( $term_id ); ?>_label"><?php echo esc_html_x( 'Label', '[Admin] Term edit field label (preset edit page)', 'yith-woocommerce-ajax-navigation' ); ?></label>
		<input type="text" name="filters[<?php echo esc_attr( $id ); ?>][terms][<?php echo esc_attr( $term_id ); ?>][label]" id="filters_<?php echo esc_attr( $id ); ?>_terms_<?php echo esc_attr( $term_id ); ?>_label" value="<?php echo ! empty( $term_options['label'] ) ? esc_attr( $term_options['label'] ) : ''; ?>"/>
	</p>

	<p class="yith-plugin-fw-field-wrapper yith-plugin-fw-text-field-wrapper term-tooltip">
		<label for="filters_<?php echo esc_attr( $id ); ?>_terms_<?php echo esc_attr( $term_id ); ?>_tooltip"><?php echo esc_html_x( 'Tooltip', '[Admin] Term edit field label (preset edit page)', 'yith-woocommerce-ajax-navigation' ); ?></label>
		<input type="text" name="filters[<?php echo esc_attr( $id ); ?>][terms][<?php echo esc_attr( $term_id ); ?>][tooltip]" id="filters_<?php echo esc_attr( $id ); ?>_terms_<?php echo esc_attr( $term_id ); ?>_tooltip" value="<?php echo ! empty( $term_options['tooltip'] ) ? esc_attr( $term_options['tooltip'] ) : ''; ?>" />
	</p>

	<div class="tab tab-color" <?php echo ( ! empty( $term_options['mode'] ) && 'color' !== $term_options['mode'] ) ? 'style="display: none;"' : ''; ?> >
		<p>
			<label for="filters_<?php echo esc_attr( $id ); ?>_terms_<?php echo esc_attr( $term_id ); ?>_color_1"><?php echo esc_html_x( 'Color', '[Admin] Term edit field label (preset edit page)', 'yith-woocommerce-ajax-navigation' ); ?></label>
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'      => "filters_{$id}_terms_{$term_id}_color_1",
					'name'    => "filters[$id][terms][{$term_id}][color_1]",
					'value'   => ! empty( $term_options['color_1'] ) ? $term_options['color_1'] : '#007694',
					'type'    => 'colorpicker',
					'default' => '#007694',
				),
				true
			);
			?>
		</p>
	</div>

	<input type="hidden" class="term-mode" id="filters_<?php echo esc_attr( $id ); ?>_terms_<?php echo esc_attr( $term_id ); ?>_mode" name="filters[<?php echo esc_attr( $id ); ?>][terms][<?php echo esc_attr( $term_id ); ?>][mode]" value="color"/>
</div>
