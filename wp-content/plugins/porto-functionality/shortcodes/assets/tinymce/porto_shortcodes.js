function porto_shortcode_close() {

}

(function() {
	'use strict';

	function porto_shortcode_animation_type() {
		var html = '<option value="">none</option>\
		<optgroup label="Attention Seekers">\
			<option value="bounce">bounce</option>\
			<option value="flash">flash</option>\
			<option value="pulse">pulse</option>\
			<option value="rubberBand">rubberBand</option>\
			<option value="shake">shake</option>\
			<option value="swing">swing</option>\
			<option value="tada">tada</option>\
			<option value="wobble">wobble</option>\
		</optgroup>\
		<optgroup label="Bouncing Entrances">\
			<option value="bounceIn">bounceIn</option>\
			<option value="bounceInDown">bounceInDown</option>\
			<option value="bounceInLeft">bounceInLeft</option>\
			<option value="bounceInRight">bounceInRight</option>\
			<option value="bounceInUp">bounceInUp</option>\
		</optgroup>\
		<optgroup label="Fading Entrances">\
			<option value="fadeIn">fadeIn</option>\
			<option value="fadeInDown">fadeInDown</option>\
			<option value="fadeInDownBig">fadeInDownBig</option>\
			<option value="fadeInLeft">fadeInLeft</option>\
			<option value="fadeInLeftBig">fadeInLeftBig</option>\
			<option value="fadeInRight">fadeInRight</option>\
			<option value="fadeInRightBig">fadeInRightBig</option>\
			<option value="fadeInUp">fadeInUp</option>\
			<option value="fadeInUpBig">fadeInUpBig</option>\
		</optgroup>\
		<optgroup label="Flippers">\
			<option value="flip">flip</option>\
			<option value="flipInX">flipInX</option>\
			<option value="flipInY">flipInY</option>\
		</optgroup>\
		<optgroup label="Lightspeed">\
			<option value="lightSpeedIn">lightSpeedIn</option>\
		</optgroup>\
		<optgroup label="Rotating Entrances">\
			<option value="rotateIn">rotateIn</option>\
			<option value="rotateInDownLeft">rotateInDownLeft</option>\
			<option value="rotateInDownRight">rotateInDownRight</option>\
			<option value="rotateInUpLeft">rotateInUpLeft</option>\
			<option value="rotateInUpRight">rotateInUpRight</option>\
		</optgroup>\
		<optgroup label="Sliders">\
			<option value="slideInDown">slideInDown</option>\
			<option value="slideInLeft">slideInLeft</option>\
			<option value="slideInRight">slideInRight</option>\
		</optgroup>\
		<optgroup label="Specials">\
			<option value="hinge">hinge</option>\
			<option value="rollIn">rollIn</option>\
		</optgroup>';

		return html;
	}

	function porto_shortcode_testimonial_view_type() {
		var html = '<option value="default">Default</option>\
			<option value="simple">Simple</option>\
			<option value="transparent">Transparent</option>';

		return html;
	}

	function porto_shortcode_testimonial_color_skin() {
		var html = '<option value="">Normal</option>\
			<option value="white">White</option>';

		return html;
	}

	function porto_shortcode_align() {
		var html = '<option value="">None</option>\
			<option value="left">Left</option>\
			<option value="right">Right</option>\
			<option value="center">Center</option>\
			<option value="justify">Justify</option>';

		return html;
	}

	function porto_shortcode_boolean_true() {
		var html = '<option value="true" selected="selected">True</option>\
			<option value="">False</option>';

		return html;
	}

	function porto_shortcode_boolean_false() {
		var html = '<option value="true">True</option>\
			<option value="" selected="selected">False</option>';

		return html;
	}

	function porto_shortcode_blog_layout() {
		var html = '<option value="full">Full</option>\
			<option value="large">Large</option>\
			<option value="large-alt">Large Alt</option>\
			<option value="medium">Medium</option>\
			<option value="grid">Grid</option>\
			<option value="timeline" selected="selected">Timeline</option>';

		return html;
	}

	function porto_shortcode_blog_grid_columns() {
		var html = '<option value="2">2</option>\
			<option value="3" selected="selected">3</option>\
			<option value="4">4</option>';

		return html;
	}

	function porto_shortcode_portfolio_layout() {
		var html = '<option value="grid">Grid</option>\
			<option value="timeline" selected="selected">Timeline</option>\
			<option value="medium">Medium</option>\
			<option value="large">Large</option>\
			<option value="full">Full</option>';

		return html;
	}

	function porto_shortcode_portfolio_grid_view() {
		var html = '<option value="">Classic</option>\
			<option value="full">Full</option>';

		return html;
	}

	function porto_shortcode_portfolio_grid_columns() {
		var html = '<option value="2">2</option>\
			<option value="3">3</option>\
			<option value="4">4</option>\
			<option value="5">5</option>\
			<option value="6">6</option>';

		return html;
	}

	function porto_shortcode_products_view() {
		var html = '<option value="grid">Grid</option>\
			<option value="list">List</option>\
			<option value="products-slider">Slider</option>';

		return html;
	}

	function porto_shortcode_products_grid_columns() {
		var html = '<option value="1">1</option>\
			<option value="2">2</option>\
			<option value="3">3</option>\
			<option value="4" selected="selected">4</option>\
			<option value="5">5</option>\
			<option value="6">6</option>\
			<option value="7">7 (without sidebar)</option>\
			<option value="8">8 (without sidebar)</option>';

		return html;
	}

	function porto_shortcode_products_grid_column_width() {
		var html = '<option value="">Default</option>\
			<option value="1">1/1 of content width</option>\
			<option value="2">1/2 of content width</option>\
			<option value="3">1/3 of content width</option>\
			<option value="4">1/4 of content width</option>\
			<option value="5">1/5 of content width</option>\
			<option value="6">1/6 of content width</option>\
			<option value="7">1/7 of content width</option>\
			<option value="8">1/8 of content width</option>';

		return html;
	}

	function porto_shortcode_products_orderby() {
		var html = '<option value=""></option>\
			<option value="date">Date</option>\
			<option value="ID">ID</option>\
			<option value="author">Author</option>\
			<option value="title">Title</option>\
			<option value="modified">Modified</option>\
			<option value="rand">Random</option>\
			<option value="comment_count">Comment count</option>\
			<option value="menu_order">Menu order</option>';

		return html;
	}

	function porto_shortcode_products_order() {
		var html = '<option value=""></option>\
			<option value="DESC">Descending</option>\
			<option value="ASC">Ascending</option>';

		return html;
	}

	function porto_shortcode_products_addlinks_pos() {
		var html = '<option value="">Default</option>\
			<option value="outimage">Out of Image</option>\
			<option value="onimage">On Image</option>';

		return html;
	}

	function porto_shortcode_product_view() {
		var html = '<option value="grid">Grid</option>\
			<option value="list">List</option>';

		return html;
	}

	function porto_shortcode_product_categories_view() {
		var html = '<option value="grid">Grid</option>\
			<option value="products-slider">Slider</option>';

		return html;
	}

	function porto_shortcode_widget_products_show() {
		var html = '<option value="">All Products</option>\
			<option value="featured">Featured Products</option>\
			<option value="onsale">On-sale Products</option>';

		return html;
	}

	function porto_shortcode_widget_products_orderby() {
		var html = '<option value="date">Date</option>\
			<option value="price">Price</option>\
			<option value="rand">Random</option>\
			<option value="sales">Sales</option>';

		return html;
	}

	function porto_shortcode_blockquote_view_type() {
		var html = '<option value="">Default</option>\
			<option value="with-borders">With Borders</option>';

		return html;
	}

	function porto_shortcode_blockquote_dir() {
		var html = '<option value="">Default</option>\
			<option value="blockquote-reverse">Reverse</option>';

		return html;
	}

	function porto_shortcode_skin_color() {
		var html = '<option value="custom"></option>\
			<option value="primary">Primary</option>\
			<option value="secondary">Secondary</option>\
			<option value="tertiary">Tertiary</option>\
			<option value="quaternary">Quaternary</option>\
			<option value="dark">Dark</option>\
			<option value="light">Light</option>';

		return html;
	}

	function porto_shortcode_position() {
		var html = '<option value="top">Top</option>\
			<option value="right">Right</option>\
			<option value="bottom">Bottom</option>\
			<option value="left">Left</option>';

		return html;
	}

	function porto_shortcode_display_type() {
		var html = '<option value="">Inline</option>\
			<option value="block">Block</option>';

		return html;
	}

	function porto_shortcode_tooltip_type() {
		var html = '<option value="">Link</option>\
			<option value="btn-link">Button Link</option>\
			<option value="btn">Button</option>';

		return html;
	}

	function porto_shortcode_popover_type() {
		var html = '<option value="">Link</option>\
			<option value="btn-link">Button Link</option>\
			<option value="btn">Button</option>';

		return html;
	}

	function porto_shortcode_size() {
		var html = '<option value="">Normal</option>\
			<option value="lg">Large</option>\
			<option value="sm">Small</option>\
			<option value="xs">Extra Small</option>';

		return html;
	}

	function porto_shortcode_colors() {
		var html = '<option value="custom"></option>\
			<option value="primary">Primary</option>\
			<option value="secondary">Secondary</option>\
			<option value="tertiary">Tertiary</option>\
			<option value="quaternary">Quaternary</option>\
			<option value="dark">Dark</option>\
			<option value="light">Light</option>';

		return html;
	}

	function porto_shortcode_contextual() {
		var html = '<option value="">None</option>\
			<option value="success">Success</option>\
			<option value="info">Info</option>\
			<option value="warning">Warning</option>\
			<option value="danger">Danger</option>';

		return html;
	}


	jQuery(function($) {

		var form = jQuery('<div id="porto_block-form"><table id="porto_block-table" class="form-table">\
				<tr>\
					<th colspan="2"><strong>Input Block id or slug name.</strong></th>\
				</tr>\
				<tr>\
					<th><label for="porto_block-id">Block ID *</label></th>\
					<td><input type="text" name="id" id="porto_block-id" value="" />\
					<br/><small>numerical value</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_block-name">Block Slug Name *</label></th>\
					<td><input type="text" name="name" id="porto_block-name" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_block-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_block-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_block-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_block-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_block-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_block-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_block-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_block-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_block-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_block-submit').click(function(){

			var options = {
				'id'                 : '',
				'name'               : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_block';

			for( var index in options) {
				var value = table.find('#porto_block-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			if (table.find('#porto_block-id').val() == '' && table.find('#porto_block-name').val() == '') {
				alert('Please input block id or slug name');
			} else {
				tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

				tb_remove();
			}
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_container-form"><table id="porto_container-table" class="form-table">\
				<tr>\
					<th><label for="porto_container-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_container-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_container-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_container-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_container-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_container-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_container-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_container-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_container-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_container-submit').click(function(){

			var options = {
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_container';

			for( var index in options) {
				var value = table.find('#porto_container-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']Insert Content[/porto_container]';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_animation-form"><table id="porto_animation-table" class="form-table">\
				<tr>\
					<th><label for="porto_animation-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_animation-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_animation-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_animation-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_animation-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_animation-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_animation-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_animation-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_animation-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_animation-submit').click(function(){

			var options = {
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_animation';

			for( var index in options) {
				var value = table.find('#porto_animation-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']Insert Content[/porto_animation]';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {
		var form = jQuery('<div id="porto_testimonial-form"><table id="porto_testimonial-table" class="form-table">\
				<tr>\
					<th><label for="porto_testimonial-name">Name</label></th>\
					<td><input type="text" name="name" id="porto_testimonial-name" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_testimonial-role">Role</label></th>\
					<td><input type="text" name="role" id="porto_testimonial-role" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_testimonial-company">Company</label></th>\
					<td><input type="text" name="company" id="porto_testimonial-company" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_testimonial-author_url">Author Link</label></th>\
					<td><input type="text" name="author_url" id="porto_testimonial-author_url" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_testimonial-photo_url">Photo URL</label></th>\
					<td><input type="text" name="photo_url" id="porto_testimonial-photo_url" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_testimonial-view">View Type</label></th>\
					<td><select name="view" id="porto_testimonial-view">\
					' + porto_shortcode_testimonial_view_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_testimonial-color">Color Skin (If View Type is Transparent)</label></th>\
					<td><select name="color" id="porto_testimonial-color">\
					' + porto_shortcode_testimonial_color_skin() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_testimonial-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_testimonial-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_testimonial-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_testimonial-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_testimonial-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_testimonial-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_testimonial-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_testimonial-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_testimonial-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_testimonial-submit').click(function(){

			var options = {
				'name'               : '',
				'role'               : '',
				'company'            : '',
				'author_url'         : '',
				'photo_url'          : '',
				'view'               : '',
				'color'              : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_testimonial';

			for( var index in options) {
				var value = table.find('#porto_testimonial-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']Insert Quote[/porto_testimonial]';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_content_box-form"><table id="porto_content_box-table" class="form-table">\
				<tr>\
					<th><label for="porto_content_box-border_top_color">Border Top Color</label></th>\
					<td><input type="text" name="border_top_color" id="porto_content_box-border_top_color" value="" />\
					<br/><small>default: skin color</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_content_box-border_radius">Border Radius</label></th>\
					<td><input type="text" name="border_radius" id="porto_content_box-border_radius" value="" />\
					<br/><small>numerical value (unit: px)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_content_box-border_top_width">Border Top Width</label></th>\
					<td><input type="text" name="border_top_width" id="porto_content_box-border_top_width" value="" />\
					<br/><small>numerical value (unit: px)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_content_box-bg_top_color">Background Gradient Top Color</label></th>\
					<td><input type="text" name="bg_top_color" id="porto_content_box-bg_top_color" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_content_box-bg_bottom_color">Background Gradient Bottom Color</label></th>\
					<td><input type="text" name="bg_bottom_color" id="porto_content_box-bg_bottom_color" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_content_box-align">Align</label></th>\
					<td><select name="align" id="porto_content_box-align">\
					' + porto_shortcode_align() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_content_box-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_content_box-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_content_box-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_content_box-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_content_box-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_content_box-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_content_box-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_content_box-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_content_box-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_content_box-submit').click(function(){

			var options = {
				'border_top_color'   : '',
				'border_radius'      : '',
				'border_top_width'   : '',
				'bg_top_color'       : '',
				'bg_bottom_color'    : '',
				'align'              : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_content_box';

			for( var index in options) {
				var value = table.find('#porto_content_box-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']Insert Content[/porto_content_box]';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_history-form"><table id="porto_history-table" class="form-table">\
				<tr>\
					<th><label for="porto_history-year">Year</label></th>\
					<td><input type="text" name="year" id="porto_history-year" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_history-image_url">Photo URL</label></th>\
					<td><input type="text" name="image_url" id="porto_history-image_url" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_history-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_history-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_history-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_history-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_history-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_history-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_history-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_history-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_history-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_history-submit').click(function(){

			var options = {
				'year'               : '',
				'image_url'          : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_history';

			for( var index in options) {
				var value = table.find('#porto_history-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']Insert History[/porto_history]';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_grid_container-form"><table id="porto_grid_container-table" class="form-table">\
				<tr>\
					<th><label for="porto_grid_container-gutter_size">Gutter Size</label></th>\
					<td><input type="text" name="gutter_size" id="porto_grid_container-gutter_size" value="2%" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_grid_container-grid_size">Grid Min Size</label></th>\
					<td><input type="text" name="grid_size" id="porto_grid_container-grid_size" value="0" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_grid_container-max_width">Max Width</label></th>\
					<td><input type="text" name="max_width" id="porto_grid_container-max_width" value="767px" />\
					<br/><small>Will be show as grid only when window width > max width.</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_animation-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_animation-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_grid_container-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_grid_container-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_grid_container-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_grid_container-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_grid_container-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_grid_container-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_grid_container-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_grid_container-submit').click(function(){

			var options = {
				'gutter_size'        : '2%',
				'grid_size'          : '0',
				'max_width'          : '767px',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_grid_container';

			for( var index in options) {
				var value = table.find('#porto_grid_container-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']Insert Porto Grid Item Shortcodes[/porto_grid_container]';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_grid_item-form"><table id="porto_grid_item-table" class="form-table">\
				<tr>\
					<th><label for="porto_grid_item-width">Width</label></th>\
					<td><input type="text" name="width" id="porto_grid_item-width" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_grid_item-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_grid_item-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_grid_item-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_grid_item-submit').click(function(){

			var options = {
				'width'              : '',
				'el_class'           : ''
			};

			var shortcode = '[porto_grid_item';

			for( var index in options) {
				var value = table.find('#porto_grid_item-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']Insert Content[/porto_grid_item]';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_links_block-form"><table id="porto_links_block-table" class="form-table">\
				<tr>\
					<th><label for="porto_links_block-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_links_block-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_links_block-show_icon">Show FontAwesome Icon</label></th>\
					<td><select name="show_icon" id="porto_links_block-show_icon">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_links_block-icon">Font Awesome Icon or Icon Class</label></th>\
					<td><input type="text" name="icon" id="porto_links_block-icon" value="" />\
					<br/><small>Input font awesome icon or icon class. You can see <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome Icons in here</a>.</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_links_block-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_links_block-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_links_block-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_links_block-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_links_block-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_links_block-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_links_block-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_links_block-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_links_block-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_links_block-submit').click(function(){

			var options = {
				'title'              : '',
				'show_icon'          : '',
				'icon'               : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_links_block';

			for( var index in options) {
				var value = table.find('#porto_links_block-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']Insert Porto Links Item Shortcodes[/porto_links_block]';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_links_item-form"><table id="porto_links_item-table" class="form-table">\
				<tr>\
					<th><label for="porto_links_item-label">Label</label></th>\
					<td><input type="text" name="label" id="porto_links_item-label" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_links_item-link">Link</label></th>\
					<td><input type="text" name="link" id="porto_links_item-link" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_links_item-show_icon">Show FontAwesome Icon</label></th>\
					<td><select name="show_icon" id="porto_links_item-show_icon">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_links_item-icon">Font Awesome Icon or Icon Class</label></th>\
					<td><input type="text" name="icon" id="porto_links_item-icon" value="" />\
					<br/><small>Input font awesome icon or icon class. You can see <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome Icons in here</a>.</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_links_item-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_links_item-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_links_item-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_links_item-submit').click(function(){

			var options = {
				'label'         : '',
				'link'          : '',
				'show_icon'     : '',
				'icon'          : '',
				'el_class'      : ''
			};

			var shortcode = '[porto_links_item';

			for( var index in options) {
				var value = table.find('#porto_links_item-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_recent_posts-form"><table id="porto_recent_posts-table" class="form-table">\
				<tr>\
					<th><label for="porto_recent_posts-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_recent_posts-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_posts-number">Posts Count</label></th>\
					<td><input type="text" name="number" id="porto_recent_posts-number" value="8" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_posts-cats">Category IDs</label></th>\
					<td><input type="text" name="cats" id="porto_recent_posts-cats" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_posts-show_image">Show Post Image</label></th>\
					<td><select name="show_image" id="porto_recent_posts-show_image">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_posts-items_desktop">Items to show on Desktop</label></th>\
					<td><input type="text" name="items_desktop" id="porto_recent_posts-items_desktop" value="4" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_posts-items_tablets">Items to show on Tablets</label></th>\
					<td><input type="text" name="items_tablets" id="porto_recent_posts-items_tablets" value="3" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_posts-items_mobile">Items to show on Mobile</label></th>\
					<td><input type="text" name="items_mobile" id="porto_recent_posts-items_mobile" value="2" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_posts-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_recent_posts-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_posts-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_recent_posts-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_posts-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_recent_posts-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_posts-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_recent_posts-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_recent_posts-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_recent_posts-submit').click(function(){

			var options = {
				'title'              : '',
				'number'             : '8',
				'cats'               : '',
				'show_image'         : 'true',
				'items_desktop'      : '4',
				'items_tablets'      : '3',
				'items_mobile'       : '2',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_recent_posts';

			for( var index in options) {
				var value = table.find('#porto_recent_posts-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_recent_portfolios-form"><table id="porto_recent_portfolios-table" class="form-table">\
				<tr>\
					<th><label for="porto_recent_portfolios-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_recent_portfolios-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_portfolios-number">Posts Count</label></th>\
					<td><input type="text" name="number" id="porto_recent_portfolios-number" value="8" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_portfolios-cats">Category IDs</label></th>\
					<td><input type="text" name="cats" id="porto_recent_portfolios-cats" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_portfolios-items_desktop">Items to show on Desktop</label></th>\
					<td><input type="text" name="items_desktop" id="porto_recent_portfolios-items_desktop" value="4" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_portfolios-items_tablets">Items to show on Tablets</label></th>\
					<td><input type="text" name="items_tablets" id="porto_recent_portfolios-items_tablets" value="3" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_portfolios-items_mobile">Items to show on Mobile</label></th>\
					<td><input type="text" name="items_mobile" id="porto_recent_portfolios-items_mobile" value="2" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_portfolios-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_recent_portfolios-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_portfolios-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_recent_portfolios-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_portfolios-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_recent_portfolios-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_portfolios-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_recent_portfolios-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_recent_portfolios-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_recent_portfolios-submit').click(function(){

			var options = {
				'title'              : '',
				'number'             : '8',
				'cats'               : '',
				'items_desktop'      : '4',
				'items_tablets'      : '3',
				'items_mobile'       : '2',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_recent_portfolios';

			for( var index in options) {
				var value = table.find('#porto_recent_portfolios-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_recent_members-form"><table id="porto_recent_members-table" class="form-table">\
				<tr>\
					<th><label for="porto_recent_members-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_recent_members-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_members-number">Posts Count</label></th>\
					<td><input type="text" name="number" id="porto_recent_members-number" value="8" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_members-cats">Category IDs</label></th>\
					<td><input type="text" name="cats" id="porto_recent_members-cats" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_members-items_desktop">Items to show on Desktop</label></th>\
					<td><input type="text" name="items_desktop" id="porto_recent_members-items_desktop" value="4" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_members-items_tablets">Items to show on Tablets</label></th>\
					<td><input type="text" name="items_tablets" id="porto_recent_members-items_tablets" value="3" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_members-items_mobile">Items to show on Mobile</label></th>\
					<td><input type="text" name="items_mobile" id="porto_recent_members-items_mobile" value="2" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_members-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_recent_members-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_members-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_recent_members-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_members-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_recent_members-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_members-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_recent_members-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_recent_members-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_recent_members-submit').click(function(){

			var options = {
				'title'              : '',
				'number'             : '8',
				'cats'               : '',
				'items_desktop'      : '4',
				'items_tablets'      : '3',
				'items_mobile'       : '2',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_recent_members';

			for( var index in options) {
				var value = table.find('#porto_recent_members-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_blog-form"><table id="porto_blog-table" class="form-table">\
				<tr>\
					<th><label for="porto_blog-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_blog-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blog-post_layout">Blog Layout</label></th>\
					<td><select name="post_layout" id="porto_blog-post_layout">\
					' + porto_shortcode_blog_layout() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blog-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_blog-columns">\
					' + porto_shortcode_blog_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blog-cats">Category IDs</label></th>\
					<td><input type="text" name="cats" id="porto_blog-cats" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blog-post_in">Post IDs</label></th>\
					<td><input type="text" name="post_in" id="porto_blog-post_in" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blog-number">Posts Count</label></th>\
					<td><input type="text" name="number" id="porto_blog-number" value="8" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blog-view_more">Show View More</label></th>\
					<td><select name="view_more" id="porto_blog-view_more">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blog-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_blog-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blog-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_blog-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blog-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_blog-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blog-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_blog-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_blog-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_blog-submit').click(function(){

			var options = {
				'title'              : '',
				'post_layout'        : '',
				'columns'            : '',
				'cats'               : '',
				'post_in'            : '',
				'number'             : '8',
				'view_more'          : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_blog';

			for( var index in options) {
				var value = table.find('#porto_blog-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_portfolios-form"><table id="porto_portfolios-table" class="form-table">\
				<tr>\
					<th><label for="porto_portfolios-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_portfolios-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-portfolio_layout">Portfolio Layout</label></th>\
					<td><select name="portfolio_layout" id="porto_portfolios-post_layout">\
					' + porto_shortcode_portfolio_layout() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-view">Grid View Type</label></th>\
					<td><select name="view" id="porto_portfolios-view">\
					' + porto_shortcode_portfolio_grid_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_portfolios-columns">\
					' + porto_shortcode_portfolio_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-cats">Category IDs</label></th>\
					<td><input type="text" name="cats" id="porto_portfolios-cats" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-post_in">Portfolio IDs</label></th>\
					<td><input type="text" name="post_in" id="porto_portfolios-post_in" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-number">Portfolios Count</label></th>\
					<td><input type="text" name="number" id="porto_portfolios-number" value="8" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-view_more">Show View More</label></th>\
					<td><select name="view_more" id="porto_portfolios-view_more">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_portfolios-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_portfolios-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_portfolios-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_portfolios-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_portfolios-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_portfolios-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_portfolios-submit').click(function(){

			var options = {
				'title'              : '',
				'portfolio_layout'   : '',
				'view'               : '',
				'columns'            : '',
				'cats'               : '',
				'post_in'            : '',
				'number'             : '8',
				'view_more'          : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_portfolios';

			for( var index in options) {
				var value = table.find('#porto_portfolios-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_faqs-form"><table id="porto_faqs-table" class="form-table">\
				<tr>\
					<th><label for="porto_faqs-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_faqs-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_faqs-cats">Category IDs</label></th>\
					<td><input type="text" name="cats" id="porto_faqs-cats" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_faqs-post_in">FAQ IDs</label></th>\
					<td><input type="text" name="post_in" id="porto_faqs-post_in" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_faqs-number">FAQs Count</label></th>\
					<td><input type="text" name="number" id="porto_faqs-number" value="8" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_faqs-view_more">Show View More</label></th>\
					<td><select name="view_more" id="porto_faqs-view_more">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_faqs-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_faqs-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_faqs-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_faqs-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_faqs-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_faqs-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_faqs-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_faqs-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_faqs-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_faqs-submit').click(function(){

			var options = {
				'title'              : '',
				'cats'               : '',
				'post_in'            : '',
				'number'             : '8',
				'view_more'          : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_faqs';

			for( var index in options) {
				var value = table.find('#porto_faqs-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_members-form"><table id="porto_members-table" class="form-table">\
				<tr>\
					<th><label for="porto_members-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_members-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_members-cats">Category IDs</label></th>\
					<td><input type="text" name="cats" id="porto_members-cats" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_members-post_in">Member IDs</label></th>\
					<td><input type="text" name="post_in" id="porto_members-post_in" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_members-number">Members Count</label></th>\
					<td><input type="text" name="number" id="porto_members-number" value="8" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_members-view_more">Show View More</label></th>\
					<td><select name="view_more" id="porto_members-view_more">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_members-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_members-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_members-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_members-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_members-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_members-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_members-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_members-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_members-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_members-submit').click(function(){

			var options = {
				'title'              : '',
				'cats'               : '',
				'post_in'            : '',
				'number'             : '8',
				'view_more'          : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_members';

			for( var index in options) {
				var value = table.find('#porto_members-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});

	jQuery(function($) {

		var form = jQuery('<div id="porto_concept-form"><table id="porto_concept-table" class="form-table">\
				<tr>\
					<th><label for="porto_concept-title1">Title 1</label></th>\
					<td><input type="text" name="title1" id="porto_concept-title1" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-link1">Link 1</label></th>\
					<td><input type="text" name="link1" id="porto_concept-link1" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-image1_url">Image URL 1</label></th>\
					<td><input type="text" name="image1_url" id="porto_concept-image1_url" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-title2">Title 2</label></th>\
					<td><input type="text" name="title2" id="porto_concept-title2" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-link2">Link 2</label></th>\
					<td><input type="text" name="link2" id="porto_concept-link2" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-image2_url">Image URL 2</label></th>\
					<td><input type="text" name="image2_url" id="porto_concept-image2_url" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-title3">Title 3</label></th>\
					<td><input type="text" name="title3" id="porto_concept-title3" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-link3">Link 3</label></th>\
					<td><input type="text" name="link3" id="porto_concept-link3" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-image3_url">Image URL 3</label></th>\
					<td><input type="text" name="image3_url" id="porto_concept-image3_url" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-title4">Slideshow Title</label></th>\
					<td><input type="text" name="title4" id="porto_concept-title4" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-slide_link1">Slide Link 1</label></th>\
					<td><input type="text" name="slide_link1" id="porto_concept-slide_link1" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-slide_image1_url">Slide Image URL 1</label></th>\
					<td><input type="text" name="slide_image1_url" id="porto_concept-slide_image1_url" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-slide_link2">Slide Link 2</label></th>\
					<td><input type="text" name="slide_link2" id="porto_concept-slide_link2" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-slide_image2_url">Slide Image URL 2</label></th>\
					<td><input type="text" name="slide_image2_url" id="porto_concept-slide_image2_url" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-slide_link3">Slide Link 3</label></th>\
					<td><input type="text" name="slide_link3" id="porto_concept-slide_link3" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-slide_image3_url">Slide Image URL 3</label></th>\
					<td><input type="text" name="slide_image3_url" id="porto_concept-slide_image3_url" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_concept-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_concept-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_concept-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_concept-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_concept-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_concept-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_concept-submit').click(function(){

			var options = {
				'title1'             : '',
				'link1'              : '',
				'image1_url'         : '',
				'title2'             : '',
				'link2'              : '',
				'image2_url'         : '',
				'title3'             : '',
				'link3'              : '',
				'image3_url'         : '',
				'title4'             : '',
				'slide_link1'        : '',
				'slide_image1_url'   : '',
				'slide_link2'        : '',
				'slide_image2_url'   : '',
				'slide_link3'        : '',
				'slide_image3_url'   : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_concept';

			for( var index in options) {
				var value = table.find('#porto_concept-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_map_section-form"><table id="porto_map_section-table" class="form-table">\
				<tr>\
					<th><label for="porto_map_section-container">Wrap as Container</label></th>\
					<td><select name="container" id="porto_map_section-container">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_map_section-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_map_section-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_map_section-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_map_section-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_map_section-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_map_section-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_map_section-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_map_section-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_map_section-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_map_section-submit').click(function(){

			var options = {
				'container'          : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_map_section';

			for( var index in options) {
				var value = table.find('#porto_map_section-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']Insert Content[/porto_map_section]';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_blockquote-form"><table id="porto_blockquote-table" class="form-table">\
				<tr>\
					<th><label for="porto_blockquote-footer_before">Footer Text 1</label></th>\
					<td><input type="text" name="footer_before" id="porto_blockquote-footer_before" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_blockquote-footer_after">Footer Text 2</label></th>\
					<td><input type="text" name="footer_after" id="porto_blockquote-footer_after" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_blockquote-view">View Type</label></th>\
					<td><select name="view" id="porto_blockquote-view">\
					' + porto_shortcode_blockquote_view_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blockquote-dir">Direction</label></th>\
					<td><select name="dir" id="porto_blockquote-dir">\
					' + porto_shortcode_blockquote_dir() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blockquote-skin">Skin Color</label></th>\
					<td><select name="skin" id="porto_blockquote-skin">\
					' + porto_shortcode_skin_color() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blockquote-color">Border Color</label></th>\
					<td><input type="text" name="color" id="porto_blockquote-color" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_blockquote-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_blockquote-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blockquote-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_blockquote-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blockquote-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_blockquote-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_blockquote-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_blockquote-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_blockquote-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_blockquote-submit').click(function(){

			var options = {
				'footer_before'      : '',
				'footer_after'       : '',
				'view'               : '',
				'dir'                : '',
				'skin'               : 'custom',
				'color'              : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_blockquote';

			for( var index in options) {
				var value = table.find('#porto_blockquote-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']Insert Content[/porto_blockquote]';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_tooltip-form"><table id="porto_tooltip-table" class="form-table">\
				<tr>\
					<th><label for="porto_tooltip-prefix">Prefix</label></th>\
					<td><input type="text" name="prefix" id="porto_tooltip-prefix" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-text">Text</label></th>\
					<td><input type="text" name="text" id="porto_tooltip-text" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-tooltip_text">Tooltip Text</label></th>\
					<td><input type="text" name="tooltip_text" id="porto_tooltip-tooltip_text" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-suffix">Suffix</label></th>\
					<td><input type="text" name="suffix" id="porto_tooltip-suffix" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-position">Position</label></th>\
					<td><select name="position" id="porto_tooltip-position">\
					' + porto_shortcode_position() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-display">Display Type</label></th>\
					<td><select name="display" id="porto_tooltip-display">\
					' + porto_shortcode_display_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-type">Text Type</label></th>\
					<td><select name="type" id="porto_tooltip-type">\
					' + porto_shortcode_tooltip_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-link">Link</label></th>\
					<td><input type="text" name="link" id="porto_tooltip-link" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-btn_size">Button Size</label></th>\
					<td><select name="btn_size" id="porto_tooltip-btn_size">\
					' + porto_shortcode_size() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-btn_skin">Button Skin Color</label></th>\
					<td><select name="btn_skin" id="porto_tooltip-btn_skin">\
					' + porto_shortcode_colors() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-btn_context">Button Contextual Class</label></th>\
					<td><select name="btn_context" id="porto_tooltip-btn_context">\
					' + porto_shortcode_contextual() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_tooltip-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_tooltip-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_tooltip-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_tooltip-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_tooltip-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_tooltip-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_tooltip-submit').click(function(){

			var options = {
				'prefix'             : '',
				'text'               : '',
				'text_before'        : '',
				'suffix'             : '',
				'position'           : '',
				'display'            : '',
				'type'               : '',
				'link'               : '',
				'btn_size'           : '',
				'btn_skin'           : 'custom',
				'btn_context'        : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_tooltip';

			for( var index in options) {
				var value = table.find('#porto_tooltip-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_popover-form"><table id="porto_popover-table" class="form-table">\
				<tr>\
					<th><label for="porto_popover-prefix">Prefix</label></th>\
					<td><input type="text" name="prefix" id="porto_popover-prefix" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_popover-text">Text</label></th>\
					<td><input type="text" name="text" id="porto_popover-text" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_popover-suffix">Suffix</label></th>\
					<td><input type="text" name="suffix" id="porto_popover-suffix" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_popover-display">Display Type</label></th>\
					<td><select name="display" id="porto_popover-display">\
					' + porto_shortcode_display_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_popover-type">Text Type</label></th>\
					<td><select name="type" id="porto_popover-type">\
					' + porto_shortcode_popover_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_popover-link">Link</label></th>\
					<td><input type="text" name="link" id="porto_popover-link" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_popover-btn_size">Button Size</label></th>\
					<td><select name="btn_size" id="porto_popover-btn_size">\
					' + porto_shortcode_size() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_popover-btn_skin">Button Skin Color</label></th>\
					<td><select name="btn_skin" id="porto_popover-btn_skin">\
					' + porto_shortcode_colors() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_popover-btn_context">Button Contextual Class</label></th>\
					<td><select name="btn_context" id="porto_popover-btn_context">\
					' + porto_shortcode_contextual() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_popover-popover_title">Popover Title</label></th>\
					<td><input type="text" name="popover_title" id="porto_popover-popover_title" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_popover-popover_text">Popover Text</label></th>\
					<td><input type="text" name="popover_text" id="porto_popover-popover_text" value="" />\
				</tr>\
				<tr>\
					<th><label for="porto_popover-popover_position">Popover Position</label></th>\
					<td><select name="popover_position" id="porto_popover-popover_position">\
					' + porto_shortcode_position() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_popover-popover_skin">Popover Skin Color</label></th>\
					<td><select name="popover_skin" id="porto_popover-popover_skin">\
					' + porto_shortcode_position() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_popover-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_popover-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_popover-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_popover-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_popover-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_popover-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_popover-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_popover-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_popover-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_popover-submit').click(function(){

			var options = {
				'prefix'             : '',
				'text'               : '',
				'suffix'             : '',
				'display'            : '',
				'type'               : '',
				'link'               : '',
				'btn_size'           : '',
				'btn_skin'           : 'custom',
				'btn_context'        : '',
				'popover_title'      : '',
				'popover_text'       : '',
				'popover_position'   : '',
				'popover_skin'       : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_popover';

			for( var index in options) {
				var value = table.find('#porto_popover-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_recent_products-form"><table id="porto_recent_products-table" class="form-table">\
				<tr>\
					<th><label for="porto_recent_products-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_recent_products-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-view">View Mode</label></th>\
					<td><select name="view" id="porto_recent_products-view">\
					' + porto_shortcode_products_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-per_page">Per Page</label></th>\
					<td><input type="text" name="per_page" id="porto_recent_products-per_page" value="12" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_recent_products-columns">\
					' + porto_shortcode_products_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-column_width">Grid Column Width</label></th>\
					<td><select name="column_width" id="porto_recent_products-column_width">\
					' + porto_shortcode_products_grid_column_width() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-orderby">Order by</label></th>\
					<td><select name="orderby" id="porto_recent_products-orderby">\
					' + porto_shortcode_products_orderby() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-order">Order way</label></th>\
					<td><select name="order" id="porto_recent_products-order">\
					' + porto_shortcode_products_order() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-addlinks_pos">Add Links Position</label></th>\
					<td><select name="addlinks_pos" id="porto_recent_products-addlinks_pos">\
					' + porto_shortcode_products_addlinks_pos() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-navigation">Show Navigation</label></th>\
					<td><select name="navigation" id="porto_recent_products-navigation">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-pagination">Show Pagination</label></th>\
					<td><select name="pagination" id="porto_recent_products-pagination">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_recent_products-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_recent_products-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_recent_products-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_recent_products-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_recent_products-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_recent_products-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_recent_products-submit').click(function(){

			var options = {
				'title'              : '',
				'view'               : '',
				'per_page'           : '12',
				'columns'            : '4',
				'column_width'       : '',
				'orderby'            : '',
				'order'              : '',
				'addlinks_pos'       : '',
				'navigation'         : 'true',
				'pagination'         : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_recent_products';

			for( var index in options) {
				var value = table.find('#porto_recent_products-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_featured_products-form"><table id="porto_featured_products-table" class="form-table">\
				<tr>\
					<th><label for="porto_featured_products-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_featured_products-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-view">View Mode</label></th>\
					<td><select name="view" id="porto_featured_products-view">\
					' + porto_shortcode_products_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-per_page">Per Page</label></th>\
					<td><input type="text" name="per_page" id="porto_featured_products-per_page" value="12" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_featured_products-columns">\
					' + porto_shortcode_products_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-column_width">Grid Column Width</label></th>\
					<td><select name="column_width" id="porto_featured_products-column_width">\
					' + porto_shortcode_products_grid_column_width() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-orderby">Order by</label></th>\
					<td><select name="orderby" id="porto_featured_products-orderby">\
					' + porto_shortcode_products_orderby() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-order">Order way</label></th>\
					<td><select name="order" id="porto_featured_products-order">\
					' + porto_shortcode_products_order() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-addlinks_pos">Add Links Position</label></th>\
					<td><select name="addlinks_pos" id="porto_featured_products-addlinks_pos">\
					' + porto_shortcode_products_addlinks_pos() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-navigation">Show Navigation</label></th>\
					<td><select name="navigation" id="porto_featured_products-navigation">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-pagination">Show Pagination</label></th>\
					<td><select name="pagination" id="porto_featured_products-pagination">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_featured_products-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_featured_products-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_featured_products-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_featured_products-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_featured_products-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_featured_products-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_featured_products-submit').click(function(){

			var options = {
				'title'              : '',
				'view'               : '',
				'per_page'           : '12',
				'columns'            : '4',
				'column_width'       : '',
				'orderby'            : '',
				'order'              : '',
				'addlinks_pos'       : '',
				'navigation'         : 'true',
				'pagination'         : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_featured_products';

			for( var index in options) {
				var value = table.find('#porto_featured_products-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_sale_products-form"><table id="porto_sale_products-table" class="form-table">\
				<tr>\
					<th><label for="porto_sale_products-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_sale_products-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-view">View Mode</label></th>\
					<td><select name="view" id="porto_sale_products-view">\
					' + porto_shortcode_products_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-per_page">Per Page</label></th>\
					<td><input type="text" name="per_page" id="porto_sale_products-per_page" value="12" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_sale_products-columns">\
					' + porto_shortcode_products_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-column_width">Grid Column Width</label></th>\
					<td><select name="column_width" id="porto_sale_products-column_width">\
					' + porto_shortcode_products_grid_column_width() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-orderby">Order by</label></th>\
					<td><select name="orderby" id="porto_sale_products-orderby">\
					' + porto_shortcode_products_orderby() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-order">Order way</label></th>\
					<td><select name="order" id="porto_sale_products-order">\
					' + porto_shortcode_products_order() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-addlinks_pos">Add Links Position</label></th>\
					<td><select name="addlinks_pos" id="porto_sale_products-addlinks_pos">\
					' + porto_shortcode_products_addlinks_pos() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-navigation">Show Navigation</label></th>\
					<td><select name="navigation" id="porto_sale_products-navigation">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-pagination">Show Pagination</label></th>\
					<td><select name="pagination" id="porto_sale_products-pagination">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_sale_products-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_sale_products-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_sale_products-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_sale_products-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_sale_products-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_sale_products-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_sale_products-submit').click(function(){

			var options = {
				'title'              : '',
				'view'               : '',
				'per_page'           : '12',
				'columns'            : '4',
				'column_width'       : '',
				'orderby'            : '',
				'order'              : '',
				'addlinks_pos'       : '',
				'navigation'         : 'true',
				'pagination'         : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_sale_products';

			for( var index in options) {
				var value = table.find('#porto_sale_products-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_best_selling_products-form"><table id="porto_best_selling_products-table" class="form-table">\
				<tr>\
					<th><label for="porto_best_selling_products-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_best_selling_products-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-view">View Mode</label></th>\
					<td><select name="view" id="porto_best_selling_products-view">\
					' + porto_shortcode_products_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-per_page">Per Page</label></th>\
					<td><input type="text" name="per_page" id="porto_best_selling_products-per_page" value="12" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_best_selling_products-columns">\
					' + porto_shortcode_products_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-column_width">Grid Column Width</label></th>\
					<td><select name="column_width" id="porto_best_selling_products-column_width">\
					' + porto_shortcode_products_grid_column_width() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-addlinks_pos">Add Links Position</label></th>\
					<td><select name="addlinks_pos" id="porto_best_selling_products-addlinks_pos">\
					' + porto_shortcode_products_addlinks_pos() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-navigation">Show Navigation</label></th>\
					<td><select name="navigation" id="porto_best_selling_products-navigation">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-pagination">Show Pagination</label></th>\
					<td><select name="pagination" id="porto_best_selling_products-pagination">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_best_selling_products-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_best_selling_products-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_best_selling_products-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_best_selling_products-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_best_selling_products-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_best_selling_products-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_best_selling_products-submit').click(function(){

			var options = {
				'title'              : '',
				'view'               : '',
				'per_page'           : '12',
				'columns'            : '4',
				'column_width'       : '',
				'addlinks_pos'       : '',
				'navigation'         : 'true',
				'pagination'         : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_best_selling_products';

			for( var index in options) {
				var value = table.find('#porto_best_selling_products-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_top_rated_products-form"><table id="porto_top_rated_products-table" class="form-table">\
				<tr>\
					<th><label for="porto_top_rated_products-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_top_rated_products-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-view">View Mode</label></th>\
					<td><select name="view" id="porto_top_rated_products-view">\
					' + porto_shortcode_products_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-per_page">Per Page</label></th>\
					<td><input type="text" name="per_page" id="porto_top_rated_products-per_page" value="12" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_top_rated_products-columns">\
					' + porto_shortcode_products_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-column_width">Grid Column Width</label></th>\
					<td><select name="column_width" id="porto_top_rated_products-column_width">\
					' + porto_shortcode_products_grid_column_width() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-orderby">Order by</label></th>\
					<td><select name="orderby" id="porto_top_rated_products-orderby">\
					' + porto_shortcode_products_orderby() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-order">Order way</label></th>\
					<td><select name="order" id="porto_top_rated_products-order">\
					' + porto_shortcode_products_order() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-addlinks_pos">Add Links Position</label></th>\
					<td><select name="addlinks_pos" id="porto_top_rated_products-addlinks_pos">\
					' + porto_shortcode_products_addlinks_pos() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-navigation">Show Navigation</label></th>\
					<td><select name="navigation" id="porto_top_rated_products-navigation">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-pagination">Show Pagination</label></th>\
					<td><select name="pagination" id="porto_top_rated_products-pagination">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_top_rated_products-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_top_rated_products-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_top_rated_products-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_top_rated_products-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_top_rated_products-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_top_rated_products-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_top_rated_products-submit').click(function(){

			var options = {
				'title'              : '',
				'view'               : '',
				'per_page'           : '12',
				'columns'            : '4',
				'column_width'       : '',
				'orderby'            : '',
				'order'              : '',
				'addlinks_pos'       : '',
				'navigation'         : 'true',
				'pagination'         : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_top_rated_products';

			for( var index in options) {
				var value = table.find('#porto_top_rated_products-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_products-form"><table id="porto_products-table" class="form-table">\
				<tr>\
					<th><label for="porto_products-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_products-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-view">View Mode</label></th>\
					<td><select name="view" id="porto_products-view">\
					' + porto_shortcode_products_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_products-columns">\
					' + porto_shortcode_products_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-column_width">Grid Column Width</label></th>\
					<td><select name="column_width" id="porto_products-column_width">\
					' + porto_shortcode_products_grid_column_width() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-orderby">Order by</label></th>\
					<td><select name="orderby" id="porto_products-orderby">\
					' + porto_shortcode_products_orderby() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-order">Order way</label></th>\
					<td><select name="order" id="porto_products-order">\
					' + porto_shortcode_products_order() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-ids">Product IDs</label></th>\
					<td><input type="text" name="ids" id="porto_products-ids" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-addlinks_pos">Add Links Position</label></th>\
					<td><select name="addlinks_pos" id="porto_products-addlinks_pos">\
					' + porto_shortcode_products_addlinks_pos() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-navigation">Show Navigation</label></th>\
					<td><select name="navigation" id="porto_products-navigation">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-pagination">Show Pagination</label></th>\
					<td><select name="pagination" id="porto_products-pagination">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_products-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_products-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_products-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_products-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_products-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_products-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_products-submit').click(function(){

			var options = {
				'title'              : '',
				'view'               : '',
				'columns'            : '4',
				'column_width'       : '',
				'orderby'            : '',
				'order'              : '',
				'ids'                : '',
				'addlinks_pos'       : '',
				'navigation'         : 'true',
				'pagination'         : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_products';

			for( var index in options) {
				var value = table.find('#porto_products-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_product_category-form"><table id="porto_product_category-table" class="form-table">\
				<tr>\
					<th><label for="porto_product_category-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_product_category-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-view">View Mode</label></th>\
					<td><select name="view" id="porto_product_category-view">\
					' + porto_shortcode_products_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-per_page">Per Page</label></th>\
					<td><input type="text" name="per_page" id="porto_product_category-per_page" value="12" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_product_category-columns">\
					' + porto_shortcode_products_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-column_width">Grid Column Width</label></th>\
					<td><select name="column_width" id="porto_product_category-column_width">\
					' + porto_shortcode_products_grid_column_width() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-orderby">Order by</label></th>\
					<td><select name="orderby" id="porto_product_category-orderby">\
					' + porto_shortcode_products_orderby() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-order">Order way</label></th>\
					<td><select name="order" id="porto_product_category-order">\
					' + porto_shortcode_products_order() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-category">Category</label></th>\
					<td><input type="text" name="category" id="porto_product_category-category" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-addlinks_pos">Add Links Position</label></th>\
					<td><select name="addlinks_pos" id="porto_product_category-addlinks_pos">\
					' + porto_shortcode_products_addlinks_pos() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-navigation">Show Navigation</label></th>\
					<td><select name="navigation" id="porto_product_category-navigation">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-pagination">Show Pagination</label></th>\
					<td><select name="pagination" id="porto_product_category-pagination">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_product_category-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_product_category-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_product_category-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_product_category-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_product_category-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_product_category-submit').click(function(){

			var options = {
				'title'              : '',
				'view'               : '',
				'per_page'           : '12',
				'columns'            : '4',
				'column_width'       : '',
				'orderby'            : '',
				'order'              : '',
				'category'           : '',
				'addlinks_pos'       : '',
				'navigation'         : 'true',
				'pagination'         : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_product_category';

			for( var index in options) {
				var value = table.find('#porto_product_category-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_product_attribute-form"><table id="porto_product_attribute-table" class="form-table">\
				<tr>\
					<th><label for="porto_product_attribute-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_product_attribute-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-view">View Mode</label></th>\
					<td><select name="view" id="porto_product_attribute-view">\
					' + porto_shortcode_products_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-per_page">Per Page</label></th>\
					<td><input type="text" name="per_page" id="porto_product_attribute-per_page" value="12" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_product_attribute-columns">\
					' + porto_shortcode_products_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-column_width">Grid Column Width</label></th>\
					<td><select name="column_width" id="porto_product_attribute-column_width">\
					' + porto_shortcode_products_grid_column_width() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-orderby">Order by</label></th>\
					<td><select name="orderby" id="porto_product_attribute-orderby">\
					' + porto_shortcode_products_orderby() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-order">Order way</label></th>\
					<td><select name="order" id="porto_product_attribute-order">\
					' + porto_shortcode_products_order() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-attribute">Attribute</label></th>\
					<td><input type="text" name="attribute" id="porto_product_attribute-attribute" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-filter">Filter</label></th>\
					<td><input type="text" name="filter" id="porto_product_attribute-filter" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-addlinks_pos">Add Links Position</label></th>\
					<td><select name="addlinks_pos" id="porto_product_attribute-addlinks_pos">\
					' + porto_shortcode_products_addlinks_pos() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-navigation">Show Navigation</label></th>\
					<td><select name="navigation" id="porto_product_attribute-navigation">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-pagination">Show Pagination</label></th>\
					<td><select name="pagination" id="porto_product_attribute-pagination">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_product_attribute-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_product_attribute-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_product_attribute-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_attribute-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_product_attribute-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_product_attribute-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_product_attribute-submit').click(function(){

			var options = {
				'title'              : '',
				'view'               : '',
				'per_page'           : '12',
				'columns'            : '4',
				'column_width'       : '',
				'orderby'            : '',
				'order'              : '',
				'attribute'          : '',
				'filter'             : '',
				'addlinks_pos'       : '',
				'navigation'         : 'true',
				'pagination'         : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_product_attribute';

			for( var index in options) {
				var value = table.find('#porto_product_attribute-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_product-form"><table id="porto_product-table" class="form-table">\
				<tr>\
					<th><label for="porto_product-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_product-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product-view">View Mode</label></th>\
					<td><select name="view" id="porto_product-view">\
					' + porto_shortcode_product_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product-column_width">Grid Width</label></th>\
					<td><select name="column_width" id="porto_product-column_width">\
					' + porto_shortcode_products_grid_column_width() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product-id">Product ID</label></th>\
					<td><input type="text" name="id" id="porto_product-id" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product-addlinks_pos">Add Links Position</label></th>\
					<td><select name="addlinks_pos" id="porto_product-addlinks_pos">\
					' + porto_shortcode_products_addlinks_pos() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_product-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_product-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_product-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_product-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_product-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_product-submit').click(function(){

			var options = {
				'title'              : '',
				'view'               : '',
				'column_width'       : '',
				'id'                 : '',
				'addlinks_pos'       : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_product';

			for( var index in options) {
				var value = table.find('#porto_product-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_product_categories-form"><table id="porto_product_categories-table" class="form-table">\
				<tr>\
					<th><label for="porto_product_categories-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_product_categories-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-view">View Mode</label></th>\
					<td><select name="view" id="porto_product_categories-view">\
					' + porto_shortcode_product_categories_view() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-number">Number</label></th>\
					<td><input type="text" name="number" id="porto_product_categories-number" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-columns">Grid Columns</label></th>\
					<td><select name="columns" id="porto_product_categories-columns">\
					' + porto_shortcode_products_grid_columns() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-column_width">Grid Column Width</label></th>\
					<td><select name="column_width" id="porto_product_categories-column_width">\
					' + porto_shortcode_products_grid_column_width() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-orderby">Order by</label></th>\
					<td><select name="orderby" id="porto_product_categories-orderby">\
					' + porto_shortcode_products_orderby() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-order">Order way</label></th>\
					<td><select name="order" id="porto_product_categories-order">\
					' + porto_shortcode_products_order() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-category">Category</label></th>\
					<td><input type="text" name="category" id="porto_product_categories-category" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-hide_empty">Number (Hide Empty)</label></th>\
					<td><input type="text" name="hide_empty" id="porto_product_categories-hide_empty" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-ids">Category IDs</label></th>\
					<td><input type="text" name="ids" id="porto_product_categories-ids" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-addlinks_pos">Add Links Position</label></th>\
					<td><select name="addlinks_pos" id="porto_product_category-addlinks_pos">\
					' + porto_shortcode_products_addlinks_pos() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_category-hide_count">Hide Products Count</label></th>\
					<td><select name="hide_count" id="porto_product_category-hide_count">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-navigation">Show Navigation</label></th>\
					<td><select name="navigation" id="porto_product_categories-navigation">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-pagination">Show Pagination</label></th>\
					<td><select name="pagination" id="porto_product_categories-pagination">\
					' + porto_shortcode_boolean_false() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_product_categories-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_product_categories-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_product_categories-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_product_categories-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_product_categories-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_product_categories-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_product_categories-submit').click(function(){

			var options = {
				'title'              : '',
				'view'               : '',
				'number'             : '',
				'columns'            : '4',
				'column_width'       : '',
				'orderby'            : '',
				'order'              : '',
				'hide_empty'         : '',
				'ids'                : '',
				'addlinks_pos'       : '',
				'hide_count'         : 'true',
				'navigation'         : 'true',
				'pagination'         : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_product_categories';

			for( var index in options) {
				var value = table.find('#porto_product_categories-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_widget_woo_products-form"><table id="porto_widget_woo_products-table" class="form-table">\
				<tr>\
					<th><label for="porto_widget_woo_products-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_widget_woo_products-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_products-number">Number of products</label></th>\
					<td><input type="text" name="number" id="porto_widget_woo_products-number" value="5" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_products-show">Show</label></th>\
					<td><select name="show" id="porto_widget_woo_products-show">\
					' + porto_shortcode_widget_products_show() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_products-orderby">Order by</label></th>\
					<td><select name="orderby" id="porto_widget_woo_products-orderby">\
					' + porto_shortcode_widget_products_orderby() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_products-order">Order way</label></th>\
					<td><select name="order" id="porto_widget_woo_products-order">\
					' + porto_shortcode_products_order() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_products-hide_free">Hide free products</label></th>\
					<td><select name="hide_free" id="porto_widget_woo_products-hide_free">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_products-show_hidden">Show hidden products</label></th>\
					<td><select name="show_hidden" id="porto_widget_woo_products-show_hidden">\
					' + porto_shortcode_boolean_true() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_products-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_widget_woo_products-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_products-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_widget_woo_products-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_products-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_widget_woo_products-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_products-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_widget_woo_products-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_widget_woo_products-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_widget_woo_products-submit').click(function(){

			var options = {
				'title'              : '',
				'number'             : '5',
				'show'               : '',
				'orderby'            : '',
				'order'              : '',
				'hide_free'          : 'true',
				'show_hidden'        : 'true',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_widget_woo_products';

			for( var index in options) {
				var value = table.find('#porto_widget_woo_products-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_widget_woo_top_rated_products-form"><table id="porto_widget_woo_top_rated_products-table" class="form-table">\
				<tr>\
					<th><label for="porto_widget_woo_top_rated_products-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_widget_woo_top_rated_products-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_top_rated_products-number">Number of products</label></th>\
					<td><input type="text" name="number" id="porto_widget_woo_top_rated_products-number" value="5" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_top_rated_products-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_widget_woo_top_rated_products-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_top_rated_products-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_widget_woo_top_rated_products-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_top_rated_products-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_widget_woo_top_rated_products-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_top_rated_products-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_widget_woo_top_rated_products-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_widget_woo_top_rated_products-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_widget_woo_top_rated_products-submit').click(function(){

			var options = {
				'title'              : '',
				'number'             : '5',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_widget_woo_top_rated_products';

			for( var index in options) {
				var value = table.find('#porto_widget_woo_top_rated_products-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_widget_woo_recently_viewed-form"><table id="porto_widget_woo_recently_viewed-table" class="form-table">\
				<tr>\
					<th><label for="porto_widget_woo_recently_viewed-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_widget_woo_recently_viewed-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_recently_viewed-number">Number of products</label></th>\
					<td><input type="text" name="number" id="porto_widget_woo_recently_viewed-number" value="5" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_recently_viewed-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_widget_woo_recently_viewed-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_recently_viewed-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_widget_woo_recently_viewed-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_recently_viewed-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_widget_woo_recently_viewed-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_recently_viewed-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_widget_woo_recently_viewed-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_widget_woo_recently_viewed-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_widget_woo_recently_viewed-submit').click(function(){

			var options = {
				'title'              : '',
				'number'             : '5',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_widget_woo_recently_viewed';

			for( var index in options) {
				var value = table.find('#porto_widget_woo_recently_viewed-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_widget_woo_recent_reviews-form"><table id="porto_widget_woo_recent_reviews-table" class="form-table">\
				<tr>\
					<th><label for="porto_widget_woo_recent_reviews-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_widget_woo_recent_reviews-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_recent_reviews-number">Number of products</label></th>\
					<td><input type="text" name="number" id="porto_widget_woo_recent_reviews-number" value="6" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_recent_reviews-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_widget_woo_recent_reviews-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_recent_reviews-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_widget_woo_recent_reviews-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_recent_reviews-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_widget_woo_recent_reviews-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_recent_reviews-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_widget_woo_recent_reviews-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_widget_woo_recent_reviews-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_widget_woo_recent_reviews-submit').click(function(){

			var options = {
				'title'              : '',
				'number'             : '6',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_widget_woo_recent_reviews';

			for( var index in options) {
				var value = table.find('#porto_widget_woo_recent_reviews-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});


	jQuery(function($) {

		var form = jQuery('<div id="porto_widget_woo_product_tags-form"><table id="porto_widget_woo_product_tags-table" class="form-table">\
				<tr>\
					<th><label for="porto_widget_woo_product_tags-title">Title</label></th>\
					<td><input type="text" name="title" id="porto_widget_woo_product_tags-title" value="" /></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_product_tags-animation_type">Animation Type</label></th>\
					<td><select name="animation_type" id="porto_widget_woo_product_tags-animation_type">\
					' + porto_shortcode_animation_type() + '\
					</select></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_product_tags-animation_duration">Animation Duration</label></th>\
					<td><input type="text" name="animation_duration" id="porto_widget_woo_product_tags-animation_duration" value="1000" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_product_tags-animation_delay">Animation Delay</label></th>\
					<td><input type="text" name="animation_delay" id="porto_widget_woo_product_tags-animation_delay" value="0" />\
					<br/><small>numerical value (unit: milliseconds)</small></td>\
				</tr>\
				<tr>\
					<th><label for="porto_widget_woo_product_tags-el_class">Extra Class Name</label></th>\
					<td><input type="text" name="el_class" id="porto_widget_woo_product_tags-el_class" value="" /></td>\
				</tr>\
			</table>\
			<p class="submit">\
				<input type="button" id="porto_widget_woo_product_tags-submit" class="button-primary" value="Insert Shortcode" name="submit" />\
			</p>\
			</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#porto_widget_woo_product_tags-submit').click(function(){

			var options = {
				'title'              : '',
				'animation_type'     : '',
				'animation_duration' : '1000',
				'animation_delay'    : '0',
				'el_class'           : ''
			};

			var shortcode = '[porto_widget_woo_product_tags';

			for( var index in options) {
				var value = table.find('#porto_widget_woo_product_tags-' + index).val();

				if ( value !== options[index] && (typeof value !== 'undefined'))
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});
})();