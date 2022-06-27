<?php 
	global $userpro_social,$userpro;
	$background_pic = userpro_profile_data('custom_profile_bg', $user_id);
	if( empty($background_pic) ){
		$background_pic = userpro_url.'profile-layouts/layout'.$layout.'/images/cover.jpg';
	} 
	
?>
     	<div class="container" id="main_section">

               <div class="row overlay-grand">

                         <div class="col-lg-12" id="header_bg" style="background:url(<?php echo $background_pic;?>);"></div>
                         <!--column ends-->
                         <div class="col-lg-12 bottom-stroke"></div>


               </div><!--row1 ends-->

               <div class="clearfix"></div><!--clearfix-->

               <div class="row grand-row-content">

                     <div class="col-lg-4 col-sm-4 col-xs-12" id="left-section-user">


                              <div class="row">
                                   <div class="profilepic" id="profile-picture">
										<?php if ( userpro_get_option('lightbox') && userpro_get_option('profile_lightbox') ) { ?>
											<div class="userpro-profile-img" data-key="profilepicture">
												<a href="<?php echo $userpro->profile_photo_url($user_id); ?>" class="userpro-tip-fade lightview" data-lightview-caption="<?php echo $userpro->profile_photo_title( $user_id ); ?>" title="<?php _e('View member photo','userpro'); ?>">
													<?php echo get_avatar( $user_id, $profile_thumb_size ); ?>
												</a>
											</div>
										<?php } else { ?>
											<div class="userpro-profile-img" data-key="profilepicture">
												<a href="<?php echo $userpro->permalink($user_id); ?>" title="<?php _e('View Profile','userpro'); ?>">
													<?php echo get_avatar( $user_id, $profile_thumb_size ); ?>
												</a>
											</div>
										<?php } ?>
							  	   </div>
                                  
                                  
                                  <div class="social-media text-center">
							           
										
										<?php 
										
										$default_social_fields = array('facebook', 'twitter', 'google_plus', 'user_url');
										foreach( userpro_fields_group_by_template( 'social', $args["social_group"] ) as $key => $array ) {
											if(in_array($key,$default_social_fields)){ 
												if(!empty(userpro_profile_data($key,$user_id))){?>
	                                             	<a href="<?php echo userpro_link_filter(userpro_profile_data($key,$user_id), $key);?>"><img src="<?php echo userpro_url."profile-layouts/layout".$layout."/images/".$key."_icon.png"?>" alt="<?php echo $array['label'];?>"/></a>
	                                        <?php }
											}
										} ?>
                                 </div>

                              </div>
                         <?php do_action('userpro_after_profile_img' , $user_id); ?>
                              <br>
                                <?php  if (userpro_get_option('modstate_social') ){ ?>
                                <div class="row">
                  
                                   <div class="col-lg-6 col-xs-12 text-center social_extension_text">
                                       <label><a href="<?php echo $userpro->permalink($user_id, 'following', 'userpro_sc_pages'); ?>"><?php _e('FOLLOWING','userpro') ?></a></label>
                                       <span class="numbers"><a href="<?php echo $userpro->permalink($user_id, 'following', 'userpro_sc_pages'); ?>"><?php echo $userpro_social->following_count_plain($user_id);?></a></span>

                                    </div>

                                   <div class="col-lg-6 col-xs-12 text-center social_extension_text">
                                         <label><a href="<?php echo $userpro->permalink($user_id, 'followers', 'userpro_sc_pages'); ?>"><?php _e('FOLLOWERS','userpro') ?></a></label>
                                         <span class="numbers"><a href="<?php echo $userpro->permalink($user_id, 'followers', 'userpro_sc_pages'); ?>"><?php echo $userpro_social->followers_count_plain($user_id);?></a></span>
                                    </div>
             
                               </div>

                                <div class="row connect">
                  
                                     <div class="col-lg-6 col-xs-12 text-center social_extension_text">
                                       <label><a href="<?php echo $userpro->permalink($user_id, 'connections','userpro_connections'); ?>"><?php _e('CONNECTIONS','userpro');?></a></label>
                                        <span class="numbers"><a href="<?php echo $userpro->permalink($user_id, 'connections','userpro_connections'); ?>"><?php echo $userpro->get_connection_count($user_id);?></a></span>
                                    </div>
                  
                                </div>
                                <?php } ?>
                                <hr>
								<?php $activated_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
								if( in_array('userpro-rating/user-pro_rating.php', $activated_plugins) ){?>
                                <div class="grand-view-reviews text-center">
                                <?php 
                                $page_id = get_option('userpro_review_page_link');
								if($page_id != ''){
									$link = get_review_page_link($user_id); ?>
                                	   <img src="<?php echo userpro_url."profile-layouts/layout".$layout."/images/review.png"?>" /><a href="<?php echo $link?>"><?php echo __('View Reviews','userpro-rating')?></a>
                                </div>
                                <?php }
								}?>
             
                      </div><!--left-section-user-->

                       <div class="col-lg-8 col-sm-8 col-xs-12" id="right-section-user">

                            <div class="row">

                                 <div class="col-lg-8 col-xs-7 user_display_name">
                                        <h5><a href="<?php echo $userpro->permalink($user_id); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a></h5>
                                 </div>
                 				<?php if($user_id != get_current_user_id()){?>
                                 <div class="col-lg-4 col-xs-4 text-right follow-button">
                                        <?php 
                                        if (userpro_get_option('modstate_social') ){
                                        	echo uplgrand_follow_text($user_id, get_current_user_id()); 
                                        }	
                                        ?>
                                  </div><!--follow-button-->
								<?php }?>
								
								
                                  <div class="up-right-button">
									<?php if ( userpro_can_edit_user( $user_id ) || userpro_get_edit_userrole() ) {?>
										<a href="<?php echo $userpro->permalink($user_id,'edit')?>" class="up-edit userpro-tip" original-title="<?php _e('Edit','userpro');?>"><i class="userpro-icon-edit"></i></a>
									<?php }?>
									<?php if ( $user_id == get_current_user_id() ) {?>
										<a href="<?php echo wp_logout_url();?>" class="up-logout userpro-tip" original-title="<?php _e('Logout','userpro');?>"><i class="userpro-icon-user-signout"></i></a>
									<?php }?>
									</div>
									
                            </div><!--row-->
              
                            <div class="row">

                                 <div class="col-lg-8">
                                 	<p class="user_badges"><?php if(userpro_get_option('show_badges_profile')=='1')
											echo userpro_show_badges( $user_id );
									?>
                                        </p>
                                 </div>
                 
                   

                            </div><!--row-->
             
                            <div class="row">
                 
                                <div class="col-lg-12">

                                                        <div class="board">

                                                            <div class="board-inner">
                                                                <ul class="nav nav-tabs" id="myTab">
                                                                 <li class="col-lg-4 col-xs-12 userpro-tabs profile-details">
                                                                     <a href="#home" data-toggle="tab" title="Profile Details">
                                                                      <span class="round-tabs">
                                                                         <img src="<?php echo userpro_url."profile-layouts/layout".$layout."/"?>images/basic-icon.png" /><br>
                                                                          <p>Basic Info</p>
                                                                      </span>
                                                                  </a>
                                                                </li>
																<?php $activated_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
																	if( in_array('userpro-mediamanager/index.php', $activated_plugins) ){?>
	                                                              <li class="col-lg-4 col-xs-12 userpro-tabs profile-media"><a href="#profile" data-toggle="tab" title="Media">
	                                                                 <span class="round-tabs">
	                                                                       <img src="<?php echo userpro_url."profile-layouts/layout".$layout."/"?>images/uploads-icon.png" /><br>
	                                                                     <p>My Uploads</p>
	                                                                 </span>
	                                                               </a>
	                                                             </li>
	                                                             <?php }?>
	
	                                                             <li class="col-lg-4 col-xs-12 userpro-tabs profile-social-details">
	                                                             	<a href="#social_profiles" data-toggle="tab" title="Social Profiles">
	                                                                 <span class="round-tabs">
	                                                                       <img src="<?php echo userpro_url."profile-layouts/layout".$layout."/"?>images/social-profile-icon.png" /><br>
	                                                                      <p>Social Profiles</p>
	                                                                 </span> </a>
	                                                            </li>
	
	                                                              </ul>
	                                                      </div><!--board-inner-->

                                                             <div class="tab-content">
                                                              <div class="tab-pane fade in active" id="home">

                                                                  <h3 class="head">Basic Info </h3>
                                                                  <hr>
                                                                  <p class="narrow">
                                   
				                                                   <form class="form-horizontal" role="form">
				                                                   <?php // Hook into fields $args, $user_id
																	if (!isset($user_id)) $user_id = 0;
																	$hook_args = array_merge($args, array('user_id' => $user_id, 'unique_id' => $i));
																	?>
																	
																	<?php foreach( userpro_fields_group_by_template( $template, $args["{$template}_group"] ) as $key => $array ) { ?>
																		
																		<?php  if ($array) echo userpro_show_field( $key, $array, $i, $args, $layout, $user_id ) ?>
																		
																	<?php } ?>
				                          
				                                                 </form> <!-- /form -->
				
				
																 </div>
                                                              <div class="tab-pane fade" id="profile">
                                                                  <h3 class="head">My Uploads</h3>
                                                                  <hr>
                                                                  <p class="narrow text-center"></p>
                                                                      <div class="userpro_media_content">
                                                                                
                                                                     <?php
												                        do_shortcode('[media_manager media = "view" user_id="'.$user_id.'"]');
                                                                                                                        $up_media = array();
												                        $up_media = get_option('userpro_media_gallery');
												                        $upm_flag = false;
                                                                                                                        if(!empty($up_media)){
												                        foreach ($up_media as $up_inner_media){
												                            if($up_inner_media['user_id'] == $user_id)
												                                $upm_flag = true;
												                        }
                                                                                                                        }
												                        if(empty($upm_flag)){
												                           echo 'No media Available'; 
												                        }
												                    ?>    
                                                                                
                                                                                
                                                                    </div>

                                                              </div>



                                                              <div class="tab-pane fade" id="social_profiles">

                                                                  <h3 class="head">Social Profiles</h3>
                                                                  <hr>
                                                                  <p class="narrow"></p>

                                                                      <form class="form-horizontal" role="form">

																		<div class="form-group social_profiles">
																		 <?php foreach( userpro_fields_group_by_template( 'social', $args["social_group"] ) as $key => $array ) { 
																		 	if(userpro_profile_data($key, $user_id) != ''){
																		 	?>
                                                                           	 <label for="<?php echo $key?>" class="col-sm-4 control-label"><?php echo $array['label'];?>:</label>
                                                                             <div class="col-sm-8 social_profile_value">
                                                                             <label type="text" id="<?php echo $key?>_url" class="form-control">
                                                                                <?php
                                                                                  	if($key != 'user_email')
                                                                                  		echo '<a href="'.userpro_link_filter( userpro_profile_data($key, $user_id), $key ).'" class="userpro-profile-icon userpro-tip" title="'.$array['label'].'">'.userpro_link_filter( userpro_profile_data($key, $user_id), $key ).'</a>';
																					else
																						echo '<a href="'.userpro_link_filter( userpro_profile_data($key, $user_id), $key ).'" class="userpro-profile-icon userpro-tip" title="'.$array['label'].'">'.userpro_profile_data($key, $user_id).'</a>';
																				?>
													 						</label>

                                                                           </div>
                                                                           <?php }
																		 } ?>
                                                                            </div>

                                                                      </form>


                                                                </div>
                                                                 </div>
                               
                                        <div class="clearfix"></div>
                    

                                        </div><!--tab-content-->


                                </div><!--col-lg-12 closed-->

                           </div> <!--row closed-->


                       </div><!--right-section-user-->
          
                     </div><!--row closed-->
           
                      <div class="clearfix"></div><!--clearfix-->

                      <div class="row">
                          <div class="footer"></div>
                     </div><!--footer-->

               </div><!--main_section ends-->
               
               <?php
        function uplgrand_follow_text($to, $from){
            $body = '';
            $caption = '';
            $link = '';
            $name ='';
            $description = '';
            if ($to != $from && userpro_is_logged_in() ) {
                    /** Facebook Auto Post Bring Back , Added By Rahul */
                    if (userpro_get_option('facebook_follow_autopost')) {
                            if ( userpro_get_option('facebook_follow_autopost_name') ) {
                                    $name = userpro_get_option('facebook_follow_autopost_name');  // post title
                            } else {
                                    $name = '';
                            }
                            if ( userpro_get_option('facebook_follow_autopost_body') ) {
                                    $body = userpro_get_option('facebook_follow_autopost_body'); // post body
                            } else {
                                    $body = '';
                            }
                            if ( userpro_get_option('facebook_follow_autopost_caption') ) {
                                    $caption = userpro_get_option('facebook_follow_autopost_caption'); // caption, url, etc.
                            } else {
                                    $caption = '';
                            }
                            if ( userpro_get_option('facebook_follow_autopost_description') ) {
                                    $description = userpro_get_option('facebook_follow_autopost_description'); // full description
                            } else {
                                    $description = '';
                            }
                            if ( userpro_get_option('facebook_follow_autopost_link') ) {
                                    $link = userpro_get_option('facebook_follow_autopost_link'); // link
                            } else {
                                    $link = '';
                            }
                    }
                    $iamfollowing = get_user_meta($from, '_userpro_following_ids', true);
                    if (isset($iamfollowing[$to])){
                            return '<div class="upg_follow"><a href="#" class="userpro-button userpro-follow following" data-follow-text="'.__('Follow','userpro').'" data-unfollow-text="'.__('Unfollow','userpro').'" data-following-text="'.__('Following','userpro').'" data-follow-to="'.$to.'">'.__('Following','userpro').'</a></div>';
                    } else {
                            return '<div class="upg_follow"><a href="#" class="userpro-button secondary userpro-follow notfollowing" data-follow-text="'.__('Follow','userpro').'" data-unfollow-text="'.__('Unfollow','userpro').'" data-following-text="'.__('Following','userpro').'" data-follow-to="'.$to.'" id="fb-post-data" data-fbappid="'.userpro_get_option('facebook_app_id').'" data-message="'.$body.'" data-caption="'.$caption.'" data-link="'.$link.'" data-name="'.$name.'" data-description="'.$description.'" ><i class="userpro-icon-share"></i>'.__('Follow','userpro').'</a></div>';}
            }
	} 
    ?>
               
