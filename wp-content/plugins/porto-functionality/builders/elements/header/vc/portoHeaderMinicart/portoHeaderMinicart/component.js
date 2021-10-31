import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService('portoComponent')

export default class PortoHeaderMinicart extends portoComponent.shortcodeComponent {
  constructor (props) {
    super(props)
  }

  componentDidMount () {
    const atts = this.props.atts
    super.updateShortcodeToHtml(this.getHBSearchformShortcode(atts.type, atts.content_type, atts.icon_cl), this.ref)
  }

  componentDidUpdate (prevProps, prevState) {
    const atts = this.props.atts,
      shortcode = this.getHBSearchformShortcode(atts.type, atts.content_type, atts.icon_cl)
    if (shortcode !== this.getHBSearchformShortcode(prevProps.atts.type, prevProps.atts.content_type, prevProps.atts.icon_cl)) {
      super.updateShortcodeToHtml(shortcode, this.ref)
    }
  }

  shouldComponentUpdate(nextProps, nextState) {
    return true
  }

  getHBSearchformShortcode (type, content_type, icon_cl) {
    return `[porto_hb_mini_cart type="${type}" content_type="${content_type}" icon_cl="${icon_cl}"]`
  }

  render () {
    const { id, editor, atts } = this.props
    const doAll = this.applyDO('all')
    const { type, content_type, icon_cl, el_class } = atts

    let wrapClass = 'vce-porto-hb-mini-cart'

    return (
      <div className={wrapClass + (el_class ? ' ' + el_class : '')} {...editor} id={'el-' + id} {...doAll}>
        <div className="porto-hb-mini-cart vcvhelper"  ref={(ref) => { this.ref = ref }} data-vcvs-html={this.getHBSearchformShortcode(type, content_type, icon_cl)}>
        </div>
      </div>
    )
  }
}
