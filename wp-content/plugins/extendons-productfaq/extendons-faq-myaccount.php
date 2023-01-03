<?php  if ( ! defined( 'ABSPATH' ) ) exit;  
	
	global $wpdb, $post ;
	
	$curruser_product_account = wp_get_current_user();
	
	$id_user = $curruser_product_account->ID; 
?>
<!-- <p data-open-modal="modal-demo">Trigger Element</p> -->


<!--My Account frontend-->
<div id="main-product-myaccount">
	
	<div id="wrapper-product-myaccont">
		
		<!--Start question div section-->
		<div clss="divfor_Yourquestion">

			<?php  if($curruser_product_account != '') ?>
			
			<div id="User-name-my-account">
				Dear <strong><?php echo $curruser_product_account->display_name;  ?></strong>
				<?php _e('All Your Questions Listed Below', 'extendons_faq_domain'); ?>
			</div>

			<table id="question-tabl">
		
					<thead>
						<tr>
							<th><?php _e('Product', 'extendons_faq_domain'); ?></th>
							<th><?php _e('My Questions', 'extendons_faq_domain'); ?></th>
							<th><?php _e('Answers', 'extendons_faq_domain'); ?></th>
							<th><?php _e('Status', 'extendons_faq_domain'); ?></th>
							<th><?php _e('View Answers', 'extendons_faq_domain'); ?></th>
						</tr>
					</thead><!--end of table head-->
			

					<tbody>
						<?php 
						
						$question_mypage = get_option('myaccount_perpage_setting');
						if($question_mypage != ''){
						$question_mypage = get_option('myaccount_perpage_setting');
						}else {
						$question_mypage = 10;
						}
						
						$args = array( 
							'author' =>  $id_user, 
							'post_status' => array('publish', 'pending'),
							'post_type' => 'product_review_post',
							'posts_per_page' => -1 
						);
						
						$loop = new WP_Query( $args );
						while ( $loop->have_posts() ) : $loop->the_post(); 	?>
						<tr>
						
						<td width="80"><!--start td for looping titles-->
						<?php 	$id_products = get_post_meta( get_the_id(), '_product_id_value_key', true ); 
						if(is_array($id_products))
							foreach ($id_products as $vafflue) {
								$post_titels =  $wpdb->get_results( "SELECT ID, post_title from ".$wpdb->prefix."posts where ID =".$vafflue );
						foreach($post_titels as $title) { ?> 
						<a href="<?php echo esc_url( get_permalink($title->ID) ); ?>"><?php echo $title->post_title;?></a>,<br>
							<?php }
						}else {
						$post_titels =  $wpdb->get_results( "SELECT ID, post_title from ".$wpdb->prefix."posts where ID =".$id_products );
						foreach($post_titels as $title){ ?>
						<a href="<?php echo esc_url( get_permalink($title->ID) ); ?>"><?php echo $title->post_title;?></a><br>
						<?php } }?>	
						</td><!--end the td loop-->

						<td><?php the_title();?></td><!--title td -->
						
						<td><!--start td for getting comments-->
							<?php 
							 $comments_count = wp_count_comments($post->ID);	
							if($comments_count->total_comments > 0) {
							echo "Yes ". $comments_count->total_comments." Times"; 
							} else { echo "Not Yet"; }
							?> 
						</td><!--end the td loop-->
						
						<td>
							<?php $p_status = get_post_status();
							if($p_status == 'publish') {
							echo '<img class="pub_unpub" title="Published" src="' . plugins_url( 'img/published.png', __FILE__ ) . '" > ';
							}else { echo '<img class="pub_unpub" title="UnPublished" src="' . plugins_url( 'img/pending.png', __FILE__ ) . '" > '; }
							?>	
						</td><!--status td-->
						<td>
							<a id="myacc_ext_model-<?php echo $post->ID; ?>" data-open-modal="modal-demo<?php echo $post->ID; ?>" class="ext_have_questoin" href="#">view</a>

							<div id="modal-demo<?php echo $post->ID; ?>" class="modal" data-modal>
							  <div class="modal-container">
							    <div onclick="test()" class="modal-close" data-close-modal></div>
							    <div class="modal-content">
							      <h1><?php echo "All Answers Reagrding This Question"; ?></h1>
							      <ol class="commentlist">
									<?php
										
										$comments = get_comments(array(
											'post_id' => $post->ID,
											'status' => 'approve' 
										));

										if($comments) {
												
												wp_list_comments(array(
												'per_page' => 10, 
												'reverse_top_level' => false 
												), $comments);
										} else {

											echo "Currently This Question have no Comments";
										}
			
									?>
								</ol>
							    </div>
							  </div>
							</div>
							<script type="text/javascript">

							jQuery('#myacc_ext_model-<?php echo $post->ID; ?>').click(function(e) {
							    e.preventDefault();
								jQuery( "body" ).addClass( "hiddenOverflow"  );
								
							});	

							function test() {
								jQuery( "body" ).removeClass( "hiddenOverflow"  );
							}

							</script>
						</td>
						</tr>

					<?php endwhile; ?>		
					
					</tbody><!--end of table body-->
		
			</table><!--End of table -->

		</div><!--end of your question div-->	

		<!--Start answer div section-->
		<div class="divfor_youanswer">
			
			<?php  if($curruser_product_account != '') ?>

			<div id="User-name-my-account">
				Dear <strong><?php echo $curruser_product_account->display_name; ?></strong>
				<?php _e('All Your Answers Listed Below', 'extendons_faq_domain')?>
			</div>
		
			<table id="answer-tabl">
				
				<thead>
					
					<tr>
						<th><?php _e('Product', 'extendons_faq_domain'); ?></th>
						<th><?php _e('Questions', 'extendons_faq_domain'); ?></th>
						<th><?php _e('My Answers', 'extendons_faq_domain'); ?></th>
						<th><?php _e('Status', 'extendons_faq_domain'); ?></th>
					</tr>
				
				</thead>	

				<tbody>
				 
				<?php 
				
					$answers_mypage = get_option('myaccountans_perpage_setting');
					if($answers_mypage != ''){
					$answers_mypage = get_option('myaccountans_perpage_setting');
					}else {
					$answers_mypage = 10;
					}


					$getuser_answer = $wpdb->get_results("select * from ".$wpdb->prefix."comments WHERE user_id =".$id_user); ?>
					
					<?php foreach($getuser_answer as $answers){ ?>
					
					<tr>

						<td width="80">
						<?php 
						$comments_quston =  $wpdb->get_results("SELECT post_id, meta_key, meta_value from ".$wpdb->prefix."postmeta WHERE post_id = ".$answers->comment_post_ID." AND meta_key ='_product_id_value_key'"); 
						foreach ($comments_quston as $value) {

						$get_meta_key = $value->meta_value;
						 $unserilize_data_key = maybe_unserialize( $get_meta_key );
				

						if(is_array($unserilize_data_key)) {
						foreach ($unserilize_data_key as $value_sings) {?>
						<a href="<?php echo esc_url( get_permalink($value_sings)); ?>"><?php echo get_the_title($value_sings)?></a><br>
						<?php 	} }

						 else {?>
						<a href="<?php echo esc_url( get_permalink($unserilize_data_key)); ?>"><?php echo get_the_title($unserilize_data_key)?></a><br>	
						<?php }
						
						}?>
						
						</td>

						<td>
						<?php 
						$usercomm_qu = $wpdb->get_results("select * from ".$wpdb->prefix."posts WHERE ID =".$answers->comment_post_ID);
						foreach ($usercomm_qu as $pos_title) {
						echo $pos_title->post_title; } ?>
						</td>
						
						<td>
						<?php echo $answers->comment_content;?>	
						</td>
						
						<td>

						<?php 
						
						if($answers->comment_approved == 1) {
						echo '<img class="pub_unpub" title="Published" src="' . plugins_url( 'img/published.png', __FILE__ ) . '" > ';
						}else { echo '<img class="pub_unpub" title="UnPublished" src="' . plugins_url( 'img/pending.png', __FILE__ ) . '" > '; }
						?>
						</td>
					
						<?php } ?>
					</tr>

				</tbody>
			
			</table>

		</div><!--end of your all answer div-->

	</div><!--end of wrapper product my account-->

</div><!--end of main-product-myaccount-->

<script>
	

jQuery(document).ready(function() {
	
	jQuery('#question-tabl').DataTable({ 

		ordering: false,
		searching: true,
		pageLength: <?php echo $question_mypage;?>,
	
	});

});

jQuery(document).ready(function() {

	jQuery('#answer-tabl').DataTable({

		ordering: false,
		searching: true,
		pageLength: <?php echo $answers_mypage;?>, 

	});

});

</script>