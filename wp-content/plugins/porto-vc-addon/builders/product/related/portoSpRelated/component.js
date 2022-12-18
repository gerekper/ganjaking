import React from 'react'
import { getService } from 'vc-cake'
const portoComponent = getService( 'portoComponent' )

export default class PortoSpRelated extends portoComponent.shortcodeComponent {
	show_sort = null
	componentDidMount() {
		const atts = this.props.atts
		this.show_sort = Object.assign( [], atts.show_sort )
		super.updateShortcodeToHtml( this.getProductsShortcode( atts, this.show_sort ), this.refs.vcvhelper )
	}

	componentDidUpdate( prevProps ) {
		const atts = this.props.atts,
			shortcode = this.getProductsShortcode( atts, atts.show_sort )
		if ( shortcode !== this.getProductsShortcode( prevProps.atts, this.show_sort ) ) {
			super.updateShortcodeToHtml( shortcode, this.refs.vcvhelper )
			this.show_sort = Object.assign( [], atts.show_sort )
		}
	}

	shouldComponentUpdate() {
		return true
	}

	getProductsShortcode( options, show_sort_val ) {
		if ( !show_sort_val ) {
			show_sort_val = Object.assign( [], options.show_sort )
		}
		let shortcode = `[porto_products ${ typeof portoRelated != undefined ? 'ids="' + portoRelated.join( ',' ) + '"' : '' }`
		jQuery.each( options, function ( key, val ) {
			if ( 'show_sort' == key ) {
				if ( show_sort_val.length ) {
					shortcode += ` show_sort="${ show_sort_val }"`
				}
			} else if ( 'order1' == key ) {
				shortcode += ` order="${ val }"`
			} else if ( typeof val === 'boolean' ) {
				if ( 'navigation' === key && !val ) {
					shortcode += ` ${ key }="0"`
				} else if ( val ) {
					shortcode += ` ${ key }="1"`
				}
			} else if ( val ) {
				shortcode += ` ${ key }="${ val }"`
			}
			if ( 'autoplay_timeout' === key ) {
				return false
			}
		} )
		shortcode += `]`
		return shortcode
	}

	render() {
		const { id, atts, editor } = this.props
		let { el_class } = atts // destructuring assignment for attributes from settings.json with access publc
		let wrappr_cls = "vc-sp-related"
		wrappr_cls = wrappr_cls.concat( ` single-related-${ id }` )

		if ( typeof el_class === 'string' && el_class ) {
			wrappr_cls += ' ' + el_class
		}

		const doAll = this.applyDO( 'margin border padding animation' )

		return (
			<div className={ wrappr_cls } { ...editor } id={ 'el-' + id } { ...doAll }>
				<div className='vcvhelper' ref='vcvhelper' data-vcvs-html={ this.getProductsShortcode( atts, this.show_sort ) } />
			</div>
		)
	}
}
