import React from 'react'
import { getService } from 'vc-cake'
const portoComponent = getService( 'portoComponent' )

export default class PortoSpTab extends portoComponent.shortcodeComponent {
	componentDidMount() {
		super.updateShortcodeToHtml( this.getShortcode( this.props.atts ), this.refs.vcvhelper )
	}

	componentDidUpdate( prevProps ) {
		if ( this.getShortcode( this.props.atts ) !== this.getShortcode( prevProps.atts ) ) {
			super.updateShortcodeToHtml( this.getShortcode( this.props.atts ), this.refs.vcvhelper )
		}
	}

	shouldComponentUpdate() {
		return true
	}
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
			style: atts.tabType ? atts.tabType : ''
		}
		return '[porto_single_product_tabs ' + this.getAttr( shortcodeAttr ) + ']'
	}

	render() {
		const { id, atts, editor } = this.props
		let { el_class } = atts // destructuring assignment for attributes from settings.json with access publc
		let wrappr_cls = "vc-sp-tab"
		wrappr_cls = wrappr_cls.concat( ` single-tab-${ id }` )

		if ( typeof el_class === 'string' && el_class ) {
			wrappr_cls += ' ' + el_class
		}

		const doAll = this.applyDO( 'margin border padding animation' )

		return (
			<div className={ wrappr_cls } { ...editor } id={ 'el-' + id } { ...doAll }>
				<div className='vcvhelper' ref='vcvhelper' data-vcvs-html={ this.getShortcode( atts ) } />
			</div>
		)
	}
}
