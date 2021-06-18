<?php
/**
 * Render Smush pages.
 *
 * @package WP_Smush
 *
 * @var Abstract_Page $this
 */

use Smush\App\Abstract_Page;

if ( ! defined( 'WPINC' ) ) {
	die;
}

$this->do_meta_boxes( 'summary' );

?>

<?php if ( 'smush' === $this->get_slug() && ! apply_filters( 'wpmudev_branding_hide_doc_link', false ) && ! get_option( WP_SMUSH_PREFIX . 'hide-tutorials' ) ) : ?>
	<div id="smush-dash-tutorials"></div>
<?php endif; ?>

<?php if ( 'smush-tutorials' === $this->get_slug() ) : ?>
	<div id="smush-box-tutorials"></div>
<?php endif; ?>

<?php if ( ! $this->get_current_tab() ) : ?>
	<form id="<?php echo esc_attr( $this->get_slug() ); ?>-form" method="post">
		<?php $this->do_meta_boxes(); ?>
	</form>
<?php else : ?>
	<?php if ( 'configs' !== $this->get_current_tab() ) : ?>
		<form id="<?php echo esc_attr( $this->get_slug() ); ?>-form" method="post">
	<?php endif; ?>
		<div class="sui-row-with-sidenav">
			<?php $this->show_tabs(); ?>
			<?php $this->do_meta_boxes( $this->get_current_tab() ); ?>

			<?php if ( 'configs' === $this->get_current_tab() ) : ?>
				<div id="smush-box-configs"></div>
			<?php endif; ?>
		</div>
		<input type="hidden" name="tab" value="<?php echo esc_attr( $this->get_current_tab() ); ?>">
	<?php if ( 'configs' !== $this->get_current_tab() ) : ?>
		</form>
	<?php endif; ?>
<?php endif; ?>

<?php if ( $this->has_meta_boxes( 'box-dashboard-left' ) || $this->has_meta_boxes( 'box-dashboard-right' ) ) : ?>
	<div class="sui-row">
		<div class="sui-col-lg-6"><?php $this->do_meta_boxes( 'box-dashboard-left' ); ?></div>
		<div class="sui-col-lg-6"><?php $this->do_meta_boxes( 'box-dashboard-right' ); ?></div>
	</div>
<?php endif; ?>

<?php
if ( ! WP_Smush::is_pro() && 'smush' === $this->get_slug() ) {
	$this->view( 'footer-plugins-upsell', array(), 'common' );
}

$this->view( 'footer-links', array(), 'common' );