<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

$demoSliders = LS_Sources::getDemoSliders();

?>
<script type="text/javascript">
	window.lsImportNonce = '<?php echo wp_create_nonce('ls-import-demos'); ?>';
</script>
<script type="text/html" id="tmpl-import-sliders">
	<div id="ls-import-modal-window" class="ls-box <?php echo $lsStoreHasUpdate ? 'has-updates' : '' ?>">
		<header class="header">

			<img src="<?php echo LS_ROOT_URL.'/static/admin/img/ls-logo.png' ?>" alt="LayerSlider Logo" class="ls-logo">

			<h1>
				<?php _e('Template Store', 'LayerSlider') ?>
			</h1>

			<div class="last-update">
				<strong><?php _e('Last updated: ', 'LayerSlider') ?></strong>
				<span>
					<?php
						if( $lsStoreUpdate ) {
							echo human_time_diff($lsStoreUpdate), __(' ago', 'LayerSlider');
						} else {
							_e('Just now', 'LayerSlider');
						}
					?>
				</span>
				<a title="<?php _e('Force Library Update', 'LayerSlider') ?>"href="<?php echo wp_nonce_url( admin_url('admin.php?page=layerslider&action=update_store'), 'update_store') ?>" class="refresh-btn fas fa-sync-alt"></a>
			</div>
 			<b class="modal-close-btn dashicons dashicons-no"></b>
		</header>

		<!-- SLIDERS -->
		<div class="inner sliders active">
			<nav class="templates-sidemenu">
				<ul class="content-filter">
					<li data-index="0" class="active">
						<i class="fas fa-layer-group"></i>
						<?php _e('SLIDERS', 'LayerSlider') ?>
					</li>
					<li data-index="1">
						<i class="far fa-window-maximize"></i>
						<?php _e('POPUPS', 'LayerSlider') ?>
					</li>
				</ul>


				<div class="separator"></div>


				<h5>Categories</h5>
				<ul class="shuffle-filters">
					<li class="active">
						<i class="fas fa-tags"></i>
						<?php _e('All', 'LayerSlider') ?>
					</li>

					<?php if( count($demoSliders) ) : ?>
					<li data-group="bundled">
						<i class="far fa-file-archive"></i>
						<?php _e('Bundled', 'LayerSlider') ?>
					</li>
					<?php endif; ?>

					<li data-group="slider">
						<i class="fas fa-sort"></i>
						<?php _e('Slider', 'LayerSlider') ?>
					</li>

					<li data-group="landing">
						<i class="fas fa-desktop"></i>
						<?php _e('Hero Scene', 'LayerSlider') ?>
					</li>

					<li data-group="website">
						<i class="fas fa-globe-americas"></i>
						<?php _e('Website', 'LayerSlider') ?>
					</li>

					<li data-group="specialeffects">
						<i class="far fa-snowflake"></i>
						<?php _e('Special Effects', 'LayerSlider') ?>
					</li>

					<li data-group="addons">
						<i class="fas fa-puzzle-piece"></i>
						<?php _e('Add-Ons', 'LayerSlider') ?>
					</li>
				</ul>

				<h5>Filter</h5>
				<ul class="shuffle-filters">
					<li class="active">
						<i class="fas fa-tags"></i>
						<?php _e('All', 'LayerSlider') ?>
					</li>
					<li data-group="free">
						<i class="fas fa-gift"></i>
						<?php _e('Free', 'LayerSlider') ?>
					</li>
					<li data-group="premium">
						<i class="fas fa-star"></i>
						<?php _e('Premium', 'LayerSlider') ?>
					</li>
				</ul>
			</nav>

			<div class="items">
				<?php
					if( ! empty($lsStoreData) && ! empty($lsStoreData['sliders']) ) {
						$demoSliders = array_merge($demoSliders, $lsStoreData['sliders']);
					}
					$now = time();
					foreach($demoSliders as $handle => $item) :

						if( ! empty( $item['popup'] ) ) { continue; }
				?>
				<figure class="item" data-name="<?php echo $item['name'] ?>" data-groups="<?php echo $item['groups'] ?>" data-handle="<?php echo $handle; ?>" data-bundled="<?php echo ! empty($item['bundled']) ? 'true' : 'false' ?>" data-premium="<?php echo ( ! empty($item['premium']) ) ? 'true' : 'false' ?>" data-version-warning="<?php echo version_compare($item['requires'], LS_PLUGIN_VERSION, '>') ? 'true' : 'false' ?>">
					<div class="aspect">
						<div class="item-picture" style="background: url(<?php echo $item['preview'] ?>);">
						</div>
						<figcaption>
							<h5>
								<?php echo $item['name'] ?>
								<span>By Kreatura</span>
							</h5>
						</figcaption>
						<div class="item-action item-preview">
							<a target="_blank" href="<?php echo ! empty($item['url']) ? $item['url'] : '#' ?>" >
								<b class="dashicons dashicons-format-image"></b><?php _e('preview', 'LayerSlider') ?>
							</a>
						</div>
						<div class="item-action item-import">
							<a href="#">
								<b class="dashicons dashicons-download"></b><?php _e('import', 'LayerSlider') ?>
							</a>
						</div>

						<?php if( ! empty( $item['released'] ) ) : ?>
							<?php if( strtotime($item['released']) + MONTH_IN_SECONDS > $now ) :  ?>
							<span class="badge-new"><?php _ex('NEW', 'Template Store', 'LayerSlider') ?>
							<?php endif ?>
						<?php endif ?>
					</div>
				</figure>
				<?php endforeach ?>
				<figure class="coming-soon">
					<div class="aspect">
						<table class="absolute-wrapper">
							<tr>
								<td>
									<span><?php _e('Coming soon,<br>stay tuned!', 'LayerSlider') ?></span>
								</td>
							</tr>
						</table>
					</div>
				</figure>
			</div>
		</div>















		<!-- KREATURA POPUPS -->
		<div class="inner popups">
			<nav class="templates-sidemenu">

				<ul class="content-filter">
					<li data-index="0">
						<i class="fas fa-layer-group"></i>
						<?php _e('SLIDERS', 'LayerSlider') ?>
					</li>
					<li data-index="1" class="active">
						<i class="far fa-window-maximize"></i>
						<?php _e('POPUPS', 'LayerSlider') ?>
					</li>
				</ul>

				<div class="separator"></div>

				<h5>Sources</h5>
				<ul class="source-filter">
					<li data-index="1" class="active">
						<img src="<?php echo LS_ROOT_URL.'/static/admin/img/kreatura-logo-red.png' ?>" alt="Kreatura logo">
						<?php _e('Kreatura', 'LayerSlider') ?>
					</li>
					<li data-index="2">
						<img src="<?php echo LS_ROOT_URL.'/static/admin/img/webshopworks-logo-red.png' ?>" alt="WebshopWorks logo">
						<?php _e('WebshopWorks', 'LayerSlider') ?>
					</li>
				</ul>


			</nav>

			<div class="items">
				<?php if( ! empty( $lsStoreData['kreatura-popups'] ) ) : ?>
				<?php foreach( $lsStoreData['kreatura-popups'] as $handle => $item) : ?>
				<figure class="item" data-collection="kreatura-popups" data-name="<?php echo $item['name'] ?>" data-groups="<?php echo $item['groups'] ?>" data-handle="<?php echo $handle; ?>" data-bundled="<?php echo ! empty($item['bundled']) ? 'true' : 'false' ?>" data-premium="<?php echo ( ! empty($item['premium']) ) ? 'true' : 'false' ?>" data-version-warning="<?php echo version_compare($item['requires'], LS_PLUGIN_VERSION, '>') ? 'true' : 'false' ?>">
					<div class="aspect">
						<div class="item-picture" style="background: url(<?php echo $item['preview'] ?>);">
						</div>
						<figcaption>
							<h5>
								<?php echo $item['name'] ?>
								<span>By Kreatura</span>
							</h5>
						</figcaption>
						<div class="item-action item-preview">
							<a target="_blank" href="<?php echo ! empty($item['url']) ? $item['url'] : '#' ?>" >
								<b class="dashicons dashicons-format-image"></b><?php _e('preview', 'LayerSlider') ?>
							</a>
						</div>
						<div class="item-action item-import">
							<a href="#">
								<b class="dashicons dashicons-download"></b><?php _e('import', 'LayerSlider') ?>
							</a>
						</div>

						<?php if( ! empty( $item['released'] ) ) : ?>
							<?php if( strtotime($item['released']) + MONTH_IN_SECONDS > $now ) :  ?>
							<span class="badge-new"><?php _ex('NEW', 'Template Store', 'LayerSlider') ?>
							<?php endif ?>
						<?php endif ?>
					</div>
				</figure>
				<?php endforeach ?>
				<?php endif ?>
				<figure class="coming-soon">
					<div class="aspect">
						<table class="absolute-wrapper">
							<tr>
								<td>
									<span><?php _e('Coming soon,<br>stay tuned!', 'LayerSlider') ?></span>
								</td>
							</tr>
						</table>
					</div>
				</figure>

			</div>

			<!-- Looking for more? slider HTML markup -->
			<div style="width: 100%; overflow: hidden;">
				<div id="popups-looking-for-more" style="width:900px;height:500px;max-width:800px;margin:0 auto;margin-bottom: 100px;">


					<!-- Slide 1-->
					<div class="ls-slide" data-ls="globalhover:true; overflow:true; kenburnsscale:1.2; parallaxevent:scroll; parallaxdurationmove:300; parallaxdistance:5;">
						<img width="900" height="500" src="<?php echo LS_ROOT_URL ?>/static/admin/img/ls-slider-296-slide-1.jpg" class="ls-tn" alt="" />
						<div style="box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08); border-radius: 1rem !important;top:100%; left:0px; background-size:inherit; background-position:inherit; font-size:18px; width:100%; height:70%; background-color:#ffffff;" class="ls-l"></div>
						<img width="447" height="297" src="<?php echo LS_ROOT_URL ?>/static/admin/img/surprise-box.png" class="ls-l" alt="" style="top:239px; left:611px; background-size:inherit; background-position:inherit; width:288px; height:191px;" data-ls="offsetyin:50; durationin:2000; easingin:easeOutQuint; loopstartat:transitioninstart ;">
						<img width="731" height="365" src="<?php echo LS_ROOT_URL ?>/static/admin/img/papers-far.png" class="ls-l" alt="" style="top:52px; left:539px; background-size:inherit; background-position:inherit; width:471px; height:235px;" data-ls="offsetyin:75; durationin:2000; easingin:easeOutQuint; scalexin:.5; scaleyin:.5; loop:true; loopoffsety:-10; loopduration:10000; loopstartat:transitioninstart ; loopeasing:easeInOutSine; looprotate:5; loopscalex:1.05; loopscaley:1.05; loopcount:-1; loopyoyo:true;">
						<p style="top:298px; left:42px; text-align:initial; font-weight:700; font-style:normal; text-decoration:none; mix-blend-mode:normal; color:#ff5f5a; font-family:Poppins; letter-spacing:0px; line-height:falsepx; font-size:67px;" class="ls-l" data-ls="offsetyin:40; durationin:2000; delayin:500; easingin:easeOutQuint; offsetyout:100; easingout:easeInQuint;">WebshopWorks</p>
						<p style="top:291px; left:306px; text-align:initial; font-weight:400; font-style:normal; text-decoration:none; mix-blend-mode:normal; color:#ff5f5a; font-family:Caveat; font-size:36px; letter-spacing:0px;" class="ls-l" data-ls="offsetyin:60; durationin:2000; delayin:500; easingin:easeOutQuint; offsetyout:80; easingout:easeInQuint;">from</p>
						<p style="top:254px; left:43px; text-align:initial; font-weight:300; font-style:normal; text-decoration:none; mix-blend-mode:normal; color:#ff5f5a; font-family:Poppins; font-size:36px; letter-spacing:0px;" class="ls-l" data-ls="offsetyin:80; durationin:2000; delayin:500; easingin:easeOutQuint; offsetyout:60; easingout:easeInQuint;">Premium Popup Template Pack</p>
						<p style="top:172px; left:40px; text-align:initial; font-weight:700; font-style:normal; text-decoration:none; mix-blend-mode:normal; color:#21d4da; font-family:Lobster; line-height:falsepx; font-size:58px; letter-spacing:2px;" class="ls-l" data-ls="offsetyin:100; durationin:2000; delayin:500; easingin:easeOutQuint; offsetyout:100; easingout:easeInQuint;">Looking for more?</p>
						<img width="363" height="290" src="<?php echo LS_ROOT_URL ?>/static/admin/img/surprise-box-top.png" class="ls-l" alt="" style="top:71px; left:655px; background-size:inherit; background-position:inherit; width:234px; height:187px;" data-ls="offsetyin:75; durationin:2000; easingin:easeOutQuint; scalexin:.5; scaleyin:.5; loop:true; loopoffsety:-30; loopduration:10000; loopstartat:transitioninstart + 0; loopeasing:easeInOutSine; looprotate:5; loopcount:-1; loopyoyo:true;">
						<img width="731" height="365" src="<?php echo LS_ROOT_URL ?>/static/admin/img/papers-close.png" class="ls-l" alt="" style="top:80px; left:559px; background-size:inherit; background-position:inherit; width:471px; height:235px;" data-ls="offsetyin:100; durationin:2000; easingin:easeOutQuint; scalexin:.5; scaleyin:.5; loop:true; loopoffsety:-60; loopduration:10000; loopstartat:transitioninstart ; loopeasing:easeInOutSine; looprotate:5; loopscalex:1.15; loopscaley:1.15; loopcount:-1; loopyoyo:true;">

						<p style="box-shadow: 0 6px 10px rgba(0,0,0,0.1);
			cursor:pointer;top:421px; left:300px; text-align:center; font-weight:600; font-style:normal; text-decoration:none; mix-blend-mode:normal; font-family:Poppins; height:48px; border-radius:24px; line-height:48px; background-color:#24d4da; font-size:20px; padding-right:30px; padding-left:30px; color:#ffffff;" class="ls-l" data-ls="offsetyin:20; durationin:2000; delayin:500; easingin:easeOutQuint; offsetyout:140; easingout:easeInQuint; hover:true; hoveroffsety:-5;">CLICK HERE TO EXPLORE</p>
						<a href="#" id="open-webshopworks-popups" target="_self" class="ls-link ls-link-on-top"></a>
					</div>
				</div>
			</div>
		</div>


















		<!-- WEBSHOPWORKS POPUPS -->
		<div class="inner popups">
			<nav class="templates-sidemenu">

				<ul class="content-filter">
					<li data-index="0">
						<i class="fas fa-layer-group"></i>
						<?php _e('SLIDERS', 'LayerSlider') ?>
					</li>
					<li data-index="1" class="active">
						<i class="far fa-window-maximize"></i>
						<?php _e('POPUPS', 'LayerSlider') ?>
					</li>
				</ul>

				<div class="separator"></div>

				<h5>Sources</h5>
				<ul class="source-filter">
					<li data-index="1">
						<img src="<?php echo LS_ROOT_URL.'/static/admin/img/kreatura-logo-red.png' ?>" alt="Kreatura logo">
						<?php _e('Kreatura', 'LayerSlider') ?>
					</li>
					<li data-index="2" class="active">
						<img src="<?php echo LS_ROOT_URL.'/static/admin/img/webshopworks-logo-red.png' ?>" alt="WebshopWorks logo">
						<?php _e('WebshopWorks', 'LayerSlider') ?>
					</li>
				</ul>

				<h5>Categories</h5>
				<ul class="shuffle-filters">
					<li class="active">
						<i class="fas fa-tags"></i>
						<?php _e('All', 'LayerSlider') ?>
					</li>

					<li data-group="newsletter">
						<i class="fas fa-envelope"></i>
						<?php _e('Newsletter', 'LayerSlider') ?>
					</li>

					<li data-group="sales">
						<i class="fas fa-percent"></i>
						<?php _e('Sales', 'LayerSlider') ?>
					</li>

					<li data-group="exit-intent">
						<i class="fas fa-door-open"></i>
						<?php _e('Exit-intent', 'LayerSlider') ?>
					</li>

					<li data-group="contact-us">
						<i class="fas fa-user-friends"></i>
						<?php _e('Contact Us', 'LayerSlider') ?>
					</li>

					<li data-group="social">
						<i class="fas fa-share-alt"></i>
						<?php _e('Social', 'LayerSlider') ?>
					</li>

					<li data-group="age-verification">
						<i class="fas fa-user-check"></i>
						<?php _e('Age-verification', 'LayerSlider') ?>
					</li>

					<li data-group="seasonal">
						<i class="fas fa-tree"></i>
						<?php _e('Seasonal', 'LayerSlider') ?>
					</li>

					<li data-group="coupon">
						<i class="fas fa-ticket-alt"></i>
						<?php _e('Coupons', 'LayerSlider') ?>
					</li>

					<li data-group="promotion">
						<i class="fas fa-tshirt"></i>
						<?php _e('Promotion', 'LayerSlider') ?>
					</li>

					<li data-group="fullscreen">
						<i class="fas fa-expand-arrows-alt"></i>
						<?php _e('Fullscreen', 'LayerSlider') ?>
					</li>
				</ul>

			</nav>

			<div class="items">
				<?php if( ! empty( $lsStoreData['webshopworks-popups'] ) ) : ?>
				<?php foreach( $lsStoreData['webshopworks-popups'] as $handle => $item) : ?>
				<figure class="item" data-collection="webshopworks-popups" data-name="<?php echo $item['name'] ?>" data-groups="<?php echo $item['groups'] ?>" data-handle="<?php echo $handle; ?>" data-bundled="<?php echo ! empty($item['bundled']) ? 'true' : 'false' ?>" data-premium="<?php echo ( ! empty($item['premium']) ) ? 'true' : 'false' ?>" data-version-warning="<?php echo version_compare($item['requires'], LS_PLUGIN_VERSION, '>') ? 'true' : 'false' ?>">
					<div class="aspect">
						<div class="item-picture" style="background: url(<?php echo $item['preview'] ?>);">
						</div>
						<figcaption>
							<h5>
								<?php echo $item['name'] ?>
								<span>By WebshopWorks</span>
							</h5>
						</figcaption>
						<div class="item-action item-preview">
							<a target="_blank" href="<?php echo ! empty($item['url']) ? $item['url'] : '#' ?>" >
								<b class="dashicons dashicons-format-image"></b><?php _e('preview', 'LayerSlider') ?>
							</a>
						</div>
						<div class="item-action item-import">
							<a href="#">
								<b class="dashicons dashicons-download"></b><?php _e('import', 'LayerSlider') ?>
							</a>
						</div>

						<?php if( ! empty( $item['released'] ) ) : ?>
							<?php if( strtotime($item['released']) + MONTH_IN_SECONDS > $now ) :  ?>
							<span class="badge-new"><?php _ex('NEW', 'Template Store', 'LayerSlider') ?>
							<?php endif ?>
						<?php endif ?>
					</div>
				</figure>
				<?php endforeach ?>
				<?php endif ?>
				<figure class="coming-soon">
					<div class="aspect">
						<table class="absolute-wrapper">
							<tr>
								<td>
									<span><?php _e('Coming soon,<br>stay tuned!', 'LayerSlider') ?></span>
								</td>
							</tr>
						</table>
					</div>
				</figure>
			</div>
		</div>
	</div>
</script>