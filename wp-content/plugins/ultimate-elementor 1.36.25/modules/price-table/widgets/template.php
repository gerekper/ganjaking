<?php
/**
 * UAEL Info Box Module Template.
 *
 * @package UAEL
 */

use UltimateElementor\Classes\UAEL_Helper;

if ( '1' === $settings['pricetable_style'] ) {
	?>
	<div class="uael-module-content uael-price-table-container uael-pricing-style-<?php echo esc_attr( $settings['pricetable_style'] ); ?>">
		<div class="uael-price-table">
			<?php $this->render_style_header( $settings ); ?>				
			<?php $this->render_price( $settings ); ?>
			<?php $this->render_features( $settings ); ?>
			<?php $this->render_cta( $settings ); ?>
		</div>
		<?php $this->render_ribbon( $settings ); ?>
	</div>
	<?php
} elseif ( '2' === $settings['pricetable_style'] ) {
	?>
	<div class="uael-module-content uael-price-table-container uael-pricing-style-<?php echo esc_attr( $settings['pricetable_style'] ); ?>">
		<div class="uael-price-table">
			<?php $this->render_style_header( $settings ); ?>				
			<?php $this->render_price( $settings ); ?>
			<?php $this->render_subheading_text( $settings ); ?>	
			<?php $this->render_cta( $settings ); ?>
			<?php $this->render_separator( $settings ); ?>
			<?php $this->render_features( $settings ); ?>
		</div>
		<?php $this->render_ribbon( $settings ); ?>
	</div>
	<?php
} elseif ( '3' === $settings['pricetable_style'] ) {
	?>
	<div class="uael-module-content uael-price-table-container uael-pricing-style-<?php echo esc_attr( $settings['pricetable_style'] ); ?>">
		<div class="uael-price-table">
			<?php $this->render_style_header( $settings ); ?>				
			<?php $this->render_price( $settings ); ?>
			<?php $this->render_features( $settings ); ?>
			<?php $this->render_cta( $settings ); ?>
		</div>
		<?php $this->render_ribbon( $settings ); ?>
	</div>
	<?php
} elseif ( '4' === $settings['pricetable_style'] ) {
	?>
	<div class="uael-module-content uael-price-table-container uael-pricing-style-<?php echo esc_attr( $settings['pricetable_style'] ); ?>">
		<div class="uael-price-table">
		<?php $this->render_style_header( $settings ); ?>				
			<?php $this->render_features( $settings ); ?>
			<?php $this->render_price( $settings ); ?>
			<?php $this->render_cta( $settings ); ?>
		</div>
		<?php $this->render_ribbon( $settings ); ?>
	</div>
	<?php
}
