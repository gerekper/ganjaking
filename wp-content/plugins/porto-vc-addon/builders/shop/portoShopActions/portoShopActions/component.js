import React from 'react'
import { getService } from 'vc-cake'
const portoComponent = getService( 'portoComponent' )

export default class PortoShopActions extends portoComponent.shortcodeComponent {

	getAttr( attr ) {
		let res = '';
		for ( let i = 0; i < Object.keys( attr ).length; i++ ) {
			let key = Object.keys( attr )[ i ];
			let value = attr[ key ];
			if ( value === '' ) continue;
			if ( typeof value === 'boolean' && value === false ) value = '';
			res += key + '=';
			res += ( typeof value === 'number' || typeof value === 'boolean' ) ? ( value + ' ' ) : ( '"' + value + '" ' )
		}
		return res;
	}

	getShortcode( atts ) {
		const shortcodeAttr = {
			action: atts.hookActions ? atts.hookActions : ''
		}
		return '[porto_sb_actions ' + this.getAttr( shortcodeAttr ) + ']'
	}
	render() {
		const { id, atts, editor } = this.props
		let { el_class } = atts // destructuring assignment for attributes from settings.json with access publc
		let wrappr_cls = "vc-shop-hook"

		if ( typeof el_class === 'string' && el_class ) {
			wrappr_cls += ' ' + el_class
		}

		const doAll = this.applyDO( 'margin border padding animation' )

		return (
			<div className={ wrappr_cls } { ...editor } id={ 'el-' + id } { ...doAll }>
				<div className='vcvhelper' ref='vcvhelper' data-vcvs-html={ this.getShortcode( atts ) } >
					To change the products Archives's layout, go to Porto / Theme Options / WooCommerce / Product Archives.<br />The editor's preview might look different from the live site. Please check the frontend.
				</div>
			</div>
		)
	}
}
