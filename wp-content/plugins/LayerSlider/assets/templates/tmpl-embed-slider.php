<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<script type="text/html" id="tmpl-embed-slider">
	<div id="ls-embed-modal-window">
		<h1 class="kmw-modal-title"><?php _e('Embed Slider', 'LayerSlider') ?></h1>

		<p><?php printf( __('There are a number of ways you can include LayerSlider sliders to your posts and pages. Please review the available methods below or refer to our %sonline documentation%s for more information.', 'LayerSlider'), '<a href="https://layerslider.kreaturamedia.com/documentation/#publishing-sliders" target="_blank">', '</a>') ?></p>
		<div class="km-accordion km-embed-accordion">
			<div>
				<div class="km-accordion-head">
					<?php _e('Method 1: Shortcode', 'LayerSlider') ?>
					<small class="green">
						<?php _e('Easy', 'LayerSlider') ?>
					</small>
				</div>
				<div class="km-accordion-body">
					<a class="button" href="https://layerslider.kreaturamedia.com/documentation/#publish-shortcode" target="_blank">
						<i class="dashicons dashicons-external"></i>
						<?php _e('Learn more', 'LayerSlider') ?>
					</a>
					<p>
						<?php printf( __('Shortcodes are small text snippets that will be replaced with the actual slider on your front-end pages. This is one of the most commonly used methods. It works almost all places where you can enter text, including 3rd party page builders. Just copy and paste the following shortcode: %s', 'LayerSlider'), '<input class="shortcode" value="[layerslider id=&quot;1&quot;]" onclick="this.focus(); this.select();">') ?>
						<br>
					</p>

				</div>
			</div>
			<div>
				<div class="km-accordion-head">
					<?php _e('Method 2: Gutenberg', 'LayerSlider') ?>
					<small class="green">
						<?php _e('Easy', 'LayerSlider') ?>
					</small>
				</div>
				<div class="km-accordion-body">
					<a class="button" href="https://layerslider.kreaturamedia.com/documentation/#publish-gutenberg" target="_blank">
						<i class="dashicons dashicons-external"></i>
						<?php _e('Learn more', 'LayerSlider') ?>
					</a>
					<p>
						<?php printf( __('The new WordPress editing experience is here and LayerSlider provides a full-fledged Gutenberg block for your convenience. Just press the + sign in the new WordPress page / post editor and select the LayerSlider block. The rest is self-explanatory, but we also have a %svideo tutorial%s if you are new to Gutenberg.', 'LayerSlider'), '<a href="https://youtu.be/ArzG3Pr2UF4" target="_blank">', '</a>') ?>
					</p>

				</div>
			</div>
			<div>
				<div class="km-accordion-head">
					<?php _e('Method 3: Widget', 'LayerSlider') ?>
					<small class="green">
						<?php _e('Easy', 'LayerSlider') ?>
					</small>
				</div>
				<div class="km-accordion-body">
					<a class="button" href="https://layerslider.kreaturamedia.com/documentation/#publish-widgets" target="_blank">
						<i class="dashicons dashicons-external"></i>
						<?php _e('Learn more', 'LayerSlider') ?>
					</a>
					<p>
						<?php printf( __('Widgets can provide a super easy drag and drop way of sharing your sliders when it comes to embedding content to a commonly used part on your site like the header area, sidebar or the footer. However, the available widget areas are controlled by your theme and it might not offer the perfect spot that you’re looking for. Just head to %sAppearance → Widgets%s to see the options your theme offers.', 'LayerSlider'), '<a href="'.admin_url('widgets.php').'" target="_blank">', '</a>') ?>
					</p>
				</div>
			</div>


			<div>
				<div class="km-accordion-head">
					<?php _e('Method 4: Page Builders', 'LayerSlider') ?>
					<small class="green">
						<?php _e('Easy', 'LayerSlider') ?>
					</small>
				</div>
				<div class="km-accordion-body">
					<a class="button" href="https://layerslider.kreaturamedia.com/documentation/#publish-page-builders" target="_blank">
						<i class="dashicons dashicons-external"></i>
						<?php _e('Learn more', 'LayerSlider') ?>
					</a>
					<p>
						<?php _e('Most page builders support LayerSlider out of the box. Popular plugins like Visual Composer or Elementor has dedicated options to embed sliders. Even if there’s no LayerSlider specific option, shortcodes and widgets are widely supported and can be relied upon in almost all cases. In general, wherever you can insert text or widgets, it can also be used to embed sliders.', 'LayerSlider') ?>
					</p>
				</div>
			</div>

			<div>
				<div class="km-accordion-head">
					<?php _e('Method 5: PHP Function', 'LayerSlider') ?>
					<small class="red">
						<?php _e('Advanced', 'LayerSlider') ?>
					</small>
				</div>
				<div class="km-accordion-body">
					<a class="button" href="https://layerslider.kreaturamedia.com/documentation/#publish-php" target="_blank">
						<i class="dashicons dashicons-external"></i>
						<?php _e('Learn more', 'LayerSlider') ?>
					</a>
					<p><?php _e('You can use the layerslider() PHP function to insert sliders by editing your theme’s template files. Since you can implement custom logic in code, this option gives you unlimited control on how your sliders are embedded.', 'LayerSlider') ?></p>
					<p><?php _e('However, this approach require programming skills, thus we cannot recommend it to users lacking the necessary experience in web development.', 'LayerSlider') ?></p>
				</div>
			</div>
		</div>

	</div>
</script>