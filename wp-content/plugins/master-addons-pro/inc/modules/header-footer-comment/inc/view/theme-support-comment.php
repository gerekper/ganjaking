<?php do_action('masteraddons/template/before_footer'); ?>
	<div class="jltma-comments-template">
		<?php
			$template = \MasterHeaderFooter\JLTMA_HF_Activator::template_ids();
			echo \MasterHeaderFooter\Master_Header_Footer::render_elementor_content($template[2]); 
		?>
	</div>
<?php do_action('masteraddons/template/after_footer'); ?>