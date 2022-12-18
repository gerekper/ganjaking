import React from 'react'
import { getService } from 'vc-cake'
const portoComponent = getService( 'portoComponent' )

export default class PortoShopSort extends portoComponent.shortcodeComponent {

	getShortcode() {
		return '[porto_sb_sort]'
	}
	render() {
		const { id, atts, editor } = this.props
		let { sbAlign, el_class } = atts // destructuring assignment for attributes from settings.json with access publc
		let wrappr_cls = "vc-shop-sort"
		wrappr_cls = wrappr_cls.concat( ` shop-sort-${ id }` )

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
					<label>Sort By: </label>
					<select name="orderby" class="orderby" aria-label="Shop order">
						<option value="menu_order">Default sorting</option>
						<option value="popularity">Sort by popularity</option>
						<option value="rating" selected="selected">Sort by average rating</option>
						<option value="date">Sort by latest</option>
						<option value="price">Sort by price: low to high</option>
						<option value="price-desc">Sort by price: high to low</option>
					</select>
					<input type="hidden" name="paged" value="1"></input>
					<br />To change the products Archives's layout, go to Porto / Theme Options / WooCommerce / Product Archives.<br />The editor's preview might look different from the live site. Please check the frontend.
				</div>
			</div>
		)
	}
}
