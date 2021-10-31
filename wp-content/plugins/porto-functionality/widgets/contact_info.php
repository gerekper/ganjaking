<?php
add_action( 'widgets_init', 'porto_contact_info_load_widgets' );

function porto_contact_info_load_widgets() {
	register_widget( 'Porto_Contact_Info_Widget' );
}

class Porto_Contact_Info_Widget extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'contact-info',
			'description' => __( 'Add contact information.', 'porto-functionality' ),
		);

		$control_ops = array( 'id_base' => 'contact-info-widget' );

		parent::__construct( 'contact-info-widget', __( 'Porto: Contact Info', 'porto-functionality' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title          = apply_filters( 'widget_title', $instance['title'] );
		$contact_before = $instance['contact_before'];
		$address_label  = ( isset( $instance['address_label'] ) && $instance['address_label'] ) ? $instance['address_label'] : __( 'Address', 'porto-functionality' );
		$phone_label    = ( isset( $instance['phone_label'] ) && $instance['phone_label'] ) ? $instance['phone_label'] : __( 'Phone', 'porto-functionality' );
		$email_label    = ( isset( $instance['email_label'] ) && $instance['email_label'] ) ? $instance['email_label'] : __( 'Email', 'porto-functionality' );
		$working_label  = ( isset( $instance['working_label'] ) && $instance['working_label'] ) ? $instance['working_label'] : __( 'Working Days/Hours', 'porto-functionality' );
		$address        = $instance['address'];
		$phone          = $instance['phone'];
		$email          = $instance['email'];
		$working        = $instance['working'];
		$contact_after  = $instance['contact_after'];
		$icon           = ( isset( $instance['icon'] ) && 'on' == $instance['icon'] ) ? true : false;
		$view           = ( isset( $instance['view'] ) && $instance['view'] ) ? $instance['view'] : 'inline';

		echo porto_filter_output( $before_widget );

		if ( $title ) {
			echo $before_title . sanitize_text_field( $title ) . $after_title;
		}
		?>
		<div class="contact-info<?php echo 'block' == $view ? ' contact-info-block' : ''; ?>">
			<?php
			if ( $contact_before ) :
				?>
				<?php echo do_shortcode( $contact_before ); ?><?php endif; ?>
			<ul class="contact-details<?php echo ! $icon ? '' : ' list list-icons'; ?>">
				<?php
				if ( $address ) :
					?>
					<li><i class="far fa-dot-circle"></i> <strong><?php echo porto_strip_script_tags( $address_label ); ?>:</strong> <span><?php echo force_balance_tags( $address ); ?></span></li><?php endif; ?>
				<?php
				if ( $phone ) :
					?>
					<li><i class="fab fa-whatsapp"></i> <strong><?php echo porto_strip_script_tags( $phone_label ); ?>:</strong> <span><?php echo force_balance_tags( $phone ); ?></span></li><?php endif; ?>
				<?php
				if ( $email ) :
					?>
					<li><i class="far fa-envelope"></i> <strong><?php echo porto_strip_script_tags( $email_label ); ?>:</strong> <span><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo force_balance_tags( $email ); ?></a></span></li><?php endif; ?>
				<?php
				if ( $working ) :
					?>
					<li><i class="far fa-clock"></i> <strong><?php echo porto_strip_script_tags( $working_label ); ?>:</strong> <span><?php echo force_balance_tags( $working ); ?></span></li><?php endif; ?>
			</ul>
			<?php
			if ( $contact_after ) :
				?>
				<?php echo do_shortcode( $contact_after ); ?><?php endif; ?>
		</div>

		<?php
		echo porto_filter_output( $after_widget );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['contact_before'] = $new_instance['contact_before'];
		$instance['address_label']  = $new_instance['address_label'];
		$instance['address']        = $new_instance['address'];
		$instance['phone_label']    = $new_instance['phone_label'];
		$instance['phone']          = $new_instance['phone'];
		$instance['email_label']    = $new_instance['email_label'];
		$instance['email']          = $new_instance['email'];
		$instance['working_label']  = $new_instance['working_label'];
		$instance['working']        = $new_instance['working'];
		$instance['contact_after']  = $new_instance['contact_after'];
		$instance['view']           = $new_instance['view'];
		if ( isset( $new_instance['icon'] ) ) {
			$instance['icon'] = $new_instance['icon'];
		}

		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'          => __( 'Contact Us', 'porto-functionality' ),
			'contact_before' => '',
			'address_label'  => '',
			'address'        => '1234 Street Name, City Name, Country Name',
			'phone_label'    => '',
			'phone'          => '(123) 456-7890',
			'email_label'    => '',
			'email'          => 'mail@example.com',
			'working_label'  => '',
			'working'        => 'Mon - Sun / 9:00 AM - 8:00 PM',
			'contact_after'  => '',
			'view'           => 'inline',
			'icon'           => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<strong><?php esc_html_e( 'Title', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo isset( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'contact_before' ); ?>">
				<strong><?php esc_html_e( 'Before Description', 'porto-functionality' ); ?>:</strong>
				<textarea class="widefat" id="<?php echo $this->get_field_id( 'contact_before' ); ?>" name="<?php echo $this->get_field_name( 'contact_before' ); ?>"><?php echo isset( $instance['contact_before'] ) ? wp_kses_post( $instance['contact_before'] ) : ''; ?></textarea>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'address_label' ); ?>">
				<strong><?php esc_html_e( 'Address Label', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'address_label' ); ?>" name="<?php echo $this->get_field_name( 'address_label' ); ?>" value="<?php echo isset( $instance['address_label'] ) ? porto_strip_script_tags( $instance['address_label'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Address', 'porto-functionality' ); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'address' ); ?>">
				<strong><?php esc_html_e( 'Address', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>" value="<?php echo isset( $instance['address'] ) ? porto_strip_script_tags( $instance['address'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'phone_label' ); ?>">
				<strong><?php esc_html_e( 'Phone Label', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'phone_label' ); ?>" name="<?php echo $this->get_field_name( 'phone_label' ); ?>" value="<?php echo isset( $instance['phone_label'] ) ? porto_strip_script_tags( $instance['phone_label'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Phone', 'porto-functionality' ); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'phone' ); ?>">
				<strong><?php esc_html_e( 'Phone', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'phone' ); ?>" name="<?php echo $this->get_field_name( 'phone' ); ?>" value="<?php echo isset( $instance['phone'] ) ? $instance['phone'] : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'email_label' ); ?>">
				<strong><?php esc_html_e( 'Email Label', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'email_label' ); ?>" name="<?php echo $this->get_field_name( 'email_label' ); ?>" value="<?php echo isset( $instance['email_label'] ) ? porto_strip_script_tags( $instance['email_label'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Email', 'porto-functionality' ); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'email' ); ?>">
				<strong><?php esc_html_e( 'Email', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" value="<?php echo isset( $instance['email'] ) ? porto_strip_script_tags( $instance['email'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'working_label' ); ?>">
				<strong><?php esc_html_e( 'Working Days/Hours Label', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'working_label' ); ?>" name="<?php echo $this->get_field_name( 'working_label' ); ?>" value="<?php echo isset( $instance['working_label'] ) ? porto_strip_script_tags( $instance['working_label'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Working Days/Hours', 'porto-functionality' ); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'working' ); ?>">
				<strong><?php esc_html_e( 'Working Days/Hours', 'porto-functionality' ); ?>:</strong>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'working' ); ?>" name="<?php echo $this->get_field_name( 'working' ); ?>" value="<?php echo isset( $instance['working'] ) ? porto_strip_script_tags( $instance['working'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'contact_after' ); ?>">
				<strong><?php esc_html_e( 'After Description', 'porto-functionality' ); ?>:</strong>
				<textarea class="widefat" id="<?php echo $this->get_field_id( 'contact_after' ); ?>" name="<?php echo $this->get_field_name( 'contact_after' ); ?>"><?php echo isset( $instance['contact_after'] ) ? wp_kses_post( $instance['contact_after'] ) : ''; ?></textarea>
			</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['icon'], 'on' ); ?> id="<?php echo $this->get_field_id( 'icon' ); ?>" name="<?php echo $this->get_field_name( 'icon' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'icon' ); ?>"><?php esc_html_e( 'Highlight Icons', 'porto-functionality' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'view' ); ?>">
				<strong><?php esc_html_e( 'View Type', 'porto-functionality' ); ?>:</strong>
				<select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'view' ); ?>">
					<option value="inline"<?php echo ( isset( $instance['view'] ) && 'inline' == $instance['view'] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Inline', 'porto-functionality' ); ?></option>
					<option value="block"<?php echo ( isset( $instance['view'] ) && 'block' == $instance['view'] ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Separate', 'porto-functionality' ); ?></option>
				</select>
			</label>
		</p>
		<?php
	}
}
