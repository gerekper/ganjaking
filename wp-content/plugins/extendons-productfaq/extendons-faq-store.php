<?php  if ( ! defined( 'ABSPATH' ) ) exit;  

	//faq setting class
	class EXTENDONS_FAQ_STORE_CLASS extends EXTENDONS_FAQ_MAIN_CLASS {
		
		public function __construct() {
			
		

			add_action( 'init', array( $this,'extendons_post_type_faq'), 0 );

			add_shortcode( 'faq', array($this, 'faq' ));

			add_action( 'init', array($this, 'create_taxonomies'));
			
	        add_action( 'publish_product_faq_post',   array($this,'post_auto_cat'),10, 2 );

	        add_action('wp_ajax_contact_form', array($this, 'contact_form'));

	        add_action('wp_ajax_nopriv_contact_form', array($this, 'contact_form'));

	       

		}
	  

		//adding sub menu for question post type	
		function extendonss_admin_store_option() {
			
			add_submenu_page('edit.php?post_type=product_review_post',
					        'Store Faq',
					        __( 'Store Faq', 'extendons_faq_domain' ),
					        'manage_options',
					        'extendon-store-option',
					        array($this, 'extendons_post_type_faq' ));

		}
		
		//post type
		function extendons_post_type_faq() {

			$labels = array(
			'name' => __('Product FAQ', 'extendons_faq_domain'),
			'singular_name' => __('Product FAQ', 'extendons_faq_domain'),
			'add_new' => __('Add New FAQ', 'extendons_faq_domain'),
			'add_new_item' => __('Add New FAQ', 'extendons_faq_domain'),
			'edit_item' => __('Edit FAQ', 'extendons_faq_domain'),
			'new_item' => __('Add New FAQ', 'extendons_faq_domain'),
			'view_item' => __('View FAQ', 'extendons_faq_domain'),
			'search_items' => __('Search FAQ', 'extendons_faq_domain'),
			'not_found' =>  __('Nothing found', 'extendons_faq_domain'),
			'not_found_in_trash' => __('Nothing found in Trash', 'extendons_faq_domain'),
			'parent_item_colon' => ''
			);
			$args = array(
			'label'               => __( 'Product FAQ', 'extendons_faq_domain' ),
			'description'         => __( 'Product FAQ and reviews', 'extendons_faq_domain' ),
			'labels'              => $labels,
			'taxonomies'          => array( 'genres' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_icon'           => product_question_url.'img/extendons-24x24.png',
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			);

		  register_post_type( 'product_faq_post', $args );

		}
		// for taxonomies
		    function create_taxonomies() {
			$labels = array(
			'name'              => _x( 'Categories', 'taxonomy general name', 'extendons_faq_domain' ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name', 'extendons_faq_domain' ),
			'search_items'      => __( 'Search Category', 'extendons_faq_domain' ),
			'all_items'         => __( 'All Categories', 'extendons_faq_domain' ),
			'parent_item'       => __( 'Parent Category', 'extendons_faq_domain' ),
			'parent_item_colon' => __( 'Parent Category:', 'extendons_faq_domain' ),
			'edit_item'         => __( 'Edit Category', 'extendons_faq_domain' ),
			'update_item'       => __( 'Update Category', 'extendons_faq_domain' ),
			'add_new_item'      => __( 'Add New Category', 'extendons_faq_domain' ),
			'new_item_name'     => __( 'New Category Name', 'extendons_faq_domain' ),
			'menu_name'         => __( 'Categories', 'extendons_faq_domain' ),
			);
			//aturday, 31. December 1927, 23:54:08 local standard time instead

			$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'faq-categories' ),
			);

			register_taxonomy( 'faq-categories', array( 'product_faq_post' ), $args );

		}
		//assign uncategories taxonomy
		function post_auto_cat( $post_ID ) {
       
			$post_type = 'product_faq_post';
	        $cat_name = wp_get_post_terms( $post_ID, 'faq-categories' , array( 'fields' => 'names' ));
	        if(count($cat_name) == 1||count($cat_name) == '' ) {
	            $name = implode(" ",$cat_name);
				  if($name == ''){
		            wp_set_object_terms( $post_ID, 'Uncategorized', 'faq-categories' );
				  }
			     else{

		          wp_set_object_terms( $post_ID, $name, 'faq-categories' );

		         }
	      }else{
		  echo "Error";
	      }
			return $post_ID;
	    }
			// stortcode for FAQ
		   function faq( $atts ){

          global $post;
		  $post_slug=$post->post_name;
		  if($post_slug == 'ext_faq') {
           wp_enqueue_style('bootstrap1-css', plugins_url( 'Styles/bootstrap1.css', __FILE__ ), false );
		  }
				$post_type = 'product_faq_post';
				$tax = 'faq-categories';
				$tax_terms = get_terms($tax);
                ?>  <div id="massege" style="margin-left: 300px;"></div> <?php
				if ($tax_terms) {
					$i = 0;
				 foreach ($tax_terms as $tax_term) {
					$i++;
					

					$args = array(
					'post_type' => $post_type,
					"$tax" => $tax_term->slug,
					'post_status' => 'publish'

					);
					$my_query = new WP_Query($args);

			  if ($my_query->have_posts()){
				?>

				<div class="row" >
					<div class="col-md-8">
						<h3> <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample<?php echo $i?>" role="button" aria-expanded="false" aria-controls="collapseExample" >+</a>
						<font size="3"> <b>	<?php echo  $tax_term->name; ?> </b> </font>
						</h3>
					</div>
				</div>

				<div class="collapse" id="collapseExample<?php echo $i?>"  >
					<div class="card card-body">
					
						<?php
	                         
                        
						while ($my_query->have_posts()) : $my_query->the_post();
						

						?>
	                
			       <?php  $rating = get_post_meta(get_the_id(),"rating",true);
                      $totalip =  count(get_post_meta( get_the_id(), 'ratingip', false ));
               	        if($totalip == ''){
               	        	 $average = 0;
               	        	

               	        }
               	        else{
               	        	 $average = $rating/$totalip;
               	        }
               	       
     
                 ?>
	              
             	




			<div style="margin-left: 40px;" >
	           <div class="row" >
					<div class="col-md-8">

						<h3> <a class="btn btn1 btn-primary btnhover" data-toggle="collapse" href="#collapseExample<?php echo $i?><?php echo the_id(); ?>" role="button" aria-expanded="false" aria-controls="collapseExample" style = "width:400px;text-align: left; color: black;background-color: #efefe3; border: #efefe3; hover: yellow; "><b>Question: </b><?php the_title();?></a>
						
						</h3>
					</div>
			<div class="col-md-4" >
				<div id="rating12-<?php echo the_id(); ?><?php echo $i?>" class="rating12">
                   
                   <?php if($average == 0 ){ ?>
                    
					<span class="faq_rating" data-rating='{"rating":"5", "qid":"<?php echo the_id(); ?>"}'>☆</span> 
					<span class="faq_rating"  data-rating='{"rating":"4", "qid":"<?php echo the_id(); ?>"}'>☆</span>
					<span class="faq_rating"  data-rating='{"rating":"3", "qid":"<?php echo the_id(); ?>"}'>☆</span>
					<span class="faq_rating"  data-rating='{"rating":"2", "qid":"<?php echo the_id(); ?>"}'>☆</span>
					<span class="faq_rating"  data-rating='{"rating":"1", "qid":"<?php echo the_id(); ?>"}'>☆</span>
					<?php } ?>
                  <?php if($average >= 0.5 && $average <= 1 ){ ?>
                    
					<span class="faq_rating" data-rating='{"rating":"5", "qid":"<?php echo the_id(); ?>"}'>☆</span> 
					<span class="faq_rating"  data-rating='{"rating":"4", "qid":"<?php echo the_id(); ?>"}'>☆</span>
					<span class="faq_rating"  data-rating='{"rating":"3", "qidrating12":"<?php echo the_id(); ?>"}'>☆</span>
					<span class="faq_rating"  data-rating='{"rating":"2", "qid":"<?php echo the_id(); ?>"}'>☆</span>
					<span class="faq_rating"  data-rating='{"rating":"1", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<?php } ?>
					 <?php if($average >= 1.5 && $average < 2.5  ){ ?>
                    
					<span class="faq_rating" data-rating='{"rating":"5", "qid":"<?php echo the_id(); ?>"}'>☆</span> 
					<span class="faq_rating"  data-rating='{"rating":"4", "qid":"<?php echo the_id(); ?>"}'>☆</span>
					<span class="faq_rating"  data-rating='{"rating":"3", "qid":"<?php echo the_id(); ?>"}'>☆</span>
					<span class="faq_rating"  data-rating='{"rating":"2", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<span class="faq_rating"  data-rating='{"rating":"1", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<?php } ?>
					<?php if($average >= 2.5 && $average < 3.5 ){ ?>
                    
					<span class="faq_rating" data-rating='{"rating":"5", "qid":"<?php echo the_id(); ?>"}'>☆</span> 
					<span class="faq_rating"  data-rating='{"rating":"4", "qid":"<?php echo the_id(); ?>"}'>☆</span>
					<span class="faq_rating"  data-rating='{"rating":"3", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<span class="faq_rating"  data-rating='{"rating":"2", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<span class="faq_rating"  data-rating='{"rating":"1", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<?php } ?>
					<?php if($average >= 3.5 && $average < 4.5 ){ ?>
                    
					<span class="faq_rating" data-rating='{"rating":"5", "qid":"<?php echo the_id(); ?>"}'>☆</span> 
					<span class="faq_rating"  data-rating='{"rating":"4", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<span class="faq_rating"  data-rating='{"rating":"3", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<span class="faq_rating"  data-rating='{"rating":"2", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<span class="faq_rating"  data-rating='{"rating":"1", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<?php } ?>
					<?php if($average >= 4.5 && $average <= 5 ){ ?>
                    
					<span class="faq_rating" data-rating='{"rating":"5", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<span class="faq_rating"  data-rating='{"rating":"4", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<span class="faq_rating"  data-rating='{"rating":"3", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<span class="faq_rating"  data-rating='{"rating":"2", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<span class="faq_rating"  data-rating='{"rating":"1", "qid":"<?php echo the_id(); ?>"}' style = "color: #f9d909;"  >&starf;</span>
					<?php } ?>

				</div>
					</div>
				</div>  
			<div class="collapse"  id="collapseExample<?php echo $i?><?php echo the_id(); ?>"  >
				<div class="card card-body">
							<h4>
									<b>
									   Answer: 
									</b>
							</h4> 
							<font size="3"> 
									   <?php the_content();?>
									</font>
							</div>
							</div>
							</div>	
	             
						<?php
						
						endwhile;
						?>
						
					</div>
				</div>
				<?php
			} // END if have_posts loop

		     wp_reset_query();
		  } // END foreach $tax_terms
		} // END if $tax_terms
         ?>

         <script type="text/javascript">
         	jQuery(function() {
			    jQuery(".faq_rating").click(function(event) {
			        var options = jQuery(this).data("rating");
			        var rating = options.rating;
			        var id = options.qid;
			        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
			       jQuery.ajax({
		              type: "POST",
		              url: ajaxurl,
		              data: {action:'contact_form',rating: rating,id: id},
		              success: function(data){
                            
		               if(data.indexOf('Already Rated') > -1) {
                     // alert('Already Rated');
                      jQuery('#massege').html("<strong class='alert alert-danger'>Already Rated ").show().delay(3000).fadeOut();
                     } else {
                   var i;
                   for (i = 0; i <= id; i++) {   
                   if(rating == 1){
                    jQuery("#rating12-"+id+i).replaceWith('<div class="rating12" 	>                                                <span class="faq_rating" onclick="myFunction()" data-rating={"rating":"5", "qid":'+id+'}  style = "margin-left : 3px;" >☆</span> <span class="faq_rating" onclick="myFunction()" data-rating={"rating":"4", "qid":'+id+'}  style = "margin-left : 3px;"   >☆</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"3", "qid":'+id+'}  style = "margin-left : 3px;"  >☆</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"2", "qid":'+id+'} style = "margin-left : 3px;"  >☆</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"1", "qid":'+id+'} style = "color: #f9d909; margin-left : 3px;"  >&starf;</span> </div>');
                  }
                 if(rating == 2){
                     jQuery("#rating12-"+id+i).replaceWith('<div class="rating12">                                                <span class="faq_rating" onclick="myFunction()" data-rating={"rating":"5", "qid":'+id+'} style = "margin-left : 3px;"  >☆</span> <span class="faq_rating" onclick="myFunction()" data-rating={"rating":"4", "qid":'+id+'} >☆</span><span class="faq_rating" style = "margin-left : 3px;"onclick="myFunction()" data-rating={"rating":"3", "qid":'+id+'} style = "margin-left : 3px;" >☆</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"2", "qid":'+id+'} style = "margin-left : 3px; color: #f9d909;" >&starf;</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"1", "qid":'+id+'} style = "margin-left : 3px; color: #f9d909;"  >&starf;</span> </div>');
                  }
                 if(rating == 3){
                     jQuery("#rating12-"+id+i).replaceWith('<div class="rating12">                                                <span class="faq_rating" onclick="myFunction()" data-rating={"rating":"5", "qid":'+id+'}  style = "margin-left : 3px;" >☆</span> <span class="faq_rating" onclick="myFunction()" data-rating={"rating":"4", "qid":'+id+'} style = "margin-left : 3px;" >☆</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"3", "qid":'+id+'} style = "color: #f9d909; margin-left : 3px;"  >&starf;</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"2", "qid":'+id+'} style = "color: #f9d909; margin-left : 3px;" >&starf;</span><span class="faq_rating" onclick="myFunction()"  data-rating={"rating":"1", "qid":'+id+'} style = "color: #f9d909; margin-left : 3px;" >&starf;</span> </div>');
                 }
                 if(rating == 4){
                     jQuery("#rating12-"+id+i).replaceWith('<div class="rating12">                                                <span class="faq_rating" onclick="myFunction()" data-rating={"rating":"5", "qid":'+id+'} style = "margin-left : 3px;"   >☆</span> <span class="faq_rating" onclick="myFunction()" data-rating={"rating":"4", "qid":'+id+'} style = "margin-left : 3px; color: #f9d909;" >&starf;</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"3", "qid":'+id+'} style = "margin-left : 3px; color: #f9d909;"  >&starf;</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"2", "qid":'+id+'} style = "margin-left : 3px; color: #f9d909;"  >&starf;</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"1", "qid":'+id+'} style = "margin-left : 3px; color: #f9d909;" >&starf;</span> </div>');
                 }
	                if(rating == 5){
	                     jQuery("#rating12-"+id+i).replaceWith('<div class="rating12">                                                <span class="faq_rating" onclick="myFunction()"  data-rating={"rating":"5", "qid":'+id+'} style = "margin-left : 3px; color: #f9d909; " >&starf;</span> <span class="faq_rating" onclick="myFunction()" data-rating={"rating":"4", "qid":'+id+'} style = "margin-left : 3px; color: #f9d909; " >&starf;</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"3", "qid":'+id+'} style = "margin-left : 3px; color: #f9d909 ; "   >&starf;</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"2", "qid":'+id+'} style = "margin-left : 3px; color: #f9d909; "  >&starf;</span><span class="faq_rating" onclick="myFunction()" data-rating={"rating":"1", "qid":'+id+'} style = "color: #f9d909; margin-left : 3px;" >&starf;</span> </div>');
	                       
	                 }
	             }
                
                        } 
		               }
	                });
			    });
			});

         	
         </script>
               <script>
function myFunction() {
      jQuery('#massege').html("<strong class='alert alert-danger'>Already Rated ").show().delay(3000).fadeOut();
}
</script>

	<?php }
	function contact_form()
	    {
	    $id = $_POST['id'];
	    $my_post = $_POST['rating'];
	    $ip= $_SERVER['REMOTE_ADDR'];
	    $getip= get_post_meta($id,"ratingip",false);
	     if(in_array($ip, $getip)) {
             echo "Already Rated";  
	     }
	     else{
	        $rating = get_post_meta($id,"rating",true);
	        $rating = $rating + $my_post;
	        update_post_meta($id, "rating", $rating); 
	        add_post_meta($id, "ratingip", $ip);
	        
	         
	      }
	     } 
	     

	}

		new EXTENDONS_FAQ_STORE_CLASS();

		?>