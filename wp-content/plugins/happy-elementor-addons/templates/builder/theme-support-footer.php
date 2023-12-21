<?php do_action('happyaddons/template/before_footer'); ?>
<div class="ekit-template-content-markup ekit-template-content-footer ekit-template-content-theme-support">
<?php
	// $template = \Happy_Addons\Elementor\Theme_Builder::template_ids();
	// echo \Happy_Addons\Elementor\Theme_Builder::render_builder_data($template[1]);
	echo \Happy_Addons\Elementor\Theme_Builder::instance()->render_builder_data_location('footer');
?>
</div>
<?php do_action('happyaddons/template/after_footer'); ?>
<?php wp_footer(); ?>
</body>
</html>
