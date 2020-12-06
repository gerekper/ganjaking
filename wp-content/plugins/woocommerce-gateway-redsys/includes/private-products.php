<?php
/*
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function redsys_is_private_product( $user_id, $product_id ) {
	
	$is_active     = get_post_meta( $product_id, 'redsys_private_active', true );
	$user_selected = maybe_unserialize( get_post_meta( get_the_ID(), 'redsys_users_private' ) );
	
	if ( 'yes' === $is_active && in_array( esc_html( $user_id ), $user_selected['0'], true ) ) {
		return false;
	} elseif ( 'yes' === $is_active && ! in_array( esc_html( $user_id ), $user_selected['0'], true ) ) {
		return true;
	} else {
		return false;
	}
}

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_make_private() {
	global $post, $wp_query;
	
	if ( is_product() ) {
		if ( is_user_logged_in() ) {
			$user_id    = get_current_user_id();
		} else {
			$user_id = '0';
		}
		$product_id = $post->ID;
		$is_private = redsys_is_private_product( $user_id, $product_id );
		if ( $is_private ) {
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		}
	} else {
		return;
	}
}
add_action( 'wp', 'redsys_make_private' );

/**
* Adding a custom tab
*/
/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_private_product_tab( $tabs ) {
	
	$tabs['redsys_private_product'] = array(
		'label'  => __( 'Private Product', 'woocommerce-redsys' ),
		'target' => 'redsys_private_product',
		'class'  => array(),
	);
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'redsys_private_product_tab' );

// Adding content to custom panel
/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_private_product_tab_panel() {
	
	$users         = get_users();
	$user_selected = maybe_unserialize( get_post_meta( get_the_ID(), 'redsys_users_private' ) );
	?>
	<div id="redsys_private_product" class="panel woocommerce_options_panel">
		<div class="options_group">
			<p class="form-field">
				<?php
					$field = array(
						'id' => 'redsys_private_active',
						'label' => __( 'Private Product', 'woocommerce-redsys' ),
					);
					woocommerce_wp_checkbox( $field );
				?>
			</p>
			<p class="form-field">
				<label for="redsys_users_private"><?php esc_html_e( 'Users', 'woocommerce-redsys' ); ?></label>
				<select multiple="multiple" id="redsys_users_private" name="redsys_users_private_label_field[]" class="js-redsys-users">
			<?php
				foreach ( $users as $user ) { ?>
					<option value="<?php echo esc_attr( $user->ID ); ?>" <?php if ( $user_selected['0'] && in_array( esc_html( $user->ID ), $user_selected['0'], true ) ) echo ' selected'; ?>><?php echo esc_html( $user->user_email ); ?></option>
				<?php } ?>
				</select><?php echo wc_help_tip( __( 'Select user that will be allowed to see this product', 'woocommerce-redsys' ) ); ?>
			</p>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_product_data_panels', 'redsys_private_product_tab_panel' );
	
// Saving data
/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_save_private_product( $post_id ) {
	
	$redsys_private_active = isset( $_POST['redsys_private_active'] ) ? $_POST['redsys_private_active'] : 'no';
	$redsys_private_users  = isset( $_POST['redsys_users_private_label_field'] ) ? $_POST['redsys_users_private_label_field'] : '';
	$product               = wc_get_product( $post_id );
	$product->update_meta_data( 'redsys_private_active', $redsys_private_active );
	$product->update_meta_data( 'redsys_users_private',  $redsys_private_users  );
	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'redsys_save_private_product' );

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_load_scripts_product() {
	global $current_screen;
	if ( 'product' === $current_screen->post_type ) {
		wp_enqueue_script( 'custom-js', REDSYS_PLUGIN_URL . 'assets/js/users-product.js' );
	}
}
add_action('admin_enqueue_scripts', 'redsys_load_scripts_product');

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_private_product_query( $q ){
	
	$meta_query = $q->get( 'meta_query' );
	
	$meta_query['relation'] = 'OR';
	$meta_query[] = array(
		'key'     => 'redsys_private_active',
		'value'   => 'yes',
		'compare' => 'NOT EXISTS'
		);
	$meta_query[] = array(
		'key'     => 'redsys_private_active',
		'value'   => 'no',
		'compare' => '='
	);
	$q->set( 'meta_query', $meta_query );
}
add_action( 'woocommerce_product_query', 'redsys_private_product_query' );
