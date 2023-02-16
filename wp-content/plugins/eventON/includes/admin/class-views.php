<?php
/**
 * HTML Views for eventon
 */

class EVO_Views{

	function get_html($type, $args= ''){

		ob_start();
		switch($type){

		// FORMS
			case 'evo_activation_form':
				global $ajde;
				$style_input = "width:100%; margin-top:5px; display:block;border-radius:5px; font-size:20px";
				?>
				<div class='evo_license_section_form'>
					<p style='padding-top:10px;'><?php _e('Enter Your EventON Purchase Key','eventon');?>
						<input class='fields' name='key' type='text' style='<?php echo $style_input;?>'/>
						<input class='eventon_slug fields' name='slug' type='hidden' value='eventon' />
						<input class='eventon_license_div' type='hidden' value='evo_license_main' />
						<i style='opacity:0.6;padding-top:5px; display:block'><?php _e('More information on','eventon');?> <a href='https://docs.myeventon.com/documentations/how-to-find-eventon-license-key/' target='_blank'><?php _e('How to find eventON purchase key','eventon');?></a></i>
					</p>

					<p style='padding-top:10px;'>
						<label class='pea'><?php _e('Envato Username','eventon'); EVO()->elements->echo_tooltips('This is the envato account username used for loggin into to codecanyon.');?></label>
						<input class='fields' name='envato_username' type='text' style='<?php echo $style_input;?>'/>
					</p>					
					<p style='text-align:center'><a class='eventon_submit_license evo_admin_btn btn_prime' data-type='main' data-slug='eventon'><?php _e('Activate Now','eventon');?></a></p>
				</div>
				<?php
			break;

			case 'evo_addon_activation_form':
				global $ajde;

				?>
				<div class='evo_license_section_form'>
					<p>
						<label><?php _e('Addon License Key','eventon');?>*</label>
						<input class='eventon_license_key_val fields' name='key' type='text' style='width:100%' placeholder='Enter the addon license key'/>
						<input class='instance fields' name='instance' type='hidden' value='<?php echo md5(get_site_url());?>' />
						<input class='eventon_slug fields' name='slug' type='hidden' value='<?php echo $args['slug'];?>' />
						<input class='eventon_id fields' name='product_id' type='hidden' value='<?php echo $args['product_id'];?>' />
						<input class='eventon_license_div' type='hidden' value='evoaddon_<?php echo $args['slug'];?>' />
						<i style='opacity:0.6;padding-top:5px; display:block'><?php _e('Find addon license key from','eventon');?> <a href='http://www.myeventon.com/my-account/licenses/' target='_blank'><?php _e('My eventon > My licenses','eventon');?></a></i>
					</p>

					<p>
						<label class='pea'><?php _e('Email Address','eventon');?>* <?php EVO()->elements->echo_tooltips('The email address you have used to purchase eventon addon from myeventon.com.');?></label>
						<input class='eventon_email_val fields' name='email' type='text' style='width:100%' placeholder='Email address used for purchasing addon'/>
					</p>
							
					<p><a class='eventonADD_submit_license evo_admin_btn btn_prime' data-type='addon' data-slug='<?php echo $args['slug'];?>'>Activate Now</a></p>
				</div>
				<?php
			break;

		// EVO
			case 'evo_activated_box':				
				global $ajde;
				$EVO_prod = new EVO_Product_Lic('eventon');
				$has_update = $EVO_prod->has_update();

				$new_update_details_btn = ($has_update)?
					"<b class='evo_admin_addons_newup_bar'>".__('New Update availale','eventon')."</b><a class='evo_admin_btn btn_primary' href='".admin_url()."update-core.php'>Update Now</a> <a class='thickbox evo_admin_btn btn_primary' href='".BACKEND_URL."plugin-install.php?tab=plugin-information&plugin=eventon&section=changelog&TB_iframe=true&width=600&height=400'>Version Details</a> ":null;

				$new_update_details_btn .= "<a class='evo_admin_btn btn_secondary' href='http://docs.myeventon.com' target='_blank'>Docs</a> <a class='evo_admin_btn btn_secondary' href='http://www.myeventon.com/news/' target='_blank'>News</a>";
				?>
					<div id='evoaddon_eventon' class="addon main activated <?php echo ($has_update)? 'hasupdate':null;?>">
						<h2>EventON</h2>
						<p class='version'>v<?php echo EVO()->version;?><?php if($has_update):?><span>/<?php echo $EVO_prod->get_remote_version();?></span><?php endif;?></p>
						<p><?php _e('License Status','eventon');?>: <strong style='text-transform:uppercase'><?php _e('Activated','eventon');?></strong> | <a id='evoDeactLic' style='cursor:pointer'><?php _e('Deactivate','eventon');?></a></p>
						<p><?php _e('Purchase Key','eventon');?>: <strong><?php echo $EVO_prod->get_partial_license('eventon');?></strong></p>

						<?php if( !$EVO_prod->remotely_validated()): ?>
							<p><?php _e('Validatation Status','eventon');?>: <strong>Locally</strong></p>
						<?php endif;?>
						<p><i><?php _e('Info: You will need a seperate license to use eventON on another site.','eventon');?></i><?php EVO()->elements->echo_tooltips('EventON license you have purchased from Codecanyon, either regular or extended will allow you to install eventON in ONE site only. In order to install eventON in another site you will need a seperate license.');?></p>
						<p class='links' style='padding-top:10px;'><?php echo $new_update_details_btn;?></p>
					</div>
				<?php 
	
			break;

			case 'evo_not_activated_box':
				?>
				<div id='evoaddon_eventon' class="addon main">
					<h2>EventON</h2>
					<p class='version'>v<?php echo EVO()->version;?><span></span></p>
					<p class='status'><?php _e('License Status','eventon');?>: <strong style='text-transform:uppercase'><?php _e('Not Activated','eventon');?></strong>
					</p>
					<p class='action'>
						<a id='evo_license_form_trig' class='ajde_popup_trig evo_admin_btn btn_prime' data-dynamic_c='1' data-content_id='eventon_pop_content_001' poptitle='Activate EventON License'><?php _e('Activate Now','eventon');?></a>
					</p>
					<p class='activation_text'><i><a href='https://docs.myeventon.com/documentations/how-to-find-eventon-license-key/' target='_blank'>How to find activation key</a><?php EVO()->elements->echo_tooltips('EventON license you have purchased from Codecanyon, either regular or extended will allow you to install eventON in ONE site only. In order to install eventON in another site you will need a seperate license.','L');?></i>
					</p>

					<div id='eventon_pop_content_001' class='evo_hide_this'><p class='evo_loader'></p></div>

				</div>
				<?php
			break;

			case 'evo_box':
				$EVO_prod = new EVO_Product_Lic('eventon');
				if($EVO_prod->kriyathmakada()){
					echo $this->get_html('evo_activated_box');
				}else{
					echo $this->get_html('evo_not_activated_box');
				}

			break;

		// ADDONS
			case 'evo_addon_activated_box':
				extract($args);

				$ADDON = new EVO_Product($slug);
				$has_update = '';
				if($ADDON->has_update()){
					$has_update = "<span class='has_update'>".__('New version available','eventon') . "</span>";
				}
				?>
				<div id='evoaddon_<?php echo $slug;?>' class="addon activated" data-slug='<?php echo $slug;?>' data-key='<?php echo $ADDON->get_prop('key');?>' data-email='<?php echo $ADDON->get_prop('email');?>' data-product_id='<?php echo $product['id'];?>'>
							
					<?php echo $has_update;?>

					<h2><?php echo $product['name']?></h2>
					<?php if(!empty($version)):?>
						<p class='version'><span><?php echo $version?></span> </p>
					<?php endif;?>

					<p class='status'>License Status: <strong><?php $ADDON->kriyathmaka_localda()? _e('Activated Locally','eventon'): _e('Activated','eventon');?> </strong></p>
					<?php if($ADDON->kriyathmaka_localda()):?>
						<p><a class='evo_admin_btn btn_triad evo_retry_remote_activation' >Try Remote Activate</a></p>
					<?php endif;?>
					<p><a class='evo_deact_adodn evo_admin_btn btn_triad' >Deactivate</a></p>
					<p class="links"><?php echo $guide;?><a href='<?php echo $product['link'];?>' target='_blank'><?php _e('Learn More','eventon');?></a></p>
				</div>
				<?php
			break;

			case 'evo_addon_not_activated_box':

				extract($args);
				?>
				<div id='evoaddon_<?php echo $slug;?>' class="addon <?php echo (!$has_addon)?'donthaveit':null;?>" data-slug='<?php echo $slug;?>' data-key='<?php echo !empty($this_addon['key'])? $this_addon['key']:'';?>' data-email='<?php echo !empty($this_addon['email'])?$this_addon['email']:'';?>' data-product_id='<?php echo !empty($product['id'])? $product['id']:'';?>'>

					<h2><?php echo $product['name']?></h2>

					<?php if(!empty($version)):?>
						<p class='version'><span><?php echo $version?></span> <?php echo $remote_version;?></p>
					<?php endif;?>

					<p class='status'><?php _e('License Status','eventon');?>: <strong><?php _e('Not Activated','eventon');?></strong></p>
					<p class='action'><?php echo $action_btn;?></p>
					<p class="links"><?php echo $guide;?><a href='<?php echo $product['link'];?>' target='_blank'><?php _e('Learn More','eventon');?></a></p>
					<p class='activation_text'></p>
					<div id='eventon_pop_content_<?php echo $slug;?>' class='evo_hide_this'><p class="evo_loader"></p></div>
				</div>
				<?php
			break;

			// input slug, activeplugins array
			case 'evo_addon_box':
				extract($args);
				$ADDON = new EVO_Product($slug);

				$_this_addon = $ADDON->get_product_array();

				$addons_list = new EVO_Addons_List();
				$product = $addons_list->addon($slug);
								
				$_has_addon = false;

				// check if the product is activated within wordpress
					$active_plugins = !empty($active_plugins)? $active_plugins : get_option( 'active_plugins' );
					if(!empty($active_plugins)){
						foreach($active_plugins as $plugin){
							// check if foodpress is in activated plugins list
							if(strpos( $plugin, $slug.'.php') !== false){
								$_has_addon = true;
							}
						}
					}
						
				$version = $ADDON->get_prop('version');
				$remote_version = $ADDON->get_remote_version();

				// initial variables
					$guide = ($_has_addon && $ADDON->get_prop('guide_file') )? "<span class='eventon_guide_btn ajde_popup_trig' ajax_url='{$ADDON->get_prop('guide_file')}' poptitle='How to use {$product['name']}'>Guide</span> | ":null;
					
					$__action_btn = (!$_has_addon)? 
					"<a class='evo_admin_btn btn_secondary' target='_blank' href='". $product['download']."'>Get it now</a>": 
					"<a class='ajde_popup_trig evo_admin_btn btn_prime evo_addon_license_form_trigger' data-dynamic_c='1' data-content_id='eventon_pop_content_{$slug}' poptitle='Activate {$product['name']} License' data-slug='{$slug}' data-product_id='{$product['id']}'>Activate Now</a>";

					$__remote_version = '<span title="Remote server version" style="opacity:0.2"> /'.$remote_version.'</span>';

					// HTML message when there are updates 
					$__has_updates = ($ADDON->has_update() && $_has_addon) ? "<span class='has_update'>".__('New version available','eventon') . "</span>":'';
					
				// get eventon addons views
				if( $ADDON->is_active() && $_has_addon):
										
					echo $this->get_html(
						'evo_addon_activated_box',
						array(
							'slug'				=>$slug,
							'version'			=>$version,
							'remote_version'	=>$__remote_version,
							'has_addon'			=>$_has_addon,
							'has_updates'		=>$__has_updates,
							'product'			=>$product,
							'this_addon'		=>$_this_addon,
							'action_btn'		=> $__action_btn,
							'guide'				=>$guide
						)
					);
				else:

					echo $this->get_html(
						'evo_addon_not_activated_box',
						array(
							'slug'				=>$slug,
							'version'			=>$version,
							'remote_version'	=>$__remote_version,
							'has_addon'			=>$_has_addon,
							'has_updates'		=>$__has_updates,
							'product'			=>$product,
							'this_addon'		=>$_this_addon,
							'action_btn'		=> $__action_btn,
							'guide'				=>$guide
						)
					);
					
				endif;
			break;
		
		}
		return ob_get_clean();
	}
}