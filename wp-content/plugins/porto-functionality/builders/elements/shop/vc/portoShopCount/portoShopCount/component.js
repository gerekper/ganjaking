import React from 'react'
import { getService } from 'vc-cake'
const portoComponent = getService( 'portoComponent' )

export default class PortoShopCount extends portoComponent.shortcodeComponent {

	getShortcode() {
		return '[porto_sb_count]'
	}
	render() {
		const { id, atts, editor } = this.props
		let { sbAlign, el_class } = atts // destructuring assignment for attributes from settings.json with access publc
		let wrappr_cls = "vc-shop-count"
		wrappr_cls = wrappr_cls.concat( ` shop-count-${ id }` )

		if ( sbAlign ) {
			wrappr_cls = wrappr_cls.concat( ` text-${ sbAlign }` )
		}

		if ( typeof el_class === 'string' && el_class ) {
			wrappr_cls += ' ' + el_class
		}

		const doAll = this.applyDO( 'margin border padding animation' )

		return (
			<div className={ wrappr_cls } { ...editor } id={ 'el-' + id } { ...doAll }>
				<div className='vcvhelper' ref='vcvhelper' data-vcvs-html={ this.getShortcode() } >
					<label>Show: </label>
					<select name="count" className="count">
						<option value="12">12</option>
						<option value="24" selected="selected">24</option>
						<option value="36">36</option>
					</select>
					<input type="hidden" name="paged" value="" />
					<br />To change the products Archives's layout, go to Porto / Theme Options / WooCommerce / Product Archives.<br />The editor's preview might look different from the live site. Please check the frontend.
				</div>
			</div>
		)
	}
}
