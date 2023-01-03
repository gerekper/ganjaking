<?php  if ( ! defined( 'ABSPATH' ) ) exit;  

//Extendon front Class
class EXTENDONS_FAQ_FRONT_CLASS extends EXTENDONS_FAQ_MAIN_CLASS {
	
    //front class constructor
    public function __construct() {
 
    	add_action( 'wp_loaded', array( $this,'extendons_scripts_style_front'));
		
		add_filter( 'woocommerce_product_tabs', array($this,'extendons_pwoo_add_tab_in_singlepage' ));
		
		add_action( 'init', array($this,'extendons_product_question_endpoint' ));
		
		add_filter( 'woocommerce_account_menu_items', array($this,'extendons_my_account_menu_items' ));
		
		add_action( 'woocommerce_account_product-question-endpoint_endpoint', array($this,'extendons_product_question_endpoint_content' ));
		
		add_filter( 'query_vars', array($this,'extendons_product_question_query_vars'), 0 );
		
		add_filter( 'the_title', array($this,'extendons_product_question_endpoint_title' ));
		
		add_action('woocommerce_single_product_summary', array($this,'extendon_have_faq'), 20);
			
	}

	// have a question function
	function extendon_have_faq() { 
		
		$setings = $this->extendons_settings(); ?>
		
		<div class="<?php echo ($setings['havfaqenable'] == "show") ? 'ask_enable' : 'ask_disable'; ?> mainhave_a_quest">
			<a class="ext_have_questoin" ><?php echo $setings['ext_haveaquest'] ?></a>	
		</div>
		
		<script type="text/javascript">
			jQuery(".ext_have_questoin").click(function() {
				jQuery( "#tab-title-product_question a" ).click();
				jQuery( "#adding-qu" ).click();
			    jQuery('html,body').animate({ scrollTop: 800 }, 'slow');
			    return false;
			});

		</script>

	<?php }


	//function for adding new tabs in woo single product	
	function extendons_pwoo_add_tab_in_singlepage( $tabs ) {
		/* Adds the new tab */
		$setings = $this->extendons_settings();
	
		$tabs['product_question'] = array(
			'title' 	=> __( $setings['singlepagetabtitle'], 'extendons_faq_domain' ),
			'priority' 	=> 50,  
			'callback' 	=> array($this,'new_product_tab_content')
		);
		return $tabs;  
	}


	//question shown in single page
	function new_product_tab_content() { 
		
		global $wpdb, $post;  
		
		$postid = $post->ID; 

		$setings = $this->extendons_settings();
		
		if(is_user_logged_in()) {

			$user = wp_get_current_user();
		
		} else { $user = ""; } ?>

		<div class="extendons-main-accordian-section">
		    <div class="faq_block">
		        
		        <h2><?php echo $setings['blocktitle']; ?> <a class="<?php echo ($setings['ask_queston'] == "enabled") ? 'ask_enable' : 'ask_disable'; ?>" data-text-swap="<?php _e('Hide Question Form', 'extendons_faq_domain'); ?>" id="adding-qu" href="javascript:void(0)"><?php _e('Ask a Question', 'extendons_faq_domain'); ?></a></h2>
		        
		        <div class="extendons-newfaq">
		        	
		        	

		       	 	<div class="add_question_form" id="extendons-add-new-question">
					
						<div id="message-success-product">
							<strong><?php _e('Success!', 'extendons_faq_domain'); ?></strong>
							<h4><?php _e('Question Added Successfully', 'extendons_faq_domain'); ?></h4>
	                    </div>

						<form id="form-faq-main" action="" method="post">
	                    	
		                    <p class="woocommerce-form-row woocommerce-form-row--first form-row">
								<input <?php if($user !="" && $user->ID > 0) echo  "disabled" ?> data-parsley-required type="text" id="asker-name" placeholder="<?php _e('User Name', 'extendons_faq_domain'); ?>" name="asker-name" value="<?php if($user !="") echo $user->display_name; ?>">
							</p>

							<p class="woocommerce-form-row woocommerce-form-row--first form-row">
								<input <?php if($user !="" && $user->ID > 0) echo  "disabled" ?> data-parsley-required  data-parsley-type="email" type="email" id="asker-email" placeholder="<?php _e('Your Email', 'extendons_faq_domain'); ?>" name="asker-email"  value="<?php if($user !="") echo $user->user_email; ?>">
							</p> 
		                       
		                   	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<textarea data-parsley-required class="area-field field" rows="5" cols="5" id="questin_from_front" name="questin_from_front" placeholder="<?php _e('Your Question', 'extendons_faq_domain'); ?>"></textarea>	
							</p>

							<div class="clear"></div>

							<p>
								<?php _e('Private Question..?', 'extendons_faq_domain'); ?> <input type="checkbox" name="priques" id="privateq"  /> 
							</p>

							<p>
								<div id="g-recaptcha-response" data-theme="white" class="g-recaptcha" data-sitekey="<?php echo $setings['captcha_sitek']; ?>"></div>
								<p id="extendons-cap-error"><?php _e('Robot verification failed, please try again.', 'extendons_faq_domain'); ?></p>
							</p>

							<p>
								<input  type="hidden" id="curruser-id" value="<?php echo $user->ID ?>"/>
		                        <input type="hidden" name="mode_email" id="send_questionmail" value="send_questionmail<?php echo $setings['email_notify']; ?>">
		                        <input type="hidden" name="product_id_qu" id="product_id_qu" name="product_id_qu" value="<?php the_id(); ?>" />
								<input type="hidden" name="mode" id="add_question" value="add_question" />
								<button type="button" class="" onclick="addnewquestion()" id="submit-form" value="Add Question"><?php _e('Add Question', 'extendons_faq_domain'); ?></button>
							</p>

	                    </form>

					</div>

					<div class="sort_by extendons-action-block">
			            <?php _e('Sort by', 'extendons_faq_domain');?> &nbsp; &nbsp; <select id="qu_sorting" name="qu_sorting" onchange="sortingfunction(this.value)">
		               		<option value="ID:ASC:<?php echo $postid; ?>" select="selected"><?php _e('Most Recent', 'extendons_faq_domain'); ?></option>
		               		<option value="post_title:ASC:<?php echo $postid; ?>"><?php _e('Question Title: A to Z', 'extendons_faq_domain'); ?></option>
		               		<option value="post_title:DESC:<?php echo $postid; ?>"><?php _e('Question Title: Z to A', 'extendons_faq_domain'); ?></option>
		               		<option value="post_date:ASC:<?php echo $postid; ?>"><?php _e('Date: Low to High', 'extendons_faq_domain'); ?></option>
		               		<option value="post_date:DESC:<?php echo $postid; ?>"><?php _e('Date: High to Low', 'extendons_faq_domain'); ?></option>	
	                    </select>
		       	 	</div>

		        </div>

		        <div id="main-accordiaon-extendons">
		        	<div class="extendons-accordion">
		        		
		        		<?php 

						$questions = $wpdb->get_results("Select *, m.post_id, m.meta_key, m.meta_value, m1.post_id, m1.meta_key, m1.meta_value from ".$wpdb->prefix."posts p LEFT JOIN ".$wpdb->prefix."postmeta m on ( p.ID = m.post_id ) LEFT JOIN ".$wpdb->prefix."postmeta m1 on(p.ID = m1.post_id ) where m.meta_key='_private_question_key' AND m.meta_value != 1 AND m1.meta_key ='_product_id_value_key' AND m1.meta_value LIKE '%".$postid."%' AND p.post_type ='product_review_post' AND p.post_status='publish'" ); 
						?>

		        		<ul id="accordion" class="accordion">
		          			
		          			<?php 
		        			
		        			if(count($questions) > 0) {
							
							foreach ($questions as $question ) { 

								$_likescount = get_post_meta($question->ID, '_faq_likes', true);
								if($_likescount > 0){
									$_likescount = get_post_meta($question->ID, '_faq_likes', true);	
								} else{
									$_likescount = "0";
								}

								$_dislikecount = get_post_meta($question->ID, '_faq_dislike', true);
								if($_dislikecount > 0){
									$_dislikecount = get_post_meta($question->ID, '_faq_dislike', true);	
								} else{
									$_dislikecount = "0";
								}

							?>

					        <li class="faq-ext">

				        		<div class="qtoggle">
				        			<?php echo $question->post_title; ?>
				            	</div>
							        
							    <div class="under">
							           	
							        <p>
							        	<?php echo $question->post_content; ?>
							        </p>
							     		
							     	<div class="<?php echo ($setings['likes_endis'] == "enabled") ? 'ask_enable' : 'ask_disable'; ?> faq faq_like" id="faq_like<?php echo $question->ID;?>'">
							     		

							     	<button type="button" onclick="Likequestion('<?php echo $question->ID;?>');" class="fa fa-lg fa-thumbs-up likes_extendon">
							     		<span id="likecount<?php echo $question->ID;?>">(<?php echo $_likescount; ?>)</span>
							     	</button>

							     	<button type="button" onclick="Dislikequestion('<?php echo $question->ID;?>');" class="fa fa-lg fa-thumbs-down likes_extendon">
							     		<span id="discount<?php echo $question->ID;?>">(<?php echo $_dislikecount; ?>)</span>
							     	</button>
							     										
									</div>	

							        <div class="answered_by">
							        	<span><?php _e('Answer by :', 'extendons_faq_domain'); ?> <?php echo get_post_meta($question->ID, "_product_name_value_key", true);?> <?php _e('on', 'extendons_faq_domain'); ?> <?php echo date('M j, Y h:i:s A',strtotime($question->post_date)); ?></span>
							        </div>
						             
						            <div class="comment-section" id="comment-section<?php echo $question->ID; ?>">

						            <?php 	
						   				$args = array('orderby' => 'comment_post_ID', 'order' => 'DESC', 'post_id' => $question->ID, 'status' => 'approve' ); 
										$comments = get_comments( $args );
										foreach ( $comments as $comment ) { ?>

						            	<div class="add_thread1">

							                <div class="anss-info">
								                <p><?php echo $comment->comment_content; ?></p>
								        
								                <div class="answered_by"><?php _e('Comment by:', 'extendons_faq_domain'); ?> <?php echo $comment->comment_author; ?> <?php _e('on', 'extendons_faq_domain'); ?> <?php echo date('M j, Y h:i:s A',strtotime( $comment->comment_date)); ?></div>
							                </div> 

						            	</div>

						            	<?php } ?>

						            </div>
						            
							    <div class="<?php echo ($setings['commentopencc'] == "open") ? 'ask_enable' : 'ask_disable'; ?> add_comments">
								
									<form class="<?php if($user->ID == 0){ echo "user_not_login"; } ?> comment_form" id="comment_form<?php echo $question->ID;?>" method="post">
								        
									<textarea rows="4" cols="4" data-parsley-required class="faq_comentarea" name="comment" id="comment<?php echo $question->ID;?>"></textarea>
															        
									<input type="hidden" name="postid" id="postid" value="<?php echo $question->ID; ?>" >
															       	
									<input type="hidden" name="mode_email_comment" id="emailcomm_setting" value="emailcomm_setting<?php echo $setings['commen_notify']; ?>">
									<input type="hidden" name="mode" id="add_comment" value="add_comment" />
													   				
									<input id="id_submit_coment" class="field"  onclick="ajaxcommentvali('<?php echo $question->ID;?>')" type="button" value="<?php _e('Add Answer', 'extendons_faq_domain'); ?>" /> 
									
									</form>	

									<a class="<?php echo ($user->ID > 0) ? 'userlogin' : 'notlogin'; ?>" href="<?php echo wp_login_url(); ?>" target="_blank"><?php _e('Add Answer', 'extendons_faq_domain'); ?></a>

								</div>

						        </div>  

					        </li>

					        <?php } } else { ?>

								<li class="no-faq">

									<p><?php _e('This Product have no Question..!', 'extendons_faq_domain'); ?></p>

								</li>

							<?php } ?>

		        		</ul>
		        		<div class="holder"></div>

		        	</div>
		        
		        </div>

		    </div>
		</div>


		<script>

			//ajax onclick like 
			function Dislikequestion(id){ 

				var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				var condition = 'dislikecount';
				var dlike = "1";
					jQuery.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						action : 'question_dislike',
						condition :condition,
						dlike : dlike,
						questionid :id,
					},
					success : function( response ) {

						jQuery('#discount'+id).html(response);

					}
				});
			}	


			//Question likes
			function Likequestion(id) { 

				var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				var condition = 'likecount';
				var like = "1";
				jQuery.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						action : 'question_like',
						condition :condition,
						like : like,
						questionid :id,
					},
					success : function( response ) {

						jQuery('#likecount'+id).html(response);
					}
				});
			}	


			//Comment submission
			function ajaxcommentvali(id) { 

				jQuery('#comment_form'+id).parsley().validate();
				
				var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				var comment = jQuery('#comment'+id).val(); 
				var mode = jQuery('#add_comment').val();
				var comemail = jQuery('#emailcomm_setting').val();
				if (comment == '' ) {
					return false;
				} else {
					jQuery.ajax({
						url : ajaxurl,
						type : 'post',
						data : {
							action : 'form_comments',
							comment : comment,
							mode : mode,
							comemail : comemail,
							id : id,
						},
						success : function( response ) {
							jQuery('#comment_form'+id ).each(function(){this.reset(); 
							});
							jQuery('#comment-section'+id).append(response);
							// alert(response);
						}
					});
				}
			}


			// pagination
			jQuery(function() {
			    jQuery("div.holder").jPages({
			      containerID: "accordion",
			      previous : "«",
			      next : "»",
			      perPage:<?php echo $setings['faq_perpage']; ?>,
			      minHeight : false,
			      animation : "",
			    });
			});


			//accordions  
			jQuery('.qtoggle').click(function(e) {
			    e.preventDefault();
				
			    var $this = jQuery(this);
			    
			    if ($this.next().hasClass('show')) {
			    	$this.parent().removeClass('minus');
			        $this.next().removeClass('show');
			        $this.next().slideUp(350);
			    } else {
			        $this.parent().parent().find('li .under').removeClass('show');
			        $this.parent().parent().parent().find('li.faq-ext').removeClass('minus');
			        $this.parent().parent().find('li .under').slideUp(350);
			        $this.next().toggleClass('show');
			        $this.parent().toggleClass('minus');
			        $this.next().slideToggle(350);
			    }
			});


			//add new question ajax function
			function addnewquestion() {	
				
				jQuery('#form-faq-main').parsley().validate();
				var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
				var name = jQuery('#asker-name').val(); 
				var email = jQuery('#asker-email').val();
				var question = jQuery('#questin_from_front').val();
				var capt = jQuery("#g-recaptcha-response").val();
				var user = jQuery('#curruser-id').val();
				var mode = jQuery('#add_question').val();
				var curpid = jQuery ('#product_id_qu').val();
				var email_send = jQuery('#send_questionmail').val();
				if (jQuery('#privateq').is(":checked"))
				{ var pri_pub = "1";} else { var pri_pub = "0"; }
				if(name == '' && email == '' && question == '' && capt == '') {
					return false;
				}else if (!pattern.test(email)){
					return false;
				}else if (name == '') { 
					return false;
				}else if (email == '') {
					return false;
				}else if (question == '' ) {
					return false;
				}else if (capt == '') { 
					jQuery('#extendons-cap-error').show().delay(2000).fadeOut();
					return false;
				}else {
					jQuery.ajax({
						url : ajaxurl,
						type : 'post',
						data : {
							action : 'addnewquestion',
							name : name,
							email : email,
							question : question,
							capt : capt,
							user :user,
							mode : mode,
							curpid : curpid,
							pri_pub :pri_pub,
							email_send :email_send,
						},
						success : function(response) {
							
							jQuery('#message-success-product').show().delay(3000).fadeOut();
							jQuery( '#form-faq-main' ).each(function(){this.reset();});
							grecaptcha.reset();
						}
					});
				}		
			};
			

			// Question sorting
			function sortingfunction(id) { 
				
				var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				var condition = 'sorting';
				var sortval = jQuery('#qu_sorting').val();
				jQuery.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						action : 'sorting_questions',
						condition :condition,
						sortval : sortval,
						id : id
						
					},
					success : function( response ) {
						jQuery('#main-accordiaon-extendons').html(response);
					}
				});
			}

		</script>

	<?php }

	
	//register endpoints
	function extendons_product_question_endpoint() {
	    add_rewrite_endpoint( 'product-question-endpoint', EP_ROOT | EP_PAGES );
	    flush_rewrite_rules();
	}

	
	// myaccount endpoint
	function extendons_my_account_menu_items( $items ) {
	    
		$setings = $this->extendons_settings();

	    $logout = $items['customer-logout'];
	    unset( $items['customer-logout'] );
	    $items['product-question-endpoint'] = __( $setings['myaccounttab'], 'extendons_faq_domain' );
	    $items['customer-logout'] = $logout;
	    return $items;
	}

	
	//showing content on myaccount
	function extendons_product_question_endpoint_content() {
	    
	    require_once( product_question_plguin_dir.'extendons-faq-myaccount.php');
	}

	
	//myaccount query vars
	function extendons_product_question_query_vars( $vars ) {
	    
	    $vars[] = 'my-custom-endpoint';

	    return $vars;
	}

	
	//endpoints my account
	function extendons_product_question_endpoint_title( $title ) {
	    
	    global $wp_query;

	    $setsings = $this->extendons_settings();

	    $is_endpoint = isset( $wp_query->query_vars['product-question-endpoint'] );

	    if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
	       
	        $title = __( $setsings['myaccountT'], 'extendons_faq_domain' );

	        remove_filter( 'the_title', array($this,'extendons_product_question_endpoint_title' ));
	    }

	    return $title;
	}

	//Scripts and styles
	function extendons_scripts_style_front() { 

		$setings = $this->extendons_settings();

		wp_enqueue_script('jquery');
		
		wp_enqueue_script('datatables-js', plugins_url( 'Scripts/jquery.dataTables.min.js', __FILE__ ), false );
		wp_enqueue_script('extendons-fornt-js', plugins_url( 'Scripts/front-end.js', __FILE__ ), false );
		wp_enqueue_style('dataTables-css', plugins_url( 'Styles/jquery.dataTables.min.css', __FILE__ ), false );
		
		wp_enqueue_style('front-css', plugins_url( 'Styles/front-style.css', __FILE__ ), false );
		
		//wp_enqueue_script('google-captcha-js', plugins_url('Scripts/api.js', __FILE__), false );

		wp_enqueue_script( 'Google reCaptcha JS', '//www.google.com/recaptcha/api.js', false );

		wp_enqueue_script('ejabet-js', plugins_url( 'Scripts/jPages.min.js', __FILE__ ), false );
		
		wp_enqueue_style('jpage-css', plugins_url( 'Styles/jPages.css', __FILE__ ), false );
	} 	


}
new EXTENDONS_FAQ_FRONT_CLASS();