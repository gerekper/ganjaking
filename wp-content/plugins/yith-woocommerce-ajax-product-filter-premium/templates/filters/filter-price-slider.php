<?php
/**
 * Price Slider template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Filters
 * @version 4.16.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset YITH_WCAN_Preset
 * @var $filter YITH_WCAN_Filter_Price_Slider
 * @var $term WP_Term
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>


<div class="yith-wcan-filter <?php echo esc_attr( $filter->get_additional_classes() ); ?>" id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>" data-filter-type="<?php echo esc_attr( $filter->get_type() ); ?>" data-filter-id="<?php echo esc_attr( $filter->get_id() ); ?>">
	<?php echo $filter->render_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<div class="filter-content">
		<div class="price-slider <?php echo esc_attr( $filter->get_price_slider_design() ); ?>" data-min="<?php echo esc_attr( $filter->get_real_min() ); ?>" data-max="<?php echo esc_attr( $filter->get_real_max() ); ?>" data-step="<?php echo esc_attr( $filter->get_price_slider_step() ); ?>">
			<?php if ( 'fields' !== $filter->get_price_slider_design() ) : ?>
				<input class="price-slider-ui" type="hidden" />
			<?php endif; ?>

			<?php if ( 'fields' === $filter->get_price_slider_design() ) : ?>
				<label for="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>_min">
					<?php echo esc_html( apply_filters( 'yith_wcan_filter_price_slider_from_label', _x( 'From', '[FRONTEND] Label used in price slider filter', 'yith-woocommerce-ajax-navigation' ) ) ); ?>
				</label>
			<?php endif; ?>
			<input
				type="<?php echo 'slider' !== $filter->get_price_slider_design() ? 'number' : 'hidden'; ?>"
				class="price-slider-min" name="filter[<?php echo esc_attr( $preset->get_id() ); ?>][<?php echo esc_attr( $filter->get_id() ); ?>][min]"
				id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>_min"
				value="<?php echo esc_attr( $filter->get_current_min() ); ?>"
				min="<?php esc_attr( $filter->get_real_min() ); ?>"
				max="<?php esc_attr( $filter->get_real_max() ); ?>"
				inputmode="decimal"
				pattern="/d*"
				step="<?php esc_attr( $filter->get_price_slider_step() ); ?>"
			/>

			<?php if ( 'fields' === $filter->get_price_slider_design() ) : ?>
				<label for="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>_max">
					<?php echo esc_html( apply_filters( 'yith_wcan_filter_price_slider_to_label', _x( 'To', '[FRONTEND] Label used in price slider filter', 'yith-woocommerce-ajax-navigation' ) ) ); ?>
				</label>
			<?php endif; ?>
			<input
				type="<?php echo 'slider' !== $filter->get_price_slider_design() ? 'number' : 'hidden'; ?>"
				class="price-slider-max" name="filter[<?php echo esc_attr( $preset->get_id() ); ?>][<?php echo esc_attr( $filter->get_id() ); ?>][max]"
				id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>_max"
				value="<?php echo esc_attr( $filter->get_current_max() ); ?>"
				min="<?php esc_attr( $filter->get_real_min() ); ?>"
				max="<?php esc_attr( $filter->get_real_max() ); ?>"
				inputmode="decimal"
				pattern="/d*"
				step="<?php esc_attr( $filter->get_price_slider_step() ); ?>"
			/>

			<?php if ( 'fields' === $filter->get_price_slider_design() ) : ?>
				<span class="currency">
					<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>
				</span>
			<?php endif; ?>
		</div>
	</div>
</div>
