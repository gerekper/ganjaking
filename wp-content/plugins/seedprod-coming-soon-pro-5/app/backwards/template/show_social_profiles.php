<?php
if ( ! empty( $settings->enable_socialprofiles ) ) {
	if ( ! empty( $settings->social_profiles ) ) {
		$upload_dir = wp_upload_dir();

		$fa_4_5_map = array(
			'fa-facebook-official' => 'fab fa-facebook',
			'fa-twitter'           => 'fab fa-twitter',
			'fa-linkedin'          => 'fab fa-linkedin-in',
			'fa-google-plus'       => 'fab fa-google-plus-g',
			'fa-youtube'           => 'fab fa-youtube',
			'fa-flickr'            => 'fab fa-flickr',
			'fa-vimeo'             => 'fab fa-vimeo-v',
			'fa-pinterest'         => 'fab fa-pinterest',
			'fa-instagram'         => 'fab fa-instagram',
			'fa-foursquare'        => 'fab fa-foursquare',
			'fa-skype'             => 'fab fa-skype',
			'fa-tumblr'            => 'fab fa-tumblr',
			'fa-github'            => 'fab fa-github',
			'fa-500px'             => 'fab fa-500px',
			'fa-dribbble'          => 'fab fa-dribbble',
			'fa-slack'             => 'fab fa-slack',
			'fa-soundcloud'        => 'fab fa-soundcloud',
			'fa-snapchat-ghost'    => 'fab fa-snapchat-ghost',
			'fa-rss'               => 'fas fa-rss',
			'fa-envelope'          => 'fas fa-envelope',
			'fa-phone'             => 'fas fa-phone',
			'fa-mobile'            => 'fas fa-mobile-alt',
		); ?>
<div id="cspio-socialprofiles">
		<?php
		foreach ( $settings->social_profiles as $k => $v ) {
			?>
			<?php

			if ( is_multisite() ) {
				$dirpath = $upload_dir['basedir'] . '/seedprod/' . get_current_blog_id() . '/icons-' . $settings->page_id . '/' . strtolower( str_replace( 'fa-', '', $v->icon ) ) . '.png';
				$path    = $upload_dir['baseurl'] . '/seedprod/' . get_current_blog_id() . '/icons-' . $settings->page_id . '/' . strtolower( str_replace( 'fa-', '', $v->icon ) ) . '.png';
			} else {
				$dirpath = $upload_dir['basedir'] . '/seedprod/icons-' . $settings->page_id . '/' . strtolower( str_replace( 'fa-', '', $v->icon ) ) . '.png';
				$path    = $upload_dir['baseurl'] . '/seedprod/icons-' . $settings->page_id . '/' . strtolower( str_replace( 'fa-', '', $v->icon ) ) . '.png';
			}

			//check url for custom icon
			$icon_image = '';
			$url_split  = explode( '|', $v->url );
			$v->url     = $url_split[0];
			if ( ! empty( $url_split[1] ) ) {
				//check for icon
				if ( strpos( $url_split[1], 'fa-' ) !== false ) {
					$v->icon = $url_split[1];
				}
				//check for custom image
				if ( substr( $url_split[1], 0, 4 ) === 'http' ) {
					$icon_image = $url_split[1];
				}
			}
			//var_dump($v->icon);
			$onclick = '';
			$target  = '_blank';

			if ( filter_var( $v->url, FILTER_VALIDATE_EMAIL ) ) {
				$v->url = 'mailto:' . str_replace( 'mailto:', '', $v->url );
			} elseif ( $v->icon == 'fa-skype' ) {
				$v->url = 'skype:' . str_replace( array( 'skype:', '?call' ), '', $v->url ) . '?call';
			} elseif ( $v->icon == 'fa-phone' ) {
				$v->url = 'tel:' . str_replace( array( 'tel:', '?call' ), '', $v->url ) . '';
			} elseif ( $v->url == '[seed_contact_form]' ) {
				$onclick = " onclick=\"javascript:jQuery('#cspio-cf-modal').modal('show')\" ";
				$target  = '';
				$v->url  = 'javascript:void(0)';
			} else {
				if ( filter_var( $v->url, FILTER_VALIDATE_URL ) === false ) {
					$v->url = 'http://' . $v->url;
				}
			}

			if ( file_exists( $dirpath ) ) {
				?>
	<a href="<?php echo $v->url; ?>" target="<?php echo $target; ?>" <?php echo $onclick; ?>><img
			src="<?php echo $path; ?>"></a>
				<?php
			} else {
				?>

				<?php
				if ( empty( $icon_image ) ) {
					//map old icons
					if ( ! empty( $fa_4_5_map[ $v->icon ] ) ) {
						$v->icon = $fa_4_5_map[ $v->icon ];
					}
					?>

	<a href="<?php echo $v->url; ?>" target="<?php echo $target; ?>" <?php echo $onclick; ?>><i
			class="<?php echo $v->icon; ?> <?php echo $settings->social_profiles_size; ?>"></i></a>
					<?php
				} else {
					?>

	<a href="<?php echo $v->url; ?>" target="<?php echo $target; ?>" <?php echo $onclick; ?>><img
			src="<?php echo $icon_image; ?>"></a>

					<?php
				}
				?>



				<?php
			}
			?>
			<?php
		}
		?>
</div>
		<?php
	}
}
?>
