<?php
add_action( 'widgets_init', 'porto_follow_us_load_widgets' );

function porto_follow_us_load_widgets() {
	register_widget( 'Porto_Follow_Us_Widget' );
}

class Porto_Follow_Us_Widget extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'follow-us',
			'description' => __( 'Add Social Links.', 'porto-functionality' ),
		);

		$control_ops = array( 'id_base' => 'follow-us-widget' );

		parent::__construct( 'follow-us-widget', __( 'Porto: Follow Us', 'porto-functionality' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title           = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		$nofollow        = isset( $instance['nofollow'] ) ? $instance['nofollow'] : '';
		$default_skin    = isset( $instance['default_skin'] ) ? $instance['default_skin'] : '';
		$disable_br      = isset( $instance['disable_br'] ) ? $instance['disable_br'] : '';
		$disable_tooltip = isset( $instance['disable_tooltip'] ) ? $instance['disable_tooltip'] : '';
		$follow_before   = isset( $instance['follow_before'] ) ? $instance['follow_before'] : '';
		$facebook        = isset( $instance['facebook'] ) ? $instance['facebook'] : '';
		$twitter         = isset( $instance['twitter'] ) ? $instance['twitter'] : '';
		$rss             = isset( $instance['rss'] ) ? $instance['rss'] : '';
		$pinterest       = isset( $instance['pinterest'] ) ? $instance['pinterest'] : '';
		$youtube         = isset( $instance['youtube'] ) ? $instance['youtube'] : '';
		$instagram       = isset( $instance['instagram'] ) ? $instance['instagram'] : '';
		$skype           = isset( $instance['skype'] ) ? $instance['skype'] : '';
		$linkedin        = isset( $instance['linkedin'] ) ? $instance['linkedin'] : '';
		$vk              = isset( $instance['vk'] ) ? $instance['vk'] : '';
		$xing            = isset( $instance['xing'] ) ? $instance['xing'] : '';
		$tumblr          = isset( $instance['tumblr'] ) ? $instance['tumblr'] : '';
		$reddit          = isset( $instance['reddit'] ) ? $instance['reddit'] : '';
		$vimeo           = isset( $instance['vimeo'] ) ? $instance['vimeo'] : '';
		$telegram        = isset( $instance['telegram'] ) ? $instance['telegram'] : '';
		$yelp            = isset( $instance['yelp'] ) ? $instance['yelp'] : '';
		$flickr          = isset( $instance['flickr'] ) ? $instance['flickr'] : '';
		$whatsapp        = isset( $instance['whatsapp'] ) ? $instance['whatsapp'] : '';
		$wechat          = isset( $instance['wechat'] ) ? $instance['wechat'] : '';
		$tiktok          = isset( $instance['tiktok'] ) ? $instance['tiktok'] : '';
		$follow_after    = isset( $instance['follow_after'] ) ? $instance['follow_after'] : '';

		if ( $nofollow ) {
			$nofollow_escaped = ' rel="nofollow noopener noreferrer"';
		} else {
			$nofollow_escaped = ' rel="noopener noreferrer"';
		}

		echo porto_filter_output( $before_widget );

		if ( $title ) {
			echo $before_title . sanitize_text_field( $title ) . $after_title;
		}

		$class_escaped   = 'share-links';
		$tooltip_escaped = '';
		if ( $disable_br ) {
			$class_escaped .= ' disable-br';
		}
		if ( $default_skin ) {
			$class_escaped .= ' default-skin';
		}
		if ( ! $disable_tooltip ) {
			$tooltip_escaped = 'data-toggle="tooltip" data-bs-placement="bottom" ';
		}
		?>
		<div class="<?php echo $class_escaped; ?>">
			<?php
			if ( $follow_before ) :
				?>
				<?php echo do_shortcode( $follow_before ); ?><?php endif; ?>
			<?php
			if ( $facebook ) :
				?>
				<a href="<?php echo esc_url( $facebook ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Facebook', 'porto-functionality' ); ?>" class="share-facebook"><?php esc_html_e( 'Facebook', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $twitter ) :
				?>
				<a href="<?php echo esc_url( $twitter ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Twitter', 'porto-functionality' ); ?>" class="share-twitter"><?php esc_html_e( 'Twitter', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $rss ) :
				?>
				<a href="<?php echo esc_url( $rss ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'RSS', 'porto-functionality' ); ?>" class="share-rss"><?php esc_html_e( 'RSS', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $pinterest ) :
				?>
				<a href="<?php echo esc_url( $pinterest ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Pinterest', 'porto-functionality' ); ?>" class="share-pinterest"><?php esc_html_e( 'Pinterest', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $youtube ) :
				?>
				<a href="<?php echo esc_url( $youtube ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Youtube', 'porto-functionality' ); ?>" class="share-youtube"><?php esc_html_e( 'Youtube', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $instagram ) :
				?>
				<a href="<?php echo esc_url( $instagram ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Instagram', 'porto-functionality' ); ?>" class="share-instagram"><?php esc_html_e( 'Instagram', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $skype ) :
				?>
				<a href="<?php echo esc_attr( $skype ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Skype', 'porto-functionality' ); ?>" class="share-skype"><?php esc_html_e( 'Skype', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $linkedin ) :
				?>
				<a href="<?php echo esc_url( $linkedin ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Linkedin', 'porto-functionality' ); ?>" class="share-linkedin"><?php esc_html_e( 'Linkedin', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $vk ) :
				?>
				<a href="<?php echo esc_url( $vk ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'VK', 'porto-functionality' ); ?>" class="share-vk"><?php esc_html_e( 'VK', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $xing ) :
				?>
				<a href="<?php echo esc_url( $xing ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Xing', 'porto-functionality' ); ?>" class="share-xing"><?php esc_html_e( 'Xing', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $tumblr ) :
				?>
				<a href="<?php echo esc_url( $tumblr ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Tumblr', 'porto-functionality' ); ?>" class="share-tumblr"><?php esc_html_e( 'Tumblr', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $reddit ) :
				?>
				<a href="<?php echo esc_url( $reddit ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Reddit', 'porto-functionality' ); ?>" class="share-reddit"><?php esc_html_e( 'Reddit', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $vimeo ) :
				?>
				<a href="<?php echo esc_url( $vimeo ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Vimeo', 'porto-functionality' ); ?>" class="share-vimeo"><?php esc_html_e( 'Vimeo', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $telegram ) :
				?>
				<a href="<?php echo esc_url( $telegram ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Telegram', 'porto-functionality' ); ?>" class="share-telegram"><?php esc_html_e( 'Telegram', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $yelp ) :
				?>
				<a href="<?php echo esc_url( $yelp ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Yelp', 'porto-functionality' ); ?>" class="share-yelp"><?php esc_html_e( 'Yelp', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $flickr ) :
				?>
				<a href="<?php echo esc_url( $flickr ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Flickr', 'porto-functionality' ); ?>" class="share-flickr"><?php esc_html_e( 'Flickr', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $whatsapp ) :
				?>
				<a href="whatsapp://send?text=<?php echo esc_attr( $whatsapp ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'WhatsApp', 'porto-functionality' ); ?>" class="share-whatsapp" style="display:none"><?php esc_html_e( 'WhatsApp', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $wechat ) :
				?>
				<a href="<?php echo esc_url( $wechat ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'WeChat', 'porto-functionality' ); ?>" class="share-wechat"><?php esc_html_e( 'WeChat', 'porto-functionality' ); ?></a>
				<?php
			endif;

			if ( $tiktok ) :
				?>
				<a href="<?php echo esc_url( $tiktok ); ?>" <?php echo $nofollow_escaped; ?> target="_blank" <?php echo $tooltip_escaped; ?>title="<?php esc_attr_e( 'Tiktok', 'porto-functionality' ); ?>" class="share-tiktok"><?php esc_html_e( 'Tiktok', 'porto-functionality' ); ?></a>
				<?php
			endif;
			?>
			<?php
			if ( $follow_after ) :
				?>
				<?php echo do_shortcode( $follow_after ); ?>
			<?php endif; ?>
		</div>

		<?php
		echo porto_filter_output( $after_widget );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['nofollow'] = $new_instance['nofollow'];
		if ( isset( $new_instance['default_skin'] ) ) {
			$instance['default_skin'] = $new_instance['default_skin'];
		} else {
			$instance['default_skin'] = '';
		}
		if ( isset( $new_instance['disable_br'] ) ) {
			$instance['disable_br'] = $new_instance['disable_br'];
		}
		if ( isset( $new_instance['disable_tooltip'] ) ) {
			$instance['disable_tooltip'] = $new_instance['disable_tooltip'];
		}
		$instance['follow_before'] = $new_instance['follow_before'];
		$instance['facebook']      = $new_instance['facebook'];
		$instance['twitter']       = $new_instance['twitter'];
		$instance['rss']           = $new_instance['rss'];
		$instance['pinterest']     = $new_instance['pinterest'];
		$instance['youtube']       = $new_instance['youtube'];
		$instance['instagram']     = $new_instance['instagram'];
		$instance['skype']         = $new_instance['skype'];
		$instance['linkedin']      = $new_instance['linkedin'];
		$instance['vk']            = $new_instance['vk'];
		$instance['xing']          = $new_instance['xing'];
		$instance['tumblr']        = $new_instance['tumblr'];
		$instance['reddit']        = $new_instance['reddit'];
		$instance['vimeo']         = $new_instance['vimeo'];
		$instance['telegram']      = $new_instance['telegram'];
		$instance['yelp']          = $new_instance['yelp'];
		$instance['flickr']        = $new_instance['flickr'];
		$instance['whatsapp']      = $new_instance['whatsapp'];
		$instance['follow_after']  = $new_instance['follow_after'];
		if ( isset( $new_instance['wechat'] ) ) {
			$instance['wechat'] = $new_instance['wechat'];
		}
		if ( isset( $new_instance['tiktok'] ) ) {
			$instance['tiktok'] = $new_instance['tiktok'];
		}

		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'           => __( 'Follow Us', 'porto-functionality' ),
			'nofollow'        => '',
			'default_skin'    => '',
			'disable_br'      => '',
			'disable_tooltip' => '',
			'follow_before'   => '',
			'facebook'        => '',
			'twitter'         => '',
			'rss'             => '',
			'pinterest'       => '',
			'youtube'         => '',
			'instagram'       => '',
			'skype'           => '',
			'linkedin'        => '',
			'vk'              => '',
			'xing'            => '',
			'tumblr'          => '',
			'reddit'          => '',
			'vimeo'           => '',
			'telegram'        => '',
			'yelp'            => '',
			'flickr'          => '',
			'whatsapp'        => '',
			'wechat'          => '',
			'tiktok'          => '',
			'follow_after'    => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<strong><?php esc_html_e( 'Title', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo isset( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['nofollow'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'nofollow' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'nofollow' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'nofollow' ) ); ?>"><?php esc_html_e( 'Add rel="nofollow" to links', 'porto-functionality' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['disable_br'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'disable_br' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'disable_br' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'disable_br' ) ); ?>"><?php esc_html_e( 'Disable border radius', 'porto-functionality' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['disable_tooltip'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'disable_tooltip' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'disable_tooltip' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'disable_tooltip' ) ); ?>"><?php esc_html_e( 'Disable tooltip', 'porto-functionality' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['default_skin'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'default_skin' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'default_skin' ) ); ?>" value="on" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'default_skin' ) ); ?>"><?php esc_html_e( 'Use default skin', 'porto-functionality' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'follow_before' ) ); ?>">
				<strong><?php esc_html_e( 'Before Description', 'porto-functionality' ); ?>:</strong>
				<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'follow_before' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'follow_before' ) ); ?>"><?php echo isset( $instance['follow_before'] ) ? esc_attr( $instance['follow_before'] ) : ''; ?></textarea>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'facebook' ) ); ?>">
				<strong><?php esc_html_e( 'Facebook', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'facebook' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'facebook' ) ); ?>" value="<?php echo isset( $instance['facebook'] ) ? esc_attr( $instance['facebook'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>">
				<strong><?php esc_html_e( 'Twitter', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter' ) ); ?>" value="<?php echo isset( $instance['twitter'] ) ? esc_attr( $instance['twitter'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'rss' ) ); ?>">
				<strong><?php esc_html_e( 'RSS', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'rss' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rss' ) ); ?>" value="<?php echo isset( $instance['rss'] ) ? esc_attr( $instance['rss'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>">
				<strong><?php esc_html_e( 'Pinterest', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pinterest' ) ); ?>" value="<?php echo isset( $instance['pinterest'] ) ? esc_attr( $instance['pinterest'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>">
				<strong><?php esc_html_e( 'Youtube', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'youtube' ) ); ?>" value="<?php echo isset( $instance['youtube'] ) ? esc_attr( $instance['youtube'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>">
				<strong><?php esc_html_e( 'Instagram', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'instagram' ) ); ?>" value="<?php echo isset( $instance['instagram'] ) ? esc_attr( $instance['instagram'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'skype' ) ); ?>">
				<strong><?php esc_html_e( 'Skype', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'skype' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'skype' ) ); ?>" value="<?php echo isset( $instance['skype'] ) ? esc_attr( $instance['skype'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'linkedin' ) ); ?>">
				<strong><?php esc_html_e( 'Linkedin', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'linkedin' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linkedin' ) ); ?>" value="<?php echo isset( $instance['linkedin'] ) ? esc_attr( $instance['linkedin'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'vk' ) ); ?>">
				<strong><?php esc_html_e( 'VK', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'vk' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vk' ) ); ?>" value="<?php echo isset( $instance['vk'] ) ? esc_attr( $instance['vk'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'xing' ) ); ?>">
				<strong><?php esc_html_e( 'Xing', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'xing' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'xing' ) ); ?>" value="<?php echo isset( $instance['xing'] ) ? esc_attr( $instance['xing'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'tumblr' ) ); ?>">
				<strong><?php esc_html_e( 'Tumblr', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tumblr' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tumblr' ) ); ?>" value="<?php echo isset( $instance['tumblr'] ) ? esc_attr( $instance['tumblr'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'reddit' ) ); ?>">
				<strong><?php esc_html_e( 'Reddit', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'reddit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'reddit' ) ); ?>" value="<?php echo isset( $instance['reddit'] ) ? esc_attr( $instance['reddit'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'vimeo' ) ); ?>">
				<strong><?php esc_html_e( 'Vimeo', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'vimeo' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vimeo' ) ); ?>" value="<?php echo isset( $instance['vimeo'] ) ? esc_attr( $instance['vimeo'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'telegram' ) ); ?>">
				<strong><?php esc_html_e( 'Telegram', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'telegram' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'telegram' ) ); ?>" value="<?php echo isset( $instance['telegram'] ) ? esc_attr( $instance['telegram'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'yelp' ) ); ?>">
				<strong><?php esc_html_e( 'Yelp', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'yelp' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'yelp' ) ); ?>" value="<?php echo isset( $instance['yelp'] ) ? esc_attr( $instance['yelp'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'flickr' ) ); ?>">
				<strong><?php esc_html_e( 'Flickr', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'flickr' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'flickr' ) ); ?>" value="<?php echo isset( $instance['flickr'] ) ? esc_attr( $instance['flickr'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'whatsapp' ) ); ?>">
				<strong><?php esc_html_e( 'WhatsApp', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'whatsapp' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'whatsapp' ) ); ?>" value="<?php echo isset( $instance['whatsapp'] ) ? esc_attr( $instance['whatsapp'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'wechat' ) ); ?>">
				<strong><?php esc_html_e( 'WeChat', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wechat' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wechat' ) ); ?>" value="<?php echo isset( $instance['wechat'] ) ? esc_attr( $instance['wechat'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'tiktok' ) ); ?>">
				<strong><?php esc_html_e( 'Tiktok', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tiktok' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tiktok' ) ); ?>" value="<?php echo isset( $instance['tiktok'] ) ? esc_attr( $instance['tiktok'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'follow_after' ) ); ?>">
				<strong><?php esc_html_e( 'After Description', 'porto-functionality' ); ?>:</strong>
				<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'follow_after' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'follow_after' ) ); ?>"><?php echo isset( $instance['follow_after'] ) ? wp_kses_post( $instance['follow_after'] ) : ''; ?></textarea>
			</label>
		</p>
		<?php
	}
}
?>
