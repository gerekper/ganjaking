<?php 
function register_Inviteuser_widget() {
    register_widget( 'Inviteuser_Widget' );
}
add_action( 'widgets_init', 'register_Inviteuser_widget' );


class Inviteuser_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'Inviteuser_widget', // Base ID
			__( 'Userpro invite users', 'userpro' ), // Name
			array( 'description' => __( 'userpro invite users widget for invite users from frontend ', 'userpro' ), ) // Args
		);
	}

	
	public function widget( $args, $instance ) {
		if(userpro_get_option('userpro_invite_emails_enable')==1 && is_user_logged_in())
		{
		$text = empty( $instance['text'] ) ? '' : apply_filters( 'widget_text', $instance['text'] );		
		echo $args['before_widget'];
	
	if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
	?>
	
	<?php include userpro_path . "templates/inviter_user_widgets.php";
	echo $args['after_widget'];
	}
	}

	
	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'InviteUser', 'userpro' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p><?php 
	}

	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		if(isset($instance['text']))
		$instance['text'] =  $new_instance['text'];
		return $instance;
	}

} 
?>
