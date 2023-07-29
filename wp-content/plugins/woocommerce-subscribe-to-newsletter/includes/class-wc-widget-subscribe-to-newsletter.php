<?php
/**
 * Subscribe to Newsletter Widget
 *
 * @package WC_Newsletter_Subscription/Widgets
 * @since   2.3.5
 */

/**
 * Subscribe to newsletter widget class.
 */
class WC_Widget_Subscribe_To_Newsletter extends WC_Widget {

	/**
	 * Constructor.
	 *
	 * @since 2.3.5
	 */
	public function __construct() {
		$this->widget_id          = 'woocommerce_subscribe_to_newsletter';
		$this->widget_name        = _x( 'WooCommerce Subscribe to Newsletter', 'widget title', 'woocommerce-subscribe-to-newsletter' );
		$this->widget_description = _x( 'Allows users to subscribe to your newsletter.', 'widget desc', 'woocommerce-subscribe-to-newsletter' );
		$this->widget_cssclass    = 'widget_subscribe_to_newsletter';

		parent::__construct();

		add_action( 'woocommerce_widget_field_wc_newsletter_subscription_no_service', array( $this, 'output_no_service_field' ) );
	}

	/**
	 * Initializes the widget settings.
	 *
	 * @since 2.8.0
	 */
	public function init_settings() {
		if ( empty( $this->settings ) ) {
			$this->settings = $this->get_setting_fields();
		}
	}

	/**
	 * Gets the widget settings.
	 *
	 * @since 2.5.0
	 *
	 * @return array An array with the widget settings.
	 */
	public function get_setting_fields() {
		$provider = wc_newsletter_subscription_get_provider();

		if ( ! $provider || ! $provider->is_enabled() ) {
			$settings = array(
				'no_service' => array(
					'type' => 'wc_newsletter_subscription_no_service',
					'std'  => '',
				),
			);
		} else {
			$lists = $provider->get_lists();

			$settings = array(
				'title'       => array(
					'type'  => 'text',
					'label' => _x( 'Title:', 'widget setting', 'woocommerce-subscribe-to-newsletter' ),
					'std'   => _x( 'Subscribe to our Newsletter', 'widget default title', 'woocommerce-subscribe-to-newsletter' ),
				),
				'message'     => array(
					'type'  => 'textarea',
					'label' => _x( 'Message:', 'widget setting', 'woocommerce-subscribe-to-newsletter' ),
					'std'   => _x( 'Join our mailing list to receive the latest news.', 'widget default message', 'woocommerce-subscribe-to-newsletter' ),
				),
				'list'        => array(
					'type'    => 'select',
					'label'   => _x( 'List:', 'widget setting', 'woocommerce-subscribe-to-newsletter' ),
					'std'     => '',
					'options' => $lists,
				),
				'name_fields' => array(
					'type'    => 'select',
					'label'   => _x( 'Name field(s):', 'widget setting', 'woocommerce-subscribe-to-newsletter' ),
					'std'     => 'no_name',
					'options' => array(
						'no_name'         => _x( 'No name', 'widget setting option', 'woocommerce-subscribe-to-newsletter' ),
						'single_name'     => _x( 'Single name', 'widget setting option', 'woocommerce-subscribe-to-newsletter' ),
						'first_last_name' => _x( 'First + Last name', 'widget setting option', 'woocommerce-subscribe-to-newsletter' ),
					),
				),
			);
		}

		/**
		 * Filters the widget settings.
		 *
		 * @since 2.5.0
		 *
		 * @param array $settings An array with the widget settings.
		 */
		return apply_filters( 'wc_newsletter_subscription_widget_settings', $settings );
	}

	/**
	 * Outputs the content of the custom field 'wc_subscribe_to_newsletter_no_service'.
	 *
	 * @since 2.5.0
	 */
	public function output_no_service_field() {
		?>
		<div class="wc-subscribe-to-newsletter-widget-no-service">
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: settings URL */
						__( 'You must set up <a href="%s">API details</a> before using this widget.', 'woocommerce-subscribe-to-newsletter' ),
						esc_url( wc_newsletter_subscription_get_settings_url() )
					)
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @since 2.5.0
	 *
	 * @see WC_Widget->form
	 *
	 * @param array $instance Widget Instance.
	 */
	public function form( $instance ) {
		// Backward compatibility.
		if ( ! empty( $instance ) && ! isset( $instance['message'] ) ) {
			$instance['message'] = '';
		}

		if ( isset( $instance['show_name'] ) ) {
			$instance['name_fields'] = 'single_name';

			unset( $instance['show_name'] );
		}

		$this->init_settings();

		parent::form( $instance );
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @since 2.8.0
	 *
	 * @see WC_Widget->update
	 *
	 * @param array $new_instance New instance.
	 * @param array $old_instance Old instance.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$this->init_settings();

		return parent::update( $new_instance, $old_instance );
	}

	/**
	 * Enqueue widget scripts.
	 *
	 * @since 2.5.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'wc_subscribe_to_newsletter_inline_widget_styles', WC_NEWSLETTER_SUBSCRIPTION_URL . 'assets/css/subscription-widget.css', array(), WC_NEWSLETTER_SUBSCRIPTION_VERSION );

		$suffix = wc_newsletter_subscription_get_scripts_suffix();

		wp_enqueue_script( 'wc_newsletter_subscription_widget', WC_NEWSLETTER_SUBSCRIPTION_URL . 'assets/js/frontend/subscription-widget' . $suffix . '.js', array( 'jquery' ), WC_NEWSLETTER_SUBSCRIPTION_VERSION, true );
		wp_localize_script(
			'wc_newsletter_subscription_widget',
			'wc_newsletter_subscription_widget_params',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php', 'relative' ),
			)
		);
	}

	/**
	 * Gets the widget form fields.
	 *
	 * @since 2.5.0
	 *
	 * @param array $instance Widget instance.
	 * @return array
	 */
	public function get_form_fields( $instance ) {
		$fields = array(
			'name'       => array(
				'id'          => 'name',
				'name'        => 'newsletter_name',
				'label'       => _x( 'Name:', 'widget label', 'woocommerce-subscribe-to-newsletter' ),
				'placeholder' => _x( 'Name', 'widget placeholder', 'woocommerce-subscribe-to-newsletter' ),
			),
			'first_name' => array(
				'id'          => 'first_name',
				'name'        => 'newsletter_first_name',
				'label'       => _x( 'First Name:', 'widget label', 'woocommerce-subscribe-to-newsletter' ),
				'placeholder' => _x( 'First name', 'widget placeholder', 'woocommerce-subscribe-to-newsletter' ),
			),
			'last_name'  => array(
				'id'          => 'last_name',
				'name'        => 'newsletter_last_name',
				'label'       => _x( 'Last Name:', 'widget label', 'woocommerce-subscribe-to-newsletter' ),
				'placeholder' => _x( 'Last name', 'widget placeholder', 'woocommerce-subscribe-to-newsletter' ),
			),
			'phone'      => array( // Honeypot field.
				'id'                => 'phone',
				'name'              => 'newsletter_phone',
				'label'             => _x( 'Phone:', 'widget label', 'woocommerce-subscribe-to-newsletter' ),
				'placeholder'       => _x( 'Phone', 'widget placeholder', 'woocommerce-subscribe-to-newsletter' ),
				'custom_attributes' => array(
					'autocomplete' => 'off',
					'tabindex'     => '-1',
				),
			),
			'email'      => array(
				'id'                => 'email',
				'type'              => 'email',
				'name'              => 'newsletter_email',
				'label'             => _x( 'Email Address:', 'widget label', 'woocommerce-subscribe-to-newsletter' ),
				'placeholder'       => _x( 'Email address', 'widget placeholder', 'woocommerce-subscribe-to-newsletter' ),
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'submit'     => array(
				'id'    => 'newsletter_subscribe',
				'type'  => 'submit',
				'class' => 'button alt',
				'value' => _x( 'Subscribe', 'widget submit', 'woocommerce-subscribe-to-newsletter' ),
			),
		);

		$name_fields = ( ! empty( $instance['name_fields'] ) ? $instance['name_fields'] : '' );

		// Backward compatibility.
		if ( ! $name_fields && ! empty( $instance['show_name'] ) ) {
			$name_fields = 'single_name';
		}

		if ( 'first_last_name' === $name_fields ) {
			unset( $fields['name'] );
		} elseif ( 'single_name' === $name_fields ) {
			unset( $fields['first_name'], $fields['last_name'] );
		} else {
			unset( $fields['name'], $fields['first_name'], $fields['last_name'] );
		}

		/**
		 * Filters the widget form fields.
		 *
		 * @since 2.5.0
		 *
		 * @param array $fields   An array with the widget form fields.
		 * @param array $instance Widget instance.
		 */
		return apply_filters( 'wc_newsletter_subscription_widget_form_fields', $fields, $instance );
	}

	/**
	 * Output the content of the widget.
	 *
	 * @since 2.3.5
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! wc_newsletter_subscription_is_connected() ) {
			return;
		}

		$list_id = isset( $instance['list'] ) ? $instance['list'] : '';
		$message = isset( $instance['message'] ) ? $instance['message'] : '';
		$fields  = $this->get_form_fields( $instance );

		$this->enqueue_scripts();

		$this->widget_start( $args, $instance );
		?>
		<?php if ( $message ) : ?>
			<p class="wc-subscribe-to-newsletter-message"><?php echo wp_kses_post( $message ); ?></p>
		<?php endif; ?>

		<div class="wc-subscribe-to-newsletter-notice"></div>

		<form id="subscribeform" class="woocommerce" method="post">
			<?php wp_nonce_field( 'wc_subscribe_to_newsletter_widget' ); ?>

			<input type="hidden" name="action" value="subscribe_to_newsletter">
			<input type="hidden" name="list_id" value="<?php echo esc_attr( $list_id ); ?>">

			<div class="wc-subscribe-to-newsletter-form-container">
				<?php
				foreach ( $fields as $field ) :
					$method = ( ! empty( $field['type'] ) ? "output_{$field['type']}_field" : '' );

					if ( $method && method_exists( $this, $method ) ) :
						call_user_func( array( $this, $method ), $field );
					else :
						$this->output_text_field( $field );
					endif;
				endforeach;
				?>
			</div>
		</form>
		<?php

		$this->widget_end( $args );
	}

	/**
	 * Outputs a text field.
	 *
	 * @since 2.5.0
	 *
	 * @param array $field Field data.
	 */
	protected function output_text_field( $field ) {
		if ( ! isset( $field['id'] ) ) {
			return;
		}

		$field = wp_parse_args(
			$field,
			array(
				'type'              => 'text',
				'name'              => $field['id'],
				'value'             => '',
				'label'             => '',
				'placeholder'       => '',
				'class'             => '',
				'style'             => '',
				'wrapper_class'     => 'wc-subscribe-to-newsletter-form-field wc-subscribe-to-newsletter-form-field-' . $field['id'],
				'custom_attributes' => array(),
			)
		);

		$custom_attributes = array();

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}

		echo '<div class="' . esc_attr( $field['wrapper_class'] ) . '">';
		echo '<label class="screen-reader-text" for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';
		printf(
			'<input id="%1$s" class="%2$s" name="%3$s" type="%4$s" placeholder="%5$s" %6$s />',
			esc_attr( $field['id'] ),
			esc_attr( $field['class'] ),
			esc_attr( $field['name'] ),
			esc_attr( $field['type'] ),
			esc_attr( $field['placeholder'] ),
			implode( ' ', $custom_attributes ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		echo '</div>';
	}

	/**
	 * Outputs a submit field.
	 *
	 * @since 2.5.0
	 *
	 * @param array $field Field data.
	 */
	protected function output_submit_field( $field ) {
		$field = wp_parse_args(
			$field,
			array(
				'id'            => '',
				'class'         => 'button',
				'style'         => '',
				'value'         => '',
				'wrapper_class' => 'wc-subscribe-to-newsletter-form-field wc-subscribe-to-newsletter-form-field-submit',
			)
		);

		echo '<div class="' . esc_attr( $field['wrapper_class'] ) . '">';
		printf(
			'<input type="submit" id="%1$s" class="%2$s" value="%3$s" style="%4$s" />',
			esc_attr( $field['id'] ),
			esc_attr( $field['class'] ),
			esc_attr( $field['value'] ),
			esc_attr( $field['style'] )
		);
		echo '</div>';
	}
}
