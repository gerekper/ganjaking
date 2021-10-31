import React from 'react'
import { getService } from 'vc-cake'
const portoComponent = getService( 'portoComponent' )

export default class PortoSpCart extends portoComponent.shortcodeComponent {
	componentDidMount() {
		super.updateShortcodeToHtml( this.getShortcode(), this.refs.vcvhelper )
	}
	getShortcode() {
		return '[porto_single_product_add_to_cart]'
	}
	render() {
		const { id, atts, editor } = this.props
		let { toggleCustomHover, el_class } = atts // destructuring assignment for attributes from settings.json with access publc
		let wrappr_cls = "vc-sp-cart"
		wrappr_cls = wrappr_cls.concat( ` single-cart-${ id }` )

		if ( toggleCustomHover ) {
			wrappr_cls += ' toggle-hover'
		}
		if ( typeof el_class === 'string' && el_class ) {
			wrappr_cls += ' ' + el_class
		}

		const doAll = this.applyDO( 'margin border padding animation' )

		return (
			<div className={ wrappr_cls } { ...editor } id={ 'el-' + id } { ...doAll }>
				<div className='vcvhelper' ref='vcvhelper' data-vcvs-html={ this.getShortcode() } />
			</div>
		)
	}
}
