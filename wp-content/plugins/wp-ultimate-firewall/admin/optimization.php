<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

function wpuf_optimization_settings_page() {

	//Default Browser Cache
	if ( empty(get_option("wpuf_browser_cache_time"))) { 
		update_option("wpuf_browser_cache_time", "3600"); 
	} else { 
		get_option("wpuf_browser_cache_time"); 
	}
	
    ?>
    <div class="wrap projectStyle">
	<div id="whiteboxH" class="postbox">
	
	<div class="topHead">
		<h2><?php echo __("Optimization Settings","ua-protection-lang") ?></h2>
		<?php settings_errors(); ?>
	</div>
	
	<div class="inside">
        <form action="options.php" method="post">
        <?php settings_fields("wpuf_optimization_settings") ?>
		<h3><?php echo __("Compression Settings","ua-protection-lang") ?></h3>
            <table class="form-table">	
			    <tr valign="top">
                    <th scope="row"><label for="wpuf_gzip_comp"><?php echo __("GZip Compression","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_gzip_comp") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_gzip_comp" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("You can enable that your server gets less load by GZip compression.","ua-protection-lang") ?></p>
                    </td>
                </tr>			    
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_page_minifier"><?php echo __("HTML Compression","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_page_minifier") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_page_minifier" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("You can reduce your website’s page size by HTML compression.","ua-protection-lang") ?></p>
                    </td>
                </tr>				
		</table>
			<h3><?php echo __("Cache Settings","ua-protection-lang") ?></h3>
            <table class="form-table">
				<tr valign="top">
                    <th scope="row"><label for="wpuf_browser_caching"><?php echo __("Browser Caching","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_browser_caching") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_browser_caching" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("The Browser Caching option allows you to get a faster startup speed by saving a file with the user who is logging into your web site.","ua-protection-lang") ?></p>
                    </td>
                </tr>	
				
				<?php if( get_option("wpuf_browser_caching") == 1) : ?>
					<tr valign="top">
						<th scope="row"><label for="wpuf_browser_cache_time"><?php echo __("Browser Caching Time","wp-useful-features") ?></label></th>
						<td>
							<input name="wpuf_browser_cache_time" id="wpuf_browser_cache_time" type="text" value="<?php echo get_option("wpuf_browser_cache_time"); ?>" class="regular-text" />
							 <p style="color:#7a7a7a;" ><?php echo __("Set Browser Caching Time (Default: 3600 seconds / 60 = 60 minutes ).","wp-useful-features") ?></p>
						</td>
					</tr>				
				<?php endif; ?>
		</table>
			<h3><?php echo __("Other Optimization Settings","ua-protection-lang") ?></h3>
            <table class="form-table">
				<tr valign="top">
                    <th scope="row"><label for="wpuf_lazy_load"><?php echo __("Lazy Load","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_lazy_load") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_lazy_load" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Lazy Load option enables later upload of images which are in your website.","ua-protection-lang") ?></p>
                    </td>
                </tr>							
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_author_redirect"><?php echo __("Author Archives &amp; Links","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_author_redirect") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_author_redirect" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("By disabling author pages and addresses, you can provide security and remove unnecessary queries.","ua-protection-lang") ?></p>
                    </td>
                </tr>					
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_remove_shortlinks"><?php echo __("Shortlinks","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_remove_shortlinks") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_remove_shortlinks" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Remove Shortlink Tags from header.","ua-protection-lang") ?></p>
                    </td>
                </tr>				
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_remove_feeds"><?php echo __("RSS Feeds","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_remove_feeds") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_remove_feeds" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("You can speed up your website by disabling RSS Feeds feature and also you can solve problems like content stealing.","ua-protection-lang") ?></p>
                    </td>
                </tr>					
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_asydef_attr"><?php echo __("Async & Defer Attributes","ua-protection-lang") ?></label></th>
                    <td>
					<label>
						<select style="width:40%;margin-top:5px;" name="wpuf_asydef_attr">
							<option value="0" <?php selected( get_option("wpuf_asydef_attr"), 0); ?>><?php echo __("Disable","ua-protection-lang") ?></option>
							<option value="1" <?php selected( get_option("wpuf_asydef_attr"), 1); ?>><?php echo __("Async","ua-protection-lang") ?></option>
							<option value="2" <?php selected( get_option("wpuf_asydef_attr"), 2); ?>><?php echo __("Defer","ua-protection-lang") ?></option>
						</select>
					</label>
                        <p><?php echo __("Please ensure that you upload your JavaScript files in your website as in the form of ASYNC or DEFER. (ASYNC Recommended)","ua-protection-lang") ?></p>
                    </td>
                </tr>				
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_remove_query_strings"><?php echo __("Script Optimization","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_remove_query_strings") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_remove_query_strings" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Remove Query Strings","ua-protection-lang") ?></p>
                    </td>
                </tr>
				
				<tr valign="top">
                    <th scope="row"></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_headtofooter_opt") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_headtofooter_opt" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Move All Scripts to Footer","ua-protection-lang") ?></p>
                    </td>
                </tr>				
				
				<tr valign="top">
                    <th scope="row"></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_disable_emojis") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_disable_emojis" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Disable Wordpress Emoticon (Emoji) Feature","ua-protection-lang") ?></p>
                    </td>
                </tr>
				
				<tr valign="top">
                    <th scope="row"></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_remove_jquery_migrate") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_remove_jquery_migrate" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Remove jQuery Migrate. (Add jQuery-Core)","ua-protection-lang") ?></p>
                    </td>
                </tr>				
			
			<?php if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) : ?>
				<tr valign="top">
                    <th scope="row"></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_woo_remove_scripts") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_woo_remove_scripts" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Remove Woocommerce Scripts and CSS from Irrelevant Pages.","ua-protection-lang") ?></p>
                    </td>
                </tr>
			<?php endif; ?>			
			
			<?php if ( in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) : ?>
				<tr valign="top">
                    <th scope="row"></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_remove_bp_scripts") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_remove_bp_scripts" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Remove BuddyPress Scripts and CSS from Irrelevant Pages.","ua-protection-lang") ?></p>
                    </td>
                </tr>
			<?php endif; ?>			
			
			<?php if ( in_array( 'bbpress/bbpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) : ?>
				<tr valign="top">
                    <th scope="row"></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_bbp_style_remover") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_bbp_style_remover" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Remove bbPress Scripts and CSS from Irrelevant Pages.","ua-protection-lang") ?></p>
                    </td>
                </tr>
			<?php endif; ?>
			
             </table>
      </div>
	  
	</div>
</div>

            <div class="wrap projectStyle" id="whiteboxH">
				<div class="postbox">
				<div class="inside">
				<div style="display:inline-block">
			  
					<div class="contentDoYouLike">
					  <p><?php echo __("How would you rate <strong>WP Ultimate Firewall</strong>?", "ua-protection-lang") ?></p>
					</div>

					<div class="wrapperDoYouLike">
					  <input type="checkbox" id="st1" value="1" />
					  <label for="st1"></label>
					  <input type="checkbox" id="st2" value="2" />
					  <label for="st2"></label>
					  <input type="checkbox" id="st3" value="3" />
					  <label for="st3"></label>
					  <input type="checkbox" id="st4" value="4" />
					  <label for="st4"></label>
					  <input type="checkbox" id="st5" value="5" />
					  <label for="st5"></label>
					</div>					
					
					<a target="_blank" href="https://codecanyon.net/item/wp-ultimate-firewall-website-security-optimization/reviews/20695212" class="sabutton button button-primary" style="margin: -5px 0 0 50px;"><?php echo __("Rate this plugin!", "ua-protection-lang") ?></a>
				</div>
					<?php submit_button(); ?>
				</div>
				</div>
			</div>
	</form>
    <?php
}

add_action("admin_init","wpuf_optimization_register");
function wpuf_optimization_register() {
	//Status Button
	register_setting("wpuf_optimization_settings","wpuf_gzip_comp");
	register_setting("wpuf_optimization_settings","wpuf_page_minifier");
	register_setting("wpuf_optimization_settings","wpuf_lazy_load");
	register_setting("wpuf_optimization_settings","wpuf_disable_emojis");
	
	register_setting("wpuf_optimization_settings","wpuf_browser_caching");
		register_setting("wpuf_optimization_settings","wpuf_browser_cache_time");
	
	register_setting("wpuf_optimization_settings","wpuf_author_redirect");
	register_setting("wpuf_optimization_settings","wpuf_remove_shortlinks");
	register_setting("wpuf_optimization_settings","wpuf_remove_feeds");
	
	register_setting("wpuf_optimization_settings","wpuf_remove_jquery_migrate");
	register_setting("wpuf_optimization_settings","wpuf_remove_query_strings");
	register_setting("wpuf_optimization_settings","wpuf_headtofooter_opt");
	register_setting("wpuf_optimization_settings","wpuf_asydef_attr");
	
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :
		register_setting("wpuf_optimization_settings","wpuf_woo_remove_scripts");
	endif;	
	
	if ( in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :
		register_setting("wpuf_optimization_settings","wpuf_remove_bp_scripts");
	endif;	
	
	if ( in_array( 'bbpress/bbpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :
		register_setting("wpuf_optimization_settings","wpuf_bbp_style_remover");
	endif;
	
}