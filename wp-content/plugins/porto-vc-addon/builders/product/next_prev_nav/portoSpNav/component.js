import React from 'react'
import { getService } from 'vc-cake'
const portoComponent = getService( 'portoComponent' )

export default class PortoSpNav extends portoComponent.shortcodeComponent {
	componentDidMount() {
		super.updateShortcodeToHtml( this.getShortcode(), this.refs.vcvhelper )
	}
	getShortcode() {
		return '[porto_single_product_next_prev_nav]'
	}
	render() {
		const { id, atts, editor } = this.props
		let { spAlign, el_class } = atts // destructuring assignment for attributes from settings.json with access publc
		let wrappr_cls = "vc-sp-nav"
		wrappr_cls = wrappr_cls.concat( ` single-nav-${ id }` )

		if ( spAlign ) {
			wrappr_cls += ' text-' + spAlign
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
