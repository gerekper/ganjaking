<?php
/**
 * Admin View: Report by Date (with date filters)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div id="poststuff" class="woocommerce-reports-wide">
	<div class="postbox">
		<h3 class="stats_range">
			<?php $this->get_export_button(); ?>
			<ul>
				<?php
					foreach ( $ranges as $range => $name ) {
						echo '<li class="' . ( $current_range == $range ? 'active' : '' ) . '"><a href="' . esc_url( remove_query_arg( array( 'start_date', 'end_date' ), add_query_arg( 'range', $range ) ) ) . '">' . esc_html( $name ) . '</a></li>';
					}
				?>
				<li class="custom <?php echo $current_range == 'custom' ? 'active' : ''; ?>">
					<?php esc_html_e( 'Custom:', 'woocommerce-product-vendors' ); ?>
					<form method="GET">
						<div>
							<?php
								// Maintain query string
								foreach ( $_GET as $key => $value ) {
									if ( is_array( $value ) ) {
										foreach ( $value as $v ) {
											echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '[]" value="' . esc_attr( sanitize_text_field( $v ) ) . '" />';
										}
									} else {
										echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '" value="' . esc_attr( sanitize_text_field( $value ) ) . '" />';
									}
								}
							?>
							<input type="hidden" name="range" value="custom" />
							<input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( wc_clean( wp_unslash( $_GET['start_date'] ?? '' ) ) ); ?>" name="start_date" class="range_datepicker from" />
							<input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( wc_clean( wp_unslash( $_GET['end_date'] ?? '' ) ) ); ?>" name="end_date" class="range_datepicker to" />
							<input type="submit" class="button" value="<?php esc_attr_e( 'Go', 'woocommerce-product-vendors' ); ?>" />
						</div>
					</form>
				</li>
			</ul>
		</h3>
		<?php if ( empty( $hide_sidebar ) ) : ?>
			<div class="inside chart-with-sidebar">
				<div class="chart-sidebar">
					<ul class="chart-widgets">
						<?php foreach ( $this->get_chart_widgets() as $widget ) : ?>
							<li class="chart-widget">
								<?php if ( $widget['title'] ) : ?><h4><?php echo esc_html( $widget['title'] ); ?></h4><?php endif; ?>
								<?php call_user_func( $widget['callback'] ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php if ( $legends = $this->get_chart_legend() ) : ?>
						<ul class="chart-legend">
							<?php foreach ( $legends as $legend ) : ?>
								<li style="border-color: <?php echo esc_attr( $legend['color'] ); ?>" <?php if ( isset( $legend['highlight_series'] ) ) echo 'class="highlight_series ' . ( isset( $legend['placeholder'] ) ? 'tips' : '' ) . '" data-series="' . esc_attr( $legend['highlight_series'] ) . '"'; ?> data-tip="<?php echo isset( $legend['placeholder'] ) ? wc_sanitize_tooltip( $legend['placeholder'] ) : ''; ?>">
									<?php echo $legend['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="main">
					<?php $this->get_main_chart(); ?>
				</div>
			</div>
		<?php else : ?>
			<div class="inside">
				<?php $this->get_main_chart(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
