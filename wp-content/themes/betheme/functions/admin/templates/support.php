<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}
?>

<div id="mfn-dashboard" class="wrap about-wrap">

	<?php include_once get_theme_file_path('/functions/admin/templates/parts/header.php'); ?>

	<div class="dashboard-tab support">

		<div class="col col-left">

			<h3 class="primary"><?php esc_html_e( 'Video tutorials', 'mfn-opts' ); ?></h3>

			<ul class="videos">

				<li>
					<a class="lightbox" href="https://www.youtube.com/watch?v=Njz5XX2cGSI">
						<img src="<?php echo get_theme_file_uri('/functions/admin/assets/images/video.jpg'); ?>" alt="video" />
						<span class="desc">How to import pre-built websites</span>
					</a>
				</li>

				<li>
					<a class="lightbox" href="https://www.youtube.com/watch?v=k29jfkjae2o">
						<img src="<?php echo get_theme_file_uri('/functions/admin/assets/images/video.jpg'); ?>" alt="video" />
						<span class="desc">How to use Muffin Options</span>
					</a>
				</li>

				<li>
					<a class="lightbox" href="https://www.youtube.com/watch?v=8kAGSKVM3eo">
						<img src="<?php echo get_theme_file_uri('/functions/admin/assets/images/video.jpg'); ?>" alt="video" />
						<span class="desc">Muffin Builder Guided Tour</span>
					</a>
				</li>

				<li>
					<a class="lightbox" href="https://www.youtube.com/watch?v=u817Bn30UaY">
						<img src="<?php echo get_theme_file_uri('/functions/admin/assets/images/video.jpg'); ?>" alt="video" />
						<span class="desc">How to use Page Options</span>
					</a>
				</li>

				<li>
					<a class="lightbox" href="https://www.youtube.com/watch?v=bKJbY5crvTE">
						<img src="<?php echo get_theme_file_uri('/functions/admin/assets/images/video.jpg'); ?>" alt="video" />
						<span class="desc">Set up the Blog and Portfolio pages</span>
					</a>
				</li>

				<li>
					<a class="lightbox" href="https://www.youtube.com/watch?v=pa0eP-fKX24">
						<img src="<?php echo get_theme_file_uri('/functions/admin/assets/images/video.jpg'); ?>" alt="video" />
						<span class="desc">Widgets and the widgets section</span>
					</a>
				</li>

			</ul>

			<a class="mfn-button mfn-button-fw mb-30" target="_blank" href="https://www.youtube.com/user/MuffinGroup/videos">View more videos</a>

			<?php if( ! mfn_is_hosted() ): ?>

				<h3 class="primary"><?php esc_html_e( 'Support forum', 'mfn-opts' ); ?></h3>

				<h4>Item support includes:</h4>

				<ul class="forum">
					<li><span class="status yes dashicons dashicons-yes"></span>Responding to questions or problems regarding the item and its features</li>
					<li><span class="status yes dashicons dashicons-yes"></span>Fixing bugs and reported issues</li>
					<li><span class="status yes dashicons dashicons-yes"></span>Providing updates to ensure compatibility with new WordPress versions</li>
			    </ul>

				<h4>However, item support does not include:</h4>

				<ul class="forum">
					<li><span class="status no dashicons dashicons-no"></span>Customization and installation services</li>
					<li><span class="status no dashicons dashicons-no"></span>Support for third party software and plug-ins</li>
			    </ul>

			    <?php if( mfn_is_registered() ): ?>

			    	<a class="mfn-button mfn-button-fw" target="_blank" href="http://forum.muffingroup.com/betheme/">BeTheme Support Forum</a>

			    <?php else: ?>

			    	<p>Please register this version of theme to get access to our support forum.</p>
			    	<a class="mfn-button mfn-button-fw" href="admin.php?page=betheme">Register BeTheme</a>

			    <?php endif; ?>

			<?php endif; ?>

		</div>

		<div class="col col-right">

			<h3><?php esc_html_e( 'Manual table of content', 'mfn-opts' ); ?></h3>

			<div class="manual">

				<h4><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#1-click-demo">Pre-built websites</a></h4>

				<div class="group">

					<h4><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#header">Layout</a></h4>

					<ul>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#header">Header</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#slider">Slider</a></li>
					</ul>

					<ul>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#footer-widgets">Footer &amp; Widgets</a></li>
					</ul>

				</div>

				<div class="group">

					<h4><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#blog">Blog, Portfolio &amp; Shop</a></h4>

					<ul>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#blog">Blog</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#portfolio">Portfolio</a></li>

					</ul>

					<ul>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#e-commerce">Shop</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#pages">Pages</a></li>
					</ul>

				</div>

				<h4><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#muffin-builder">Muffin Builder</a></h4>

				<div class="group">

					<h4><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#muffin-options">Muffin Options</a></h4>

					<ul>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#muffin-options_getting-started">Global</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#header-subheader">Header &amp; Subheader</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#menu-action_bar">Menu &amp; Action bar</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#sidebars">Sidebars</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#blog-portfolio-shop">Blog, Portfolio &amp; Shop</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#opts-pages">Pages</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#footer">Footer</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#responsive">Responsive</a></li>
					</ul>

					<ul>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#seo">SEO</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#social">Social</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#plugins">Addons &amp; Plugins</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#muffin-options_colors">Colors</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#muffin-options_fonts">Fonts</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#muffin-options_translate">Translate</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#custom-css">Custom CSS &amp; JS</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#muffin-options_import-export">Import &amp; Export</a></li>
					</ul>

				</div>

				<h4><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#translation">Translation &amp; Multilingual</a></h4>

				<div class="group">

					<h4><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#extras">Extras</a></h4>

					<ul>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#retina-ready">Retina Ready</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#seo">SEO</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#support">Support</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#theme-customizations">Theme customizations</a></li>
					</ul>

					<ul>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#hosting">Hosting</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#static-css">Static CSS</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#visual-composer">Visual Composer</a></li>
						<li><a target="_blank" href="http://themes.muffingroup.com/betheme/documentation/#white-label-panel">White Label Panel</a></li>
					</ul>

				</div>

			</div>

		</div>


	</div>

</div>
