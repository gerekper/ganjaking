<?php if ( ! empty( $settings->enable_socialbuttons ) ) {
	if ( ! empty( $settings->show_sharebutton_on ) ) {
		if ( $settings->show_sharebutton_on == 'front' || $settings->show_sharebutton_on == 'both' ) { ?>
	
			  <?php
				$ref_link = seed_cspv5_legacy_ref_link();
				?>
		
			<ul id="cspio-sharebuttons">

				<?php if ( isset( $settings->share_buttons->twitter ) && $settings->share_buttons->twitter == '1' ) { ?>
					<li id="share_twitter">
						<a onclick="return !window.open(this.href, 'Share', 'width=500,height=500')"
	target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo urlencode( $settings->tweet_text . ' ' . $ref_link ); ?>" class="btn btn-xs"><i class="fab fa-twitter"></i> Tweet</a>
					</li>
				<?php } ?>

				<?php if ( isset( $settings->share_buttons->facebook ) && $settings->share_buttons->facebook == '1' ) { ?>
					<li id="share_facebook">
						<a onclick="return !window.open(this.href, 'Share', 'width=500,height=500')"
	target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( $ref_link ); ?>" class="btn btn-xs"><i class="fab fa-facebook"></i> Share</a>
					</li>
				<?php } ?>

				<?php if ( isset( $settings->share_buttons->linkedin ) && $settings->share_buttons->linkedin == '1' ) { ?>
					<li id="share_linkedin">
					<a onclick="return !window.open(this.href, 'Share', 'width=500,height=500')"
	target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode( $ref_link ); ?>" class="btn btn-xs"><i class="fab fa-linkedin"></i> Share</a>
					</li>
				<?php } ?>

				<?php if ( isset( $settings->share_buttons->pinterest ) && $settings->share_buttons->pinterest == '1' ) { ?>
					<li id="share_pinterest">
						<a onclick="return !window.open(this.href, 'Share', 'width=500,height=500')"
	target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode( $ref_link ); ?>&media=<?php echo $settings->pinterest_thumbnail; ?>&description=<?php echo $settings->seo_description; ?>" class="btn btn-xs"><i class="fab fa-pinterest"></i> Pin</a>
					</li>
				<?php } ?>

			</ul>
		
<?php }
	}
} ?>
