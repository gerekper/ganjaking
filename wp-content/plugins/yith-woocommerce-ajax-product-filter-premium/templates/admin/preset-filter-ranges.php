<?php
/**
 * Preset filter - Price Ranges
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Admin
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $filter_id int
 * @var $ranges    array
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<button class="add-price-range button-primary"><?php echo esc_html_x( 'Add range', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ); ?></button>

<div class="ranges-wrapper" data-index="<?php echo esc_attr( count( $ranges ) ); ?>">
	<?php
	if ( ! empty( $ranges ) ) :
		foreach ( $ranges as $range_id => $range ) :
			include YITH_WCAN_DIR . 'templates/admin/preset-filter-range.php';
		endforeach;
	endif;
	?>
</div>
