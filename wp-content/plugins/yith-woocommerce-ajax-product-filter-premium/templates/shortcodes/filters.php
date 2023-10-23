<?php
/**
 * Filters Preset shortcode
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Shortcodes
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset   YITH_WCAN_Preset
 * @var $slug     string
 * @var $selector string
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php if ( $preset->has_relevant_filters() ) : ?>
	<div class="yith-wcan-filters <?php echo esc_attr( $preset->get_additional_classes() ); ?>" id="preset_<?php echo esc_attr( $preset->get_id() ); ?>" data-preset-id="<?php echo esc_attr( $preset->get_id() ); ?>" data-target="<?php echo esc_attr( $selector ); ?>">
		<div class="filters-container">
			<form method="POST">
				<?php
				/**
				 * Hook: yith_wcan_before_preset_filters.
				 *
				 * @hooked \YITH_WCAN_Frontend::filters_title - 10
				 */
				do_action( 'yith_wcan_before_preset_filters', $preset, $selector );
				?>
				<?php foreach ( $preset->get_filters() as $filter_id => $filter ) : ?>
					<?php
					if ( ! $filter->is_enabled() ) {
						continue;
					}
					?>

					<?php
					// just render filter content.
					echo $filter->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				<?php endforeach; ?>

				<?php
				/**
				 * Hook: yith_wcan_after_preset_filters.
				 *
				 * @hooked \YITH_WCAN_Frontend::apply_filters_button - 10
				 */
				do_action( 'yith_wcan_after_preset_filters', $preset, $selector );
				?>
			</form>
		</div>
	</div>
<?php endif; ?>

<?php wp_enqueue_script( 'yith-wcan-shortcodes' ); ?>
