jQuery( function( $ ) {
	var warranty_form = $( 'ul#warranty_form' );

	if ( 1 === warranty_form.length && 1 === $( 'ul#warranty_form_fields' ).length ) {

		var form = Warranty_Form_Builder;
		form.init( $( '#warranty_form' ) );

		warranty_form.sortable( {
			cursor: 'move',
			tolerance: 'pointer',
			update: function( evt, ui ) {
				form.record_fields();
			},
		} );

		$( 'a.control' ).click( function( e ) {
			e.preventDefault();

			var type = $( this ).data( 'type' );
			var options = $( this ).data( 'options' );

			var key = form.render_field( type, options );

			$( 'html,body' ).animate( { scrollTop: $( '#wfb-field-' + key ).offset().top - 100 } );

		} );

		// always start with blocks hidden
		jQuery( '#warranty_form a.toggle-field' ).click();

	}
} );

var Warranty_Form_Builder = {
	types: {
		paragraph: 'Paragraph',
		text: 'Text Field',
		textarea: 'Multi-line Text Field',
		select: 'Drop Down',
		file: 'File Upload Field',
	},
	used_keys: [],
	container: null,
	input_field: '#form_fields',
	placeholder: 'Click on a field to add to the form',
	init: function( container, placeholder_message ) {
		this.set_container( container );

		if ( placeholder_message ) {
			this.placeholder = placeholder_message;
		}

		this.maybe_show_placeholder();
		this.set_handlers();
	},
	set_container: function( el ) {
		this.container = el;
	},
	set_handlers: function() {
		var builder = this;
		jQuery( '.wfb-remove' ).on( 'click', function( e ) {
			e.preventDefault();

			jQuery( this ).parents( 'li' ).eq( 0 ).remove();
			builder.maybe_show_placeholder();
			builder.record_fields();
		} );

		jQuery( '.wfb-toggle' ).on( 'click', function( e ) {
			e.preventDefault();

			var that = this;
			var key = jQuery( this ).data( 'key' );
			var content = jQuery( '#wfb-content-' + key );

			if ( content.is( ':visible' ) ) {
				content.slideUp( function() {
					jQuery( that ).html( '&#9662;' );
				} );
			} else {
				content.slideDown( function() {
					jQuery( that ).html( '&#9652;' );
				} );
			}
		} );
	},
	maybe_show_placeholder: function() {
		if ( 0 === jQuery( this.container ).find( 'li' ).length ) {
			jQuery( this.container )
				.append( '<li class=\'placeholder\'>' + this.placeholder + '</li>' );
		}
	},
	remove_placeholder: function() {
		jQuery( this.container ).find( 'li.placeholder' ).remove();
	},
	generate_key: function() {
		min = 100;
		max = 2147483647;

		do {
			key = Math.floor( Math.random() * ( max - min + 1 ) ) + min;
		} while ( this.used_keys.indexOf( key ) > - 1 );

		this.used_keys.push( key );

		return key;
	},
	add_paragraph: function() {
		this.remove_placeholder();
		jQuery( this.container )
			.append( '<li>Paragraph Field<a class="remove-field wfb-remove" href="#">&times;</a></li>' );
	},
	add_text: function() {
		this.remove_placeholder();
		jQuery( this.container )
			.append( '<li>Text Field<a class="remove-field wfb-remove" href="#">&times;</a></li>' );
	},
	add_textarea: function() {
		this.remove_placeholder();
		jQuery( this.container )
			.append( '<li>TextArea Field<a class="remove-field wfb-remove" href="#">&times;</a></li>' );
	},
	add_select: function() {
		this.remove_placeholder();
		jQuery( this.container )
			.append( '<li>Select Field<a class="remove-field wfb-remove" href="#">&times;</a></li>' );
	},
	add_file: function() {
		this.remove_placeholder();
		jQuery( this.container )
			.append( '<li>File Field<a class="remove-field wfb-remove" href="#">&times;</a></li>' );
	},
	render_field: function( type, options ) {
		var key = this.generate_key();
		this.remove_placeholder();

		var src = ' <div class="wfb-field wfb-field-' + type + '" data-key="' + key + '" id="wfb-field-' + key + '">\
                    <div class="wfb-field-title">\
                        <h3>' + this.types[type] + '</h3>\
                        <div class="wfb-field-controls">\
                            <a class="toggle-field wfb-toggle" data-key="' + key + '" href="#">&#9652;</a>\
                            <a class="remove-field wfb-remove" href="#">&times;</a>\
                        </div>\
                    </div>\
                    <div class="wfb-content" id="wfb-content-' + key + '">\
                        <div class="wfb-field-content">\
                            <table class="form-table">';
		options = options.split( '|' );

		for ( x in options ) {
			src += this.render_option_row( options[x], key );
		}

		src += '</table></div></div>';

		jQuery( this.container )
			.append( '<li class=\'wfb-field\' data-key=\'' + key + '\' data-type=\'' + type + '\'>' + src + '</li>' );

		// record this field
		this.record_fields();

		// Tooltips
		jQuery( '.tips, .help_tip' ).tipTip( {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200,
		} );

		return key;
	},
	render_option_row: function( option, key ) {
		var src = '<tr>';

		switch(option) {
			case 'name':
				src += '<th>Name <img class="help_tip" data-tip="' + WFB.tips[option] + '" src="' + WFB.help_img_url + '" height="16" width="16" /></th><td><input type="text" name="fb_field[' + key + '][name]" /></td>';
				break;
			case 'label':
				src += '<th>Label <img class="help_tip" data-tip="' + WFB.tips[option] + '" src="' + WFB.help_img_url + '" height="16" width="16" /></th><td><input type="text" name="fb_field[' + key + '][label]" /></td>';
				break;
			case 'text':
				src += '<th>Text</th><td><textarea name="fb_field[' + key + '][text]" rows="5" cols="40"></textarea></td>';
				break;
			case 'default':
				src += '<th>Default Value <img class="help_tip" data-tip="' + WFB.tips[option] + '" src="' + WFB.help_img_url + '" height="16" width="16" /></th><td><input type="text" name="fb_field[' + key + '][default]" /></td>';
				break;
			case 'rowscols':
				src += '<th>Size</th><td><input type="text" size="2" name="fb_field[' + key + '][rows]"><span class="description">Rows</span> <input type="text" size="2" name="fb_field[' + key + '][cols]"><span class="description">Columns</span>';
				break;
			case 'options':
				src += '<th>Options <img class="help_tip" data-tip="' + WFB.tips[option] + '" src="' + WFB.help_img_url + '" height="16" width="16" /></th><td><textarea name="fb_field[' + key + '][options]" rows="3" cols="40"></textarea></td>';
				break;
			case 'multiple':
				src += '<th>Allow Multiple <img class="help_tip" data-tip="' + WFB.tips[option] + '" src="' + WFB.help_img_url + '" height="16" width="16" /></th><td><input type="checkbox" name="fb_field[' + key + '][multiple]" value="yes" /></td>';
				break;
			case 'required':
				src += '<th>Required <img class="help_tip" data-tip="' + WFB.tips[option] + '" src="' + WFB.help_img_url + '" height="16" width="16" /></th><td><input type="checkbox" name="fb_field[' + key + '][required]" value="yes" /></td>';
				break;
		}

		src += '</tr>';

		return src;
	},
	record_fields: function() {
		var fields = [];
		jQuery( 'li.wfb-field' ).each( function() {
			fields.push( {
				key: jQuery( this ).data( 'key' ), type: jQuery( this ).data( 'type' ),
			} );
		} );
		jQuery( this.input_field ).val( JSON.stringify( fields ) );
	},
};
