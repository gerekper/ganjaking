<?php
/**
 * Stock/Sale template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Filters
 * @version 4.16.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset YITH_WCAN_Preset
 * @var $filter YITH_WCAN_Filter_Stock_Sale
 * @var $term WP_Term
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>


<div class="yith-wcan-filter <?php echo esc_attr( $filter->get_additional_classes() ); ?>" id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>" data-filter-type="<?php echo esc_attr( $filter->get_type() ); ?>" data-filter-id="<?php echo esc_attr( $filter->get_id() ); ?>" data-multiple="yes">
	<?php echo $filter->render_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<div class="filter-content">
		<div class="filter-items <?php echo esc_attr( $filter->get_items_container_classes() ); ?>">
			<?php
			if ( $filter->is_sale_filter_relevant() ) :
				$on_sale_count  = $filter->get_on_sale_count();
				$on_sale_active = $filter->is_on_sale_active();
				?>
				<div class="filter-item checkbox filter-on-sale <?php echo $on_sale_active ? 'active' : ''; ?> <?php echo ! $on_sale_count ? 'disabled' : ''; ?>">
					<label for="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>_sale">
						<input type="checkbox" id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>_sale" name="filter[<?php echo esc_attr( $preset->get_id() ); ?>][<?php echo esc_attr( $filter->get_id() ); ?>][sale]" value="1" <?php checked( $on_sale_active ); ?> />
						<a href="<?php echo esc_url( $filter->get_on_sale_filter_url() ); ?>" <?php yith_wcan_add_rel_nofollow_to_url( true, true ); ?> role="button" class="term-label">
							<?php echo esc_html( apply_filters( 'yith_wcan_on_sale_text', _x( 'On sale', '[FRONTEND] On sale filter label', 'yith-woocommerce-ajax-navigation' ) ) ); ?>
							<?php echo $filter->render_count( $on_sale_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</label>
				</div>
			<?php endif; ?>

			<?php
			if ( $filter->is_stock_filter_relevant() ) :
				$in_stock_count  = $filter->get_in_stock_count();
				$in_stock_active = $filter->is_in_stock_active();
				?>
				<div class="filter-item checkbox filter-in-stock <?php echo $in_stock_active ? 'active' : ''; ?> <?php echo ! $in_stock_count ? 'disabled' : ''; ?>">
					<label for="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>_stock">
						<input type="checkbox" id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>_stock" name="filter[<?php echo esc_attr( $preset->get_id() ); ?>][<?php echo esc_attr( $filter->get_id() ); ?>][stock]" value="1" <?php checked( $in_stock_active ); ?> />
						<a href="<?php echo esc_url( $filter->get_in_stock_filter_url() ); ?>" <?php yith_wcan_add_rel_nofollow_to_url( true, true ); ?> role="button" class="term-label">
							<?php echo esc_html( apply_filters( 'yith_wcan_in_stock_text', _x( 'In stock', '[FRONTEND] On sale filter label', 'yith-woocommerce-ajax-navigation' ) ) ); ?>
							<?php echo $filter->render_count( $in_stock_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</label>
				</div>
			<?php endif; ?>

			<?php
			if ( $filter->is_featured_filter_relevant() ) :
				$featured_count        = $filter->get_featured_count();
				$featured_count_active = $filter->is_featured_active();
				?>
				<div class="filter-item checkbox filter-featured <?php echo $featured_count_active ? 'active' : ''; ?> <?php echo ! $featured_count ? 'disabled' : ''; ?>">
					<label for="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>_featured">
						<input type="checkbox" id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>_featured" name="filter[<?php echo esc_attr( $preset->get_id() ); ?>][<?php echo esc_attr( $filter->get_id() ); ?>][featured]" value="1" <?php checked( $featured_count_active ); ?> />
						<a href="<?php echo esc_url( $filter->get_featured_filter_url() ); ?>" <?php yith_wcan_add_rel_nofollow_to_url( true, true ); ?> role="button" class="term-label">
							<?php echo esc_html( apply_filters( 'yith_wcan_featured_text', _x( 'Featured', '[FRONTEND] On sale filter label', 'yith-woocommerce-ajax-navigation' ) ) ); ?>
							<?php echo $filter->render_count( $featured_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</label>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
