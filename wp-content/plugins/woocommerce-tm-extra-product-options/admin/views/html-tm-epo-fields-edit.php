<?php
/**
 * View for displaying add/edit screen
 *
 * @package Extra Product Options/Admin/Views
 * @version 4.8
 */

defined( 'ABSPATH' ) || exit;

require_once( ABSPATH . 'wp-admin/includes/meta-boxes.php' );

if ( isset( $post )
     && isset( $post_type )
     && isset( $title )
     && isset( $post_type_object )
     && isset( $post_ID )
     && isset( $nonce_action )
) {

	/* translators: Publish box date format, see https://secure.php.net/date */
	$scheduled_date = date_i18n( esc_html__( 'M j, Y @ H:i', 'woocommerce-tm-extra-product-options' ), strtotime( $post->post_date ) );

	$messages                                       = array();
	$messages[ THEMECOMPLETE_EPO_GLOBAL_POST_TYPE ] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => esc_html__( 'Form updated.', 'woocommerce-tm-extra-product-options' ),
		2  => esc_html__( 'Form updated.', 'woocommerce-tm-extra-product-options' ),
		3  => esc_html__( 'Form updated.', 'woocommerce-tm-extra-product-options' ),
		4  => esc_html__( 'Form updated.', 'woocommerce-tm-extra-product-options' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Form restored to revision from %s', 'woocommerce-tm-extra-product-options' ), wp_post_revision_title( (int) $_GET['revision'], FALSE ) ) : FALSE,
		6  => esc_html__( 'Form published.', 'woocommerce-tm-extra-product-options' ),
		7  => esc_html__( 'Form saved.', 'woocommerce-tm-extra-product-options' ),
		8  => esc_html__( 'Form submitted.', 'woocommerce-tm-extra-product-options' ),
		9  => sprintf( __( 'Form scheduled for: %s.', 'woocommerce-tm-extra-product-options' ), '<strong>' . $scheduled_date . '</strong>' ),
		10 => esc_html__( 'Form draft updated.', 'woocommerce-tm-extra-product-options' )
	);

	$message = FALSE;
	if ( isset( $_GET['message'] ) ) {
		$_GET['message'] = absint( $_GET['message'] );
		if ( isset( $messages[ $post_type ][ $_GET['message'] ] ) ) {
			$message = $messages[ $post_type ][ $_GET['message'] ];
		}
	}
	?>
    <div class="wrap">
    <h2><?php
		echo esc_html( $title );
		if ( isset( $post_new_file ) && current_user_can( $post_type_object->cap->create_posts ) ) {
			echo ' <a href="' . esc_url( admin_url( $post_new_file ) ) . '" class="add-new-h2">' . esc_html( $post_type_object->labels->add_new ) . '</a>';
		}
		?></h2>
	<?php if ( $message ) : ?>
        <div id="message" class="updated"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

    <form name="post" action="" method="post" id="post">
        <input type="hidden" id="post_ID" name="post_ID" value="<?php echo esc_attr( (int) $post_ID ); ?>"/>
        <input type="hidden" id="hiddenaction" name="action" value="editpost"/>
		<?php
		wp_nonce_field( $nonce_action );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', FALSE );
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', FALSE );
		?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">

                    <div id="titlediv">
                        <div id="titlewrap">
                            <label class="screen-reader-text" id="title-prompt-text" for="title"><?php esc_html_e( 'Enter title here', 'woocommerce-tm-extra-product-options' ); ?></label>
                            <input type="text" name="post_title" size="30" value="<?php echo esc_attr( $post->post_title ); ?>" id="title" autocomplete="off"/>
                        </div>
                    </div>
                </div>

                <div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes( NULL, "side", $post ); ?>
                </div>

                <div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( NULL, "normal", $post ); ?>
                </div>

            </div>
            <br class="clear">
        </div>
    </form>
    </div><?php
}