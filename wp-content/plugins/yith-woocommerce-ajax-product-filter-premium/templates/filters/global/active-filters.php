<?php
/**
 * Active filters labels
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Filters
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $active_filters array
 * @var $show_titles    bool
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php if ( ! empty( $active_filters ) ) : ?>
	<div class="yith-wcan-active-filters <?php echo ! $show_titles ? 'no-titles' : ''; ?> <?php echo 'custom' === yith_wcan_get_option( 'yith_wcan_filters_style', 'default' ) ? 'custom-style' : ''; ?>">

		<?php do_action( 'yith_wcan_before_active_filters' ); ?>

		<?php if ( ! empty( $labels_heading ) && ! empty( $active_filters ) ) : ?>
			<h4><?php echo esc_html( $labels_heading ); ?></h4>
		<?php endif; ?>

		<?php foreach ( $active_filters as $filter => $options ) : ?>
			<?php
			if ( empty( $options['values'] ) ) :
				continue;
			endif;
			?>
			<div class="active-filter">
				<?php if ( $show_titles ) : ?>
					<b><?php echo esc_html( $options['label'] ); ?>:</b>
				<?php endif; ?>
				<?php foreach ( $options['values'] as $value ) : ?>
					<a
						href="<?php echo esc_url( YITH_WCAN_Query()->get_filter_url( array(), $value['query_vars'] ) ); ?>"
						<?php yith_wcan_add_rel_nofollow_to_url( true, true ); ?>
						class="active-filter-label"
						data-filters="<?php echo esc_attr( wp_json_encode( $value['query_vars'] ) ); ?>"
					>
						<?php echo wp_kses_post( $value['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>

		<?php do_action( 'yith_wcan_after_active_filters' ); ?>

	</div>
<?php endif; ?>
