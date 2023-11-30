<?php
/**
 * Available variables
 * 
 * $class: (string) class(es) to be added to switcher container
 * $default_currency (string): woocommerce default currency code
 * $default_label: (string) woocommerce default currency label
 * $default_symbol: (string) woocommerce default currency symbol
 * $currencies: (array) currencies list added by admin (associated array )
 * $currency: (string) current currency code
 * $show_currency: (boolean) whether to add currency symbol to switcher or not
 * $show_flag: (boolean) whether to add currency flag to switcher or not
 */

if (count($currencies) ) {
	wp_enqueue_style('wccr_css', WCCS_PLUGIN_URL . 'assets/frontend/css/rates_shortcode_style.css', '', '1.0');          
	?>
	<div class="wcc_rates_container <?php echo esc_attr($class); ?>">
		<h4>
	<?php
	/**
	 * Filter
	 * 
	 * @since 1.0.0
	 */ 
	echo esc_html(apply_filters('wcc_rates_title', __('Currency Rates', 'wccs'))); 
	?>
		</h4>
		<ul class="wcc_rates_list">
	<?php
	foreach ( $currencies as $code => $info ) {
		?>
			<li>
				<span class="wcc-flag <?php if ($show_flag && $info['flag'] ) { ?>
				flag-icon flag-icon-
					<?php 
					echo esc_attr($info['flag']); 
									  } 
										?>
									  "></span>
				<span class="wcc-name"><?php echo esc_html($code); ?></span>
		<?php if ($show_currency ) { ?>
				<span class="wcc-symbol">(<?php echo esc_html($info['symbol']); ?>)</span>
		<?php } ?>
				<span class="wcc-dots">:</span>
				<span class="wcc-value"><?php echo esc_html(number_format($info['rate'], $info['decimals'])); ?></span>
			</li>
		<?php
	}
	?>
		</ul>
		<div class="wcc_rates_base">* <?php echo esc_html__('Exchange Rates is based on 1 ', 'wccs') . esc_html($default_currency); ?>
	<?php 
	if ($show_currency ) { 
		echo esc_html(' (' . $default_symbol . ')'); 
	} 
	?>
		</div>
	</div>    
	<?php
}
