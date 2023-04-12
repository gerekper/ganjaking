<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Account_Funds_Widget
 */
class WC_Account_Funds_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'widget_account_funds', __( 'My Account Funds', 'woocommerce-account-funds' ) );
	}

	/**
	 * The widget
	 */
	public function widget( $args, $instance ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
		?>
		<div class="woocommerce woocommerce-account-funds">
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: 1: funds amount, 2: funds name */
						__( 'You currently have <strong>%1$s</strong> worth of %2$s in your account.', 'woocommerce-account-funds' ),
						WC_Account_Funds::get_account_funds(),
						wc_get_account_funds_name()
					)
				);
				?>
			</p>

			<p><a class="button" href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ) . '/' . get_option( 'woocommerce_myaccount_account_funds_endpoint', 'account-funds' ); ?>"><?php _e( 'View deposits', 'woocommerce-account-funds' ); ?></a></p>
		</div>
		<?php
		echo $after_widget;
	}

	/**
	 * Update settings
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = wc_clean( $new_instance['title'] );
		return $instance;
	}

	/**
	 * Settings forms
	 */
	function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = sprintf(
				/* translators: %s: funds name */
				__( 'My %s', 'woocommerce-account-funds' ),
				wc_get_account_funds_name()
			);
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'woocommerce-account-funds' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}
}

register_widget( 'WC_Account_Funds_Widget' );
