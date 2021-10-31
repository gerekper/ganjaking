import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService('portoComponent')

export default class PortoHeaderWishlist extends portoComponent.shortcodeComponent {
  constructor (props) {
    super(props)
  }

  componentDidMount () {
    const atts = this.props.atts
    super.updateShortcodeToHtml(this.getHBWishlistShortcode(atts.icon_cl), this.ref)
  }

  componentDidUpdate (prevProps, prevState) {
    const atts = this.props.atts,
      shortcode = this.getHBWishlistShortcode(atts.icon_cl)
    if (shortcode !== this.getHBWishlistShortcode(prevProps.atts.icon_cl)) {
      super.updateShortcodeToHtml(shortcode, this.ref)
    }
  }

  shouldComponentUpdate(nextProps, nextState) {
    return true
  }

  getHBWishlistShortcode (icon_cl) {
    return `[porto_hb_wishlist icon_cl="${icon_cl}"]`
  }

  render () {
    const { id, editor, atts } = this.props
    const doAll = this.applyDO('all')
    const { el_class } = atts

    return (
      <div className={'vce-porto-hb-wishlist' + (el_class ? ' ' + el_class : '')} {...editor} id={'el-' + id} {...doAll}>
        <div className="porto-hb-wishlist vcvhelper" ref={(ref) => { this.ref = ref }} data-vcvs-html={this.getHBWishlistShortcode(atts.icon_cl)}>
        </div>
      </div>
    )
  }
}
