<?php  if ( ! defined( 'ABSPATH' ) ) exit;  

//Extendon admin class
class EXTENDONS_FAQ_ADMIN_CLASS extends EXTENDONS_FAQ_MAIN_CLASS {
	
	public function __construct() {
		
		add_action('add_meta_boxes',array($this, 'extendons_faq_add_meta_boxes'));
		
		add_action('save_post', array($this,'extendons_faq_woocommerce_products'));
		
		add_action('save_post', array($this,'extendons_faq_woocommerce_name'));
		
		add_action('save_post', array($this, 'extendons_faq_woocommerce_email'));
		
		add_action('save_post', array($this, 'extendons_faq_woocommerce_private'));
		
		add_filter( 'manage_edit-product_review_post_columns', array($this, 'extendons_custom_edit_product_review_post_columns' ));
		
		add_action('manage_product_review_post_posts_custom_column',array($this,'extendons_product_review_post_column'),10, 2 );
		
		add_filter( 'post_row_actions', array($this,'extendons_my_disable_quick_edit'), 10, 2 );
	}

	//function for register metaboxes for faq
	function extendons_faq_add_meta_boxes() {
		
		add_meta_box( 'pwoo_id', __( 'Select Product For Question', 'extendons_faq_domain' ), array($this,'pwoo_woo_meta_callback'),'product_review_post','side');
		
		add_meta_box( 'pwoo_asker_name', __('Customer Name', 'extendons_faq_domain' ), array($this,'pwoo_woo_asker_callback'),'product_review_post','side');
		
		add_meta_box( 'pwoo_asker_email', __('Customer Email', 'extendons_faq_domain' ), array($this,'pwoo_woo_askerem_callback'),'product_review_post','side');
		
		add_meta_box( '_private_question_key', __('Question Type', 'extendons_faq_domain' ), array($this,'pwoo_woo_private_callback'),'product_review_post','side');
	}

	
	//======================================================================
	// GETTING ALL PRODUCTS FOR QUESTIONS TO LINK WITH
	//======================================================================

	//product getting callback
	function pwoo_woo_meta_callback ($post) {

		$args = array(
			'post_type'              => 'product',
			'posts_per_page'            => -1,
		);
		$products = new WP_Query( $args );
		
		if ($products) { ?>						
		
		<?php  $pids = get_post_meta($post->ID, "_product_id_value_key", true); ?>
		<select name="post_get_product_field[]" class="js-example-basic-single" multiple="multiple">
		
		<?php foreach ( $products->posts as $product ) { ?>	
						<?php if(is_array($pids)){ ?>
		<option value="<?php echo $product->ID ;?>" <?php if(in_array($product->ID,$pids)) {echo 'selected'; }  ?>>
						<?php echo $product->post_title; ?></option>
						<?php } else { ?>
		<option value="<?php echo $product->ID ;?>" <?php echo selected($product->ID,$pids); ?>>
						<?php echo $product->post_title; ?></option>
		<?php 	} } ?>
		</select>

		<script type="text/javascript">
			jQuery('.js-example-basic-single').select2();
		</script>

		<?php 	} else { echo "No Products Found";	}
 	}


 	//savinig the product for question
	function extendons_faq_woocommerce_products($post_id) {

	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
	        return;
	    }

	    if (!current_user_can('edit_post', $post_id)) {
	        return;
	    }

	    if (isset($_POST['post_get_product_field'])) {

	        $my_data = $_POST['post_get_product_field'];
	       
			$data = array();
			
			if($my_data != '') {

				foreach ($my_data as $multiple_values) {	
				
					array_push($data, $multiple_values);
				
				}
			}
			
			update_post_meta($post_id, '_product_id_value_key', $data);	

	    } else {
	        
	        delete_post_meta($post_id, '_product_id_value_key');
	    }
	}

	//======================================================================
	// END OF PRODUCT GETTING META BOX
	//======================================================================





	//======================================================================
	// GETTING USERNAME FOR QUESTIONS TO LINK WITH
	//======================================================================

	// question asker user callback
	function pwoo_woo_asker_callback($post) {
	 
		if(is_user_logged_in()) {

			$user = wp_get_current_user();
		} 

		$user_data = get_post_meta($post->ID, "_product_name_value_key",  true);
		
		if($user_data != '') {

			$user_data1 = $user_data;

		} elseif ($user_data!='' && $user != '') {

			$user_data1 = get_post_meta($post->ID, "_product_name_value_key",  true);
		
		} else {
			
			$user_data1 = $user->user_login;
		} ?>
	 		
	 	<input  type="text" name="name_asker_question" id="name_asker_question" value="<?php echo $user_data1; ?>" placeholder="<?php _e('Customer Name', 'extendons_faq_domain'); ?>">	

	<?php }

	// saving question asker user
	function extendons_faq_woocommerce_name($post_id) {

	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
	        return;
	    }

	    if (!current_user_can('edit_post', $post_id)) {
	        return;
	    }

	    if (isset($_POST['name_asker_question'])) {
			
			$data = $_POST['name_asker_question'];

			update_post_meta($post_id, '_product_name_value_key', $data);	

	    } else {
	        
	        delete_post_meta($post_id, '_product_name_value_key');
	    }
	}

	//======================================================================
	// END OF USERNAME FOR QUESTIONS TO LINK WITH
	//======================================================================





	//======================================================================
	// GETTING USEREMAIL FOR QUESTIONS TO LINK WITH
	//======================================================================

	// callback fo question asker email
	function pwoo_woo_askerem_callback($post) {
		
		if(is_user_logged_in()) {

			$user = wp_get_current_user();
		} 

		$user_data = get_post_meta($post->ID, "_product_email_value_key",  true);
		
		if($user_data != '') {

			$user_data1 = $user_data;
		
		} elseif($user_data!='' && $user != '') {
			
			$user_data1 = get_post_meta($post->ID, "_product_email_value_key",  true);
		
		} else {
			
			$user_data1 = $user->user_email;
		
		}
		
		wp_nonce_field( basename( __FILE__ ), 'pwoo_product_asker_nonce'); ?>

		<input  type="text" name="email_asker_question"  id="email_asker_question" placeholder="<?php _e('Customer Email', 'extendons_faq_domain'); ?>" value="<?php echo $user_data1; ?>" >	

	<?php 	 }


	// saving question asker email
	function extendons_faq_woocommerce_email($post_id) {

	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
	        return;
	    }

	    if (!current_user_can('edit_post', $post_id)) {
	        return;
	    }

	    if (isset($_POST['email_asker_question'])) {
			
			$data = $_POST['email_asker_question'];

			update_post_meta($post_id, '_product_email_value_key', $data);	

	    } else {
	        
	        delete_post_meta($post_id, '_product_email_value_key');
	    }
	}

	//======================================================================
	// END GETTING USEREMAIL FOR QUESTIONS TO LINK WITH
	//======================================================================


	



	//======================================================================
	// GETTING QUESTION IS PRIVATE
	//======================================================================

	// callback for question is private ?
	function pwoo_woo_private_callback($post) {

		$value = get_post_meta($post->ID, '_private_question_key', true); 
			
		if($value == "1"){

			$value =  "Private Question";
		
		} elseif($value == "0") {
			
			$value = "Public Question";
		
		}else{

			$value = "Public Question";
		} ?>

		<label class="privpubliclab"><?php echo __($value, 'extendons_faq_domain'); ?></label>
		<input type="hidden" name="_private_question_key" value="0">

	<?php }


	// saving question private
	function extendons_faq_woocommerce_private($post_id) {

	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
	        return;
	    }

	    if (!current_user_can('edit_post', $post_id)) {
	        return;
	    }

	    if(isset($_POST['_private_question_key'])) {

	    	$my_pke = sanitize_text_field($_POST['_private_question_key']);
			
			update_post_meta($post_id, '_private_question_key', $my_pke);
		}
	}

	//======================================================================
	// END OF GETTING QUESTION IS PRIVATE
	//======================================================================


	//faq edit columns
	function extendons_custom_edit_product_review_post_columns($columns) {
		$newcolumns = array();
		$newcolumns['cb'] = '<input type="checkbox" />';
		$newcolumns['title'] = __('Question', 'extendons_faq_domain');
		$newcolumns['against'] = __('Against Product', 'extendons_faq_domain');
		$newcolumns['private'] = __('Public/Private', 'extendons_faq_domain');
		$newcolumns['name'] = __('Customer Name', 'extendons_faq_domain');
		$newcolumns['email'] = __('Customer Email', 'extendons_faq_domain');
		$newcolumns['date'] = __('Publish Date', 'extendons_faq_domain');
		return $newcolumns;
	}

	
	//function for setting content under changing head of post
	function extendons_product_review_post_column( $column, $post_id) { 
				
		switch ($column){
			
			case 'name':
				echo  get_post_meta($post_id, "_product_name_value_key", true);
			break;
			
			case 'email';
				echo get_post_meta($post_id, "_product_email_value_key",  true);
			break;

			case 'against';
				$headinid = get_post_meta($post_id, "_product_id_value_key",  true);
				
				if(is_array($headinid)) {

					foreach ($headinid as $value) { 

						echo '<a href="'. esc_url( get_permalink($value) ).'">'.
								get_the_title($value)
							.'</a>'.','.'<br>';
					}
				
				} else {
			
						// echo get_the_title($headinid);
						echo '<a href="'. esc_url( get_permalink($headinid) ).'">'.
								get_the_title($headinid)
							.'</a>'.'<br>';
				}
			break;

			case 'private';
				
				$private_qu = get_post_meta($post_id, "_private_question_key", true);
			
				if($private_qu > 0){
					
					echo "<strong>Private</strong>";
				
				} else {
				
				echo "Public";
			
			break;
			}
		}
	}

	// quick edit action disable
	function extendons_my_disable_quick_edit( $actions = array(), $post = null ) {

	    if ( isset( $actions['inline hide-if-no-js'] ) ) {
	        
	        unset( $actions['inline hide-if-no-js'] );
	    }

	    return $actions;
	}


}
new EXTENDONS_FAQ_ADMIN_CLASS();





