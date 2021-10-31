import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService( 'portoComponent' )

export default class PortoSpHeading extends portoComponent.shortcodeComponent {

	componentDidMount() {
		super.updateShortcodeToHtml( `[porto_single_product_title]`, this.ref )
	}

	render() {
		const { id, editor, atts } = this.props
		const doAll = this.applyDO( 'margin border padding animation' )

		let wrapper_cls = 'vc-sp-heading', style_inline = {}

		if ( atts.text_transform ) {
			style_inline.textTransform = atts.text_transform
		}
		if ( atts.use_theme_fonts && atts.title_font && atts.title_font.fontFamily ) {
			style_inline.fontFamily = atts.title_font.fontFamily
			if ( atts.title_font.fontStyle ) {
				if ( 'italic' === atts.title_font.fontStyle.style ) {
					style_inline.fontStyle = 'italic'
				}
				if ( atts.title_font.fontStyle.weight ) {
					style_inline.fontWeight = atts.title_font.fontStyle.weight
				}
			}
		}
		if ( atts.font_size ) {
			let unit = atts.font_size.replace( /[0-9.]/g, '' )
			if ( !unit ) {
				atts.font_size += 'px'
			}
			style_inline.fontSize = atts.font_size
		}
		if ( atts.font_weight ) {
			style_inline.fontWeight = Number( atts.font_weight )
		}
		if ( atts.line_height ) {
			let unit = atts.line_height.replace( /[0-9.]/g, '' )
			if ( !unit && atts.line_height > 3 ) {
				atts.line_height += 'px'
			}
			style_inline.lineHeight = atts.line_height
		}
		if ( atts.letter_spacing ) {
			let unit = atts.letter_spacing.replace( /[0-9.]/g, '' )
			if ( !unit ) {
				atts.letter_spacing += 'px'
			}
			style_inline.letterSpacing = atts.letter_spacing
		}
		if ( atts.color ) {
			style_inline.color = atts.color
		}


		if ( atts.alignment ) {
			wrapper_cls += ` text-${ atts.alignment }`
		}
		if ( atts.el_class ) {
			wrapper_cls += ` ${ atts.el_class }`
		}

		return (
			<div className={ wrapper_cls } style={ style_inline } id={ 'el-' + id } { ...doAll } { ...editor }>
				<div ref={ ( ref ) => { this.ref = ref } } className='vcvhelper' data-vcvs-html='[porto_single_product_title]' />
			</div>
		)
	}
}
