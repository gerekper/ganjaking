<?php
/**
 * Backbone modal popup.
 * */
defined('ABSPATH') || exit;

?>
<script type="text/template" id="tmpl-<?php echo esc_attr($template_name); ?>">
<div class="wc-backbone-modal">
	<div class="wc-backbone-modal-content <?php echo esc_attr($wrapper_class_name); ?>">
		<section class="wc-backbone-modal-main" role="main">
			<header class="wc-backbone-modal-header">
				<h1><?php echo esc_html($title) ; ?></h1>
				<button class="modal-close modal-close-link dashicons dashicons-no-alt">
					<span class="screen-reader-text">Close modal panel</span>
				</button>
			</header>
			<article>
				<div class="<?php echo esc_attr($contents_class_name); ?>">
					{{{ data.html }}} 
				</div>
			</article>
			<footer>
				<div class="inner"></div>
			</footer>
		</section>
	</div>
</div>
<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
<?php
