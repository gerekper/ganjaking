<!-- Start tabs -->
<ul class="wp-tab-bar master_addons_navbar">

	<?php if (isset($jltma_hide_welcome) && !$jltma_hide_welcome) { ?>
		<li class="wp-tab-active">
			<a href="#welcome">
				<?php _e('Welcome', MELA_TD); ?>
			</a>
		</li>
	<?php } ?>

	<?php if (isset($jltma_hide_addons) && !$jltma_hide_addons) { ?>
		<li>
			<a href="#ma-addons">
				<?php _e('Addons', MELA_TD); ?>
			</a>
		</li>
	<?php } ?>

	<?php if (isset($jltma_hide_extensions) && !$jltma_hide_extensions) { ?>
		<li>
			<a href="#extensions">
				<?php _e('Extensions', MELA_TD); ?>
			</a>
		</li>
	<?php } ?>

	<?php if (isset($jltma_hide_api) && !$jltma_hide_api) { ?>
		<li>
			<a href="#ma_api_keys">
				<?php _e('API', MELA_TD); ?>
			</a>
		</li>
	<?php } ?>

	<?php if (isset($jltma_hide_white_label) && !$jltma_hide_white_label) { ?>
		<li>
			<a href="#jltma_white_label">
				<?php _e('White Label', MELA_TD);
				?>
			</a>
		</li>
	<?php } ?>

	<?php if (isset($jltma_hide_version) && !$jltma_hide_version) { ?>
		<li>
			<a href="#version">
				<?php _e('Version', MELA_TD); ?>
			</a>
		</li>
	<?php } ?>

	<?php if (isset($jltma_hide_changelogs) && !$jltma_hide_changelogs) { ?>
		<li>
			<a href="#changelogs">
				<?php _e('Changelogs', MELA_TD); ?>
			</a>
		</li>
	<?php } ?>

	<?php if (isset($jltma_hide_system_info) && !$jltma_hide_system_info) { ?>
		<li>
			<a href="#jltma_system_info">
				<?php _e('System Info', MELA_TD); ?>
			</a>
		</li>
	<?php } ?>

	

</ul>
