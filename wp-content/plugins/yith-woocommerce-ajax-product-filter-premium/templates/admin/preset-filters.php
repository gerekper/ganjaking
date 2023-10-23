<?php
/**
 * Preset filters list - Admin view
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Admin
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset bool|YITH_WCAN_Preset
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="preset-filters-wrapper">
	<h4><?php echo esc_html_x( 'Filters of this preset', '[Admin] Label in new preset page', 'yith-woocommerce-ajax-navigation' ); ?></h4>

	<div class="preset-filters">
		<?php
		$filters   = $preset ? $preset->get_filters() : array();
		$show_more = YITH_WCAN_Presets::FILTERS_PER_PAGE < count( $filters );

		YITH_WCAN()->admin->show_empty_content(
			array(
				'item_name'    => _x( 'filter', '[Admin] Name of the item missing, shown in preset-empty-content template', 'yith-woocommerce-ajax-navigation' ),
				'hide'         => ! empty( $filters ),
				'button_label' => _x( 'Add a new filter', '[Admin] New filter button label', 'yith-woocommerce-ajax-navigation' ),
				'button_class' => 'add-new-filter',
			)
		);

		if ( ! empty( $filters ) ) :
			if ( $show_more ) {
				$filters = array_slice( $filters, 0, YITH_WCAN_Presets::FILTERS_PER_PAGE, true );
			}

			foreach ( $filters as $filter_id => $filter ) :
				include YITH_WCAN_DIR . 'templates/admin/preset-filter.php';
			endforeach;
		endif;
		?>
	</div>

	<?php
	if ( $show_more ) :
		$first = false;
		include YITH_WCAN_DIR . 'templates/admin/preset-load-more-filters.php';
	endif;
	?>

	<a href="#" id="add_new_filter" style="<?php echo empty( $filters ) ? 'display: none;' : ''; ?>" class="add-new-filter"><?php echo esc_html_x( '+ Add filter', '[Admin] Add new filter in new preset page', 'yith-woocommerce-ajax-navigation' ); ?></a>
</div>

<script type="text/template" id="tmpl-yith-wcan-filter">
	<?php
	$filter    = yith_wcan_get_filter();
	$filter_id = '{{data.id}}';

	require YITH_WCAN_DIR . 'templates/admin/preset-filter.php';
	?>
</script>

<?php
// retrieve supported filter types.
$supported_types = array_keys( YITH_WCAN_Filter_Factory::get_supported_types() );
?>

<?php if ( in_array( 'tax', $supported_types, true ) ) : ?>
	<script type="text/template" id="tmpl-yith-wcan-filter-term">
		<?php
		$filter_id    = '{{data.id}}';
		$term_id      = '{{data.term_id}}';
		$term_name    = '{{data.name}}';
		$term_options = array(
			'label'   => '{{data.label}}',
			'tooltip' => '{{data.tooltip}}',
		);

		YITH_WCAN()->admin->filter_term_field( $filter_id, $term_id, $term_name, $term_options );
		?>
	</script>
<?php endif; ?>

<?php if ( in_array( 'price_range', $supported_types, true ) ) : ?>
	<script type="text/template" id="tmpl-yith-wcan-filter-range">
		<?php
		$range_id  = '{{data.range_id}}';
		$filter_id = '{{data.id}}';
		$range     = array(
			'min' => '{{data.min}}',
			'max' => '{{data.max}}',
		);

		include YITH_WCAN_DIR . 'templates/admin/preset-filter-range.php';
		?>
	</script>
<?php endif; ?>
