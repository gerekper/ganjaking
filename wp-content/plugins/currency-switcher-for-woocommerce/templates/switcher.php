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
	wp_enqueue_style('wccs_pretty_dd_style', WCCS_PLUGIN_URL . 'assets/lib/pretty_dropdowns/prettydropdowns.css', '', '1.0');
	wp_enqueue_style('wccs_css', WCCS_PLUGIN_URL . 'assets/frontend/css/shortcode_style.css', '', '1.0');
				
	wp_enqueue_script('wccs_pretty_dd_script', WCCS_PLUGIN_URL . 'assets/lib/pretty_dropdowns/jquery.prettydropdowns.js', array( 'jquery' ), '1.0');
	wp_enqueue_script('wccs_shortcode_script', WCCS_PLUGIN_URL . 'assets/frontend/js/shortcode_script.js', array( 'wccs_pretty_dd_script' ), '1.0');
	?>
	<div class="wcc_switcher_container <?php echo esc_html($class); ?>">
		<form class="wcc_switcher_form" method="post" action="">
			<select class="wcc_switcher" name="wcc_switcher">
				<option value="<?php echo esc_attr($default_currency); ?>" <?php if ($show_flag && $default_currency_flag ) { ?>
				data-suffix="<img width='30' height='20' src='<?php echo esc_html($default_currency_flag); ?>'>"
			   <?php } ?> >
				<?php echo esc_html($default_label); ?>
				<?php if ($show_currency  ) { ?>
					<?php 
					echo esc_html(' "' . $default_symbol . '" '); 
				} 
				?>
				<?php esc_html_e('(Default)', 'wccs'); ?>
				</option>
	<?php
	foreach ( $currencies as $code => $info ) {
		?>
				<option value="<?php echo esc_attr($code); ?>" <?php if ($show_flag && $info['flag'] ) { ?>
				data-suffix="<img width='30' height='20' src='<?php echo esc_html($info['flag']); ?>'>"
			   <?php } ?>
		<?php if ($currency == $code ) { ?> 
				selected
		<?php } ?>><?php echo esc_html($info['label']); ?><?php if ($show_currency ) { ?>
					<?php 
					echo esc_html(' "' . $info['symbol'] . '"'); 
		} 
		?>
		&nbsp;</option>
		<?php
	}
	?>
			</select>
		</form>
	</div>    
	<?php
}
