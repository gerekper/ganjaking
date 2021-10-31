import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService( 'portoComponent' )

export default class PortoHeaderCompare extends portoComponent.shortcodeComponent {
	constructor( props ) {
		super( props )
	}
	render() {
		const { id, editor, atts } = this.props
		const doAll = this.applyDO( 'all' )
		const { el_class } = atts

		return (
			<div className={ 'vce-porto-hb-compare porto-hb-compare' + ( el_class ? ' ' + el_class : '' ) } { ...editor } id={ 'el-' + id } { ...doAll }>
				{ typeof portoCompareCount != 'undefined' ? ( <a href="#" title="Compare" className="yith-woocompare-open">
					<i className={ atts.icon_cl != '' ? atts.icon_cl : "porto-icon-compare-link" }></i>
					<span className="compare-count">{ portoCompareCount ? portoCompareCount : 0 }</span>
				</a> ) : 'Please install YITH WooCompare' }
			</div>
		)
	}
}
