<?php
/**
 * Provide a admin area view for the plugin Slide Settings
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */
 
if(!defined('ABSPATH')) exit();
?>
<!-- SLIDE SETTINGS -->
<div id="slide_settings">

	<div class="form_collector slide_settings_collector" data-type="sliderconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div class="main_mode_breadcrumb_wrap"><div class="main_mode_submode"><?php _e('Slide Options', 'revslider');?></div></div>
		<div class="gso_wrap" id="slide_menu_gso_wrap">
			<div id="gst_slide_1" class="slide_submodule_trigger selected opensettingstrigger" data-select="#gst_slide_1" data-unselect=".slide_submodule_trigger" data-collapse="true" data-forms='["#form_slidebg"]'><i class="material-icons">image</i><span class="gso_title"><?php _e('Background', 'revslider');?></span></div><!--
			--><div id="gst_slide_6" data-select="#gst_slide_6" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger" data-collapse="true" data-forms='["#form_slide_thumbnail"]'><i class="material-icons">photo_album</i><span class="gso_title"><?php _e('Thumbnail', 'revslider');?></span></div><!--
			--><div id="gst_slide_2" data-select="#gst_slide_2" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger" data-collapse="true" data-forms='["#form_slide_transition"]'><i class="material-icons">movie</i><span class="gso_title"><?php _e('Animation', 'revslider');?></span></div><!--
			--><div id="gst_slide_5" class="slide_submodule_trigger opensettingstrigger" data-select="#gst_slide_5" data-unselect=".slide_submodule_trigger" data-collapse="true" data-forms='["#form_slidebg_filters"]'><i class="material-icons">blur_on</i><span class="gso_title"><?php _e('Filters', 'revslider');?></span></div><!--
			--><div id="gst_slide_8" data-select="#gst_slide_8" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger" data-collapse="true" data-forms='["#form_slide_progress"]'><i class="material-icons">timer</i><span class="gso_title"><?php _e('Progress', 'revslider');?></span></div><!--
			--><div id="gst_slide_9" data-select="#gst_slide_9" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger" data-collapse="true" data-forms='["#form_slide_publish"]'><i class="material-icons">access_time</i><span class="gso_title"><?php _e('Pub. Rules', 'revslider');?></span></div><!--
			--><div id="gst_slide_4" data-select="#gst_slide_4" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger" data-collapse="true" data-forms='["#form_slidegeneral"]'><i class="material-icons">code</i><span class="gso_title"><?php _e('Tags & Link', 'revslider');?></span></div><!--
			--><div id="gst_slide_10" data-select="#gst_slide_10" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger" data-collapse="true" data-forms='["#form_slidestatic"]'><i class="material-icons">album</i><span class="gso_title"><?php _e('Static Layer', 'revslider');?></span></div><!--
			--><div id="gst_slide_3" data-select="#gst_slide_3" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger slidebg_image_settings slidebg_external_settings slide_bg_settings" data-collapse="true" data-forms='["#form_slide_kenburn_outter"]'><i id="gst_kenburns_title_icon" class="material-icons">leak_add</i><span id="gst_kenburns_title" class="gso_title"><?php _e('Ken Burns', 'revslider');?></span></div><!--
			--><div id="gst_slide_7" data-select="#gst_slide_7" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger" data-collapse="true" data-forms='["#form_slide_parameters"]'><i class="material-icons">info</i><span class="gso_title"><?php _e('Params', 'revslider');?></span></div><!--
			--><div id="gst_slide_11" data-select="#gst_slide_11" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger callEvent" data-evt="updateSlideLoopRange" data-collapse="true" data-forms='["#form_slide_loops"]'><i class="material-icons">repeat_one</i><span class="gso_title"><?php _e('Loop Layers', 'revslider');?></span></div><!--
			--><div id="gst_slide_12" data-select="#gst_slide_12" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger" data-collapse="true" data-forms='["#form_slide_onscroll"]'><i class="material-icons">system_update_alt</i><span class="gso_title"><?php _e('On Scroll', 'revslider');?></span></div>
			<?php
if ($wpml->wpml_exists()) {
	?>
<div id="gst_slide_13" data-select="#gst_slide_13" data-unselect=".slide_submodule_trigger" class="slide_submodule_trigger opensettingstrigger" data-collapse="true" data-forms='["#form_slide_wpml"]'><i class="material-icons">language</i><span class="gso_title"><?php _e('WPML', 'revslider');?></span></div>
			<?php 
} 
?>
		</div>
	</div>


	<!-- SLIDE BACKGROUND -->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slidebg"  class="formcontainer form_menu_inside" data-select="#gst_slide_1"  >
			<!--<div class="collectortabwrap"><div id="collectortab_form_slidebg" class="collectortab form_menu_inside" data-forms='["#form_slidebg"]'><i class="material-icons">filter_hdr</i><?php _e('Background', 'revslider');?></div></div>-->
			<!-- SOURCE -->
			<div id="form_slidebg_source" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">link</i><?php _e('Source', 'revslider');?></div>
				<!--<div class="form_intoaccordion" data-trigger="#sl_fbg_l1_1"><i class="material-icons">arrow_drop_down</i></div>-->
				<div class="collapsable">
					<label_a><?php _e('Type', 'revslider');?></label_a><div class="input_with_buttonextenstion"><select id="slide_bg_type" data-available=".sss_for_*val*" data-unavailable=".sss_notfor_*val*" data-updatetext="#selected_slide_source" data-triggerinp="#slide_bg_*val*_alt,#slide_bg_*val*_title" data-evt="updateslidebasic" data-evtparam="kenburnupdate" data-show=".slidebg_*val*_settings" data-hide=".slide_bg_settings" class="slideinput tos2 nosearchbox easyinit "  data-r="bg.type"><option value="image"><?php _e('Image', 'revslider');?></option><option value="external"><?php _e('External Image', 'revslider');?></option><option value="trans"><?php _e('Transparent', 'revslider');?></option><option value="solid"><?php _e('Colored', 'revslider');?></option><option value="youtube"><?php _e('YouTube Video', 'revslider');?></option><option value="vimeo"><?php _e('Vimeo Video', 'revslider');?></option><option value="html5"><?php _e('HTML5 Video', 'revslider');?></option></select>
						<div class="buttonextenstion slidebg_image_settings slidebg_external_settings slide_bg_settings slidebg_youtube_settings slidebg_html5_settings slidebg_vimeo_settings">
							<input class="dontseeme" id="slide_bg_image_path" />
							<div class="basic_action_button copyclipboard onlyicon dark_action_button" data-clipboard-action="copy" data-clipboard-target="#slide_bg_image_path"><i class="material-icons">link</i></div>
						</div>
					</div><!--
					--><div class="slidebg_image_settings slide_bg_settings">
						<label_a></label_a><div id="slide_bg_image_btn" data-evt="updateslidebasic" data-evtparam="kenburnupdate" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" data-lib="#slide#.slide.bg.imageLib" data-sty="#slide#.slide.bg.imageSourceType" class="getImageFromMediaLibrary basic_action_button longbutton callEventButton"><i class="material-icons">style</i><?php _e('Media Library', 'revslider');?></div>
						<label_a></label_a><div id="slide_object_library_btn" data-evt="updateslidebasic" data-evtparam="double" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" data-lib="#slide#.slide.bg.imageLib" data-sty="#slide#.slide.bg.imageSourceType" class="getImageFromObjectLibrary basic_action_button longbutton callEventButton"><i class="material-icons">camera_enhance</i><?php _e('Object Library', 'revslider');?></div>
					</div><!--
					--><div class="slidebg_external_settings slide_bg_settings">
						<label_a><?php _e('Source', 'revslider');?></label_a><input id="s_ext_src" data-evt="updateslidebasic" data-evtparam="kenburnupdate" class="slideinput easyinit" type="text" data-r="bg.externalSrc" placeholder="<?php _e('Enter Image URL', 'revslider');?>">
						<label_a><?php _e('', 'revslider');?></label_a><div data-evt="updateslidebasic" data-evtparam="kenburnupdate" class="basic_action_button  longbutton callEventButton"><i class="material-icons">refresh</i><?php _e('Refresh Source', 'revslider');?></div>
					</div><!--
					--><div class="slidebg_solid_settings slide_bg_settings">
						<label_a><?php _e('BG Color', 'revslider');?></label_a><input type="text" data-evt="updateslidebasic" data-editing="<?php _e('Background Color', 'revslider');?>" name="slide_bg_color" id="s_bg_color" data-visible="true" class="my-color-field slideinput easyinit" data-r="bg.color" value="#fff">																	
					</div><!--
					--><div class="slidebg_trans_settings slide_bg_settings">						
					</div><!--
					--><div class="slidebg_youtube_settings slide_bg_settings">
						<label_a><?php _e('YouTube ID', 'revslider');?></label_a><input id="s_bg_youtube_src" data-evt="updateslidebasic" class="slideinput easyinit" type="text" data-r="bg.youtube" placeholder="<?php _e('Enter YouTube ID', 'revslider');?>">
						<div class="div25"></div>
						<label_a><?php _e('Poster Image', 'revslider');?></label_a><div data-r="#slide#.slide.bg.image" data-f="#slide#.slide.bg.youtube" data-evtparam="double" data-evt="updateslidebasic" data-lib="#slide#.slide.bg.imageLib" data-sty="#slide#.slide.bg.imageSourceType"  class="getImageFromYouTube basic_action_button longbutton "><i class="material-icons">style</i><?php _e('YouTube Poster', 'revslider');?></div>
						<label_a></label_a><div data-evt="updateslidebasic" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" class="getImageFromMediaLibrary basic_action_button longbutton"><i class="material-icons">style</i><?php _e('Media Library', 'revslider');?></div>
						<label_a></label_a><div data-evt="updateslidebasic" data-evtparam="double" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" data-lib="#slide#.slide.bg.imageLib" data-sty="#slide#.slide.bg.imageSourceType" class="getImageFromObjectLibrary basic_action_button longbutton callEventButton"><i class="material-icons">camera_enhance</i><?php _e('Object Library', 'revslider');?></div>
						<label_a></label_a><div data-evt="updateslidebasic" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" class="removePosterImage basic_action_button longbutton"><i class="material-icons">delete</i><?php _e('Remove', 'revslider');?></div><span class="linebreak"></span>
					</div><!--
					--><div class="slidebg_vimeo_settings slide_bg_settings">
						<label_a><?php _e('Vimeo ID', 'revslider');?></label_a><input id="s_bg_vimeo_src" data-evt="updateslidebasic" class="slideinput easyinit" type="text" data-r="bg.vimeo" placeholder="<?php _e('Enter Vimeo ID', 'revslider');?>">
						<div class="div25"></div>
						<label_a><?php _e('Poster Image', 'revslider');?></label_a><div data-evt="updateslidebasic" data-evtparam="double" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" data-lib="#slide#.slide.bg.imageLib" data-sty="#slide#.slide.bg.imageSourceType"  class="getImageFromMediaLibrary basic_action_button  longbutton"><i class="material-icons">style</i><?php _e('Media Library', 'revslider');?></div>
						<label_a></label_a><div data-evt="updateslidebasic" data-evtparam="double" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" data-lib="#slide#.slide.bg.imageLib" data-sty="#slide#.slide.bg.imageSourceType" class="getImageFromObjectLibrary basic_action_button longbutton callEventButton"><i class="material-icons">camera_enhance</i><?php _e('Object Library', 'revslider');?></div>						
						<label_a></label_a><div data-evt="updateslidebasic" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" class="removePosterImage basic_action_button longbutton"><i class="material-icons">delete</i><?php _e('Remove', 'revslider');?></div><span class="linebreak"></span>
					</div><!--
					--><div class="slidebg_html5_settings slide_bg_settings">
						<label_a><?php _e('MPEG', 'revslider');?></label_a><input id="s_bg_mpeg_src" data-evt="updateslidebasic" class="slideinput easyinit nmarg" type="text" data-r="bg.mpeg" placeholder="<?php _e('Enter MPEG Source', 'revslider');?>">
						<label_a></label_a><div data-evt="updateslidebasic" data-target="#s_bg_mpeg_src" data-rid="#slide#.slide.bg.videoId" class="getVideoFromMediaLibrary basic_action_button longbutton "><i class="material-icons">style</i><?php _e('Media Library', 'revslider');?></div>
						<label_a></label_a><div data-evt="updateslidebasic" data-target="#s_bg_mpeg_src" data-r="bg.mpeg" class="getVideoFromObjectLibrary basic_action_button longbutton "><i class="material-icons">camera_enhance</i><?php _e('Object Library', 'revslider');?></div>
						<!--<label_a><?php _e('WEBM', 'revslider');?></label_a><div class="input_with_buttonextenstion"><input id="s_bg_webm_src" data-evt="updateslidebasic" class="slideinput easyinit nmarg" type="text" data-r="bg.mpeg" placeholder="<?php _e('Optional WEBM Source', 'revslider');?>"><div data-evt="updateslidebasic" data-target="#s_bg_webm_src" class="getVideoFromMediaLibrary basic_action_button onlyicon dark_action_button callEventButton"><i class="material-icons">style</i></div></div>-->
						<!--<label_a><?php _e('OGV', 'revslider');?></label_a><div class="input_with_buttonextenstion"><input id="s_bg_ogv_src" data-evt="updateslidebasic" class="slideinput easyinit nmarg" type="text" data-r="bg.ogv" placeholder="<?php _e('Optional OGV Source', 'revslider');?>"><div data-evt="updateslidebasic" data-target="#s_bg_ogv_src" class="getVideoFromMediaLibrary basic_action_button  onlyicon dark_action_button callEventButton"><i class="material-icons">style</i></div></div>-->
						<div class="div25"></div>
						<label_a><?php _e('Poster Image', 'revslider');?></label_a><div data-evt="updateslidebasic" data-evtparam="double" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" data-lib="#slide#.slide.bg.imageLib" data-sty="#slide#.slide.bg.imageSourceType" class="getImageFromMediaLibrary basic_action_button longbutton "><i class="material-icons">style</i><?php _e('Media Library', 'revslider');?></div>
						<label_a></label_a><div  data-evt="updateslidebasic" data-evtparam="double" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" data-lib="#slide#.slide.bg.imageLib" data-sty="#slide#.slide.bg.imageSourceType" class="getImageFromObjectLibrary basic_action_button longbutton callEventButton"><i class="material-icons">camera_enhance</i><?php _e('Object Library', 'revslider');?></div>
						<!--<label_a></label_a><div data-evt="updateslidebasic" class="basic_action_button longbutton "><i class="material-icons">camera_enhance</i><?php _e('Object Library', 'revslider');?></div>-->
						<label_a></label_a><div data-evt="updateslidebasic" data-r="#slide#.slide.bg.image" data-rid="#slide#.slide.bg.imageId" data-lib="#slide#.slide.bg.imageLib" data-sty="#slide#.slide.bg.imageSourceType" class="removePosterImage basic_action_button longbutton"><i class="material-icons">delete</i><?php _e('Remove', 'revslider');?></div><span class="linebreak"></span>
					</div><!--					
					--><div class="slidebg_image_settings slidebg_vimeo_settings slidebg_html5_settings slidebg_youtube_settings slide_bg_settings">
						<div style="display:none"><label_a class="singlerow"><?php _e('Used Library', 'revslider');?></label_a><select class="slideinput easyinit" data-r="bg.imageLib" data-show="#slidebg_srctype_*val*" data-hide=".slidebg_srctype_all" data-showprio="show"><option value="">Nothing</option><option value="objectlibrary">Objectlibrary</option><option value="medialibrary">MediaLibrary</option></select></div>
						<div id="slidebg_srctype_objectlibrary" class="slidebg_srctype_all"><label_a class="singlerow"><?php _e('Source Type', 'revslider');?></label_a><select data-theme="dark" id="slide_bg_img_ssize_a" class="slideinput tos2 nosearchbox easyinit" data-evt="getNewImageSize" data-evtparam="slidebg.object" data-r="bg.imageSourceType"><option value="100" selected="selected"><?php _e("Original", 'revslider');?></option><option value="75" selected="selected"><?php _e("Large", 'revslider');?></option><option value="50" selected="selected"><?php _e("Medium", 'revslider');?></option><option value="25" selected="selected"><?php _e("Small", 'revslider');?></option><option value="10" selected="selected"><?php _e("Extra Small", 'revslider');?></option></select></div>
						<div id="slidebg_srctype_medialibrary" class="slidebg_srctype_all"><label_a class="singlerow"><?php _e('Source Type', 'revslider');?></label_a><select data-theme="dark" id="slide_bg_img_ssize_b" class="slideinput tos2 nosearchbox easyinit" data-evt="getNewImageSize" data-evtparam="slidebg.media"  data-r="bg.imageSourceType"><option value="auto" selected="selected"><?php _e("Default Setting", 'revslider');?></option><?php foreach ($img_sizes as $imghandle => $imgSize) { echo '<option value="' . $imghandle . '">' . $imgSize . '</option>';}?></select></div>
					</div><!--
					--><div class="slidebg_html5_settings slide_bg_settings slidebg_vimeo_settings slidebg_youtube_settings slidebg_image_settings slidebg_external_settings"><!--
						--><div class="div25"></div><!--
						--><longoption><i class="material-icons">language</i><label_a ><?php _e('Image from Stream if exists', 'revslider');?></label_a><input type="checkbox" class="easyinit slideinput" data-r="bg.imageFromStream"></longoption><!--
					--></div><!--
					--><div class="slidebg_html5_settings slide_bg_settings slidebg_vimeo_settings slidebg_youtube_settings"><!--
						--><longoption><i class="material-icons">language</i><label_a ><?php _e('Video from Stream if exists', 'revslider');?></label_a><input type="checkbox" class="easyinit slideinput" data-r="bg.videoFromStream"></longoption><!--
					--></div><!--
				--></div>
			</div><!-- SOURCE END -->

			<!-- SOURCE SETTINGS -->
			<div id="form_slidebg_ssettings" class="form_inner open sss_notfor_solid sss_notfor_trans sss_for_image sss_for_external sss_for_youtube sss_for_vimeo sss_for_html5">
				<div class="form_inner_header"><i class="material-icons">chrome_reader_mode</i><span id="selected_slide_source"></span><?php _e('Settings', 'revslider');?></div>
				<!--<div class="form_intoaccordion" data-trigger="#sl_fbg_l1_2"><i class="material-icons">arrow_drop_down</i></div>-->
				<div class="collapsable">

					<!-- BACKGROUND / COVER IMAGE SETTINGS -->
					<div class="slidebg_image_settings slidebg_external_settings slide_bg_settings">

						<div id="ken_burn_bg_setting_off">
							<div id="slide_bg_settings_wrapper">
								<div id="slide_bg_and_repeat_fit_wrap">
									<label_a><?php _e('BG Fit', 'revslider');?></label_a>
									<div class="radiooption">
										<div><input type="radio" class="slideinput easyinit" value="cover" name="slide_bg_fit"  data-evt="updateslidebasic" data-show=".slide_bg_fit_*val*" data-hide=".slide_bg_fit" data-r="bg.fit"><label_sub>Cover</label_sub></div>
										<div><input type="radio" class="slideinput easyinit" value="contain" name="slide_bg_fit"  data-evt="updateslidebasic" data-show=".slide_bg_fit_*val*" data-hide=".slide_bg_fit" data-r="bg.fit"><label_sub>Contain</label_sub></div>
										<div><input type="radio" class="slideinput easyinit" value="percentage" name="slide_bg_fit"  data-evt="updateslidebasic" data-show=".slide_bg_fit_*val*" data-hide=".slide_bg_fit" data-r="bg.fit"><label_sub>Percentage</label_sub></div>
										<div><input type="radio" class="slideinput easyinit" value="auto" name="slide_bg_fit"  data-evt="updateslidebasic" data-show=".slide_bg_fit_*val*" data-hide=".slide_bg_fit" data-r="bg.fit"><label_sub>Auto</label_sub></div>
									</div>
									<div class="div15"></div>

									<div class="slide_bg_fit slide_bg_fit_percentage">
										<row class="direktrow">
											<onelong><label_icon class="ui_width"></label_icon><input data-allowed="%" data-numeric="true" id="slide_bg_fitX" data-evt="updateslidebasic" class="slideinput easyinit withsuffix" type="text" data-r="bg.fitX"></onelong>
											<oneshort><label_icon class="ui_height"></label_icon><input data-allowed="%" data-numeric="true" id="slide_bg_fitY" data-evt="updateslidebasic" class="slideinput easyinit withsuffix" type="text" data-r="bg.fitY"></oneshort>
										</row>
									</div>
									<label_a><?php _e('Repeat', 'revslider');?></label_a><select data-theme="dark" id="slide_bg_repeat"  data-evt="updateslidebasic" class="slideinput tos2 nosearchbox easyinit"  data-r="bg.repeat">
										<option value="no-repeat" selected="selected">no-repeat</option>
										<option value="repeat">repeat</option>
										<option value="repeat-x">repeat-x</option>
										<option value="repeat-y">repeat-y</option>
									</select><span class="linebreak"></span>
									<div class="div10"></div>
								</div>
								<label_a><?php _e('Position', 'revslider');?></label_a><select style="display:none !important" data-theme="dark" id="slide_bg_position" data-unselect=".slide_bg_position_selector" data-select="#slide_bg_position_*val*" data-evt="updateslidebasic" data-evtparam="kenburnupdate" data-show=".slide_bg_pos_*val*" data-hide=".slide_bg_pos" class="slideinput easyinit"  data-r="bg.position"><option value="left center"><?php _e('left center', 'revslider');?></option><option value="left bottom"><?php _e('left bottom', 'revslider');?></option><option value="left top"><?php _e('left top', 'revslider');?></option><option value="center top"><?php _e('center top', 'revslider');?></option><option value="center center"><?php _e('center center', 'revslider');?></option><option value="center bottom"><?php _e('center bottom', 'revslider');?></option>																				<option value="right top"><?php _e('right top', 'revslider');?></option><option value="right center"><?php _e('right center', 'revslider');?></option><option value="right bottom"><?php _e('right bottom', 'revslider');?></option><option value="percentage"><?php _e('(x%, y%)', 'revslider');?></option>
								</select><div class="bg_alignselector_wrap"><!--
									--><div class="bg_align_row">
										<div class="triggerselect slide_bg_position_selector bg_alignselector" data-select="#slide_bg_position" data-val="left top" id="slide_bg_position_left-top"></div>
										<div class="triggerselect slide_bg_position_selector bg_alignselector" data-select="#slide_bg_position" data-val="center top" id="slide_bg_position_center-top"></div>
										<div class="triggerselect slide_bg_position_selector bg_alignselector" data-select="#slide_bg_position" data-val="right top" id="slide_bg_position_right-top"></div>
									</div>
									<div class="bg_align_row">
										<div class="triggerselect slide_bg_position_selector bg_alignselector" data-select="#slide_bg_position" data-val="left center" id="slide_bg_position_left-center"></div>
										<div class="triggerselect slide_bg_position_selector bg_alignselector" data-select="#slide_bg_position" data-val="center center" id="slide_bg_position_center-center"></div>
										<div class="triggerselect slide_bg_position_selector bg_alignselector" data-select="#slide_bg_position" data-val="right center" id="slide_bg_position_right-center"></div>
									</div>
									<div class="bg_align_row">
										<div class="triggerselect slide_bg_position_selector bg_alignselector" data-select="#slide_bg_position" data-val="left bottom" id="slide_bg_position_left-bottom"></div>
										<div class="triggerselect slide_bg_position_selector bg_alignselector" data-select="#slide_bg_position" data-val="center bottom" id="slide_bg_position_center-bottom"></div>
										<div class="triggerselect slide_bg_position_selector bg_alignselector" data-select="#slide_bg_position" data-val="right bottom" id="slide_bg_position_right-bottom"></div>
									</div>
									<div class="bg_align_xy">
										<div class="triggerselect slide_bg_position_selector bg_alignselector" data-select="#slide_bg_position" data-val="percentage" id="slide_bg_position_percentage"></div>
										<xy_label><?php _e('X% Y%', 'revslider');?></xy_label>
									</div>
								</div>
								<row class="directrow slide_bg_pos slide_bg_pos_percentage">
									<onelong><label_icon class="ui_x"></label_icon><input id="slide_bg_positionX" data-evt="updateslidebasic" class="slideinput easyinit shortinput" data-numeric="true" data-allowed="%" type="text" data-r="bg.positionX"></onelong>
									<oneshort><label_icon class="ui_y"></label_icon><input id="slide_bg_positionY" data-evt="updateslidebasic" class="slideinput easyinit" data-numeric="true" data-allowed="%" type="text" data-r="bg.positionY"></oneshort>
								</row>

							</div>
						</div>
					</div>


					<!-- HTML ATTRIBUTE ALT FOR BG IMAGE -->
					<div class="slidebg_image_settings slide_bg_settings">
						<label_a><?php _e('"Alt" Attr.', 'revslider');?></label_a><select data-theme="dark" id="slide_bg_image_alt"  data-show=".slide_bg_*val*alt" data-hide=".slide_bg_customalt" class="slideinput tos2 nosearchbox easyinit"  data-r="attributes.altOption">
							<option value="media_library"><?php _e('Media Library', 'revslider');?></option>
							<option value="file_name"><?php _e('Filename', 'revslider');?></option>
							<option value="custom"><?php _e('Custom', 'revslider');?></option>
						</select><span class="linebreak"></span>
					</div>
					<div class="slidebg_image_settings slidebg_external_settings slide_bg_settings slide_bg_customalt">
						<label_a><?php _e('Custom "Alt"', 'revslider');?></label_a><input placeholder='Enter "Alt" Value' id="slide_bg_img_calt" class="slideinput easyinit" type="text" data-r="attributes.alt"><span class="linebreak"></span>
					</div>

					<!-- HTML ATTRIBUTE TITLE FOR BG IMAGE -->
					<div class="slidebg_image_settings slide_bg_settings">
						<label_a><?php _e('"Title" Attr.', 'revslider');?></label_a><select data-theme="dark" id="slide_bg_image_title"  data-show=".slide_bg_*val*title" data-hide=".slide_bg_customtitle" class="slideinput tos2 nosearchbox easyinit"  data-r="attributes.titleOption">
							<option value="media_library"><?php _e('Media Library', 'revslider');?></option>
							<option value="file_name"><?php _e('Filename', 'revslider');?></option>
							<option value="custom"><?php _e('Custom', 'revslider');?></option>
						</select><span class="linebreak"></span>
					</div>

					<div class="slidebg_image_settings slidebg_external_settings slide_bg_settings slide_bg_customtitle">
						<label_a><?php _e('Custom "Title"', 'revslider');?></label_a><input placeholder='Enter "Title" Value' id="slide_bg_img_ctit" class="slideinput easyinit" type="text" data-r="attributes.title"><span class="linebreak"></span>
					</div>
					<!-- HTML ATTRIBUTE WIDTH AND HEIGHT FOR BG IMAGE -->
					<div class="slidebg_external_settings slide_bg_settings">
						<label_a><?php _e('Width Attrib.', 'revslider');?></label_a><input id="slide_bg_width" data-evt="updateslidebasic" class="slideinput easyinit" type="text" data-r="bg.width" data-numeric="true" data-allowed="px">
						<label_a><?php _e('Height Attrib.', 'revslider');?></label_a><input data-numeric="true" data-allowed="px" id="slide_bg_height" data-evt="updateslidebasic" class="slideinput easyinit" type="text" data-r="bg.height">
					</div>


					<!-- YOUTUBE / VIMEO HTML5 SETTINGS-->
					<div class="slidebg_youtube_settings slidebg_vimeo_settings slidebg_html5_settings slide_bg_settings">
						<div class="div10"></div>
						<label_a><?php _e('Aspect Ratio', 'revslider');?></label_a><select data-theme="dark" id="slide_vid_aratio" class="slideinput tos2 nosearchbox easyinit"  data-r="bg.video.ratio"><option value="16:9">16:9</option><option value="4:3">4:3</option></select><span class="linebreak"></span>
						<div id="slide_dotted_overlay">
							<label_a><?php _e('Overlay', 'revslider');?></label_a><select data-evt="updateslidebasic" id="sl_vid_overlay" class="slideinput tos2 nosearchbox easyinit" data-r="bg.video.dottedOverlay" data-theme="dark"><option value="none" selected="selected"><?php _e('none', 'revslider');?></option><option value="twoxtwo"><?php _e('2 x 2 Black', 'revslider');?></option><option value="twoxtwowhite"><?php _e('2 x 2 White', 'revslider');?></option><option value="threexthree"><?php _e('3 x 3 Black', 'revslider');?></option><option value="threexthreewhite"><?php _e('3 x 3 White', 'revslider');?></option></select><span class="linebreak"></span>
						</div>
						<!--<label_a><?php _e('Loop Mode', 'revslider');?></label_a><select data-theme="dark" id="slide_vid_loop" class="slideinput tos2 nosearchbox easyinit"  data-r="bg.video.loop">
							<option value="none"><?php _e('Disable', 'revslider');?></option>
							<option value="loop"><?php _e('Slider Timer paused', 'revslider');?></option>
							<option value="loopandnoslidestop"><?php _e('Slider Timer keep going', 'revslider');?></option>
						</select><span class="linebreak"></span>-->						
						<longoption><i class="material-icons">open_with</i><label_a><?php _e('Force Cover Mode', 'revslider');?></label_a><input type="checkbox"  id="sl_vid_force_cover" class="slideinput easyinit" data-r="bg.video.forceCover" data-showhide="#slide_dotted_overlay" data-showhidedep="true"/></longoption>
						<longoption><i class="material-icons">pause</i><label_a ><?php _e('Pause Timer during Play', 'revslider');?></label_a><input type="checkbox" class="easyinit slideinput" data-r="bg.video.pausetimer"></longoption>
						<longoption><i class="material-icons">loop</i><label_a ><?php _e('Loop Media', 'revslider');?></label_a><input type="checkbox" class="easyinit slideinput" id="sl_vid_loop_me" data-change="#sl_vid_nextslide" data-changeto="false" data-changewhennot="false" data-r="bg.video.loop"></longoption>										
						<longoption><i class="material-icons">query_builder</i><label_a><?php _e('Start after Slide Transition', 'revslider');?></label_a><input type="checkbox"  id="sl_vid_after_slide_trans" class="slideinput easyinit" data-r="bg.video.startAfterTransition"/></longoption>
						<longoption><i class="material-icons">skip_next</i><label_a><?php _e('Next Slide at End', 'revslider');?></label_a><input type="checkbox"  id="sl_vid_nextslide" data-change="#sl_vid_loop_me" data-changeto="false" data-changewhennot="false" class="slideinput easyinit" data-r="bg.video.nextSlideAtEnd" /></longoption>
						<longoption><i class="material-icons">fast_rewind</i><label_a><?php _e('Rewind at Start', 'revslider');?></label_a><input type="checkbox"  id="sl_vid_forceRewind" class="slideinput easyinit" data-r="bg.video.forceRewind" /></longoption>
						<div style="display:none !important"><longoption><i class="material-icons">volume_mute</i><label_a><?php _e('Mute at Start', 'revslider');?></label_a><input type="checkbox"  id="sl_vid_mute" class="slideinput easyinit" data-r="bg.video.mute" /></longoption></div>
						<div class="div15"></div>
						<row class="slidebg_youtube_settings slidebg_vimeo_settings slide_bg_settings directrow">
							<onelong><label_icon class="ui_volume"></label_icon><input id="slide_vid_vol" class="slideinput easyinit" type="text" data-r="bg.video.volume"></onelong>
							<oneshort><label_icon class="ui_speed"></label_icon><select data-theme="dark" id="slide_vid_speed" class="slideinput tos2 nosearchbox easyinit"  data-r="bg.video.speed"><option value="0.25">1/4</option><option value="0.50">1/2</option><option selected="selected" value="1">Normal</option><option value="1.5">x1.5</option><option value="2">x2</option></select></oneshort>
						</row>
						<row class="directrow">
							<onelong><label_icon class="ui_startat"></label_icon><input id="slide_vid_startat" class="slideinput easyinit" placeholder="00:00" type="text" data-r="bg.video.startAt"></onelong>
							<oneshort><label_icon class="ui_endat"></label_icon><input id="slide_vid_endat" class="slideinput easyinit" placeholder="00:00" type="text" data-r="bg.video.endAt"></oneshort>
						</row>
					</div>
					<div class="div15"></div>
					<div class="slidebg_youtube_settings slide_bg_settings"><label_a><?php _e('Arguments', 'revslider');?></label_a><input id="slide_vid_argsyt" class="slideinput easyinit" type="text" data-r="bg.video.args"><span class="linebreak"></span></div>
					<div class="slidebg_vimeo_settings slide_bg_settings"><label_a><?php _e('Arguments', 'revslider');?></label_a><input id="slide_vid_argvim" class="slideinput easyinit" type="text" data-r="bg.video.argsVimeo"><span class="linebreak"></span></div>

				</div>
			</div><!-- END OF SOURCE SETTINGS -->			
		</div>
	</div>

	<!-- SLIDE FILTERS -->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slide_onscroll"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_12">
			<div class="collapsable">				
				<!-- GENERAL INFO IF NOTHING SET -->
				<div style="display:none">
					<div id="no_onscroll_on_slider">
						<div class="form_inner open">
							<div class="form_inner_header"><i class="material-icons">filter_9_plus</i><?php _e('On Scroll Details', 'revslider');?></div>
							<div class="collapsable">
								<row class="direktrow">
									<labelhalf><i class="material-icons vmi">sms_failed</i></labelhalf>
									<contenthalf><div id="onscroll_info" class="function_info"><?php _e('On Scroll can be Added per Slider in the General Options', 'revslider');?></div></contenthalf>
								</row>
							</div>
						</div>
					</div>
				</div>
				<!-- PARALLAX & 3D SETTINGS -->
				<div id="form_slidebg_pddd" class="form_inner open">
					<div class="form_inner_header"><i class="material-icons">filter_9_plus</i><?php _e('Parallax & 3D Settings', 'revslider');?></div>
					<!--<div class="form_intoaccordion" data-trigger="#sl_fbg_l1_4"><i class="material-icons">arrow_drop_down</i></div>-->
					<div class="collapsable">
						<div class="slider_ddd_subsettings">
							<label_a><?php _e('BG 3D Depth', 'revslider');?></label_a><input type="text" id="sr_paralaxlevel_16_slidebased" class="sliderinput easyinit smallinput callEvent" data-evt="updateParallaxdddBG" data-r="parallax.levels.15">
							<row class="direktrow">
								<labelhalf><i class="material-icons vmi">sms_failed</i></labelhalf>
								<contenthalf><div class="function_info"><?php _e('Global Value ! Option to find under Slider Settings - Parallax Tab', 'revslider');?></div></contenthalf>
							</row>
						</div>
						<div class="slide_parallax_wrap">
							<label_a><?php _e('Parallax Level', 'revslider');?></label_a><select data-theme="dark" id="slide_parallax_level" data-evt="enablePXModule" data-evtparam="slideparallax" class="slideinput tos2 nosearchbox easyinit prallaxlevelselect"  data-r="effects.parallax">
								<option value="-">No Parallax</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
							</select>
						</div>
					</div>
				</div><!--END OF PARALLAX & 3D SETTINGS-->

				
				<!--<div class="all_sbe_dependencies">-->
					<div id="form_slidefilter_scrollbased" class="form_inner open">
						<div class="form_inner_header"><i class="material-icons">system_update_alt</i><?php _e('Scroll Effects', 'revslider');?></div>
						<div class="collapsable">					
							<label_a><?php _e('Fade', 'revslider');?></label_a><select data-theme="dark" data-evt="enableScrollEffectModule" data-evtparam="fade" id="slide_effectscroll_fade" class="slideinput tos2 nosearchbox easyinit callEvent"  data-r="effects.fade">
								<option value="default"><?php _e('Default', 'revslider');?></option>
								<option value="true"><?php _e('Enabled - Scroll Based', 'revslider');?></option>
								<option value="false"><?php _e('Disabled - Time Based', 'revslider');?></option>
							</select>
							<label_a><?php _e('Blur', 'revslider');?></label_a><select data-theme="dark" data-evt="enableScrollEffectModule" data-evtparam="blur" id="slide_effectscroll_blur" class="slideinput tos2 nosearchbox easyinit callEvent"  data-r="effects.blur">
								<option value="default"><?php _e('Default', 'revslider');?></option>
								<option value="true"><?php _e('Enabled - Scroll Based', 'revslider');?></option>
								<option value="false"><?php _e('Disabled - Time Based', 'revslider');?></option>
							</select>
							<label_a><?php _e('Grayscale', 'revslider');?></label_a><select data-theme="dark" data-evt="enableScrollEffectModule" data-evtparam="grayscale" id="slide_effectscroll_grayscale" class="slideinput tos2 nosearchbox easyinit callEvent"  data-r="effects.grayscale">
								<option value="default"><?php _e('Default', 'revslider');?></option>
								<option value="true"><?php _e('Enabled - Scroll Based', 'revslider');?></option>
								<option value="false"><?php _e('Disabled - Time Based', 'revslider');?></option>
							</select>
						</div>
					</div>
				<!--</div>-->
			</div>
		</div>
	</div>


	<!-- SLIDE FILTERS -->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slidebg_filters"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_5"  >
			<!--<div class="collectortabwrap"><div id="collectortab_form_slidebg_filters" class="collectortab form_menu_inside" data-forms='["#form_slidebg_filters"]'><i class="material-icons">blur_on</i><?php _e('Filters', 'revslider');?></div></div>-->
			<!-- FILTER SETTINGS -->
			<div id="form_slidebg_filters_int" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">blur_on</i><?php _e('Filters', 'revslider');?></div>
				<!--<div class="form_intoaccordion" data-trigger="#sl_fbg_l1_3"><i class="material-icons">arrow_drop_down</i></div>-->
				<div class="collapsable">
					<label_a><?php _e('BG Filter', 'revslider');?></label_a><select data-theme="dark" id="slide_bg_filter" class="slideinput tos2 nosearchbox easyinit" data-evtparam="double" data-show=".*val*_warning" data-hide=".filter_warning" data-evt="updateslidebasic" data-unselect=".filter_selector" data-select="#filter_*val*"  data-r="bg.mediaFilter">
								<option value="none">No Filter</option>
									<option value="_1977">1977</option>
									<option value="aden">Aden</option>
									<option value="brooklyn">Brooklyn</option>
									<option value="clarendon">Clarendon</option>
									<option value="earlybird">Earlybird</option>
									<option value="gingham">Gingham</option>
									<option value="hudson">Hudson</option>
									<option value="inkwell">Inkwell</option>
									<option value="lark">Lark</option>
									<option value="lofi">Lo-Fi</option>
									<option value="mayfair">Mayfair</option>
									<option value="moon">Moon</option>
									<option value="nashville">Nashville</option>
									<option value="perpetua">Perpetua</option>
									<option value="reyes">Reyes</option>
									<option value="rise">Rise</option>
									<option value="slumber">Slumber</option>
									<option value="toaster">Toaster</option>
									<option value="walden">Walden</option>
									<option value="willow">Willow</option>
									<option value="xpro2">X-pro II</option>
							</select><span class="linebreak"></span>
					<div id="inst-filter-grid">
						<div id="filter_none" data-val="none" data-hoverevtparam="none" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_none inst-filter-griditem" data-name="No Filter"><div class="inst-filter-griditem-img none"></div></div><!--
						--><div id="filter__1977" data-val="_1977" data-hoverevtparam="_1977" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter__1977 inst-filter-griditem " data-name="1977"><div class="inst-filter-griditem-img _1977"></div></div><!--
						--><div id="filter_aden" data-val="aden" data-hoverevtparam="aden" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_aden inst-filter-griditem " data-name="Aden"><div class="inst-filter-griditem-img aden"></div></div><!--
						--><div id="filter_brooklyn" data-val="brooklyn" data-hoverevtparam="brooklyn" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_brooklyn inst-filter-griditem " data-name="Brooklyn"><div class="inst-filter-griditem-img brooklyn"></div></div><!--
						--><div id="filter_clarendon" data-val="clarendon" data-hoverevtparam="clarendon" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_clarendon inst-filter-griditem " data-name="Clarendon"><div class="inst-filter-griditem-img clarendon"></div></div><!--
						--><div id="filter_earlybird" data-val="earlybird" data-hoverevtparam="earlybird" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_earlybird inst-filter-griditem " data-name="Earlybird"><div class="inst-filter-griditem-img earlybird"></div></div><!--
						--><div id="filter_gingham" data-val="gingham" data-hoverevtparam="gingham" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_gingham inst-filter-griditem " data-name="Gingham"><div class="inst-filter-griditem-img gingham"></div></div><!--
						--><div id="filter_hudson" data-val="hudson" data-hoverevtparam="hudson" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_hudson inst-filter-griditem " data-name="Hudson"><div class="inst-filter-griditem-img hudson"></div></div><!--
						--><div id="filter_inkwell" data-val="inkwell" data-hoverevtparam="inkwell" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_inkwell inst-filter-griditem " data-name="Inkwell"><div class="inst-filter-griditem-img inkwell"></div></div><!--
						--><div id="filter_lark" data-val="lark" data-hoverevtparam="lark" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_lark inst-filter-griditem " data-name="Lark"><div class="inst-filter-griditem-img lark"></div></div><!--
						--><div id="filter_lofi" data-val="lofi" data-hoverevtparam="lofi" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_lofi inst-filter-griditem " data-name="Lo-Fi"><div class="inst-filter-griditem-img lofi"></div></div><!--
						--><div id="filter_mayfair" data-val="mayfair" data-hoverevtparam="mayfair" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_mayfair inst-filter-griditem " data-name="Mayfair"><div class="inst-filter-griditem-img mayfair"></div></div><!--
						--><div id="filter_moon" data-val="moon" data-hoverevtparam="moon" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_moon inst-filter-griditem " data-name="Moon"><div class="inst-filter-griditem-img moon"></div></div><!--
						--><div id="filter_nashville" data-val="nashville" data-hoverevtparam="nashville" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_nashville inst-filter-griditem " data-name="Nashville"><div class="inst-filter-griditem-img nashville"></div></div><!--
						--><div id="filter_perpetua" data-val="perpetua" data-hoverevtparam="perpetua" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_perpetua inst-filter-griditem " data-name="Perpetua"><div class="inst-filter-griditem-img perpetua"></div></div><!--
						--><div id="filter_reyes" data-val="reyes" data-hoverevtparam="reyes" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_reyes inst-filter-griditem " data-name="Reyes"><div class="inst-filter-griditem-img reyes"></div></div><!--
						--><div id="filter_rise" data-val="rise" data-hoverevtparam="rise" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_rise inst-filter-griditem " data-name="Rise"><div class="inst-filter-griditem-img rise"></div></div><!--
						--><div id="filter_slumber" data-val="slumber" data-hoverevtparam="slumber" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_slumber inst-filter-griditem " data-name="Slumber"><div class="inst-filter-griditem-img slumber"></div></div><!--
						--><div id="filter_toaster" data-val="toaster" data-hoverevtparam="toaster" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_toaster inst-filter-griditem " data-name="Toaster"><div class="inst-filter-griditem-img toaster"></div></div><!--
						--><div id="filter_walden" data-val="walden" data-hoverevtparam="walden" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_walden inst-filter-griditem " data-name="Walden"><div class="inst-filter-griditem-img walden"></div></div><!--
						--><div id="filter_willow" data-val="willow" data-hoverevtparam="willow" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_willow inst-filter-griditem " data-name="Willow"><div class="inst-filter-griditem-img willow"></div></div><!--
						--><div id="filter_xpro2" data-val="xpro2" data-hoverevtparam="xpro2" data-leaveevtparam="double" data-leaveevt="updateslidebasic" data-select="#slide_bg_filter" data-hoverevt="showSlideFilter" class="filter_selector callhoverevt triggerselect filter_xpro2 inst-filter-griditem " data-name="X-pro"><div class="inst-filter-griditem-img xpro2"></div></div>
					</div>
					<div class="div25"></div>					
					<row class="direktrow filter_warning earlybird_warning, lark_warning moon_warning toaster_warning willow_warning xpro2_warning">
						<labelhalf><i class="material-icons vmi">sms_failed</i></labelhalf>
						<contenthalf><div class="function_info"><?php _e('The Filter may not work with HTML5 Videos in Internet Explorer and Edge Browsers', 'revslider');?></div></contenthalf>
					</row>
					
				</div><!-- END OF COLLAPSABLE -->
			</div><!-- END OF FILTER SETTINGS -->
		</div>
	</div>

	<!-- SLIDE ANIMATION SETTINGS-->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slide_transition"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_2" >
			<!--<div class="collectortabwrap"><div id="collectortab_form_slide_transition" class="collectortab form_menu_inside" data-forms='["#form_slidebg_transition"]'><?php _e('Animation', 'revslider');?></div></div>			-->
			<!--<div class="form_intoaccordion"><i class="material-icons">arrow_drop_down</i></div>-->
			<!-- TRANSITION EXAMPLES -->
			<div id="form_slidebg_transition" class="form_inner">
				<div class="form_inner_header"><i class="material-icons">photo_filter</i><?php _e('Slide Animation', 'revslider');?></div>
				<div class="collapsable" style="display:block">

					<div id="active_transitions" class="saw_cells">
						<label_full><?php _e('Active Transition Order', 'revslider');?></label_full>
						<ul id="active_transitions_innerwrap"></ul>
						<label_a></label_a><div class="autosize rightbutton basic_action_button callEventButton" data-evt="showhidetransitions"><i class="material-icons">add</i><?php _e('Add Transition', 'revslider');?></div>
						<div class="tp-clearfix"></div>
						<div id="transition_selector" style="display:none"></div>
					</div>
				</div>
			</div>
			<div id="form_slidebg_transition_settings" class="form_inner">
				<div class="form_inner_header"><i class="material-icons">build</i><span id="cur_transition_sub_settings"><?php _e('Fade Settings', 'revslider');?></span></div>
				<div class="collapsable" style="display:block">
					<div id="active_transitions_settings" class="saw_cells">
						<label_icon class="ui_duration singlerow"></label_icon><input id="sl_trans_duration" class="withsuffix slideinput valueduekeyboard smallinput easyinit input_with_presets" data-suffix="ms" data-numeric="true" data-allowed="random,default,ms" data-presets_text="$C$1000ms!$I$Default!$R$Random" data-presets_val="1000!default!random!" data-evt="updateSlideTransitionTimeLine" data-r="timeline.duration.#curslidetrans#" data-steps="300" type="text"><span class="linebreak"></span>
						<label_icon class="ui_easing_in singlerow"></label_icon><select id="sl_trans_appear_ease" class="slideinput tos2 nosearchbox easyinit easingSelect" data-r="timeline.easeIn.#curslidetrans#" data-theme="dark"></select><span class="linebreak"></span>
						<label_icon class="ui_easing_out singlerow"></label_icon><select id="sl_trans_disappear_ease" class="slideinput tos2 nosearchbox easyinit easingSelect" data-r="timeline.easeOut.#curslidetrans#" data-theme="dark"></select><span class="linebreak"></span>
						<label_icon class="ui_slotamount singlerow"></label_icon><input id="sl_tr_box_amount" class="slideinput valueduekeyboard smallinput easyinit input_with_presets" data-numeric="true" data-allowed="random,default" data-presets_text="$C$1!$C$2!$C$5!$C$10!$I$Default!$R$Random" data-presets_val="1!2!5!10!default!random!"  data-r="timeline.slots.#curslidetrans#" data-steps="1" data-min="1" data-max="500" type="text"><span class="linebreak"></span>
						<label_icon class="ui_slotrotation singlerow"></label_icon><input id="sl_trans_slot_rotation" class="slideinput valueduekeyboard smallinput easyinit input_with_presets" data-numeric="true" data-allowed="random,default" data-presets_text="$C$0!$C$45!$C$90!$C$180!$I$Default!$R$Random" data-presets_val="0!45!90!180!default!random!"  data-r="timeline.rotation.#curslidetrans#" data-steps="45" type="text"><span class="linebreak"></span>
					</div>
				</div><!-- END OF COLLAPSABLE -->
			</div>
		</div>
	</div>
	<!-- END OF SLIDE ANIMATION SETTINGS-->


	<!-- SLIDE PAN ZOOM SETTINGS-->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slide_kenburn_outter"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_3" >
			<!--<div class="collectortabwrap"><div id="collectortab_form_slide_transition" class="collectortab form_menu_inside" data-forms='["#form_slide_kenburn_outter"]'><?php _e('Ken Burns / Pan Zoom', 'revslider');?></div></div>			-->
			<!--<div class="form_intoaccordion"><i class="material-icons">arrow_drop_down</i></div>-->
			<!-- KEN BURN SETTINGS-->
			<div id="form_slidebg_kenburn" class="form_inner">
				<div id="sl_pz_innerheader" class="form_inner_header"><i class="material-icons">photo_filter</i><?php _e('Pan Zoom Settings', 'revslider');?></div>
				<div id="sl_pz_onoff" class="on_off_navig_wrap"><input type="checkbox"  data-evt="updateKenBurnBasics" id="sl_pz_set" data-showhide="#internal_kenburn_settings" data-showhidedep="true" class="slideinput easyinit" data-r="panzoom.set" /></div>
				<div id="kenburnissue" class="collapsable">
					<row class="direktrow">
						<labelhalf><i class="material-icons vmi">sms_failed</i></labelhalf>
						<contenthalf><div id="kenburnissue_info" class="function_info"><?php _e('Place the shortcode on the page or post where you want to show this module.', 'revslider');?></div></contenthalf>
					</row>
				</div>
				<div id="internal_kenburn_settings" class="collapsable" style="display:block !important">
					<div class="slidebg_image_settings slidebg_external_settings slide_bg_settings">
						<div id="ken_burn_bg_setting_on">
						</div>

						<row id="sl_pz_fs_fe" class="direktrow">
							<onelong id="sl_pz_fs_wrap"><label_icon class="ui_scale_start"></label_icon><input data-allowed="%" data-numeric="true" data-evt="updateKenBurnSettings" data-evtparam="begin" id="sl_pz_fs" class="slideinput easyinit verysmallinput valueduekeyboard" data-min="0" data-max="1000" type="text" data-r="panzoom.fitStart"></onelong>
							<oneshort><label_icon class="ui_scale_end"></label_icon><input data-allowed="%" data-numeric="true" data-evt="updateKenBurnSettings" data-evtparam="end" id="sl_pz_fe" class="slideinput easyinit verysmallinput valueduekeyboard" data-min="0" data-max="1000" type="text" data-r="panzoom.fitEnd"></oneshort>
						</row>

						<row id="sl_pz_xs_xe" class="direktrow">
							<onelong><label_icon class="ui_x_start"></label_icon><input data-allowed="px" data-numeric="true" data-evt="updateKenBurnSettings" data-evtparam="begin" id="sl_pz_xs" class="slideinput easyinit verysmallinput valueduekeyboard" data-min="-1000" data-max="1000" type="text" data-r="panzoom.xStart"></onelong>
							<oneshort><label_icon class="ui_x_end"></label_icon><input data-allowed="px" data-numeric="true" data-evt="updateKenBurnSettings" data-evtparam="end" id="sl_pz_xe" class="slideinput easyinit verysmallinput valueduekeyboard" data-min="-1000" data-max="1000" type="text" data-r="panzoom.xEnd"></oneshort>
						</row>

						<row id="sl_pz_ys_ye" class="direktrow">
							<onelong><label_icon class="ui_y_start"></label_icon><input data-allowed="px" data-numeric="true" data-evt="updateKenBurnSettings" data-evtparam="begin" id="sl_pz_ys" class="slideinput easyinit verysmallinput valueduekeyboard" data-min="-1000" data-max="1000" type="text" data-r="panzoom.yStart"></onelong>
							<oneshort><label_icon class="ui_y_end"></label_icon><input data-allowed="px" data-numeric="true" data-evt="updateKenBurnSettings" data-evtparam="end" id="sl_pz_ye" class="slideinput easyinit verysmallinput valueduekeyboard" data-min="-1000" data-max="1000" type="text" data-r="panzoom.yEnd"></oneshort>
						</row>

						<row id="sl_pz_rs_re" class="direktrow">
							<onelong><label_icon class="ui_rotate_start"></label_icon><input data-allowed="deg" data-numeric="true" data-evt="updateKenBurnSettings" data-evtparam="begin" id="sl_pz_ro" class="slideinput easyinit verysmallinput valueduekeyboard" data-min="-2000" data-max="2000" type="text" data-r="panzoom.rotateStart"></onelong>
							<oneshort><label_icon class="ui_rotate_end"></label_icon><input data-allowed="deg" data-numeric="true" data-evt="updateKenBurnSettings" data-evtparam="end" id="sl_pz_re" class="slideinput easyinit verysmallinput valueduekeyboard" data-min="-2000" data-max="2000" type="text" data-r="panzoom.rotateEnd"></oneshort>
						</row>

						<row id="sl_pz_bs_be" class="direktrow">
							<onelong><label_icon class="ui_blur_start"></label_icon><input data-allowed="px" data-numeric="true" data-evt="updateKenBurnSettings" data-evtparam="begin" id="sl_pz_blurs" class="slideinput easyinit verysmallinput valueduekeyboard" data-min="0" data-max="100" type="text" data-r="panzoom.blurStart"></onelong>
							<oneshort><label_icon class="ui_blur_end"></label_icon><input data-allowed="px" data-numeric="true" data-evt="updateKenBurnSettings" data-evtparam="end" id="sl_pz_blure" class="slideinput easyinit verysmallinput valueduekeyboard" data-min="0" data-max="100" type="text" data-r="panzoom.blurEnd"></oneshort>
						</row>

						<label_a><?php _e('Easing', 'revslider')?></label_a><select data-evt="updateKenBurnSettings" id="sl_pz_ease" class="slideinput tos2 nosearchbox easyinit" data-theme="dark" data-r="panzoom.ease"><option value="none">none</option><option value="power0.in">power0.in</option><option value="power0.inOut">power0.inOut</option><option value="power0.out">power0.out</option><option value="power1.in">power1.in</option><option value="power1.inOut">power1.inOut</option><option value="power1.out">power1.out</option><option value="power2.in">power2.in</option><option value="power2.inOut">power2.inOut</option><option value="power2.out">power2.out</option><option value="power3.in">power3.in</option><option value="power3.inOut">power3.inOut</option><option value="power3.out">power3.out</option><option value="power4.in">power4.in</option><option value="power4.inOut">power4.inOut</option><option value="power4.out">power4.out</option><option value="back.in">back.in</option><option value="back.inOut">back.inOut</option><option value="back.out">back.out</option><option value="bounce.in">bounce.in</option><option value="bounce.inOut">bounce.inOut</option><option value="bounce.out">bounce.out</option><option value="circ.in">circ.in</option><option value="circ.inOut">circ.inOut</option><option value="circ.out">circ.out</option><option value="elastic.in">elastic.in</option><option value="elastic.inOut">elastic.inOut</option><option value="elastic.out">elastic.out</option><option value="expo.in">expo.in</option><option value="expo.inOut">expo.inOut</option><option value="expo.out">expo.out</option><option value="sine.in">sine.in</option><option value="sine.inOut">sine.inOut</option><option value="sine.out">sine.out</option><option value="slow">slow</option></select><span class="linebreak"></span>
						<label_a><?php _e('Duration', 'revslider');?></label_a><input data-allowed="ms" data-numeric="true" data-evt="updateKenBurnSettings" id="sl_pz_dur" class="slideinput easyinit valueduekeyboard withsuffix" data-suffix="ms" data-min="0" data-max="1000000" type="text" data-r="panzoom.duration">
						<div id="kenburn_timeline"><div class="pz_timedone"></div><div class="pz_pin"></div></div>
						<div id="kenburn_simulator" data-states="play,stop" data-start_state="play" data-stop="previewKenBurn" data-stop_state="" data-stop_icon="stop" data-play="previewStopKenBurn"  data-play_state="" data-play_icon="play_arrow" class="basic_action_button onlyicon switch_button"><i class="switch_button_icon material-icons"></i><span class="switch_button_state"></span></div>
						<div data-evt="rewindKenBurn" class="basic_action_button callEventButton onlyicon"><i class="material-icons">rotate_left</i></div>


					</div>
				</div><!-- END OF COLLAPSABLE-->
			</div>
			<!--END OF KEN BURN SETTINGS -->
		</div>
	</div>

	<!-- SLIDE GLOBAL LAYERS SETTINGS -->
	<div id="gst_slide_10_outter" class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper" data-collapsvisibles="true">
		<div id="form_slidestatic"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_10" >
			<!-- HTML TAGS -->
			<div id="form_slidegeneral_timing" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">album</i><?php _e('Global Layers', 'revslider');?></div>

				<div class="collapsable">
					<label_a><?php _e('Overflow', 'revslider');?></label_a><select id="sl_static_layers_overflow" class="slideinput tos2 setboxes easyinit" data-r="static.overflow" data-theme="dark">
						<option value="visible"><?php _e('Visible', 'revslider');?></option>
						<option value="hidden"><?php _e('Hidden', 'revslider');?></option>
					</select><span class="linebreak"></span>

					<label_a><?php _e('Z Position', 'revslider');?></label_a><select id="sl_static_layers_z_position" class="slideinput tos2 setboxes easyinit" data-r="static.position" data-theme="dark">
						<option value="front"><?php _e('Front', 'revslider');?></option>
						<option value="back"><?php _e('Back', 'revslider');?></option>
					</select><span class="linebreak"></span>

					<longoption><i class="material-icons">visibility</i><label_a><?php _e('Show Last Edited Slide', 'revslider');?></label_a><input type="checkbox"  id="sr_showhidelastedited" data-evt="showLastEditedSlideStatic" class="callEvent slideinput easyinit" data-r="static.lastEdited"/></longoption>
				</div>
			</div><!-- END OF HTML TAGS -->
		</div>
	</div><!-- END OF SLIDE GENERALS -->



	<!-- SLIDE GENERALS -->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper" data-collapsvisibles="true">
		<div id="form_slidegeneral"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_4" >
			<!-- HTML TAGS -->
			<div id="form_slidegeneral_timing" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">code</i><?php _e('Slide HTML Tags', 'revslider');?></div>
				<!--<div class="form_intoaccordion" data-trigger="#sl_fge_l1_1"><i class="material-icons">arrow_drop_down</i></div>-->

				<div class="collapsable">
					<label_a><?php _e('Class', 'revslider');?></label_a><input id="slide_ls_class" placeholder="Custom Slide Classes" class="slideinput easyinit" type="text" data-r="attributes.class"><span class="linebreak"></span>
					<label_a><?php _e('ID', 'revslider');?></label_a><input id="slide_ls_id" placeholder="Custom Slide ID" class="slideinput easyinit" type="text" data-r="attributes.id"><span class="linebreak"></span>
					<label_a><?php _e('HTML Data', 'revslider');?></label_a><textarea style="height:75px; line-height:20px;padding-top:5px" placeholder="i.e. data-min='12' data-custom='somevalue'" id="slide_ls_data" class="slideinput easyinit" type="text" data-r="attributes.data"></textarea>

				</div>
			</div><!-- END OF HTML TAGS -->

			<!-- Link & Seo -->
			<div id="form_slidegeneral_linkseo" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">link</i><?php _e('Link & Seo', 'revslider');?></div>
				<!--<div class="form_intoaccordion" data-trigger="#sl_fge_l1_3"><i class="material-icons">arrow_drop_down</i></div>-->

				<div class="collapsable">

					<label_a><?php _e('Slide Link', 'revslider');?></label_a><input type="checkbox"  id="sl_seo_set" class="slideinput easyinit" data-showhide="#slide_link_wrap" data-showhidedep="true" data-r="seo.set" /><span class="linebreak"></span>
					<div id="slide_link_wrap">
						<label_a><?php _e('Type', 'revslider');?></label_a><select data-theme="dark" id="slide_seo_type" class="slideinput tos2 nosearchbox easyinit"  data-r="seo.type" data-show="#slidelink_*val*_seo" data-hide=".slidelink_seo_subs">
							<option value="regular"><?php _e('Regular', 'revslider');?></option>
							<option value="slide"><?php _e('To Slide', 'revslider');?></option>
						</select><span class="linebreak"></span>
						<div class="slidelink_seo_subs" id="slidelink_regular_seo">
							<label_a><?php _e('URL', 'revslider');?></label_a><input placeholder="Enter URL to link to" id="slide_ls_link" class="slideinput easyinit" type="text" data-r="seo.link"><span class="linebreak"></span>
							<label_a><?php _e('Protocol', 'revslider');?></label_a><select data-theme="dark" id="slide_ls_url_help" class="slideinput tos2 nosearchbox easyinit"  data-r="seo.linkHelp">
								<option value="http"><?php _e('http://', 'revslider');?></option>
								<option value="https"><?php _e('https://', 'revslider');?></option>
								<option value="auto"><?php _e('Auto http / https', 'revslider');?></option>
								<option value="keep"><?php _e('Keep as it is', 'revslider');?></option>	
							</select>
							<label_a><?php _e('Target', 'revslider');?></label_a><select data-theme="dark" id="slide_ls_link_target" class="slideinput tos2 nosearchbox easyinit"  data-r="seo.target">
								<option value="_self"><?php _e('_self', 'revslider');?></option>
								<option value="_blank"><?php _e('_blank', 'revslider');?></option>
								<option value="_top"><?php _e('_top', 'revslider');?></option>
								<option value="_parent"><?php _e('_parent', 'revslider');?></option>														
							</select>							
						</div>
						<div class="slidelink_seo_subs" id="slidelink_slide_seo">
							<label_a><?php _e('Link to Slide', 'revslider');?></label_a><select data-theme="dark" id="slide_seo_linktoslide" class="slideinput tos2 nosearchbox easyinit"  data-r="seo.slideLink"></select><span class="linebreak"></span>
						</div>
						<label_a><?php _e('Sensibility', 'revslider');?></label_a><select data-theme="dark" id="slide_seo_z" class="slideinput tos2 nosearchbox easyinit"  data-r="seo.z">
							<option value="front"><?php _e('Over Layers (Front)', 'revslider');?></option>
							<option value="back"><?php _e('Behind Layers (Back)', 'revslider');?></option>
						</select>
					</div>
				</div>
			</div><!-- END OF Link & Seo -->


		</div>
	</div><!-- END OF SLIDE GENERALS -->

	<!-- SLIDE PROGRESS -->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slide_progress"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_8" >
			<!-- PROGRESS -->
			<div id="form_slidegeneral_timing" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">timer</i><?php _e('Progress', 'revslider');?></div>
				<!--<div class="form_intoaccordion" data-trigger="#sl_fge_l1_1"><i class="material-icons">arrow_drop_down</i></div>-->

				<div class="collapsable">
					<label_a><?php _e('Slide Length', 'revslider');?></label_a><input id="slide_length" class="slideinput easyinit" data-allowed="ms,Default" data-numeric="true" type="text" data-valcheck="slideMinLength"  placeholder="Default" data-evt="updateMaxTime" data-r="timeline.delay"><span class="linebreak"></span>
					<label_a><?php _e('Pause Slider', 'revslider');?></label_a><select data-theme="dark" id="slide_time_stopOnPurpose" class="slideinput tos2 nosearchbox easyinit"  data-r="timeline.stopOnPurpose">
						<option value="false"><?php _e('Default', 'revslider');?></option>
						<option value="true"><?php _e('Stop Slider Progress', 'revslider');?></option>
					</select>
				</div>
			</div><!-- END OF PROGRESS AND STATE -->

			<!-- Visibilty -->
			<div id="form_slidegeneral_visibility" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">check_circle</i><?php _e('Visibility', 'revslider');?></div>
				<!--<div class="form_intoaccordion" data-trigger="#sl_fge_l1_1"><i class="material-icons">arrow_drop_down</i></div>-->
				<div class="collapsable">
					<label_icon class="ui_hide_in_nav singlerow"></label_icon><select data-theme="dark" id="slide_visibil_hideFromNavigation" class="slideinput tos2 nosearchbox easyinit"  data-r="visibility.hideFromNavigation">
						<option value="false"><?php _e('Visible in Navigation', 'revslider');?></option>
						<option value="true"><?php _e('Hidden in Navigation', 'revslider');?></option>
					</select><span class="linebreak"></span>
					<label_icon class="ui_hide_after_loop singlerow"></label_icon><input id="slide_vis_loop" data-numeric="true" data-allowed="" class="slideinput easyinit" type="text" data-r="visibility.hideAfterLoop"><span class="linebreak"></span>
					<label_icon class="ui_hide_on_mobile singlerow"></label_icon><input type="checkbox"  id="sl_vis_hidemobile" class="slideinput easyinit" data-r="visibility.hideOnMobile" /><span class="linebreak"></span>
				</div>
			</div><!-- END OF PROGRESS AND STATE -->
		</div>
	</div>



	<!-- SLIDE PUBLISH -->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slide_publish"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_9" >

			<!-- PUBLISH AND STATE -->
			<div id="form_slidegeneral_progstate" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">event</i><?php _e('Publish', 'revslider');?></div>
				<!--<div class="form_intoaccordion" data-trigger="#sl_fge_l1_1"><i class="material-icons">arrow_drop_down</i></div>-->
				<div class="collapsable">
					<label_icon class="ui_published singlerow"></label_icon><select data-theme="dark" id="slide_publish_State" class="slideinput tos2 nosearchbox easyinit callEvent" data-evt="updatepublishicons" data-r="publish.state">
						<option value="published"><?php _e('Published', 'revslider');?></option>
						<option value="unpublished"><?php _e('Unpublished', 'revslider');?></option>
					</select>
					<row class="direktrow">
						<onelong><label_icon class="ui_published_from"></label_icon><input id="slide_pub_from" class="inputDatePicker slideinput easyinit" type="text" data-r="publish.from"></onelong>
						<oneshort><label_icon class="ui_published_until"></label_icon><input id="slide_pub_until" class="inputDatePicker slideinput easyinit" type="text" data-r="publish.to"></oneshort>
					</row>					
				</div>
			</div><!-- END OF PROGRESS AND STATE -->
		</div>
	</div>

	<!-- WPML RULES -->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slide_wpml"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_13" >

			<!-- PUBLISH AND STATE -->
			<div id="form_slidegeneral_progstate" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">event</i><?php _e('Wordpress Multi Language', 'revslider');?></div>				
				<div class="collapsable">					
					<?php
if ($wpml->wpml_exists()) {
	?>
					<div id="wpml_exists">
						<label_a><?php _e('Slide Lang.', 'revslider');?></label_a><select data-theme="dark" id="slide_wpml_language" data-evt="changeflags" class="callEvent wpml_lang_selector slideinput tos2 easyinit"  data-r="child.language">
						</select><span class="linebreak"></span>
					</div>
					<?php
}
?>
				</div>
			</div><!-- END OF PROGRESS AND STATE -->
		</div>
	</div>

	<!-- SLIDE PARAMETERS SETTINGS-->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slide_parameters"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_7" >
			<!-- PARAMETERS -->
			<div id="form_slidegeneral_params" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">info</i><?php _e('Parameters', 'revslider');?></div>
				<!--<div class="form_intoaccordion" data-trigger="#sl_fge_l1_4"><i class="material-icons">arrow_drop_down</i></div>-->

				<div id="slide_parameters" class="collapsable">
					<label_a><?php _e('Parameter 1', 'revslider');?></label_a><input placeholder="Enter Any Text" id="slide_info_p1" class="tqinput slideinput easyinit " type="text" data-r="info.params.0.v"><input id="slide_info_p1ch" class="oqinput slideinput easyinit valueduekeyboard" data-min="0" data-max="255" type="text" data-r="info.params.0.l"><span class="linebreak"></span>
					<label_a><?php _e('Parameter 2', 'revslider');?></label_a><input id="slide_info_p2" placeholder="Enter Any Text" class="tqinput slideinput easyinit " type="text" data-r="info.params.1.v"><input id="slide_info_p2ch" class="oqinput slideinput easyinit valueduekeyboard" data-min="0" data-max="255" type="text" data-r="info.params.1.l"><span class="linebreak"></span>
					<label_a><?php _e('Parameter 3', 'revslider');?></label_a><input id="slide_info_p3" placeholder="Enter Any Text" class="tqinput slideinput easyinit " type="text" data-r="info.params.2.v"><input id="slide_info_p3ch" class="oqinput slideinput easyinit valueduekeyboard" data-min="0" data-max="255" type="text" data-r="info.params.2.l"><span class="linebreak"></span>
					<label_a><?php _e('Parameter 4', 'revslider');?></label_a><input id="slide_info_p4" placeholder="Enter Any Text" class="tqinput slideinput easyinit " type="text" data-r="info.params.3.v"><input id="slide_info_p4ch" class="oqinput slideinput easyinit valueduekeyboard" data-min="0" data-max="255" type="text" data-r="info.params.3.l"><span class="linebreak"></span>
					<label_a><?php _e('Parameter 5', 'revslider');?></label_a><input id="slide_info_p5" placeholder="Enter Any Text" class="tqinput slideinput easyinit " type="text" data-r="info.params.4.v"><input id="slide_info_p5ch" class="oqinput slideinput easyinit valueduekeyboard" data-min="0" data-max="255" type="text" data-r="info.params.4.l"><span class="linebreak"></span>
					<label_a><?php _e('Parameter 6', 'revslider');?></label_a><input id="slide_info_p6" placeholder="Enter Any Text" class="tqinput slideinput easyinit " type="text" data-r="info.params.5.v"><input id="slide_info_p6ch" class="oqinput slideinput easyinit valueduekeyboard" data-min="0" data-max="255" type="text" data-r="info.params.5.l"><span class="linebreak"></span>
					<label_a><?php _e('Parameter 7', 'revslider');?></label_a><input id="slide_info_p7" placeholder="Enter Any Text" class="tqinput slideinput easyinit " type="text" data-r="info.params.6.v"><input id="slide_info_p7ch" class="oqinput slideinput easyinit valueduekeyboard" data-min="0" data-max="255" type="text" data-r="info.params.6.l"><span class="linebreak"></span>
					<label_a><?php _e('Parameter 8', 'revslider');?></label_a><input id="slide_info_p8" placeholder="Enter Any Text" class="tqinput slideinput easyinit " type="text" data-r="info.params.7.v"><input id="slide_info_p8ch" class="oqinput slideinput easyinit valueduekeyboard" data-min="0" data-max="255" type="text" data-r="info.params.7.l"><span class="linebreak"></span>
					<label_a><?php _e('Parameter 9', 'revslider');?></label_a><input id="slide_info_p9" placeholder="Enter Any Text" class="tqinput slideinput easyinit " type="text" data-r="info.params.8.v"><input id="slide_info_p9ch" class="oqinput slideinput easyinit valueduekeyboard" data-min="0" data-max="255" type="text" data-r="info.params.8.l"><span class="linebreak"></span>
					<label_a><?php _e('Parameter 10', 'revslider');?></label_a><input id="slide_info_p10" placeholder="Enter Any Text" class="tqinput slideinput easyinit " type="text" data-r="info.params.9.v"><input id="slide_info_p10ch" class="oqinput slideinput easyinit valueduekeyboard" data-min="0" data-max="255" type="text" data-r="info.params.9.l"><span class="linebreak"></span>
					<label_a><?php _e('Description', 'revslider');?></label_a><textarea placeholder="Enter any Description which can be referenced from Navigation Elements." style="height:100px; line-height:20px;padding-top:5px;width:100%" id="slide_info_desc" class="slideinput easyinit" type="text" data-r="info.description"></textarea><span class="linebreak"></span>
				</div>
			</div><!-- END OF PARAMETERS -->
		</div>
	</div>


	<!-- SLIDE LOOP SETTINGS-->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slide_loops"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_11" >
			<!-- PARAMETERS -->
			<div id="form_slide_loops_iner" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">info</i><?php _e('Loop All Layer Timeline', 'revslider');?></div>					
				<div class="collapsable">
					<label_a><?php _e('Use Slide Loop', 'revslider');?></label_a><input type="checkbox"  data-setclasson="#timeline" data-class="slideloopon" id="sl_layers_loop" class="slideinput easyinit callEvent" data-evt="updateSlideLoopRange" data-showhide="#slide_loop_wrap" data-showhidedep="true" data-r="timeline.loop.set" /><span class="linebreak"></span>
					<div id="slide_loop_wrap">
						<label_a><?php _e('Repeat', 'revslider');?></label_a><input id="slide_loop_repeat" class="slideinput easyinit input_with_presets" type="text" data-r="timeline.loop.repeat" data-numeric="true" data-allowed="unlimited" data-presets_text="$C$1!$C$2!$R$Unlimited!" data-presets_val="1!2!unlimited!"><span class="linebreak"></span>
						<label_a><?php _e('Start', 'revslider');?></label_a><input id="slide_loop_start" class="slideinput easyinit callEvent" data-evt="updateSlideLoopRange" type="text" data-r="timeline.loop.start"><span class="linebreak"></span>
						<label_a><?php _e('End', 'revslider');?></label_a><input id="slide_loop_end" class="slideinput easyinit callEvent" data-evt="updateSlideLoopRange"  type="text" data-r="timeline.loop.end"><span class="linebreak"></span>
					</div>
				</div>
			</div><!-- END OF PARAMETERS -->
		</div>
	</div>



	<!-- SLIDE THUMBNAIL ETTINGS-->
	<div class="form_collector slide_settings_collector" data-type="slideconfig" data-pcontainer="#slide_settings" data-offset="#rev_builder_wrapper">
		<div id="form_slide_thumbnail"  class="formcontainer form_menu_inside collapsed" data-select="#gst_slide_6" >

			<!--<div class="form_intoaccordion"><i class="material-icons">arrow_drop_down</i></div>-->
			<!-- THUMBNAILS -->
			<div id="form_slidegeneral_thumbnails" class="form_inner open">
				<div class="form_inner_header"><i class="material-icons">photo_album</i><?php _e('Module Admin Thumbnail', 'revslider');?></div>
				<!--<div class="form_intoaccordion" data-trigger="#sl_fge_l1_2"><i class="material-icons">arrow_drop_down</i></div>-->

				<div class="collapsable">
					<row class="direktrow">
						<onelong><label_a><?php _e('Admin Thumb', 'revslider');?></label_a><div class="miniprevimage_wrap"><i class="material-icons">filter_hdr</i><div id="admin_purpose_thumbnail"></div></div></onelong>
						<oneshort>
							<div data-evt="updateslidethumbs" data-r="#slide#.slide.thumb.customAdminThumbSrc" data-rid="#slide#.slide.thumb.customAdminThumbSrcId" class="getImageFromMediaLibrary basic_action_button  callEventButton"><i class="material-icons">folder</i><?php _e('Select', 'revslider');?></div>
							<div data-evt="resetslideadminthumb" data-evtparam="slide.thumb.customAdminThumbSrc" class="resettodefault basic_action_button  callEventButton"><i class="material-icons">update</i><?php _e('Reset', 'revslider');?></div>
						</oneshort>
					</row>

					<div class="div15"></div>
					<row>
						<onelong><label_a><?php _e('Navig. Thumb', 'revslider');?></label_a><div class="miniprevimage_wrap"><i class="material-icons">filter_hdr</i><div id="navigation_purpose_thumbnail"></div></div></onelong>
						<oneshort>
							<div data-evt="updateslidethumbs" data-r="#slide#.slide.thumb.customThumbSrc" data-rid="#slide#.slide.thumb.customThumbSrcId" class="getImageFromMediaLibrary basic_action_button  callEventButton"><i class="material-icons">folder</i><?php _e('Select', 'revslider');?></div>
							<div data-evt="resetslideadminthumb" data-evtparam="slide.thumb.customThumbSrc" class="resettodefault basic_action_button  callEventButton"><i class="material-icons">update</i><?php _e('Reset', 'revslider');?></div>
						</oneshort>
					</row>
					<label_a><?php _e('Dimension', 'revslider');?></label_a><select data-theme="dark" id="slide_thumb_dimension" class="slideinput tos2 nosearchbox easyinit"  data-r="thumb.dimension">
						<option value="slider"><?php _e('From Slider Settings', 'revslider');?></option>
						<option value="orig"><?php _e('Original Size', 'revslider');?></option>
					</select>
					
				</div>

			</div>			
		</div>
	</div>

</div><!-- END OF SLIDE SETTINGS -->





