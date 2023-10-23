<?php
/**
 * Preset empty content - Admin view
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Admin
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $show_icon bool
 * @var $item_name string
 * @var $subtitle string
 * @var $button_label string
 * @var $button_class string
 * @var $button_url string
 * @var $hide bool
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="yith-wcan-admin-no-post" <?php echo $hide ? 'style="display: none;"' : ''; ?> >
	<?php if ( $show_icon ) : ?>
		<img src="<?php echo esc_url( YITH_WCAN_URL ); ?>assets/images/empty-preset.svg" alt="<?php echo esc_attr_x( 'Empty preset', 'Alt text for empty preset image', 'yith-woocommerce-ajax-navigation' ); ?>">
	<?php endif; ?>
	<p>
		<span class="strong">
			<?php
				// translators: 1. Missing item name.
				echo esc_html( sprintf( _x( 'You don\'t have any %s yet.', '[Admin] Preset table empty message', 'yith-woocommerce-ajax-navigation' ), $item_name ) );
			?>
		</span>

		<?php if ( ! empty( $subtitle ) ) : ?>
			<span><?php echo esc_html( $subtitle ); ?></span>
		<?php endif; ?>

		<?php if ( ! empty( $button_label ) ) : ?>
			<a class="yith-add-button button-primary <?php echo $button_class ? esc_attr( $button_class ) : ''; ?>" href="<?php echo $button_url ? esc_url( $button_url ) : '#'; ?>">
				<?php echo esc_html( $button_label ); ?>
			</a>
		<?php endif; ?>
	</p>
</div>
