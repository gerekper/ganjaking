const PortoDynamicContentControl = function( {
	label,
	value,
	options,
	onChange
} ) {
	const __ = wp.i18n.__,
		TextControl = wp.components.TextControl,
		SelectControl = wp.components.SelectControl,
		useState = wp.element.useState,
		useEffect = wp.element.useEffect,
		useMemo = wp.element.useMemo,
		el = wp.element.createElement;

	if ( !value ) {
		value = {};
	}
	if ( !options.field_type ) {
		options.field_type = 'field';
	}

	let acf_fields = [{ label: __( 'Please select...', 'porto-functionality' ), value: '' }];
	if ( porto_block_vars.acf && porto_block_vars.acf[options.field_type] ) {
		porto_block_vars.acf[options.field_type].forEach( function( field_arr, index ) {
			_.forEach( field_arr.options, function( label, key ) {
				acf_fields.push( { label: field_arr.label + ' - ' + label, value: key } );
			} );
		} );
	}
	const [acfFields, setAcfFields] = useState( acf_fields );

	useMemo(
		() => {
			acf_fields = [{ label: __( 'Please select...', 'porto-functionality' ), value: '' }];
			if ( porto_block_vars.acf && porto_block_vars.acf[options.field_type] ) {
				porto_block_vars.acf[options.field_type].forEach( function( field_arr, index ) {
					_.forEach( field_arr.options, function( label, key ) {
						acf_fields.push( { label: field_arr.label + ' - ' + label, value: key } );
					} );
				} );
			}
			if ( acfFields !== acf_fields ) {
				setAcfFields( acf_fields );
			}
		},
		[porto_block_vars.acf]
	);

	let metabox_fields = [{ label: __( 'Please select...', 'porto-functionality' ), value: '' }];
	if ( porto_block_vars.meta_fields ) {
		_.forEach( porto_block_vars.meta_fields, function( value, key ) {
			if ( 'global' === key || key === options.content_type || key === options.content_type_value ) {
				_.forEach( value, function( label_type, title ) {
					if ( 'image' == options.field_type ) {
						if ( 'upload' == label_type[1] || 'attach' == label_type[1] ) {
							metabox_fields.push( { label: label_type[0], value: title } );
						}
					} else {
						if ( 'upload' != label_type[1] && 'attach' != label_type[1] ) {
							metabox_fields.push( { label: label_type[0], value: title } );
						}
					}
				} );
			}
		} );
	}

	let post_info_fields = [], tax_fields = [], woo_fields = [];

	if ( 'image' === options.field_type ) {
		post_info_fields = [{ label: __( 'Please select...', 'porto-functionality' ), value: '' }, { label: __( 'Featured Image', 'porto-functionality' ), value: 'thumbnail' }, { label: __( 'Author Picture on Gravatar', 'porto-functionality' ), value: 'author_img' }];
		tax_fields = [];
	} else if ( 'link' === options.field_type ) {
		post_info_fields = [{ label: __( 'Please select...', 'porto-functionality' ), value: '' }, { label: __( 'Permalink', 'porto-functionality' ), value: 'permalink' }, { label: __( 'Author Posts Url', 'porto-functionality' ), value: 'author_posts_url' }, { label: __( 'Featured Image Url', 'porto-functionality' ), value: 'thumbnail' }];
		tax_fields = [{ label: __( 'Please select...', 'porto-functionality' ), value: '' }, { label: __( 'Term Link', 'porto-functionality' ), value: 'term_link' }];
	} else if ( 'field' === options.field_type ) {
		post_info_fields = [{ label: __( 'Please select...', 'porto-functionality' ), value: '' }, { label: __( 'ID', 'porto-functionality' ), value: 'id' }, { label: __( 'Title', 'porto-functionality' ), value: 'title' }, { label: __( 'Content', 'porto-functionality' ), value: 'content' }, { label: __( 'Excerpt', 'porto-functionality' ), value: 'excerpt' }, { label: __( 'Date', 'porto-functionality' ), value: 'date' }, { label: __( 'Post Status', 'porto-functionality' ), value: 'status' }, { label: __( 'Like Count', 'porto-functionality' ), value: 'like_count' }];
		tax_fields = [{ label: __( 'Please select...', 'porto-functionality' ), value: '' }, { label: __( 'ID', 'porto-functionality' ), value: 'id' }, { label: __( 'Title', 'porto-functionality' ), value: 'title' }, { label: __( 'Description', 'porto-functionality' ), value: 'desc' }, { label: __( 'Post Count', 'porto-functionality' ), value: 'count' }];
		woo_fields = [{ label: __( 'Please select...', 'porto-functionality' ), value: '' }, { label: __( 'Sale End Date', 'porto-functionality' ), value: 'sale_date' }];
	}

	let source_fields = [{ label: __( 'Please select...', 'porto-functionality' ), value: '' }, { label: __( 'Page or Post Info', 'porto-functionality' ), value: 'post' }, { label: __( 'Porto Meta Box Field', 'porto-functionality' ), value: 'metabox' }, { label: __( 'Advanced Custom Field', 'porto-functionality' ), value: 'acf' }, { label: __( 'Meta Field', 'porto-functionality' ), value: 'meta' }, { label: __( 'Taxonomy', 'porto-functionality' ), value: 'tax' },];
	if ( porto_block_vars.woo_exist && ( ( 'product' == options.content_type ) || !options.content_type ) ) {
		source_fields.push( { label: __( 'WooCommerce', 'porto-functionality' ), value: 'woo' } );
	}
	return el(
		'div',
		{ className: 'porto-dynamic-content-control porto-typography-control' },
		el(
			'h3',
			{ className: 'components-base-control', style: { marginBottom: 15 } },
			label
		),
		el( SelectControl, {
			label: __( 'Source', 'porto-functionality' ),
			help: __( 'Page or Post Info is used in posts list and Taxonomy is used in terms list.', 'porto-functionality' ),
			value: value.source,
			options: source_fields,
			onChange: ( val ) => { value.source = val; onChange( value ); },
		} ),
		'post' == value.source && el( SelectControl, {
			label: __( 'Page or Post Info', 'porto-functionality' ),
			value: value.post_info,
			options: post_info_fields,
			onChange: ( val ) => { value.post_info = val; onChange( value ); },
		} ),
		'metabox' == value.source && el( SelectControl, {
			label: __( 'Porto Meta Box Field', 'porto-functionality' ),
			value: value.metabox,
			options: metabox_fields,
			onChange: ( val ) => { value.metabox = val; onChange( value ); },
		} ),
		'acf' == value.source && el( SelectControl, {
			label: __( 'Advanced Custom Field', 'porto-functionality' ),
			value: value.acf,
			options: acfFields,
			onChange: ( val ) => { value.acf = val; onChange( value ); },
		} ),
		'meta' == value.source && el( TextControl, {
			label: __( 'Custom Meta key', 'porto-functionality' ),
			value: value.meta,
			onChange: ( val ) => { value.meta = val; onChange( value ); },
		} ),
		'tax' == value.source && el( SelectControl, {
			label: __( 'Taxonomy Field', 'porto-functionality' ),
			value: value.tax,
			options: tax_fields,
			onChange: ( val ) => { value.tax = val; onChange( value ); },
		} ),
		'woo' == value.source && el( SelectControl, {
			label: __( 'WooCommerce Field', 'porto-functionality' ),
			value: value.woo,
			options: woo_fields,
			onChange: ( val ) => { value.woo = val; onChange( value ); },
		} ),
		'field' === options.field_type && el( TextControl, {
			label: __( 'Before Text', 'porto-functionality' ),
			value: value.before,
			onChange: ( val ) => { value.before = val; onChange( value ); },
		} ),
		'field' === options.field_type && el( TextControl, {
			label: __( 'After Text', 'porto-functionality' ),
			value: value.after,
			onChange: ( val ) => { value.after = val; onChange( value ); },
		} ),
		'image' !== options.field_type && el( TextControl, {
			label: __( 'Fallback', 'porto-functionality' ),
			value: value.fallback,
			onChange: ( val ) => { value.fallback = val; onChange( value ); },
		} ),
	);
};

export default PortoDynamicContentControl;