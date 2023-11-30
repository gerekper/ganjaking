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
	//wp_enqueue_style('wccs_flags_style', WCCS_PLUGIN_URL . 'assets/lib/flag-icon/flag-icon.css');
	wp_enqueue_style('wccs_main_css', WCCS_PLUGIN_URL . 'assets/frontend/themes/style_02/wcc-main.css', '', '1.0');
	wp_enqueue_style('wccs_theme2_css', WCCS_PLUGIN_URL . 'assets/frontend/themes/style_02/theme-02.css', '', '1.0');
				
	wp_enqueue_script('wccs_theme2_script', WCCS_PLUGIN_URL . 'assets/frontend/themes/style_02/wcc-main.js', array( 'jquery' ), '1.0');
	?>
	<div id="wcc-switcher-style-02" class="wcc-switcher-style-02 wcc-wrapper <?php echo esc_html($class); ?>">
		<div class="wcc-crnt-currency d-flex">
	<?php if (!$currency ) { ?>
			<span class="wcc-name"><?php echo esc_html($default_currency); ?> <?php echo esc_html__('(Default)', 'wccs'); ?></span>
	<?php } else { ?>
			<span class="wcc-name"><?php echo esc_html($currency); ?></span>
	<?php } ?>
		</div>
		<ul class="wcc-list">
	<?php if ($currency ) { ?>
			<li class="d-flex" data-code="<?php echo esc_html($default_currency); ?>">
				<span class="wcc-name"><?php echo esc_html($default_currency); ?> <?php echo esc_html__('(Default)', 'wccs'); ?></span>
			</li>
	<?php } ?>
		
	<?php
	foreach ( $currencies as $code => $info ) {
		if ($code != $currency ) {
			?>
			<li class="d-flex" data-code="<?php echo esc_html($code); ?>">
				<span class="wcc-name"><?php echo esc_html($code); ?></span>
			</li>
			<?php
		}
	}
	?>
		</ul>
		<form class="wcc_switcher_form_02" method="post" action="" style="display: none;">
			<input type="hidden" id="custom_nonce" name="custom_nonce" value="<?php echo esc_html(wp_create_nonce('custom_nonce')); ?>">
			<input type="hidden" name="wcc_switcher" class="wcc_switcher" value="">
		</form>
	</div>    
	<?php
}
