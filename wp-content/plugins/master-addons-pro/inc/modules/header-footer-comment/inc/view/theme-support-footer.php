	<?php do_action('masteraddons/template/before_footer'); ?>
	<div class="jltma-template-content-markup jltma-template-content-footer jltma-template-content-theme-support">
		<?php
			$template = \MasterHeaderFooter\JLTMA_HF_Activator::template_ids();
			echo \MasterHeaderFooter\Master_Header_Footer::render_elementor_content($template[1]);
		?>
	</div>
	<?php do_action('masteraddons/template/after_footer'); ?>
	<?php wp_footer(); ?>

	</body>
</html>
