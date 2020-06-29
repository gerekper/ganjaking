<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<script type="text/html" id="tmpl-ls-transition-modal">
	<div id="ls-transition-window" class="hide-special-effects">
		<h1 class="kmw-modal-title"><?php _e('Choose a slide transition to import', 'LayerSlider') ?></h1>

		<div id="transitiongallery-header">
			<div id="transitionmenu" class="filters buildermenu">
				<span><?php _e('Show Transitions:', 'LayerSlider') ?></span>
				<ul>
					<li class="active"><?php _e('2D', 'LayerSlider') ?></li>
					<li><?php _e('3D', 'LayerSlider') ?></li>
				</ul>
			</div>
		</div>


		<div id="ls-transitions-list">

			<!-- 2D -->
			<section data-tr-type="2d_transitions"></section>

			<!-- 3D -->
			<section data-tr-type="3d_transitions"></section>
		</div>

	</div>
</script>